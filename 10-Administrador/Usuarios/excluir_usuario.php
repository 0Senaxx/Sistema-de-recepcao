<?php
session_start();

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    // 1. Excluir os tokens vinculados ao usuário
    $stmtTokens = $conn->prepare("DELETE FROM tokens_recuperacao WHERE usuario_id = ?");
    $stmtTokens->bind_param("i", $id);
    $stmtTokens->execute();

    // 2. Excluir o usuário
    $stmt = $conn->prepare("DELETE FROM usuarios WHERE id = ?");
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        echo "Erro ao excluir usuário: " . $conn->error;
    }
} else {
    header("Location: usuarios.php");
    exit;
}
