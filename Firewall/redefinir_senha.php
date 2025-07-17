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

// Verifica se o termo de uso foi aceito ou recusado
$mostrar_termo_modal = false;

$sqlTermo = "SELECT status FROM historico_termo_uso WHERE usuario_id = ? ORDER BY data_acao DESC LIMIT 1";
$stmtTermo = $conn->prepare($sqlTermo);
$stmtTermo->bind_param("i", $usuario['id']);
$stmtTermo->execute();
$resultTermo = $stmtTermo->get_result();

if ($rowTermo = $resultTermo->fetch_assoc()) {
    if ($rowTermo['status'] !== 'aceito') {
        $mostrar_termo_modal = true;
    }
} else {
    $mostrar_termo_modal = true; // Nunca aceitou ou recusou
}


// Se o formulário foi enviado
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    if (isset($_POST['aceite_termo'])) {
        $sqlInserirTermo = "INSERT INTO historico_termo_uso (usuario_id, status, data_acao) VALUES (?, 'aceito', NOW())";
        $stmtInserir = $conn->prepare($sqlInserirTermo);
        $stmtInserir->bind_param("i", $usuario['id']);
        $stmtInserir->execute();

        // Recarrega a página sem o modal
        header("Location: redefinir_senha.php?token=" . urlencode($token));
        exit;
    }


    $novaSenha = $_POST['nova_senha'];
    $confirmarSenha = $_POST['confirmar'];


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
                <input type="password" id="senha" name="nova_senha" minlength="6" maxlength="10" placeholder="Crie uma nova senha" required>
                <ul id="requisitos-senha">
                    <li id="letra-maiuscula" class="invalido">▪ Pelo menos uma letra maiúscula</li>
                    <li id="letra-minuscula" class="invalido">▪ Pelo menos uma letra minúscula</li>
                    <li id="numero" class="invalido">▪ Pelo menos um número</li>
                    <li id="simbolo" class="invalido">▪ Pelo menos um símbolo (ex: ! @ # $ %)</li>
                    <li id="comprimento" class="invalido">▪ Entre 6 e 10 caracteres</li>
                </ul>

                <label for="confirmar">Confirmar Senha:</label>
                <input type="password" name="confirmar" id="confirmar" maxlength="10" required>
                <small id="erro-confirmar" style="color: red; display: none;">As senhas não coincidem.</small>

                <button class="btn-login" type="submit">Alterar Senha</button>
            </form>

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
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>

    <!-- Modal Termo de Uso e Privacidade -->
    <?php if ($mostrar_termo_modal && !(isset($sucesso) && $sucesso)): ?>
        <div id="termoModal" class="modal" style="display:block;">
            <div class="modal-content">
                <h2>🔒 Termo de Uso e Política de Privacidade</h2><br>
                <p>
                    Para continuar, leia com atenção o nosso
                    <a href="termo-de-uso.pdf" target="_blank">Termo de Uso</a> e
                    <a href="termo-de-uso.pdf" target="_blank">Política de Privacidade</a>.
                </p><br>
                <div class="modal-botoes">
                    <button type="button" class="btn-aceitar" id="aceitarTermo">Aceitar</button>
                    <button type="button" class="btn-recusar" id="recusarTermo">Recusar</button>
                </div>
            </div>
        </div>
    <?php endif; ?>

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
<script>
    document.getElementById("aceitarTermo")?.addEventListener("click", function() {
        fetch("salvar_termo.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "acao=aceitar"
        }).then(() => {
            document.getElementById("termoModal").style.display = "none";
        });
    });

    document.getElementById("recusarTermo")?.addEventListener("click", function() {
        fetch("salvar_termo.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/x-www-form-urlencoded"
            },
            body: "acao=recusar"
        }).then(() => {
            document.getElementById("termoModal").style.display = "none";
        });
    });

    // VERIFICAÇÃO DOS REQUISITOS DA SENHA

    const inputSenha = document.getElementById('senha');
    const inputConfirmar = document.getElementsByName('confirmar')[0];
    const erroConfirmar = document.getElementById('erro-confirmar');
    const listaRequisitos = document.getElementById('requisitos-senha');

    listaRequisitos.style.display = 'none';

    inputSenha.addEventListener('focus', () => {
        listaRequisitos.style.display = 'block';
    });

    inputSenha.addEventListener('input', function() {
        const senha = this.value;

        const temMaiuscula = /[A-Z]/.test(senha);
        const temMinuscula = /[a-z]/.test(senha);
        const temNumero = /[0-9]/.test(senha);
        const temSimbolo = /[\W_]/.test(senha);
        const tamanhoValido = senha.length >= 6 && senha.length <= 10;

        document.getElementById('letra-maiuscula').className = temMaiuscula ? 'valido' : 'invalido';
        document.getElementById('letra-minuscula').className = temMinuscula ? 'valido' : 'invalido';
        document.getElementById('numero').className = temNumero ? 'valido' : 'invalido';
        document.getElementById('simbolo').className = temSimbolo ? 'valido' : 'invalido';
        document.getElementById('comprimento').className = tamanhoValido ? 'valido' : 'invalido';

        if (senha.length < 6) {
            inputConfirmar.disabled = true;
            inputConfirmar.classList.add('desabilitado');
        } else {
            inputConfirmar.disabled = false;
            inputConfirmar.classList.remove('desabilitado');
        }

        verificarSenhasIguais();
    });

    inputConfirmar.addEventListener('input', verificarSenhasIguais);

    function verificarSenhasIguais() {
        if (inputConfirmar.value && inputSenha.value !== inputConfirmar.value) {
            erroConfirmar.style.display = 'block';
        } else {
            erroConfirmar.style.display = 'none';
        }
    }
    document.getElementById('aceitarTermo')?.addEventListener('click', () => {
        document.getElementById('status_termo').value = 'aceito';
        document.getElementById('termoModal').style.display = 'none';
    });

    document.getElementById('recusarTermo')?.addEventListener('click', () => {
        document.getElementById('status_termo').value = 'recusado';
        document.getElementById('termoModal').style.display = 'none';
    });

</script>
</html>