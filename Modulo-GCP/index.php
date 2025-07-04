<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Firewall/login.php");
    exit;
}

include '../Firewall/Auth/autenticacao.php';
include '../Firewall/Auth/controle_sessao.php';
include '../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------


$sql = "SELECT * FROM setores ORDER BY nome";
$res = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="index.css">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Setores</title>
</head>

<body>
    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a href="#">Início</a>
            <a href="Ramais/ramais.php">Ramais</a>
            <a href="../Firewall/Auth/logout.php">Sair</a>
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

                <button id="btnAbrirModal" class="btn-acao bnt-nova">
                    <img src="../Imagens/Icons/adicionar-usuario.png" alt="adicionar setor">
                    Adicionar Setor
                </button><br><br>
            </div>

            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">Sigla</th>
                            <th>Nome</th>
                            <th class="text-center">Localização</th>
                            <th class="text-center">Ramal</th>
                            <th class="text-center">Telefone</th>
                            <th class="text-center">Atualizado</th>
                            <th class="text-center">Ações</th>
                        </tr>
                    </thead>
                    <tbody id="tabela-corpo">
                        <?php while ($setor = $res->fetch_assoc()): ?>
                            <tr>
                                <td class="col-sigla text-center"><?= htmlspecialchars($setor['sigla']) ?></td>
                                <td class="col-setor"><?= htmlspecialchars($setor['nome']) ?></td>
                                <td class="col-local  text-center"><?= htmlspecialchars($setor['localizacao']) ?></td>
                                <td class="col-ramal text-center"><?= htmlspecialchars($setor['ramal']) ?></td>
                                <td class="col-telefone text-center"><?= htmlspecialchars($setor['telefone']) ?></td>
                                <td class="col-atualizacao  text-center"><?= date('d/m/Y', strtotime($setor['updated_at'])) ?></td>
                                <td class="col-acao text-center">
                                    <a href="#" class="btn-acao btnEditar"
                                        data-id="<?= $setor['id'] ?>"
                                        data-sigla="<?= htmlspecialchars($setor['sigla']) ?>"
                                        data-nome="<?= htmlspecialchars($setor['nome']) ?>"
                                        data-localizacao="<?= htmlspecialchars($setor['localizacao']) ?>"
                                        data-ramal="<?= htmlspecialchars($setor['ramal']) ?>"
                                        data-telefone="<?= htmlspecialchars($setor['telefone']) ?>">
                                        <img src="../Imagens/Icons/editar.png" alt="Editar setor">
                                        Editar
                                    </a>

                                    <a href="excluir.php?id=<?= $setor['id'] ?>" class="btn-acao bntExcluir" onclick="return confirm('Deseja excluir este setor?')">
                                        <img src="../Imagens/Icons/excluir.png" alt="excluir setor">
                                        Excluir
                                    </a>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                        <tr id="sem-resultados" style="display:none;">
                            <td colspan="7" style="text-align:center; font-style: italic;">Nenhum resultado encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
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
                    <input type="text" name="ramal" id="inputRamal" maxlength="100" placeholder="Separe os ramais com /" />
                </div>

                <div class="form-campo">
                    <label>Telefonia móvel (celulares corporativo):</label>
                    <input type="text" name="telefone" id="inputTelefone" maxlength="15" placeholder="Se aplicavel">
                </div><br><br>


                <div class="modal-botoes">
                    <button class="btn-acao bntSalvar" type="submit" id="btnSalvar">
                        <img src="../Imagens/Icons/salve.png" alt="Salvar">
                        Salvar
                    </button>

                    <button class="btn-acao bntCancelar" type="button" id="btnFecharModal">
                        <img src="../Imagens/Icons/cancelar.png" alt="cancelar operação">
                        Cancelar
                    </button>
                </div>
            </form>
        </div>
    </div>

    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
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
        const inputTelefone = document.getElementById('inputTelefone');

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
            inputTelefone.value = '';
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
                inputTelefone.value = this.dataset.telefone;

                modal.style.display = 'block';
            });
        });

        // Fechar modal ao clicar no botão Cancelar
        btnFechar.addEventListener('click', function(e) {
            e.preventDefault();
            modal.style.display = 'none';
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

        $(document).ready(function() {
            $('#inputTelefone').mask('(00) 0 0000-0000');
        });
    </script>
</body>

</html>