<?php
session_start();
include '../conexao.php';

if (!isset($_SESSION['usuario_id'])) {
    http_response_code(401);
    exit;
}

$usuario_id = $_SESSION['usuario_id'];

if (isset($_POST['acao'])) {
    $status = $_POST['acao'] === 'aceitar' ? 'aceito' : 'recusado';
    $sql = "INSERT INTO historico_termo_uso (usuario_id, status, data_acao) VALUES (?, ?, NOW())";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("is", $usuario_id, $status);
    $stmt->execute();
}
