<?php
session_start();
include '../conexao.php';

$mensagem = "";
$linkGerado = ""; // variável para guardar o link e mostrar separadamente

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $matricula = trim($_POST['matricula']);
    $cpf_ult4 = trim($_POST['cpf_ult4']);

    // Buscar usuário pela matrícula
    $sql = "SELECT * FROM usuarios WHERE matricula = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $matricula);
    $stmt->execute();
    $result = $stmt->get_result();

    if ($result->num_rows === 1) {
        $usuario = $result->fetch_assoc();

        // Extrair apenas números do CPF
        $cpf_numeros = preg_replace('/\D/', '', $usuario['cpf']);

        // Pegar últimos 4 dígitos do CPF
        $cpf_ultimos4 = substr($cpf_numeros, -4);

        if ($cpf_ult4 === $cpf_ultimos4) {
            // Gerar token
            $token = bin2hex(random_bytes(32));
            $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

            // Inserir token na tabela tokens_recuperacao
            $sqlInsert = "INSERT INTO tokens_recuperacao (usuario_id, token, expiracao, usado) VALUES (?, ?, ?, 0)";
            $stmtInsert = $conn->prepare($sqlInsert);
            $stmtInsert->bind_param("iss", $usuario['id'], $token, $expiracao);
            $stmtInsert->execute();

            // Criar link para redefinir senha
            $linkGerado = "http://" . $_SERVER['HTTP_HOST'] . "/controle-visitas/01-Login/redefinir_senha.php?token=$token";

            $mensagem = "Token gerado com sucesso! Use o link abaixo para redefinir sua senha. O link expira em 1 hora.";
        } else {
            $mensagem = "Últimos 4 dígitos do CPF incorretos.";
        }
    } else {
        $mensagem = "Matrícula não encontrada.";
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Esqueci a Senha</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            padding: 30px;
        }

        .container {
            max-width: 500px;
            margin: auto;
        }

        input, button {
            width: 100%;
            padding: 8px;
            margin-top: 10px;
        }

        .link-box {
            background-color: #f0f0f0;
            border: 1px solid #ccc;
            padding: 10px;
            word-break: break-word;
            margin-top: 15px;
        }
    </style>
</head>
<body>
    <div class="container">
        <h2>Esqueci minha senha</h2>

        <form method="POST" novalidate>
            <label>Matrícula:</label>
            <input type="text" name="matricula" required><br><br>

            <label>Últimos 4 dígitos do CPF:</label>
            <input type="text" name="cpf_ult4" maxlength="4" required pattern="\d{4}" title="Digite os últimos 4 dígitos do CPF"><br><br>

            <button type="submit">Gerar link de redefinição</button>
        </form>

        <?php if ($mensagem): ?>
            <p style="color: green;"><?= htmlspecialchars($mensagem) ?></p>
        <?php endif; ?>

        <?php if ($linkGerado): ?>
            <div class="link-box">
                <strong>Link:</strong><br>
                <a href="<?= htmlspecialchars($linkGerado) ?>" target="_blank"><?= htmlspecialchars($linkGerado) ?></a>
            </div>
        <?php endif; ?>
    </div>
</body>
</html>
