<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../conexao.php';

    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $perfil = $_POST['perfil'];

    $sql = "INSERT INTO usuarios (nome, matricula, senha, perfil) VALUES (?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $matricula, $senha, $perfil);

    if ($stmt->execute()) {
        header("Location: index.php");
        exit;
    } else {
        echo "Erro ao adicionar usuário: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Adicionar Usuário</title>
</head>
<body>
    <h1>Adicionar Novo Usuário</h1>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>matricula:</label><br>
        <input type="text" name="matricula" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>Perfil:</label><br>
        <select name="perfil" required>
            <option value="ADM">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
            <option value="GEPES">GEPES</option>
            <option value="GCP">GCP</option>
        </select><br><br>

        <button type="submit">Salvar</button>
    </form>
    <a href="usuarios.php">Voltar</a>
</body>
</html>
