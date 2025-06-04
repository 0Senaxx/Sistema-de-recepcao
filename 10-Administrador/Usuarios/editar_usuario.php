<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $perfil = $_POST['perfil'];

    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, cpf=?, senha=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $cpf, $senha, $perfil, $id);
    } else {
        $sql = "UPDATE usuarios SET nome=?, cpf=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $cpf, $perfil, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Erro ao atualizar usuário: " . $conn->error;
    }
} else {
    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Editar Usuário</title>
</head>
<body>
    <h1>Editar Usuário</h1>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

        <label>CPF:</label><br>
        <input type="text" name="cpf" value="<?= htmlspecialchars($usuario['cpf']) ?>" required><br><br>

        <label>Senha (deixe em branco para não alterar):</label><br>
        <input type="password" name="senha"><br><br>

        <label>Perfil:</label><br>
        <select name="perfil" required>
            <option value="ADM" <?= $usuario['perfil'] == 'ADM' ? 'selected' : '' ?>>Administrador</option>
            <option value="Recepcionista" <?= $usuario['perfil'] == 'Recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
            <option value="GEPES" <?= $usuario['perfil'] == 'GEPES' ? 'selected' : '' ?>>GEPES</option>
            <option value="GCP" <?= $usuario['perfil'] == 'GCP' ? 'selected' : '' ?>>GCP</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
    <a href="../index.php">Voltar</a>
</body>
</html>
