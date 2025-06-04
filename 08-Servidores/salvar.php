<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../01-Login/login.php");
    exit;
}

require_once '../conexao.php';

$id = intval($_POST['id'] ?? 0);
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$cpf = $conn->real_escape_string($_POST['cpf'] ?? '');
$contato = $conn->real_escape_string($_POST['contato'] ?? '');
$status = $conn->real_escape_string($_POST['status'] ?? 'Ativo');
$setor_id = intval($_POST['setor_id'] ?? 0);

if ($nome == '' || $cpf == '' || $setor_id == 0) {
    echo "Preencha os campos obrigatórios.";
    exit;
}

if ($id > 0) {
    // Update
    $sql = "UPDATE servidores SET 
        nome = '$nome', cpf = '$cpf', contato = '$contato', status = '$status', setor_id = $setor_id 
        WHERE id = $id";
} else {
    // Insert
    $sql = "INSERT INTO servidores (nome, cpf, contato, status, setor_id) VALUES 
        ('$nome', '$cpf', '$contato', '$status', $setor_id)";
}

if ($conn->query($sql)) {
    echo ($id > 0) ? "Servidor atualizado com sucesso." : "Servidor adicionado com sucesso.";
} else {
    echo "Erro: " . $conn->error;
}
