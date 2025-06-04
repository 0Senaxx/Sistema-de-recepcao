<?php
require_once '../conexao.php';

$id = $_GET['id'] ?? 0;
$id = intval($id);

if ($id > 0) {
    $sql = "DELETE FROM servidores WHERE id = $id";
    $conn->query($sql);
}

header('Location: index.php');
exit;
?>
