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
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Esqueci a Senha</title>
    <style>
        /* Reset básico */
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        /* Tipografia e fundo */
        body {
            font-family: Arial, sans-serif;
            font-size: 14px;
            background: rgb(248, 248, 248);
            color: #333;
            line-height: 1.4;
            display: flex;
            flex-direction: column;
            background: linear-gradient(93deg, #293264 19%, rgba(10, 133, 61, 1) 100%);
        }

        .cabecalho {
            /* Posicionamento fixo no topo */
            position: fixed;
            top: 0;
            left: 0;
            width: 100%;
            z-index: 1000;

            /* Layout flexível */
            display: flex;
            flex-wrap: wrap;
            justify-content: space-between;
            align-items: center;

            /* Estilo visual */
            color: #fff;
            padding: 10px 30px;
        }

        .rodape {
            display: flex;
            justify-content: space-between;
            align-items: center;
            color: #fff;
            padding: 10px 30px;
            position: fixed;
            bottom: 0;
            width: 100%;
            justify-content: center;
            text-align: center;
        }

        main {
            flex: 1;
            /* Faz o main ocupar o espaço restante */
            display: flex;
            justify-content: center;
            align-items: center;
            padding-top: 55px;
            /* espaço para o cabeçalho */
            padding-bottom: 55px;
            /* espaço para o rodapé */
        }

        .card {
            background: #fff;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            padding: 50px;
            width: 100%;
            max-width: 500px;
            /* Limita a largura em telas grandes */
            margin: 60px;
            text-align: center;
            font-size: 20px;
            color: rgba(41, 50, 100, 1);
        }

        label {
            width: 100%;
            display: flex;
            flex-direction: column;
            align-items: flex-start;
            justify-content: center;
            margin: 10px 0px;
            color: #444444;
            font-weight: 100;
            margin-bottom: 2px;
            font-family: Arial, sans-serif;
            font-size: 14pt;
            ;
        }

        input {
            width: 100%;
            border: 1px solid #d6d3d3;
            border-radius: 10px;
            padding: 15px;
            background-color: #ffffff;
            color: #000000;
            font-size: 12pt;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
            outline: none;
        }

        .campo-matricula {
            margin-top: 30px;
        }

        .campo-cpf {
            margin-top: 20px;
        }

        .btn-link {
            width: 100%;
            padding: 16px 0px;
            margin-top: 40px;
            border: none;
            border-radius: 6px;
            outline: none;
            text-transform: uppercase;
            font-weight: 800;
            color: rgba(41, 50, 100, 1);
            background-color: rgb(21, 206, 98);
            cursor: pointer;
            box-shadow: rgba(99, 99, 99, 0.2) 0px 2px 8px 0px;
        }

        .mensagem {
            margin-top: 20px;
            padding: 10px;
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
            border-radius: 5px;
            font-size: 15px;
        }

        .link-box {
            margin-top: 20px;
            padding: 10px;
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
            border-radius: 5px;
            font-size: 10px;
            align-items: center;
        }

        .link-box p {
            margin-bottom: 10px;
            font-size: 13px;
        }

        .btn-voltar {
            margin-top: 10px;
            text-align: center;
            font-size: 15px;
        }

        .btn-voltar a {
            text-decoration: none;
            color: #646464;
        }
    </style>
</head>

<body>
    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
    </header>

    <main>
        <section class="card">
            <h2>Esqueci minha senha</h2>

            <form method="POST" novalidate>
                <div class="campo-matricula">
                    <label>Matrícula:</label>
                    <input type="text" id="matricula" name="matricula" placeholder="Digite sua matrícula" required><br>
                </div>

                <div class="campo-cpf">
                    <label>Últimos 4 dígitos do CPF:</label>
                    <input type="text" name="cpf_ult4" maxlength="4" autocomplete="off" required pattern="\d{4}" placeholder="Digite aqui"><br>
                </div>

                <button type="submit" class="btn-link">Gerar link de redefinição</button>

            </form>

            <?php if ($mensagem): ?>
                <p class="mensagem"><?= htmlspecialchars($mensagem) ?></p>
            <?php endif; ?>

            <?php if ($linkGerado): ?>
                <div class="link-box">
                    <p>Token gerado com sucesso! Use o link abaixo para redefinir sua senha. O link expira em 1 hora.</p><br>
                    <a href="<?= htmlspecialchars($linkGerado) ?>" target="_blank"><?= htmlspecialchars($linkGerado) ?></a>
                </div>
            <?php endif; ?>

            <div class="btn-voltar">
                <a href="login.php">Voltar</a>
            </div>
        </section>
    </main>

    <footer class="rodape">
        Copyright © 2025 SEAD | EPP. Todos os direitos reservados
    </footer>
</body>
<script>
    $('#matricula').mask('000.000-0 A', {
        translation: {
            'A': {
                pattern: /[A-Za-z]/
            }
        }
    });
</script>

</html>