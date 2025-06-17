<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

$id = intval($_GET['id'] ?? 0);
$acao = $_GET['acao'] ?? '';

if ($id > 0 && in_array($acao, ['ativar', 'desativar'])) {
    $novo_status = ($acao == 'ativar') ? 1 : 0;
    $sql = "UPDATE usuarios SET ativo = $novo_status WHERE id = $id";
    $conn->query($sql);
}

header("Location: usuarios.php");
exit;
?>
