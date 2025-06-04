<?php
require_once '../conexao.php';

$id = intval($_GET['id'] ?? 0);
if ($id > 0) {
    $conn->query("DELETE FROM setores WHERE id = $id");
}
header("Location: index.php");
exit;
?>
