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
    $cpf = preg_replace('/\D/', '', $cpf);
    if (strlen($cpf) != 11) {
        return false;
    }
    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }
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

    // ➕ Novo campo do pop-up: status do termo (aceito ou recusado)
    $status_termo = isset($_POST['status_termo']) ? $_POST['status_termo'] : 'recusado';

    $cpf_numeros = preg_replace('/\D/', '', $cpf);

    if (!validaCPF($cpf_numeros)) {
        $erro = "CPF inválido. Digite um CPF válido.";
    } elseif ($nova_senha !== $confirmar) {
        $erro = "As senhas não coincidem.";
    } elseif (!preg_match('/^(?=.*[a-z])(?=.*[A-Z])(?=.*\d)(?=.*[\W_]).{6,}$/', $nova_senha)) {
        $erro = "A senha deve conter no mínimo 6 caracteres, incluindo letras maiúsculas, minúsculas, números e símbolos.";
    } else {
        $cpf_formatado = substr($cpf_numeros, 0, 3) . '.' .
            substr($cpf_numeros, 3, 3) . '.' .
            substr($cpf_numeros, 6, 3) . '-' .
            substr($cpf_numeros, 9, 2);

        $id = $_SESSION['usuario_id'];
        $hash = password_hash($nova_senha, PASSWORD_DEFAULT);

        // Atualiza senha, CPF e marca senha_temporaria como 0
        $sql = "UPDATE usuarios SET senha = ?, cpf = ?, senha_temporaria = 0, data_ultima_troca = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $hash, $cpf_formatado, $id);
        $stmt->execute();

        // ➕ Inserir histórico do termo de uso
        $sql_termo = "INSERT INTO historico_termo_uso (usuario_id, status) VALUES (?, ?)";
        $stmt_termo = $conn->prepare($sql_termo);
        $stmt_termo->bind_param("is", $id, $status_termo);
        $stmt_termo->execute();

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

            <?php if ($erro): ?>
                <p style="color:red"><?= htmlspecialchars($erro) ?></p>
            <?php endif; ?>

            <form action="" method="POST">

                <div class="campo-senha">
                    <label>Nova Senha:</label>
                    <input type="password" id="senha" name="nova_senha" minlength="6" maxlength="10" placeholder="Crie uma nova senha" required>

                    <ul id="requisitos-senha">
                        <li id="letra-maiuscula" class="invalido">▪ Pelo menos uma letra maiúscula</li>
                        <li id="letra-minuscula" class="invalido">▪ Pelo menos uma letra minúscula</li>
                        <li id="numero" class="invalido">▪ Pelo menos um número</li>
                        <li id="simbolo" class="invalido">▪ Pelo menos um símbolo (ex: ! @ # $ %)</li>
                        <li id="comprimento" class="invalido">▪ Entre 6 e 10 caracteres</li>
                    </ul>
                </div>

                <div class="campo-senha">
                    <label>Confirmar Senha:</label>
                    <input type="password" id="confirmar" name="confirmar" maxlength="10" required placeholder="Digite a senha novamente" disabled>
                    <small id="erro-confirmar" style="color: red; display: none;">As senhas não coincidem.</small>
                </div>


                <div class="campo-senha">
                    <label>CPF:</label>
                    <input type="text" id="cpf" name="cpf" autocomplete="off" maxlength="14" oninput="mascaraCPF(this)" onblur="validarCPFInput(this.value)" required placeholder="000.000.000-00">
                    <span id="mensagem-cpf" style="color: red; font-size: 14px;"></span>
                </div>

                <button type="submit" id="btn-submit" class="btn-login" disabled>Salvar e Acessar</button>

                <input type="hidden" name="status_termo" id="status_termo">

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

    function validaCPF(cpf) {
        if (cpf.length != 11) return false;
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
</script>



</html>