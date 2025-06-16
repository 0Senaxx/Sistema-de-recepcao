<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../01-Login/login.php");
    exit;
}

include '../../conexao.php';

// Buscar usuários
$sql = "SELECT id, nome, cpf, matricula, perfil FROM usuarios";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Gestão de Usuários - Administrador</title>
    <link rel="stylesheet" href="../estilo.css">
</head>
<body>
    <header>
        <h1>Gestão de Usuários</h1>
        <nav>
            <ul>
                <li><a href="../index.php">Início</a></li>
                <li><a href="usuarios.php">Usuários</a></li>
                <li><a href="../../08-Servidores/index.php">Servidores</a></li>
                <li><a href="../../09-Setores/index.php">Setores</a></li>
                <li><a href="../../07-Relatorio/gerar_relatorio.php">Relatórios</a></li>
                <li><a href="../../01-Login/Auth/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section>
            <a href="adicionar_usuario.php" class="btn-adicionar">Adicionar Novo Usuário</a>
            <table border="1">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>CPF</th>
                        <th>MATRÍCULA</th>
                        <th>Perfil</th>
                        <th>Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= htmlspecialchars($row['id']) ?></td>
                        <td><?= htmlspecialchars($row['nome']) ?></td>
                        <td><?= htmlspecialchars($row['cpf']) ?></td>
                        <td><?= htmlspecialchars($row['matricula']) ?></td>
                        <td><?= htmlspecialchars($row['perfil']) ?></td>
                        <td>
                            <a href="editar_usuario.php?id=<?= $row['id'] ?>">Editar</a> |
                            <a href="excluir_usuario.php?id=<?= $row['id'] ?>" onclick="return confirm('Tem certeza que deseja excluir este usuário?');">Excluir</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>
</body>
</html>
