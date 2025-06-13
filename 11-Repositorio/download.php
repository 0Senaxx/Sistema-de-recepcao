<?php
require_once '../01-Login/Auth/autenticacao.php';
require_once '../conexao.php';

if (!isset($_GET['id'])) {
    die('Arquivo não especificado.');
}

$id = intval($_GET['id']);

$sql = "SELECT nome_arquivo, caminho FROM documentos WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result && $result->num_rows > 0) {
    $row = $result->fetch_assoc();
    $file = $row['caminho'];
    $nomeOriginal = $row['nome_arquivo'];

    if (file_exists($file)) {
        // Define o tipo de arquivo dinamicamente
        $mime = mime_content_type($file);
        header('Content-Description: File Transfer');
        header('Content-Type: ' . $mime);
        header('Content-Disposition: attachment; filename="' . basename($nomeOriginal) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit;
    } else {
        die('Arquivo não encontrado.');
    }
} else {
    die('Documento não encontrado.');
}
?>
