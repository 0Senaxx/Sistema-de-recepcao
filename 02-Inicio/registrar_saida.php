<?php
include '../conexao.php';

date_default_timezone_set('America/Manaus'); // ajuste conforme sua região

if (isset($_POST['visita_id'])) {
    $id = $_POST['visita_id'];
    $saida = date('H:i:s');

    $sql = "UPDATE visitas SET saida = ? WHERE id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $saida, $id);
    $stmt->execute();
}

header("Location: index.php");
exit();
