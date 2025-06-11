// ======================================================
// ELEMENTOS PRINCIPAIS E VARIÁVEIS GLOBAIS
// ======================================================

const video = document.getElementById('video');
const canvas = document.getElementById('canvas');
const fotoBox = document.getElementById('foto-box');
const btnFoto = document.getElementById('btn-foto');
const fotoTexto = document.getElementById('foto-texto');
let stream = null;
let modo = 'ligar';

// ======================================================
// FUNÇÕES DE CÂMERA E CAPTURA DE FOTO
// ======================================================

async function alternarCameraOuCaptura() {
  if (modo === 'ligar') {
    await ligarCamera();
    btnFoto.textContent = 'Capturar';
    modo = 'capturar';
  } else {
    capturarFoto();
    btnFoto.textContent = 'Fotografar';
    modo = 'ligar';
  }
}

async function ligarCamera() {
  try {
    if (!navigator.mediaDevices || !navigator.mediaDevices.getUserMedia) {
      throw new Error('Este navegador não suporta acesso à câmera ou o site não está sendo acessado de forma segura (HTTPS ou localhost).');
    }

    stream = await navigator.mediaDevices.getUserMedia({ video: true });
    video.srcObject = stream;
    video.style.display = 'block';
    canvas.style.display = 'none';
    fotoTexto.style.display = 'none';
  } catch (err) {
    alert('Erro ao acessar a câmera: ' + err.message);
  }
}


function capturarFoto() {
  const context = canvas.getContext('2d');
  canvas.width = video.videoWidth;
  canvas.height = video.videoHeight;
  context.drawImage(video, 0, 0, canvas.width, canvas.height);

  const dataURL = canvas.toDataURL('image/jpeg');
  fotoBox.style.background = `url(${dataURL}) center/cover no-repeat`;

  document.getElementById('foto_base64').value = dataURL;

  canvas.style.display = 'none';
  video.style.display = 'none';
  fotoTexto.style.display = 'none';

  if (stream) {
    stream.getTracks().forEach(track => track.stop());
  }
}

// ======================================================
// ATUALIZAR DATA E HORA AUTOMATICAMENTE
// ======================================================

function atualizarDataHora() {
  const agora = new Date();
  const data = agora.toISOString().slice(0, 10);
  const horas = String(agora.getHours()).padStart(2, '0');
  const minutos = String(agora.getMinutes()).padStart(2, '0');

  document.getElementById('data-entrada').value = data;
  document.getElementById('hora-entrada').value = `${horas}:${minutos}`;
}

setInterval(atualizarDataHora, 1000);
atualizarDataHora();

// ======================================================
// MÁSCARA PARA CPF
// ======================================================
document.getElementById('cpf').addEventListener('input', function (e) {
  let value = e.target.value.replace(/\D/g, '');
  if (value.length > 11) value = value.slice(0, 11);

  value = value.replace(/(\d{3})(\d)/, '$1.$2');
  value = value.replace(/(\d{3})(\d)/, '$1.$2');
  value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');

  e.target.value = value;
});

// ======================================================
// POP-UP CUSTOMIZADO
// ======================================================
function mostrarPopup(mensagem, duracao = 3000) {
  const popup = document.getElementById('popupToast');
  popup.textContent = mensagem;
  popup.style.display = 'block';

  // Se já tiver timer rodando, limpa para reiniciar
  if (popup._timeout) clearTimeout(popup._timeout);

  // Esconde o popup depois de "duracao" ms
  popup._timeout = setTimeout(() => {
    popup.style.display = 'none';
  }, duracao);
}


// ======================================================
// BUSCA CPF
// ======================================================
document.getElementById('btnBuscarCPF').addEventListener('click', function () {
  const cpf = document.getElementById('cpf').value.trim();

  if (cpf === '') return; // não faz nada se o campo estiver vazio

  if (cpf.length !== 14) {
    mostrarPopup('CPF inválido.');
    return;
  }

  fetch(`buscar_visitante.php?cpf=${cpf}`)
    .then(response => response.json())
    .then(data => {
      if (data.encontrado) {
        document.getElementById('nome').value = data.nome;
        document.getElementById('social').value = data.social;
        document.getElementById('orgao').value = data.orgao;

        if (data.foto) {
          fotoBox.style.background = `url(${data.foto}) center/cover no-repeat`;
          video.style.display = 'none';
          canvas.style.display = 'none';
          fotoTexto.style.display = 'none';
        }

        btnFoto.textContent = 'Alterar';
        modo = 'ligar';
      } else {
        mostrarPopup('Pessoa não cadastrada.');
        limparCampos();
      }
    })
    .catch(error => {
      console.error('Erro ao buscar visitante:', error);
      mostrarPopup('CPF não cadastrado');
    });
});

document.addEventListener('DOMContentLoaded', function () {
  // Instanciar Choices.js nos campos select com acesso global
  window.setorChoices = new Choices(document.getElementById('setor_id'), {
    searchEnabled: true,
    itemSelectText: '',
    placeholderValue: 'Selecione o setor...',
  });

  window.servidorChoices = new Choices(document.getElementById('servidorInput'), {
    searchEnabled: true,
    itemSelectText: '',
    placeholderValue: 'Selecione um servidor...',
  });

  window.secretariaChoices = new Choices(document.getElementById('secretariaInput'), {
    choices: [
      { value: 'SEAD', label: 'Secretaria de Estado de Administração e Gestão - SEAD' },
      { value: 'OAB', label: 'Ordem dos Advogados do Brasil - OAB' },
      { value: 'SEFAZ', label: 'Secretaria de Estado da Fazenda - SEFAZ' },
      { value: 'SEDUC', label: 'Secretaria de Estado de Educação e Desporto Escolar - SEDUC' },
      { value: 'SECOM', label: 'Secretaria de Estado de Comunicação Social - SECOM' },
      { value: 'PC', label: 'Polícia Civil do Estado - PC' },
      { value: 'PMAM', label: 'Polícia Militar do Amazonas - PMAM' }
    ],
    searchEnabled: true,
    itemSelectText: '',
    placeholderValue: 'Selecione uma secretaria...',
  });
});

// Função para limpar todos os campos do formulário
document.getElementById('btnLimpar').addEventListener('click', () => {
  location.reload();
});


// ======================================================
// DISPARAR BUSCA AO SAIR DO CAMPO CPF
// ======================================================
document.getElementById('cpf').addEventListener('blur', () => {
  document.getElementById('btnBuscarCPF').click();
});

// ======================================================
// AUTOCOMPLETE MANUAL PARA O SETOR
// ======================================================

const input = document.getElementById('setor_id');
const list = document.getElementById('autocomplete-list');

input.addEventListener('input', function () {
  const query = this.value.toLowerCase();
  list.innerHTML = '';

  if (!query) return;

  setores
    .filter(item => item.toLowerCase().includes(query))
    .slice(0, 10)
    .forEach(match => {
      const li = document.createElement('li');
      li.textContent = match;
      li.onclick = () => {
        input.value = match;
        list.innerHTML = '';
      };
      list.appendChild(li);
    });
});

document.addEventListener('click', e => {
  if (!e.target.closest('.form-control')) {
    list.innerHTML = '';
  }
});

// ======================================================
// CHOICES.JS PARA SELECTS DINÂMICOS
// ======================================================

document.addEventListener('DOMContentLoaded', function () {
  // Select para setor
  new Choices(document.getElementById('setor_id'), {
    searchEnabled: true,
    itemSelectText: '',
    placeholderValue: 'Selecione o setor...',
  });
});

  document.getElementById('servidorInput').addEventListener('change', function () {
    const selectedOption = this.options[this.selectedIndex];
    const status = selectedOption.getAttribute('data-status') || '--';
    document.getElementById('servidorStatus').textContent = status;
  });

  // Ativar/desativar botão Salvar com base no status do servidor selecionado
const selectServidor = document.getElementById('servidorInput');
const statusSpan = document.getElementById('servidorStatus');
const btnSalvar = document.getElementById('btnSalvar');

selectServidor.addEventListener('change', function () {
  const selectedOption = this.options[this.selectedIndex];
  const status = selectedOption.getAttribute('data-status');

  // Atualiza o texto do status
  statusSpan.textContent = status || '--';

  // Habilita o botão apenas se o status for "Ativo"
  if (status === 'Ativo') {
    btnSalvar.disabled = false;
  } else {
    btnSalvar.disabled = true;
  }
});
