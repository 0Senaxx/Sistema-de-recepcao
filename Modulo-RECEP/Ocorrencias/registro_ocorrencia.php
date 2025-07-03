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
            <a class="nav" href="../Inicio/index.php">Início</a>
            <a class="nav" href="../Registrar/nova_visita.php">Nova Visita</a>
            <a class="nav" href="../Ramais/ramais.php">Ramais SEAD</a>
            <a class="nav" href="../Repositorio/repositorio.php">Repositório</a>
            <a class="nav" href="../Ocorrencias/registro_ocorrencia.php">Ocorrências</a>
            <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <h1>Registro de Ocorrência</h1><br>

            <?php if ($mensagem): ?>
                <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <div class="form-container">
                <form method="POST">
                    <label for="descricao">Descreva a Ocorrência:</label><br>
                    <textarea class="descricao" name="descricao" id="descricao" required></textarea><br>
                    <button type="submit" class="btn-acao bnt-registrar">
                        <img src="../../Imagens/Icons/salve.png" alt="Registrar Ocorrência">
                        Registrar
                    </button>
                </form>
            </div>
        </section>

        <section class="card">
            <div class="table-container">
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
            </div>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>
</body>

</html>