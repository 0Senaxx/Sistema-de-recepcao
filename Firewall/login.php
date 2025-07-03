<?php session_start(); 

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Recepção SEAD</title>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <link rel="stylesheet" href="estilo.css">
</head>

<body class="container py-5">

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
    </header>

    <main>
        <section class="card">

            <h2>LOGIN</h2>

            <?php if (isset($_SESSION['erro'])): ?>
                <div class="alert alert-danger">
                    <?= $_SESSION['erro'];
                    unset($_SESSION['erro']); ?>
                </div>

            <?php endif; ?>

            <form action="Auth/verificar_login.php" method="POST">

                <div class="campo-usuario">
                    <label for="matricula">Matrícula:</label>
                    <input type="text" name="matricula" id="matricula" placeholder="Digite sua matrícula" maxlength="12" required>
                </div>

                <div class="campo-senha">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" placeholder="Digite sua senha" maxlength="10" required>
                </div>

                <button type="submit" class="btn-login">Entrar</button>

                <div class="esqueci-senha">
                    <a href="esqueci_senha.php">Esqueci minha senha</a>
                </div>

            </form>
        </section>
    </main>


    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>

    <script>
        $('#matricula').mask('000.000-0 A', {
            translation: {
                'A': {
                    pattern: /[A-Za-z]/
                }
            }
        });
    </script>

</body>

</html>