<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start(); 

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../../01-Login/login.php");
  exit; 
}

include '../../01-Login/Auth/autenticacao.php';
include '../../01-Login/Auth/controle_sessao.php';
include '../../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

$id = $_POST['id'] ?? '';
$sigla = $conn->real_escape_string($_POST['sigla'] ?? '');
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$localizacao = $conn->real_escape_string($_POST['localizacao'] ?? '');
$ramal = $conn->real_escape_string($_POST['ramal'] ?? '');
$usuario = $conn->real_escape_string($_SESSION['nome'] ?? 'Desconhecido');

if (empty($sigla) || empty($nome)) {
    echo "Sigla e nome são obrigatórios.";
    exit;
}

if (empty($id)) {
    // Inserir novo
    $sql = "INSERT INTO setores (sigla, nome, localizacao, ramal, updated_at, updated_by)
            VALUES ('$sigla', '$nome', '$localizacao', '$ramal', NOW(), '$usuario')";
} else {
    // Atualizar existente
    $id = intval($id);
    $sql = "UPDATE setores 
            SET sigla='$sigla', nome='$nome', localizacao='$localizacao', ramal='$ramal', 
                updated_at=NOW(), updated_by='$usuario'
            WHERE id=$id";
}

if ($conn->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Erro ao salvar setor: " . $conn->error;
}
?>
