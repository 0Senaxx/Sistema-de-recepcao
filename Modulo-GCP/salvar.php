<?php
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../Firewall/login.php");
    exit;
}

include '../Firewall/Auth/autenticacao.php';
require_once '../conexao.php';

// ========================
// CAPTURA CAMPOS
// ========================
$id = $_POST['id'] ?? '';
$sigla = $conn->real_escape_string($_POST['sigla'] ?? '');
$nome = $conn->real_escape_string($_POST['nome'] ?? '');
$localizacao = $conn->real_escape_string($_POST['localizacao'] ?? '');
$ramal = $conn->real_escape_string($_POST['ramal'] ?? '');
$telefone = $conn->real_escape_string($_POST['telefone'] ?? '');
$usuario = $conn->real_escape_string($_SESSION['nome'] ?? 'Desconhecido');

if (empty($sigla) || empty($nome)) {
    echo "Sigla e nome são obrigatórios.";
    exit;
}

// ========================
// SALVA NO BANCO
// ========================
if (empty($id)) {
    // Inserir novo
    $sql = "INSERT INTO setores (sigla, nome, localizacao, ramal, telefone, updated_at, updated_by)
            VALUES ('$sigla', '$nome', '$localizacao', '$ramal', '$telefone', NOW(), '$usuario')";
} else {
    // Atualizar existente
    $id = intval($id);
    $sql = "UPDATE setores 
            SET sigla='$sigla',
                nome='$nome',
                localizacao='$localizacao',
                ramal='$ramal',
                telefone='$telefone',
                updated_at=NOW(),
                updated_by='$usuario'
            WHERE id=$id";
}

if ($conn->query($sql)) {
    header("Location: index.php");
    exit;
} else {
    echo "Erro ao salvar setor: " . $conn->error;
}
?>
