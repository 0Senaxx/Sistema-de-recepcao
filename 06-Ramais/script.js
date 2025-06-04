function filtrarTabela() {
    var input = document.getElementById("filtro");
    var filtro = input.value.toLowerCase();
    var tabela = document.getElementById("tabelaRamais");
    var tabelaCorpo = tabela.querySelector('tbody');
    var linhas = tabelaCorpo.getElementsByTagName("tr");

    if (filtro === "") {
        // Mostrar só setores e ocultar servidores
        for (let i = 0; i < linhas.length; i++) {
            let linha = linhas[i];
            if (linha.classList.contains('setor')) {
                linha.style.display = 'table-row';
                linha.setAttribute('data-expandido', 'false');
                // Atualizar ícone para fechado ▶️
                let icone = linha.querySelector('td:first-child');
                if (icone) icone.textContent = "▶️";
            } else if (linha.classList.contains('servidor')) {
                linha.style.display = 'none';
            }
        }
        return;
    }

    let setorAtual = null;
    let servidoresSetor = [];

    // Função auxiliar para processar um setor e seus servidores
    function processarSetor(setor, servidores, filtro) {
        let textoSetor = setor.textContent.toLowerCase();
        let encontrouSetor = textoSetor.includes(filtro);

        let encontrouServidor = false;
        servidores.forEach(servidor => {
            let textoServidor = servidor.textContent.toLowerCase();
            if (textoServidor.includes(filtro)) {
                servidor.style.display = "table-row";
                encontrouServidor = true;
            } else {
                servidor.style.display = "none";
            }
        });

        if (encontrouSetor || encontrouServidor) {
            setor.style.display = "table-row";
            setor.setAttribute('data-expandido', 'true');
            // Atualiza ícone para aberto 🔽
            let icone = setor.querySelector('td:first-child');
            if (icone) icone.textContent = "🔽";
        } else {
            setor.style.display = "none";
            setor.setAttribute('data-expandido', 'false');
        }
    }

    // Percorrer linhas para agrupar setores e servidores e processar
    for (let i = 0; i < linhas.length; i++) {
        let linha = linhas[i];
        if (linha.classList.contains('setor')) {
            if (setorAtual) {
                processarSetor(setorAtual, servidoresSetor, filtro);
            }
            setorAtual = linha;
            servidoresSetor = [];
        } else if (linha.classList.contains('servidor')) {
            servidoresSetor.push(linha);
        }
    }
    if (setorAtual) {
        processarSetor(setorAtual, servidoresSetor, filtro);
    }

    // Verifica se encontrou algum resultado
    let encontrou = [...linhas].some(linha => linha.style.display !== 'none');
    if (!encontrou) {
        mostrarPopup("Nenhum resultado encontrado.");
    }
}

function mostrarPopup(msg) {
    const popup = document.getElementById("popup-aviso");
    popup.textContent = msg;
    popup.style.display = "block";

    setTimeout(() => {
        popup.style.display = "none";
    }, 5000);
}

// toggleServidores agora atualiza ícone e atributo data-expandido
function toggleServidores(classe) {
    const linhas = document.querySelectorAll("." + classe);
    if (linhas.length === 0) return;

    // Pega a primeira linha-setor que controla esse grupo
    const setorLinha = document.querySelector(`tr[data-setor-id="${classe}"]`);
    if (!setorLinha) return;

    let expandido = setorLinha.getAttribute('data-expandido') === 'true';

    linhas.forEach(linha => {
        linha.style.display = expandido ? "none" : "table-row";
    });

    // Atualiza o estado e o ícone
    setorLinha.setAttribute('data-expandido', (!expandido).toString());
    let icone = setorLinha.querySelector('td:first-child');
    if (icone) {
        icone.textContent = expandido ? "▶️" : "🔽";
    }
}
