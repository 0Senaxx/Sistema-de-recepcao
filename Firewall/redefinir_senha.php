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
        // Verifica se a nova senha já foi usada nas últimas 3 trocas
        $sqlHistorico = "SELECT senha_hash FROM historico_senhas WHERE usuario_id = ? ORDER BY data_troca DESC LIMIT 3";
        $stmtHistorico = $conn->prepare($sqlHistorico);
        $stmtHistorico->bind_param("i", $usuario['id']);
        $stmtHistorico->execute();
        $resultHistorico = $stmtHistorico->get_result();

        while ($row = $resultHistorico->fetch_assoc()) {
            if (password_verify($novaSenha, $row['senha_hash'])) {
                $erro = "Você não pode reutilizar as 3 últimas senhas.";
                break;
            }
        }

        if (!isset($erro)) {
            // Atualiza a senha do usuário
            $hash = password_hash($novaSenha, PASSWORD_DEFAULT);
            $sqlUpdate = "UPDATE usuarios SET senha = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
            $stmtUpdate = $conn->prepare($sqlUpdate);
            $stmtUpdate->bind_param("si", $hash, $usuario['id']);
            $stmtUpdate->execute();

            // Salva o novo hash no histórico
            $sqlInsertHistorico = "INSERT INTO historico_senhas (usuario_id, senha_hash) VALUES (?, ?)";
            $stmtInsertHistorico = $conn->prepare($sqlInsertHistorico);
            $stmtInsertHistorico->bind_param("is", $usuario['id'], $hash);
            $stmtInsertHistorico->execute();

            // Mantém apenas os 3 últimos registros
            $sqlDeleteAntigas = "DELETE FROM historico_senhas 
                             WHERE usuario_id = ? 
                             AND id NOT IN (
                                 SELECT id FROM (
                                     SELECT id FROM historico_senhas 
                                     WHERE usuario_id = ? 
                                     ORDER BY data_troca DESC 
                                     LIMIT 3
                                 ) AS temp
                             )";
            $stmtDeleteAntigas = $conn->prepare($sqlDeleteAntigas);
            $stmtDeleteAntigas->bind_param("ii", $usuario['id'], $usuario['id']);
            $stmtDeleteAntigas->execute();

            // Marca o token como usado
            $sqlUsarToken = "UPDATE tokens_recuperacao SET usado = 1 WHERE token = ?";
            $stmtUsarToken = $conn->prepare($sqlUsarToken);
            $stmtUsarToken->bind_param("s", $token);
            $stmtUsarToken->execute();

            $sucesso = true;
        }
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Redefinir Senha</title>
    <link rel="stylesheet" href="estilo.css">
    <style>
        .modal {
            display: flex;
            position: fixed;
            z-index: 9999;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.5);
            align-items: center;
            justify-content: center;
        }

        .modal-content {
            background-color: #fff;
            padding: 30px 40px;
            border-radius: 8px;
            text-align: center;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.3);
            font-family: Arial, sans-serif;
        }

        .modal-content h3 {
            margin-bottom: 10px;
            color: #333;
        }

        .modal-content p {
            margin: 0;
            color: #555;
        }
    </style>
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
                <input type="password" name="nova_senha" id="nova_senha" maxlength="10" required>

                <label for="confirmar_senha">Confirmar Senha:</label>
                <input type="password" name="confirmar_senha" id="confirmar_senha" maxlength="10" required>

                <button class="btn-login" type="submit">Alterar Senha</button>
            </form>

            <br>
            <p>Dica: use uma senha forte com letras, números e símbolos.</p>

        </section>
        <?php if (isset($sucesso) && $sucesso): ?>
            <div id="successModal" class="modal">
                <div class="modal-content">
                    <h3>Senha alterada com sucesso!</h3>
                    <p>Redirecionando para a tela de login em <span id="countdown">4</span> segundos...</p>
                </div>
            </div>
        <?php endif; ?>
    </main>
    <footer class="rodape">
        Copyright © 2025 SEAD | EPP. Todos os direitos reservados
    </footer>

    <?php if (isset($sucesso) && $sucesso): ?>
        <script>
            let seconds = 4;
            const countdown = document.getElementById('countdown');

            const interval = setInterval(() => {
                seconds--;
                countdown.textContent = seconds;
                if (seconds <= 0) {
                    clearInterval(interval);
                    window.location.href = "login.php";
                }
            }, 1000);
        </script>
    <?php endif; ?>
</body>

</html>