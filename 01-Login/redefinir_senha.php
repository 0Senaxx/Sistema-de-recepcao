<?php
session_start();
include '../conexao.php';

// Verifica se o token está presente na URL
if (!isset($_GET['token'])) {
    echo "Token inválido.";
    exit;
}

$token = $_GET['token'];

// Busca o usuário associado ao token e verifica se ainda está válido
$sql = "SELECT u.* 
        FROM tokens_recuperacao tr
        JOIN usuarios u ON tr.usuario_id = u.id
        WHERE tr.token = ? AND tr.expiracao >= NOW() AND tr.usado = 0";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $token);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows !== 1) {
    echo "Token inválido ou expirado.";
    exit;
}

$usuario = $result->fetch_assoc();

// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar_senha'];

    // Validação da senha
    if ($novaSenha !== $confirmarSenha) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[@$!%*?&]).{6,}$/', $novaSenha)) {
        $erro = "A senha deve ter pelo menos 6 caracteres, com letra maiúscula, minúscula, número e símbolo.";
    } else {
        // Atualiza a senha do usuário
        $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
        $sqlUpdate = "UPDATE usuarios SET senha = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("si", $hash, $usuario['id']);
        $stmtUpdate->execute();

        // Marca o token como usado
        $sqlUsarToken = "UPDATE tokens_recuperacao SET usado = 1 WHERE token = ?";
        $stmtUsarToken = $conn->prepare($sqlUsarToken);
        $stmtUsarToken->bind_param("s", $token);
        $stmtUsarToken->execute();

        echo "<p>Senha alterada com sucesso. <a href='login.php'>Clique aqui para entrar</a>.</p>";
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
    </header>
    <main>


        <section class="card">
            <h2>Redefinir Senha</h2>

            <?php if (isset($erro)): ?>
                <div class="alert alert-danger"><?= $erro ?></div>
            <?php endif; ?>

            <form method="POST">
                <label for="nova_senha">Nova Senha:</label>
                <input type="password" name="nova_senha" id="nova_senha" required>

                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" required>

                <button class="btn-login" type="submit">Alterar Senha</button>
            </form>

            <br><p>Dica: use uma senha forte com letras, números e símbolos.</p>

        </section>
    </main>
    <footer class="rodape">
        Copyright © 2025 SEAD | EPP. Todos os direitos reservados
    </footer>
</body>

</html>