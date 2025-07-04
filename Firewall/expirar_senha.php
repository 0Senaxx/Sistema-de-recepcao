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

    if ($nova_senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $nova_senha)) {
        $erro = "A senha deve conter letras maiúsculas, minúsculas, números e símbolos.";
    } else {
        $id = $_SESSION['usuario_id'];
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET senha = ?, data_ultima_troca = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("si", $hash, $id);
        $stmt->execute();

        header("Location: login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Senha Expirada - SEAD</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
    </header>

    <main>
        <section class="card">
            <h2>Senha Expirada</h2>
            <p>Sua senha expirou. Para continuar, por favor, crie uma nova senha segura.</p><br>

            <?php if ($erro): ?>
                <p style="color:red"><?= $erro ?></p>
            <?php endif; ?>

            <form method="POST">
                <label>Nova Senha:</label>
                <input type="password" name="nova_senha" maxlength="10" required><br>

                <label>Confirmar Senha:</label>
                <input type="password" name="confirmar" maxlength="10" required><br>

                <button type="submit" class="btn-login">Salvar Nova Senha</button>
            </form>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>
</body>

</html>