<?php
session_start();
include '../../conexao.php';

if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../Firewall/login.php");
    exit;
}

if (isset($_GET['id'])) {
    $id = $_GET['id'];

    $sql = "UPDATE usuarios SET tentativas_erradas = 0, bloqueado = 0 WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);

    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        echo "Erro ao desbloquear usuário.";
    }
} else {
    header("Location: usuarios.php");
    exit;
}
