<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    http_response_code(403);
    exit;
}

include '../../conexao.php';

$id = intval($_GET['id'] ?? 0);
if ($id <= 0) {
    http_response_code(400);
    exit;
}

// Evita SQL Injection
$sql = "SELECT id, nome, cpf, matricula, perfil FROM usuarios WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $user = $result->fetch_assoc();

    // Se o CPF estiver vazio ou nulo, não envia no JSON
    if (empty($user['cpf'])) {
        unset($user['cpf']);
    }

    echo json_encode($user);
} else {
    http_response_code(404);
}
