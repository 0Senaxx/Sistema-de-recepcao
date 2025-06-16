<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] !== 'Recepcionista') {
    header("Location: ../01-Login/login.php");
    exit;
}

include '../conexao.php';

$mensagem = "";

// Registrar ocorrência
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $usuario_id = $_SESSION['usuario_id'];
    $descricao = trim($_POST['descricao']);

    if (!empty($descricao)) {
        $sql = "INSERT INTO ocorrencias (usuario_id, descricao) VALUES (?, ?)";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("is", $usuario_id, $descricao);

        if ($stmt->execute()) {
            $mensagem = "Ocorrência registrada com sucesso.";
        } else {
            $mensagem = "Erro ao registrar ocorrência.";
        }
    }
}

// Buscar ocorrências com JOIN no nome do usuário
$sqlOcorrencias = "
    SELECT o.id, o.data_hora, o.descricao, u.nome AS responsavel
    FROM ocorrencias o
    JOIN usuarios u ON o.usuario_id = u.id
    ORDER BY o.data_hora DESC
";
$resultOcorrencias = $conn->query($sqlOcorrencias);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="estilo.css">
    <title>Registro de Ocorrências</title>
</head>
<body>

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a class="nav" href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a class="nav" href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">Nova Visita</a>
            <a class="nav" href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a class="nav" href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a class="nav" href="../12-Ocorrencias/registro_ocorrencia.php" onclick="fadeOut(event, this)">Ocorrências</a>
            <a class="nav" href="../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="card">
            <h1>Registro de Ocorrência</h1><br>

            <?php if ($mensagem): ?>
                <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <label for="descricao">Descreva a Ocorrência:</label><br>
                    <textarea class="descricao" name="descricao" id="descricao" required></textarea><br>
                    <button type="submit" class="bnt-registrar">Registrar</button>
                </form>
            </div>

            <h2>Lista de Ocorrências</h2>
            <table>
                <thead>
                    <tr>
                        <th class="text-center">Nº</th>
                        <th class="text-center">Data</th>
                        <th>Descrição</th>
                        <th>Responsável</th>
                    </tr>
                </thead>
                <tbody>
                    <?php $numero = 1; ?>
                    <?php while ($row = $resultOcorrencias->fetch_assoc()): ?>
                        <tr>
                            <td class="text-center"><?= str_pad($numero++, 2, '0', STR_PAD_LEFT) ?></td>
                            <td class="text-center"><?= date('d/m/Y H:i', strtotime($row['data_hora'])) ?></td>
                            <td><?= nl2br(htmlspecialchars($row['descricao'])) ?></td>
                            <td><?= htmlspecialchars($row['responsavel']) ?></td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>
</body>
</html>
