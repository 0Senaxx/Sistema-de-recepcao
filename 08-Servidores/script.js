

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
    document.getElementById('cpf').value = servidor.cpf;
    document.getElementById('contato').value = servidor.contato;
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
    document.getElementById('cpf').value = '';
    document.getElementById('contato').value = '';
    document.getElementById('status').value = 'Ativo';

    // Limpa o Choices.js
    if (choicesSetor) {
      choicesSetor.setChoiceByValue('');
    }
  }
}


// Fecha o modal
document.getElementById('btnFecharModal').onclick = function() {
  document.getElementById('modalServidor').style.display = 'none';
}

// Abre modal vazio para adicionar
document.getElementById('btnAbrirModal').onclick = function() {
  abrirModal(null);
}

// Função para enviar dados via fetch (AJAX)
document.getElementById('formServidor').onsubmit = function(event) {
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

   document.addEventListener("DOMContentLoaded", function () {
    const cpfInput = document.getElementById("cpf");

    cpfInput.addEventListener("input", function () {
      let value = cpfInput.value;

      // Remove tudo que não for número
      value = value.replace(/\D/g, "");

      // Limita a 11 dígitos
      value = value.substring(0, 11);

      // Aplica a máscara: XXX.XXX.XXX-XX
      if (value.length > 9) {
        value = value.replace(/(\d{3})(\d{3})(\d{3})(\d{1,2})/, "$1.$2.$3-$4");
      } else if (value.length > 6) {
        value = value.replace(/(\d{3})(\d{3})(\d{1,3})/, "$1.$2.$3");
      } else if (value.length > 3) {
        value = value.replace(/(\d{3})(\d{1,3})/, "$1.$2");
      }

      cpfInput.value = value;
    });
  });

  document.addEventListener("DOMContentLoaded", function () {
    const contatoInput = document.getElementById("contato");

    contatoInput.addEventListener("input", function () {
      let value = contatoInput.value.replace(/\D/g, ""); // Remove tudo que não for número

      // Limita a 11 dígitos (DDD + 9 dígitos)
      value = value.substring(0, 11);

      // Formatação dinâmica
      if (value.length >= 11) {
        value = value.replace(/(\d{2})(\d{5})(\d{4})/, "($1) $2-$3");
      } else if (value.length >= 10) {
        value = value.replace(/(\d{2})(\d{4})(\d{4})/, "($1) $2-$3");
      } else if (value.length >= 6) {
        value = value.replace(/(\d{2})(\d{4})(\d{0,4})/, "($1) $2-$3");
      } else if (value.length >= 3) {
        value = value.replace(/(\d{2})(\d{0,5})/, "($1) $2");
      } else {
        value = value.replace(/(\d{0,2})/, "($1");
      }

      contatoInput.value = value;
    });
  });