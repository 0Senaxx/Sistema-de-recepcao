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
// VALIDAÇÃO DE CPF 
// ======================================================

function validarCPF() {
  const cpfInput = document.getElementById('cpf');
  const cpf = cpfInput.value.replace(/\D/g, '');

  if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
    mostrarPopup('CPF inválido.', 'error');
    cpfInput.classList.add('input-erro', 'cpf-invalido');
    return false;
  }

  // Cálculo dos dígitos verificadores
  let soma = 0;
  for (let i = 0; i < 9; i++) {
    soma += parseInt(cpf.charAt(i)) * (10 - i);
  }
  let resto = 11 - (soma % 11);
  let dig1 = resto >= 10 ? 0 : resto;

  soma = 0;
  for (let i = 0; i < 10; i++) {
    soma += parseInt(cpf.charAt(i)) * (11 - i);
  }
  resto = 11 - (soma % 11);
  let dig2 = resto >= 10 ? 0 : resto;

  if (parseInt(cpf.charAt(9)) === dig1 && parseInt(cpf.charAt(10)) === dig2) {
    cpfInput.classList.remove('input-erro', 'cpf-invalido');
    return true;
  } else {
    mostrarPopup('CPF inválido.', 'error');
    cpfInput.classList.add('input-erro', 'cpf-invalido');
    return false;
  }
}


// ======================================================
// BUSCA CPF
// ======================================================
document.getElementById('btnBuscarCPF').addEventListener('click', function () {
  const cpf = document.getElementById('cpf').value.trim();

  if (cpf === '') {
    mostrarPopup('Por favor, preencha o CPF para buscar.', 'warning');
    return;
  }

  if (cpf.length !== 14) {
    mostrarPopup('CPF inválido.', 'error');
    return;
  }

  fetch(`buscar_visitante.php?cpf=${cpf}`)
    .then(response => response.json())
    .then(data => {
      if (data.encontrado) {
  mostrarPopup('CPF já cadastrado.', 'info');

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
  mostrarPopup('CPF não cadastrado.', 'warning');
}

    })
    .catch(error => {
      console.error('Erro ao buscar visitante:', error);
      mostrarPopup('Erro ao buscar CPF.', 'error');
    });
});

// ======================================================
// POP-UP CUSTOMIZADO
// ======================================================
function mostrarPopup(mensagem, tipo = 'success', duracao = 5000) {
  const popup = document.getElementById('popupToast');
  popup.textContent = mensagem;

  popup.classList.remove('success', 'error', 'warning', 'info');
  popup.classList.add(tipo);
  popup.classList.add('show');

  if (popup._timeout) clearTimeout(popup._timeout);

  popup._timeout = setTimeout(() => {
    popup.classList.remove('show');
  }, duracao);
}

// ======================================================
// VERIFICAR CAMPOS OBRIGATÓRIOS
// ======================================================
function verificarCamposObrigatorios() {
  // Aqui você pode validar outros campos obrigatórios, estou apenas verificando CPF como exemplo
  const cpfInput = document.getElementById('cpf');
  if (!cpfInput.value.trim()) {
    mostrarPopup('Por favor, preencha todos os campos obrigatórios.', 'warning');
    cpfInput.classList.add('input-erro');
    return false;
  }
  cpfInput.classList.remove('input-erro');
  return true;
}

// ======================================================
// FUNÇÃO PARA LIMPAR CAMPOS
// ======================================================
function limparCampos() {
  document.getElementById('nome').value = '';
  document.getElementById('social').value = '';
  document.getElementById('orgao').value = '';
  document.getElementById('cpf').value = '';
  fotoBox.style.background = '';
  video.style.display = 'none';
  canvas.style.display = 'none';
  fotoTexto.style.display = 'block';
  btnFoto.textContent = 'Fotografar';
  modo = 'ligar';
}

// ======================================================
// FORMULÁRIO - ENVIO
// ======================================================
const form = document.querySelector('.formulario');

form.addEventListener('submit', function (event) {
  event.preventDefault();

  // Verificar campos obrigatórios
  if (!verificarCamposObrigatorios()) return;

  // Validar CPF
  if (!validarCPF()) return;

  mostrarPopup('Salvando visita, aguarde...', 'success', 2500);

  // Simular delay para salvar e enviar
  setTimeout(() => {
    form.submit();
  }, 2500);
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

window.servidorChoices = new Choices(document.getElementById('servidorInput'), {
  searchEnabled: true,
  itemSelectText: '',
  placeholderValue: 'Selecione um servidor...',
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

  // Habilita o botão apenas se o status for "Disponível"
  if (status === 'Disponível') {
    btnSalvar.disabled = false;
  } else {
    btnSalvar.disabled = true;
  }
});





// Função para limpar todos os campos do formulário
document.getElementById('btnLimpar').addEventListener('click', () => {
  location.reload();
});
