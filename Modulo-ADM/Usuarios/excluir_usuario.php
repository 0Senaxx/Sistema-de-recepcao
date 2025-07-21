<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

if (isset($_GET['id'])) {
    $id = intval($_GET['id']);

    // Excluir ocorrências
    $stmtOcorrencias = $conn->prepare("DELETE FROM ocorrencias WHERE usuario_id = ?");
    if (!$stmtOcorrencias) die("Erro prepare ocorrencias: " . $conn->error);
    $stmtOcorrencias->bind_param("i", $id);
    if (!$stmtOcorrencias->execute()) die("Erro execute ocorrencias: " . $stmtOcorrencias->error);

    // Excluir tokens
    $stmtTokens = $conn->prepare("DELETE FROM tokens_recuperacao WHERE usuario_id = ?");
    if (!$stmtTokens) die("Erro prepare tokens: " . $conn->error);
    $stmtTokens->bind_param("i", $id);
    if (!$stmtTokens->execute()) die("Erro execute tokens: " . $stmtTokens->error);

    // Excluir usuário
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    if (!$stmt) die("Erro prepare usuarios: " . $conn->error);
    $stmt->bind_param("i", $id);
    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        die("Erro ao excluir usuário: " . $stmt->error);
    }
} else {
    header("Location: usuarios.php");
    exit;
}
