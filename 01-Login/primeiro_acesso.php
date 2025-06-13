<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$erro = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirmar = $_POST['confirmar'];
    $email = $_POST['email'];

    // Validação de senha
    if ($nova_senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $nova_senha)) {
        $erro = "A senha deve conter no mínimo 6 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $erro = "E-mail inválido.";
    } else {
        $id = $_SESSION['usuario_id'];
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET senha = ?, email = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hash, $email, $id);
        $stmt->execute();

        header("Location: ../02-Inicio/index.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Primeiro Acesso - SEAD</title>
</head>
<body>
    <h2>Primeiro Acesso</h2>
    <p>Para proteger sua conta, altere sua senha e cadastre seu e-mail.</p>

    <?php if ($erro): ?>
        <p style="color:red"><?= $erro ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nova Senha:</label>
        <input type="password" name="nova_senha" required><br><br>

        <label>Confirmar Senha:</label>
        <input type="password" name="confirmar" required><br><br>

        <label>E-mail:</label>
        <input type="email" name="email" required><br><br>

        <button type="submit">Salvar e Acessar</button>
    </form>
</body>
</html>
