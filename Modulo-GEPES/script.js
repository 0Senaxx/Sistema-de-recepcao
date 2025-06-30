

// ======================================================
// CHOICES.JS PARA SELECTS DINÂMICOS
// ======================================================

// Armazene a instância do Choices em uma variável global
let choicesSetor;

document.addEventListener('DOMContentLoaded', function () {
  choicesSetor = new Choices(document.getElementById('setor_modal'), {
    searchEnabled: true,
    itemSelectText: '',
    placeholderValue: 'Selecione o setor...',
  });
});


// Função para abrir modal com dados (ou vazio para adicionar)
function abrirModal(servidor = null) {
  document.getElementById('modalServidor').style.display = 'flex';

  if (servidor) {
    document.getElementById('modalTitulo').textContent = 'Editar Servidor';
    document.getElementById('servidor_id').value = servidor.id;
    document.getElementById('nome').value = servidor.nome;
    document.getElementById('matricula').value = servidor.matricula;
    document.getElementById('status').value = servidor.status;
    choicesSetor.setChoiceByValue(String(servidor.setor_id));

    // Usa Choices.js para selecionar o setor no modal
    if (choicesSetor) {
      choicesSetor.setChoiceByValue(String(servidor.setor_id));
    }
  } else {
    document.getElementById('modalTitulo').textContent = 'Adicionar Servidor';
    document.getElementById('servidor_id').value = 0;
    document.getElementById('nome').value = '';
    document.getElementById('matricula').value = '';
    document.getElementById('status').value = 'Ativo';

    // Limpa o Choices.js
    if (choicesSetor) {
      choicesSetor.setChoiceByValue('');
    }
  }
}


// Fecha o modal
document.getElementById('btnFecharModal').onclick = function () {
  document.getElementById('modalServidor').style.display = 'none';
}

// Abre modal vazio para adicionar
document.getElementById('btnAbrirModal').onclick = function () {
  abrirModal(null);
}

// Função para enviar dados via fetch (AJAX)
document.getElementById('formServidor').onsubmit = function (event) {
  event.preventDefault();

  const formData = new FormData(this);

  fetch('salvar.php', {
    method: 'POST',
    body: formData
  })
    .then(response => response.text())
    .then(data => {
      alert(data); // Você pode melhorar a mensagem de retorno
      location.reload(); // Recarrega a página para mostrar atualização
    })
    .catch(err => alert('Erro ao salvar servidor.'));
};

// Função para abrir modal com dados do servidor para edição
function editarServidor(id) {
  fetch('buscar_servidor.php?id=' + id)
    .then(response => response.json())
    .then(servidor => {
      abrirModal(servidor);
    })
    .catch(err => alert('Erro ao buscar dados do servidor.'));
}


function filtrarTabela() {
  const filtro = document.getElementById('filtro').value.toLowerCase();
  const tabela = document.getElementById('tabela-corpo');
  const linhas = tabela.getElementsByTagName('tr');

  let encontrou = false;

  for (let i = 0; i < linhas.length; i++) {
    const linha = linhas[i];

    // Pula a linha da mensagem "Nenhum resultado"
    if (linha.id === 'sem-resultados') continue;

    const textoLinha = linha.textContent.toLowerCase();

    if (textoLinha.includes(filtro)) {
      linha.style.display = '';
      encontrou = true;
    } else {
      linha.style.display = 'none';
    }
  }

  // Mostrar ou esconder a linha de "Nenhum resultado"
  document.getElementById('sem-resultados').style.display = encontrou ? 'none' : '';
}

$('#matricula').mask('000.000-0 A', {
  translation: {
    'A': { pattern: /[A-Za-z]/ }
  }
});

