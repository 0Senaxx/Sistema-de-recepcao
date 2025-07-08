<?php
session_start();
if (!isset($_SESSION['usuario_id'])) {
    header('HTTP/1.1 401 Unauthorized');
    exit;
}

require_once '../../conexao.php';

$id = intval($_GET['id'] ?? 0);

if ($id <= 0) {
    header('HTTP/1.1 400 Bad Request');
    echo json_encode(['error' => 'ID inválido']);
    exit;
}

// Ajuste: usar JOIN para pegar também o setor_id e nome do setor
$sql = "
    SELECT 
        s.*, 
        se.nome AS setor_nome 
    FROM 
        servidores s 
    LEFT JOIN 
        setores se 
    ON 
        s.setor_id = se.id 
    WHERE 
        s.id = $id
";

$res = $conn->query($sql);

if (!$res || $res->num_rows === 0) {
    header('HTTP/1.1 404 Not Found');
    echo json_encode(['error' => 'Servidor não encontrado']);
    exit;
}

$servidor = $res->fetch_assoc();

header('Content-Type: application/json');
echo json_encode($servidor);
