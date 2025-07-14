<?php
session_start();
include '../../conexao.php';

// Garante segurança da sessão
if (!isset($_SESSION['usuario_id'])) {
  http_response_code(403);
  echo json_encode(['erro' => 'Acesso não autorizado']);
  exit;
}

$dataHoje = date('Y-m-d');
$sql = "
    SELECT v.id, v.hora, v.saida, 
           vis.nome, vis.social, vis.cpf,
           s.nome AS nome_setor,
           srv.nome AS nome_servidor
    FROM visitas v
    JOIN visitantes vis ON v.visitante_id = vis.id
    LEFT JOIN setores s ON v.setor = s.id
    LEFT JOIN servidores srv ON v.servidor = srv.id
    WHERE v.data = ?
    ORDER BY v.hora DESC
";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $dataHoje);
$stmt->execute();
$result = $stmt->get_result();

$visitas = [];
while ($row = $result->fetch_assoc()) {
  $visitas[] = $row;
}

header('Content-Type: application/json');
echo json_encode($visitas);
?>
