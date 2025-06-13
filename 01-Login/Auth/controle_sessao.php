<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Define tempo limite de inatividade (2 minutos para teste)
$tempo_limite = 600; //10 MINUTOS 

// Verifica se o usuário está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../01-Login/login.php");
    exit();
}

// Verifica tempo de inatividade
if (isset($_SESSION['ultimo_acesso'])) {
    $tempo_inativo = time() - $_SESSION['ultimo_acesso'];
    if ($tempo_inativo > $tempo_limite) {
        session_unset();
        session_destroy();
        header("Location: ../01-Login/login.php?mensagem=Sessão expirada por inatividade.");
        exit();
    }
}

// Atualiza o tempo de último acesso
$_SESSION['ultimo_acesso'] = time();
