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
include '../conexao.php';

// Consulta todos os setores
$sqlSetores = "SELECT * FROM setores ORDER BY nome ASC";
$resultSetores = $conn->query($sqlSetores);

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8" />
    <title>Controle de Visitas - SEAD</title>
    <link rel="stylesheet" href="estilo.css" />
</head>

<body>

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">+ Nova Visita</a>
            <a href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a href="../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="card">
            <div class="topo">
                <h2>Lista de Ramais SEAD</h2>
                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" placeholder="Digite para buscar..." autocomplete="off" onkeyup="filtrarTabela()" />
                </div>
            </div>

            <table>
                <thead>
                    <tr>
                        <th></th> <!-- Coluna para o ícone de abrir/fechar -->
                        <th class="text-center">SIGLA</th>
                        <th class="text-center">LOCALIZAÇÃO</th>
                        <th>NOME DO SETOR</th>
                        <th class="text-center">RAMAL</th>
                    </tr>
                </thead>

                <tbody id="tabelaRamais" style="border-collapse: collapse; width: 100%;">
                    <?php while ($setor = $resultSetores->fetch_assoc()) : ?>
                        <?php
                        $setorId = $setor['id'];
                        $sigla = $setor['sigla'];
                        $localizacao = $setor['localizacao'];
                        $nomeSetor = $setor['nome'];
                        $ramal = $setor['ramal'];

                        // Buscar servidores
                        $sqlServidores = "SELECT nome, contato FROM servidores WHERE setor_id = ? ORDER BY nome ASC";
                        $stmtServ = $conn->prepare($sqlServidores);
                        $stmtServ->bind_param("i", $setorId);
                        $stmtServ->execute();
                        $resultServidores = $stmtServ->get_result();

                        $classeServidores = "servidores-setor-" . $setorId;
                        ?>
                        <tr class="linha-setor setor" data-setor-id="<?= $classeServidores ?>" style="cursor:pointer" onclick="toggleServidores('<?= $classeServidores ?>')">
                            <td id="icone-<?= $classeServidores ?>" class="text-center">▶️</td>
                            <td class="text-center"><?= htmlspecialchars($sigla) ?></td>
                            <td class="text-center"><?= htmlspecialchars($localizacao) ?></td>
                            <td><?= htmlspecialchars($nomeSetor) ?></td>
                            <td class="text-center"><?= htmlspecialchars($ramal) ?></td>
                        </tr>

                        <?php if ($resultServidores->num_rows > 0) : ?>
                            <?php while ($servidor = $resultServidores->fetch_assoc()) : ?>
                                <tr class="linha-servidor <?= $classeServidores ?>" style="display:none;">
                                    <td colspan="3" ></td>
                                    <td><?= htmlspecialchars($servidor['nome']) ?></td>
                                    <td class="text-center">
                                  
                                        <!-- Verifica se o contato do servidor está preenchido -->
                                        <?php if (!empty($servidor['contato'])) : ?>
                                            <img src="../uploads/Logozap.svg" alt="WhatsApp" style="height:16px; vertical-align:middle; margin-left:4px;">
                                        <?php endif; ?>
                                        
                                        <?php if (!empty($servidor['contato'])) : ?>
                                            <a href="https://wa.me/55<?= preg_replace('/\D/', '', $servidor['contato']) ?>" target="_blank" rel="noopener noreferrer">
                                                <?= htmlspecialchars($servidor['contato']) ?>
                                            </a>
                                        <?php else : ?>
                                            —
                                        <?php endif; ?>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        <?php else : ?>
                            <tr class="linha-servidor <?= $classeServidores ?>" style="display:none;">
                                <td colspan="5" class="sem-servidor">Nenhum servidor cadastrado neste setor!</td>
                            </tr>
                        <?php endif; ?>
                    <?php endwhile; ?>
                </tbody>
            </table>



        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>

    <div class="aviso" id="popup-aviso" style="display:none;">Nenhum resultado encontrado.</div>

    <script>
        function filtrarTabela() {
            var input = document.getElementById("filtro");
            var filtro = input.value.toLowerCase();
            var tabela = document.getElementById("tabelaRamais");
            var linhas = tabela.getElementsByTagName("tr");

            if (filtro === "") {
                for (let i = 0; i < linhas.length; i++) {
                    let linha = linhas[i];
                    if (linha.classList.contains('linha-setor')) {
                        linha.style.display = 'table-row';
                    } else if (linha.classList.contains('linha-servidor')) {
                        linha.style.display = 'none';
                    }
                }
                return;
            }

            let encontrou = false;
            for (let i = 0; i < linhas.length; i++) {
                let linha = linhas[i];
                // Considera o texto da linha toda (concatena todas as colunas)
                let textoLinha = linha.textContent.toLowerCase();
                if (textoLinha.indexOf(filtro) > -1) {
                    linha.style.display = 'table-row';
                    encontrou = true;

                    // Se for linha de servidor, mostra também o setor correspondente
                    if (linha.classList.contains('linha-servidor')) {
                        // Exibir setor (linha anterior com classe linha-setor)
                        let setorLinha = linha.previousElementSibling;
                        if (setorLinha && setorLinha.classList.contains('linha-setor')) {
                            setorLinha.style.display = 'table-row';
                        }
                    }
                } else {
                    linha.style.display = 'none';
                }
            }

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

        function toggleServidores(classe) {
            const linhas = document.querySelectorAll("." + classe);
            const icone = document.getElementById("icone-" + classe);
            let algumVisivel = false;

            linhas.forEach(linha => {
                if (linha.style.display !== "none") {
                    algumVisivel = true;
                }
            });

            linhas.forEach(linha => {
                linha.style.display = algumVisivel ? "none" : "table-row";
            });

            // Atualiza o ícone
            if (icone) {
                icone.textContent = algumVisivel ? "▶️" : "🔽";
            }
        }
    </script>

</body>

</html>
