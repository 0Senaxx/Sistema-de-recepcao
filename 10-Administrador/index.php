<?php
session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/Auth/autenticacao.php';
include '../conexao.php';

// Consulta rápida de dados
$hoje = date('Y-m-d');

// Visitas hoje
$sql_visitas = "SELECT COUNT(*) AS total FROM visitas WHERE data = '$hoje'";
$result_visitas = mysqli_query($conn, $sql_visitas);
$row_visitas = mysqli_fetch_assoc($result_visitas);

// Visitantes cadastrados
$sql_visitantes = "SELECT COUNT(*) AS total FROM visitantes";
$result_visitantes = mysqli_query($conn, $sql_visitantes);
$row_visitantes = mysqli_fetch_assoc($result_visitantes);

// Servidores ativos
$sql_servidores = "SELECT COUNT(*) AS total FROM servidores WHERE status = 'Ativo'";
$result_servidores = mysqli_query($conn, $sql_servidores);
$row_servidores = mysqli_fetch_assoc($result_servidores);

// Setores cadastrados
$sql_setores = "SELECT COUNT(*) AS total FROM setores";
$result_setores = mysqli_query($conn, $sql_setores);
$row_setores = mysqli_fetch_assoc($result_setores);
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <title>Painel do Administrador - Recepção SEAD</title>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>
    <header>
        <h1>Bem-vindo, <?php echo $_SESSION['nome']; ?>!</h1>
        <nav>
            <ul>
                <li><a href="index.php">Início</a></li>
                <li><a href="Usuarios/usuarios.php">Usuários</a></li>
                <li><a href="../08-Servidores/index.php">Servidores</a></li>
                <li><a href="../09-Setores/index.php">Setores</a></li>
                <li><a href="../07-Relatorio/gerar_relatorio.php">Relatórios</a></li>
                <li><a href="documentos.php">Repositório</a></li>
                <li><a href="../01-Login/Auth/logout.php">Sair</a></li>
            </ul>
        </nav>
    </header>

    <main>
        <section class="cards">
            <div class="card">
                <h2>Visitas de Hoje</h2>
                <p><?php echo $row_visitas['total']; ?></p>
            </div>
            <div class="card">
                <h2>Visitantes Cadastrados</h2>
                <p><?php echo $row_visitantes['total']; ?></p>
            </div>
            <div class="card">
                <h2>Servidores Ativos</h2>
                <p><?php echo $row_servidores['total']; ?></p>
            </div>
            <div class="card">
                <h2>Setores</h2>
                <p><?php echo $row_setores['total']; ?></p>
            </div>
        </section>
    </main>
</body>
</html>
