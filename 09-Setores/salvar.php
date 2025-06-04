<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/autenticacao.php';

require_once '../conexao.php';

$id = $_POST['id'] ?? '';
$sigla = $conn->real_escape_string($_POST['sigla'] ?? '');
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$localizacao = $conn->real_escape_string($_POST['localizacao'] ?? '');
$ramal = $conn->real_escape_string($_POST['ramal'] ?? '');

if (empty($sigla) || empty($nome)) {
    echo "Sigla e nome são obrigatórios.";
    exit;
}

if (empty($id)) {
    // Inserir novo
    $sql = "INSERT INTO setores (sigla, nome, localizacao, ramal) VALUES ('$sigla', '$nome', '$localizacao', '$ramal')";
} else {
    // Atualizar existente
    $id = intval($id);
    $sql = "UPDATE setores SET sigla='$sigla', nome='$nome', localizacao='$localizacao', ramal='$ramal' WHERE id=$id";
}

if ($conn->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Erro ao salvar setor: " . $conn->error;
}
?>
