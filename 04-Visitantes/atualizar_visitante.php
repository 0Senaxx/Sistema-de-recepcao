<?php
include '../conexao.php';

function salvarFoto($arquivo) {
    if ($arquivo['error'] == 0) {
        $pasta = "../04-Visitantes/Fotos/";
        if (!is_dir($pasta)) mkdir($pasta);

        $ext = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . "." . $ext;
        $destino = $pasta . $nomeArquivo;

        move_uploaded_file($arquivo['tmp_name'], $destino);
        return $destino;
    }
    return null;
}

function salvarFotoBase64($base64) {
    if (!empty($base64)) {
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $base64 = base64_decode($base64);

        $pasta = "../04-Visitantes/Fotos/";
        if (!is_dir($pasta)) mkdir($pasta);

        $nomeArquivo = uniqid("visitante_", true) . ".jpg";
        $destino = $pasta . $nomeArquivo;

        file_put_contents($destino, $base64);
        return $destino;
    }
    return null;
}

$id = $_POST['id'];
$cpf = $_POST['cpf'];
$nome = $_POST['nome'];
$telefone = $_POST['telefone'];
$orgao = $_POST['orgao'];

// Verifica se há foto base64
$fotoBase64 = isset($_POST['foto_base64']) ? $_POST['foto_base64'] : '';
$fotoNova = null;

if (!empty($fotoBase64)) {
    $fotoNova = salvarFotoBase64($fotoBase64);
} elseif (isset($_FILES['foto'])) {
    $fotoNova = salvarFoto($_FILES['foto']);
}

if ($fotoNova) {
    $sql = "UPDATE visitantes SET cpf=?, nome=?, telefone=?, orgao=?, foto=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssi", $cpf, $nome, $telefone, $orgao, $fotoNova, $id);
} else {
    $sql = "UPDATE visitantes SET cpf=?, nome=?, telefone=?, orgao=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssi", $cpf, $nome, $telefone, $orgao, $id);
}

$stmt->execute();

header("Location: visitantes.php");
exit();
