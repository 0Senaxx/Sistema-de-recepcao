<?php session_start(); ?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Login - Recepção SEAD</title>
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
                <?= $_SESSION['erro']; unset($_SESSION['erro']); ?>
            </div>
            
            <?php endif; ?>

            <form action="verificar_login.php" method="POST">

                <div class="campo-usuario">
                    <label for="matricula">Matrícula:</label>
                    <input type="text" name="matricula" id="matricula" class="form-control" autocomplete="off" placeholder="Digite sua matrícula" maxlength="12" required>
                </div>

                <div class="campo-senha">
                    <label for="senha">Senha:</label>
                    <input type="password" name="senha" class="form-control" placeholder="Digite sua senha" required>
                </div>
                <button type="submit" class="btn-login">Entrar</button>
            </form>
        </section>
    </main>

    
    <footer class="rodape">
        Copyright © 2025 SEAD | EPP. Todos os direitos reservados 
    </footer>
    <script src="script.js"></script>
</body>

</html>