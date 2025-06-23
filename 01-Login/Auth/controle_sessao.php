<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once 'C:/xampp/htdocs/controle-visitas/config.php';


// Limite de inatividade
$tempo_limite = 600; // 10 minutos

// Verifica se não está logado
if (!isset($_SESSION['usuario_id'])) {
    header("Location: " . URL_LOGIN);
    exit();
}

// Verifica o tempo de inatividade
if (isset($_SESSION['ultimo_acesso'])) {
    $tempo_inativo = time() - $_SESSION['ultimo_acesso'];
    if ($tempo_inativo > $tempo_limite) {
        session_unset();
        session_destroy();
        header("Location: " . URL_LOGIN . "?mensagem=Sessão expirada por inatividade.");
        exit();
    }
}

// Atualiza o último acesso
$_SESSION['ultimo_acesso'] = time();
