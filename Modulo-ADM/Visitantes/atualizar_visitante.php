<?php
session_start();
include '../../conexao.php';

function salvarFoto($arquivo)
{
    if ($arquivo['error'] == 0) {
        $pasta = "../../Imagens/ImgVisitante/";
        if (!is_dir($pasta)) mkdir($pasta);
        $ext = pathinfo($arquivo['name'], PATHINFO_EXTENSION);
        $nomeArquivo = uniqid() . "." . $ext;
        $destino = $pasta . $nomeArquivo;
        move_uploaded_file($arquivo['tmp_name'], $destino);
        return $destino;
    }
    return null;
}

function salvarFotoBase64($base64)
{
    if (!empty($base64)) {
        $base64 = str_replace('data:image/jpeg;base64,', '', $base64);
        $base64 = base64_decode($base64);
        $pasta = "../../Imagens/ImgVisitante/";
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
$social = $_POST['social'] ?? null;  // <-- Captura nome social
$orgao = $_POST['orgao'];
$usuario_id = $_SESSION['usuario_id']; // ID do usuário logado

$fotoBase64 = $_POST['foto_base64'] ?? '';
$fotoNova = null;

if (!empty($fotoBase64)) {
    $fotoNova = salvarFotoBase64($fotoBase64);
} elseif (isset($_FILES['foto'])) {
    $fotoNova = salvarFoto($_FILES['foto']);
}

if ($fotoNova) {
    $sql = "UPDATE visitantes SET cpf=?, nome=?, social=?, orgao=?, foto=?, atualizado_por=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssssii", $cpf, $nome, $social, $orgao, $fotoNova, $usuario_id, $id);
} else {
    $sql = "UPDATE visitantes SET cpf=?, nome=?, social=?, orgao=?, atualizado_por=? WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssis", $cpf, $nome, $social, $orgao, $usuario_id, $id);
}

$stmt->execute();

header("Location: visitantes.php");
exit();
