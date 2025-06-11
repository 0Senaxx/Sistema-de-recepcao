<?php
session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/autenticacao.php';

include '../conexao.php';

// Busca setores únicos
$setores = $conn->query("SELECT id, nome FROM setores ORDER BY nome");

// Busca todos os servidores cadastrados na tabela servidores
$servidores = $conn->query("SELECT id, nome FROM servidores ORDER BY nome");


$sql = "
SELECT v.id, v.data, v.hora, v.saida, v.servidor,
       vis.nome AS nome_visitante, vis.cpf,
       s.nome AS nome_setor,
       srv.nome AS nome_servidor
FROM visitas v
JOIN visitantes vis ON v.visitante_id = vis.id
LEFT JOIN setores s ON v.setor = s.id
LEFT JOIN servidores srv ON v.servidor = srv.id
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
        <h1>Recepção SEAD</h1>
        <nav>
            <a href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">+ Nova Visita</a>
            <a href="../05-Visitas/visitas.php" onclick="fadeOut(event, this)">Lista de Visitas</a>
            <a href="../04-Visitantes/visitantes.php" onclick="fadeOut(event, this)">Lista de Visitantes</a>
            <a href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a href="../01-Login/logout.php">Sair</a>
        </nav>
    </header>
    
    <main>

        <section class="card">

        <div class="topo">
                <h2>Lista de Visitas</h2>
                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" autocomplete="off" placeholder="Digite para buscar..."
                        onkeyup="filtrarTabela()">
                    <button id="abrirModal" class="botao-relatorio">📊 Gerar Relatório</button>
                </div>
            </div>

    <table>
        <thead>
            <tr>
                <th>Data</th>
                <th>Entrada</th>
                <th>Nome do Visitante</th>
                <th>CPF</th>
                <th>Setor Visitado</th>
                <th>Servidor Visitado</th>
                <th>Saída</th>
            </tr>
        </thead>
        <tbody id="tabela-corpo">
            <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?= date('d/m/Y', strtotime($row['data'])) ?></td>
                    <td><?= $row['hora'] ?></td>
                    <td><?= $row['nome_visitante'] ?></td>
                    <td><?= $row['cpf'] ?></td>
                    <td><?= $row['nome_setor'] ?? '---' ?></td>
                    <td><?= $row['nome_servidor'] ?? '---' ?></td>
                    <td><?= $row['saida']?></td>
                </tr>
            <?php endwhile; ?>
            <tr id="sem-resultados" style="display:none;">
                <td colspan="7" style="text-align:center; font-style: italic;">Nenhum resultado encontrado.</td>
            </tr>
        </tbody>
    </table>
        </section>
    </main>

        <div id="modalRelatorio" class="modal">
            <div class="modal-conteudo">
                <span class="fechar">&times;</span>

                <h2>Gerar Relatório</h2>
                <br>

                <form action="../07-Relatorios/gerar_relatorio.php" method="get" target="_blank">
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
                        <button class="bnt-excel" type="submit" formaction="../07-Relatorios/exportar_excel.php">📥 Exportar Excel</button>
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
