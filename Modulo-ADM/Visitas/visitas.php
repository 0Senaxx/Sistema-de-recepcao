<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../Firewall/login.php");
    exit;
}

include '../../Firewall/Auth/autenticacao.php';
include '../../Firewall/Auth/controle_sessao.php';
include '../../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

// Busca setores únicos
$setores = $conn->query("SELECT id, nome FROM setores ORDER BY nome");

// Busca todos os servidores cadastrados na tabela servidores
$servidores = $conn->query("SELECT id, nome FROM servidores ORDER BY nome");

$sql = "
SELECT v.id, v.data, v.hora, v.saida, v.servidor, v.usuario_id,
       vis.nome AS nome_visitante, vis.cpf,
       s.sigla AS sigla_setor,
       srv.nome AS nome_servidor,
       u.nome AS nome_usuario
FROM visitas v
JOIN visitantes vis ON v.visitante_id = vis.id
LEFT JOIN setores s ON v.setor = s.id
LEFT JOIN servidores srv ON v.servidor = srv.id
LEFT JOIN usuarios u ON v.usuario_id = u.id
ORDER BY v.data DESC, v.hora DESC
";

$result = $conn->query($sql);
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Visitas</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body class="container py-4">

    <header class="cabecalho">
        <h1>Painel de Gestão</h1>
        <nav>
            <a class="nav" href="../index.php">Início</a>
            <a class="nav" href="../Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="../Visitantes/visitantes.php">Visitantes</a>
            <a class="nav" href="../Setores/index.php">Setores</a>
            <a class="nav" href="../Visitas/visitas.php">Visitas</a>
            <a class="nav" href="../Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <div class="topo-modulo">
                <h1>Lista de Visitas</h1>

                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" autocomplete="off" placeholder="Digite para buscar..." onkeyup="filtrarTabela()">
                    
                    <button id="abrirModal" class="btn-acao botao-relatorio">
                        <img src="../../Imagens/Icons/relatorio.png" alt="Gerar Relatório" class="icon-relatorio">
                        Gerar Relatório
                    </button>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">Data</th>
                            <th>Nome do Visitante</th>
                            <th class="text-center">CPF</th>
                            <th class="text-center">Setor</th>
                            <th>Servidor</th>
                            <th class="text-center">Entrada</th>
                            <th class="text-center">Saída</th>
                            <th class="text-center">Duração</th>
                            <th class="text-center">Registrado</th>
                        </tr>
                    </thead>

                    <tbody id="tabela-corpo">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <?php
                            // Cálculo da duração
                            $duracao = '---';
                            if (!empty($row['hora']) && !empty($row['saida'])) {
                                $entrada = new DateTime($row['hora']);
                                $saida = new DateTime($row['saida']);
                                $intervalo = $entrada->diff($saida);
                                $duracao = $intervalo->format('%H:%I:%S');
                            }
                            ?>
                            <tr>
                                <td class="text-center"><?= date('d/m/Y', strtotime($row['data'])) ?></td>

                                <td><?= $row['nome_visitante'] ?></td>
                                <td class="text-center">
                                    <?php
                                    $cpf = $row['cpf']; // Ex: "123.456.789-00"

                                    // Quebra o CPF pela máscara
                                    $partes = explode('.', $cpf); // $partes[0] = "123", $partes[1] = "456", etc.

                                    if (count($partes) === 3 && strpos($partes[2], '-') !== false) {
                                        $subpartes = explode('-', $partes[2]); // $subpartes[0] = "789", $subpartes[1] = "00"
                                        $cpf_masked = '***.' . $partes[1] . '.' . $subpartes[0] . '-**';
                                    } else {
                                        $cpf_masked = 'CPF inválido';
                                    }

                                    echo $cpf_masked;
                                    ?>
                                </td>
                                <td class="text-center"><?= $row['sigla_setor'] ?? '---' ?></td>
                                <td><?= $row['nome_servidor'] ?? '---' ?></td>
                                <td class="text-center"><?= $row['hora'] ?></td>
                                <td class="text-center"><?= $row['saida'] ?? '---' ?></td>
                                <td class="text-center"><?= $duracao ?></td>
                                <td class="text-center"><?= $row['nome_usuario'] ?? '---' ?></td>
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

    <div id="modalRelatorio" class="modal">
        <div class="modal-conteudo">
            <span class="fechar">&times;</span>

            <h2>Gerar Relatório</h2>
            <br>

            <form action="Relatorios/gerar_relatorio.php" method="get" target="_blank">
                <div class="form-linha">
                    <div class="form-campo">
                        <label for="data_inicio">Data Início:</label>
                        <input type="date" id="data_inicio" name="data_inicio">
                    </div>
                    <div class="form-campo">
                        <label for="data_fim">Data Fim:</label>
                        <input type="date" id="data_fim" name="data_fim">
                    </div>
                </div>
                <div class="form-linha">
                    <div class="form-campo">
                        <label for="setor">Setor Visitado:</label>
                        <select id="setor" name="setor">
                            <option value="">-- Todos --</option>
                            <?php while ($s = $setores->fetch_assoc()): ?>
                                <option value="<?= $s['id'] ?>"><?= $s['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                    <div class="form-campo">
                        <label for="servidor">Servidor Visitado:</label>
                        <select id="servidor" name="servidor">
                            <option value="">-- Todos --</option>
                            <?php while ($srv = $servidores->fetch_assoc()): ?>
                                <option value="<?= $srv['id'] ?>"><?= $srv['nome'] ?></option>
                            <?php endwhile; ?>
                        </select>
                    </div>
                </div>
                <div class="form-botoes">
                    <button class="bnt-pdf" type="submit">📄 Gerar PDF</button>
                    <button class="bnt-excel" type="submit" formaction="Relatorios/exportar_excel.php">📥 Exportar Excel</button>
                </div>
            </form>
        </div>
    </div>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>
    <script src="script.js"></script>
</body>

</html>