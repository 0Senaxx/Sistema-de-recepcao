<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$erro = "";

// Função para validar CPF completo
function validaCPF($cpf) {
    // Remove caracteres não numéricos
    $cpf = preg_replace('/\D/', '', $cpf);

    // Verifica se tem 11 dígitos
    if (strlen($cpf) != 11) {
        return false;
    }

    // Verifica se todos os dígitos são iguais (CPF inválido)
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    // Cálculo do primeiro dígito verificador
    for ($t = 9; $t < 11; $t++) {
        $soma = 0;
        for ($i = 0; $i < $t; $i++) {
            $soma += $cpf[$i] * (($t + 1) - $i);
        }
        $digito = (10 * $soma) % 11;
        if ($digito == 10) {
            $digito = 0;
        }
        if ($cpf[$t] != $digito) {
            return false;
        }
    }

    return true;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nova_senha = $_POST['nova_senha'];
    $confirmar = $_POST['confirmar'];
    $cpf = $_POST['cpf'];

    // Limpar CPF: só números
    $cpf_numeros = preg_replace('/\D/', '', $cpf);

    // Validar CPF completo
    if (!validaCPF($cpf_numeros)) {
        $erro = "CPF inválido. Digite um CPF válido.";
    } 
    // Validação de senha
    elseif ($nova_senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $nova_senha)) {
        $erro = "A senha deve conter no mínimo 6 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos.";
    } else {
        // Formatar CPF com pontuação: 000.000.000-00
        $cpf_formatado = substr($cpf_numeros,0,3) . '.' .
                        substr($cpf_numeros,3,3) . '.' .
                        substr($cpf_numeros,6,3) . '-' .
                        substr($cpf_numeros,9,2);

        $id = $_SESSION['usuario_id'];
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET senha = ?, cpf = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hash, $cpf_formatado, $id);
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

    <script>
    // Máscara simples para CPF no campo de input
    function mascaraCPF(i){
        var v = i.value.replace(/\D/g,''); // Remove não números
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        i.value = v;
    }
    </script>

</head>
<body>
    <h2>Primeiro Acesso</h2>
    <p>Para proteger sua conta, altere sua senha e cadastre seu CPF.</p>

    <?php if ($erro): ?>
        <p style="color:red"><?= htmlspecialchars($erro) ?></p>
    <?php endif; ?>

    <form method="POST">
        <label>Nova Senha:</label>
        <input type="password" name="nova_senha" required><br><br>

        <label>Confirmar Senha:</label>
        <input type="password" name="confirmar" required><br><br>

        <label>CPF:</label>
        <input type="text" name="cpf" maxlength="14" oninput="mascaraCPF(this)" required placeholder="000.000.000-00"><br><br>

        <button type="submit">Salvar e Acessar</button>
    </form>
</body>
</html>
