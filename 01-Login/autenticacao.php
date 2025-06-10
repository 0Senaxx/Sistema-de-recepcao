<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../01-Login/login.php");
    exit;
}

$perfil = $_SESSION['perfil'] ?? '';

$path = dirname($_SERVER['PHP_SELF']);
$pastas = explode('/', trim($path, '/'));
$pastaAtual = $pastas[1] ?? ''; // Pega a segunda pasta da URL

$permissoes = [
    'ADM' => ['01-Login', '02-Inicio', '03-Registrar', '04-Visitantes', '05-Visitas', '06-Ramais', '07-Relatorios', '08-Servidores', '09-Setores', '10-Administrador'],
    'GCP' => ['09-Setores', '06-Ramais'],
    'GEPES' => ['08-Servidores'],
    'Recepcionista' => ['02-Inicio', '03-Registrar', '04-Visitantes', '05-Visitas', '06-Ramais', '07-Relatorios', '11-Repositorio' ]
];

if (!in_array($pastaAtual, $permissoes[$perfil] ?? [])) {
    header("HTTP/1.1 403 Forbidden");
    echo "<h1>403 - Acesso negado</h1>";
    echo "<p>Você não tem permissão para acessar esta página.</p>";
    exit;
}
