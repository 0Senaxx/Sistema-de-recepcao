<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../01-Login/login.php");
    exit;
}

require_once '../conexao.php';

$id = intval($_POST['id'] ?? 0);
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$matricula = $conn->real_escape_string($_POST['matricula'] ?? '');
$status = $conn->real_escape_string($_POST['status'] ?? 'Ativo');
$setor_id = intval($_POST['setor_id'] ?? 0);

// Captura nome do usuário da sessão
$usuario = $conn->real_escape_string($_SESSION['nome'] ?? 'Desconhecido');

if ($nome == '' || $matricula == '' || $setor_id == 0) {
    echo "Preencha os campos obrigatórios.";
    exit;
}

if ($id > 0) {
    // Atualização
    $sql = "UPDATE servidores SET 
        nome = '$nome', 
        matricula = '$matricula', 
        status = '$status', 
        setor_id = $setor_id,
        updated_at = NOW(),
        updated_by = '$usuario'
        WHERE id = $id";
} else {
    // Inserção
    $sql = "INSERT INTO servidores (nome, matricula, status, setor_id, updated_at, updated_by) VALUES 
        ('$nome', '$matricula', '$status', $setor_id, NOW(), '$usuario')";
}

if ($conn->query($sql)) {
    echo ($id > 0) ? "Servidor atualizado com sucesso." : "Servidor adicionado com sucesso.";
} else {
    echo "Erro: " . $conn->error;
}
