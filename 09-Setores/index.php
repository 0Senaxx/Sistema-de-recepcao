<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/Auth/autenticacao.php';
include '../01-Login/Auth/controle_sessao.php';
require_once '../conexao.php';

$sql = "SELECT * FROM setores ORDER BY nome";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="index.css">
    <title>Setores</title>
</head>
<body>
    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a href="../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <h1>Módulo de Gestão Telefônica</h1>
        </section>

        <section class="card">
            <div class="cabecalho-visitas">
                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" autocomplete="none" placeholder="Digite para buscar..."
                        onkeyup="filtrarTabela()">
                </div>

                <button id="btnAbrirModal" class="bnt-nova">Adicionar Setor</button><br><br>
            </div>

<table>
    <thead>
        <tr>
        <th colspan="6" style="text-align:center; font-size: 1.2em;">Lista de Setores Cadastrados da SEAD</th>
    </tr>
    <tr>
        <th>Sigla</th> <th>Nome</th> <th>Localização</th> <th>Ramal</th> <th>Última Atualização</th> <th>Ações</th>
    </tr>
</thead>
<tbody id="tabela-corpo">
<?php while ($setor = $res->fetch_assoc()): ?>
    <tr>
        <td class="text-center"><?= htmlspecialchars($setor['sigla']) ?></td>
        <td><?= htmlspecialchars($setor['nome']) ?></td>
        <td class="text-center"><?= htmlspecialchars($setor['localizacao']) ?></td>
        <td class="text-center"><?= htmlspecialchars($setor['ramal']) ?></td>
        <td class="text-center"><?= date('d/m/Y H:i', strtotime($setor['updated_at'])) ?></td>
        <td class="text-center">
            <a href="#" class="btnEditar" data-id="<?= $setor['id'] ?>"
               data-sigla="<?= htmlspecialchars($setor['sigla']) ?>"
               data-nome="<?= htmlspecialchars($setor['nome']) ?>"
               data-localizacao="<?= htmlspecialchars($setor['localizacao']) ?>"
               data-ramal="<?= htmlspecialchars($setor['ramal']) ?>"
            >Editar</a>   
            <a href="excluir.php?id=<?= $setor['id'] ?>" class="bntExcluir" onclick="return confirm('Deseja excluir este setor?')">Excluir</a>
        </td>
    </tr>
<?php endwhile; ?>
<tr id="sem-resultados" style="display:none;">
                <td colspan="7" style="text-align:center; font-style: italic;">Nenhum resultado encontrado.</td>
            </tr>
</tbody>
</table>
        </section>
    </main>
 <!-- Modal -->
    <div id="modalAdicionar" class="modal">
    <div class="modal-conteudo">
        <h1 id="tituloModal">Adicionar Setor</h1><br>

        <form action="salvar.php" method="POST" id="formSetor">
            <input type="hidden" name="id" id="inputId" value="">
            
            <div class="form-campo">
                <label>Sigla:</label>
                <input type="text" name="sigla" id="inputSigla" required maxlength="10" />
            </div>

            <div class="form-campo">
                <label>Nome:</label>
                <input type="text" name="nome" id="inputNome" required maxlength="100" />
            </div>

            <div class="form-localizacao">
                <label>Localização:</label>
                <select name="localizacao" id="inputLocalizacao" required>
                    <option value="">Selecione...</option>
                    <option value="1° Andar - SEAD">1° Andar - SEAD</option>
                    <option value="Mezanino - SEAD">Mezanino - SEAD</option>
                    <option value="Térreo - SEAD">Térreo - SEAD</option>
                    <option value="Subsolo - SEAD">Subsolo - SEAD</option>
                </select>
            </div>
            
            <div class="form-campo">
                <label>Ramal:</label>
                <input type="text" name="ramal" id="inputRamal" maxlength="100" placeholder="3182 - 0000 / 0000" />
                <small>* Separe os ramais com " / "</small>
            </div>

            <div class="modal-botoes">
                <button class="bntSalvar" type="submit" id="btnSalvar">Salvar</button>
                <button class="bntCancelar" type="button" id="btnFecharModal">Cancelar</button>
            </div>
        </form>
    </div>
</div>
    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>
 <script>
     const modal = document.getElementById('modalAdicionar');
const btnAbrir = document.getElementById('btnAbrirModal');
const btnFechar = document.getElementById('btnFecharModal');
const tituloModal = document.getElementById('tituloModal');
const formSetor = document.getElementById('formSetor');

const inputId = document.getElementById('inputId');
const inputSigla = document.getElementById('inputSigla');
const inputNome = document.getElementById('inputNome');
const inputLocalizacao = document.getElementById('inputLocalizacao');
const inputRamal = document.getElementById('inputRamal');

// Força caixa alta em tempo real
inputSigla.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});

inputNome.addEventListener('input', function() {
    this.value = this.value.toUpperCase();
});


// Abrir modal para adicionar (limpa campos)
btnAbrir.addEventListener('click', function(e) {
    e.preventDefault();
    tituloModal.textContent = 'Adicionar Setor';
    formSetor.action = 'salvar.php'; // mantém salvar.php para inserir
    inputId.value = '';
    inputSigla.value = '';
    inputNome.value = '';
    inputLocalizacao.value = '';
    inputRamal.value = '';
    modal.style.display = 'block';
});

// Abrir modal para editar (preenche campos com dados da linha)
document.querySelectorAll('.btnEditar').forEach(function(btn) {
    btn.addEventListener('click', function(e) {
        e.preventDefault();

        tituloModal.textContent = 'Editar Setor';
        formSetor.action = 'salvar.php'; // usa salvar.php também para edição

        inputId.value = this.dataset.id;
        inputSigla.value = this.dataset.sigla;
        inputNome.value = this.dataset.nome;
        inputLocalizacao.value = this.dataset.localizacao;
        inputRamal.value = this.dataset.ramal;

        modal.style.display = 'block';
    });
});

// Fechar modal ao clicar no botão Cancelar
btnFechar.addEventListener('click', function(e) {
    e.preventDefault();
    modal.style.display = 'none';
});

// Fechar modal ao clicar fora da área do modal
window.addEventListener('click', function(event) {
    if (event.target == modal) {
        modal.style.display = 'none';
    }
});

// Máscara para ramal no formato 0000 - 0000
inputRamal.addEventListener('input', function() {
     this.value = this.value.replace(/[^0-9\/ -]/g, ''); // permite números, traço, espaço e barra
});

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

    </script>
</body>
</html>
