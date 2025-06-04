
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


document.getElementById("abrirModal").onclick = function () {
    document.getElementById("modalRelatorio").style.display = "block";
};

document.querySelector(".fechar").onclick = function () {
    document.getElementById("modalRelatorio").style.display = "none";
};

window.onclick = function (event) {
    if (event.target === document.getElementById("modalRelatorio")) {
        document.getElementById("modalRelatorio").style.display = "none";
    }
};

