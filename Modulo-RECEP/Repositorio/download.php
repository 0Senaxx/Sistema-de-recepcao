<?php
require_once '../../Firewall/Auth/autenticacao.php';
require_once '../../conexao.php';

if (!isset($_GET['id'])) {
    die('Arquivo não especificado.');
}

$id = intval($_GET['id']);
$sql = "SELECT nome_arquivo, nome_original, caminho FROM documentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();

    $uploadDir = realpath(__DIR__ . '/../../Modulo-ADM/Documentos/Uploads/');
    if ($uploadDir === false) {
        die('Pasta de uploads não encontrada.');
    }

    $filename = basename($row['caminho']); // Use só o nome do arquivo
    $filepath = $uploadDir . DIRECTORY_SEPARATOR . $filename;

    if (file_exists($filepath)) {
        $mime = mime_content_type($filepath);
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($row['nome_original']) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filepath));
        readfile($filepath);
        exit;
    } else {
        die('Arquivo não encontrado: ' . $filepath);
    }
} else {
    die('Documento não encontrado.');
}
