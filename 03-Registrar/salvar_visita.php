<?php
session_start();
include '../conexao.php';

$usuario_id = $_SESSION['usuario_id'] ?? null;

// Função para salvar foto (base64 ou arquivo)
function salvarFoto()
{
    if (!empty($_POST['foto_base64'])) {
        $foto_base64 = str_replace('data:image/jpeg;base64,', '', $_POST['foto_base64']);
        $foto_base64 = str_replace(' ', '+', $foto_base64);
        $foto_data = base64_decode($foto_base64);

        $pasta = "../04-Visitantes/Fotos/";
        if (!is_dir($pasta)) mkdir($pasta, 0777, true);

        $nome_arquivo = $pasta . uniqid() . '.jpg';
        file_put_contents($nome_arquivo, $foto_data);

        return $nome_arquivo;
    }

    return null;
}

// === DADOS DO FORMULÁRIO === //
$cpf      = $_POST['cpf'];
$nome     = $_POST['nome'];
$social   = $_POST['social'] ?? null;
$orgao    = $_POST['orgao'];
$data     = $_POST['data'];
$hora     = $_POST['hora'];
$servidor = $_POST['servidor'];
$setor    = intval($_POST['setor_id']);
$foto     = salvarFoto();

// === VERIFICA SE O VISITANTE JÁ EXISTE PELO CPF === //
$sql = "SELECT id FROM visitantes WHERE cpf = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $cpf);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $visitante_id = $row['id'];

    if ($foto) {
        $sqlUpdate = "UPDATE visitantes SET nome = ?, social = ?, orgao = ?, foto = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssi", $nome, $social, $orgao, $foto, $visitante_id);
    } else {
        $sqlUpdate = "UPDATE visitantes SET nome = ?, social = ?, orgao = ? WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("ssssi", $nome, $social, $orgao, $visitante_id);
    }

    $stmtUpdate->execute();
} else {
    $sqlInsert = "INSERT INTO visitantes (cpf, nome, social, orgao, foto) VALUES (?, ?, ?, ?, ?, ?)";
    $stmtInsert = $conn->prepare($sqlInsert);
    $stmtInsert->bind_param("sssss", $cpf, $nome, $social, $orgao, $foto);
    $stmtInsert->execute();

    $visitante_id = $stmtInsert->insert_id;
}

// === REGISTRA A VISITA COM USUÁRIO === //
$sqlVisita = "INSERT INTO visitas (visitante_id, data, hora, servidor, setor, usuario_id) VALUES (?, ?, ?, ?, ?, ?)";
$stmtVisita = $conn->prepare($sqlVisita);
$stmtVisita->bind_param("isssii", $visitante_id, $data, $hora, $servidor, $setor, $usuario_id);
$stmtVisita->execute();

// === REDIRECIONA === //
header("Location: ../02-Inicio/index.php");
exit();
