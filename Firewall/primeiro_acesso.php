<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../login.php");
    exit;
}

$erro = "";

// Função para validar CPF completo
function validaCPF($cpf)
{
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
        $cpf_formatado = substr($cpf_numeros, 0, 3) . '.' .
            substr($cpf_numeros, 3, 3) . '.' .
            substr($cpf_numeros, 6, 3) . '-' .
            substr($cpf_numeros, 9, 2);

        $id = $_SESSION['usuario_id'];
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        $sql = "UPDATE usuarios SET senha = ?, cpf = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hash, $cpf_formatado, $id);
        $stmt->execute();

        header("Location: Auth/verificar_login.php");
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Primeiro Acesso - SEAD</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body class="container py-5">

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
    </header>

    <main>
        <section class="card">

            <h2>Primeiro Acesso</h2>
            <p>Para proteger sua conta, altere sua senha e cadastre seu CPF.</p>

            <?php if ($erro): ?>
                <p style="color:red"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="campo-senha">
                    <label>Nova Senha:</label>
                    <input type="password" name="nova_senha" maxlength="10" required>
                </div>

                <div class="campo-senha">
                    <label>Confirmar Senha:</label>
                    <input type="password" name="confirmar" maxlength="10" required>
                </div>

                <div class="campo-senha">
                    <label>CPF:</label>
                    <input type="text" id="cpf" name="cpf" autocomplete="off" maxlength="14" oninput="mascaraCPF(this)" onblur="validarCPFInput(this.value)" required placeholder="000.000.000-00">
                    <span id="mensagem-cpf" style="color: red; font-size: 14px;"></span>
                </div>

                <button type="submit" id="btn-submit" class="btn-login" disabled>Salvar e Acessar</button>

            </form>
        </section>
    </main>


    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>

</body>

<script>
    // Máscara simples para CPF
    function mascaraCPF(i) {
        var v = i.value.replace(/\D/g, '');
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d)/, "$1.$2");
        v = v.replace(/(\d{3})(\d{1,2})$/, "$1-$2");
        i.value = v;
    }

    // Valida o CPF quando o campo perde o foco
    function validarCPFInput(value) {
        const cpf = value.replace(/\D/g, '');
        const mensagem = document.getElementById('mensagem-cpf');
        const botao = document.getElementById('btn-submit');

        if (cpf.length === 11) {
            if (!validaCPF(cpf)) {
                mensagem.textContent = "CPF inválido.";
                botao.disabled = true;
            } else {
                mensagem.textContent = ""; // Limpa mensagem
                botao.disabled = false;
            }
        } else {
            mensagem.textContent = ""; // Limpa mensagem
            botao.disabled = true;
        }
    }

    // Mesma lógica de validação de CPF no JS
    function validaCPF(cpf) {
        if (cpf.length != 11) return false;

        // Verifica se todos são iguais (ex: 111.111.111-11)
        if (/(\d)\1{10}/.test(cpf)) return false;

        for (let t = 9; t < 11; t++) {
            let soma = 0;
            for (let i = 0; i < t; i++) {
                soma += parseInt(cpf[i]) * ((t + 1) - i);
            }
            let digito = (10 * soma) % 11;
            if (digito == 10) digito = 0;

            if (parseInt(cpf[t]) !== digito) {
                return false;
            }
        }

        return true;
    }
</script>


</html>