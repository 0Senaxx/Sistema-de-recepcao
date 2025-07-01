<?php
include '../conexao.php';

$mes = isset($_GET['mes']) ? intval($_GET['mes']) : date('n');

$sqlTopSetores = "
    SELECT s.sigla AS sigla_setor, COUNT(*) AS total
    FROM visitas v
    JOIN setores s ON v.setor = s.id
    WHERE MONTH(v.data) = ?
    GROUP BY s.sigla
    ORDER BY total DESC
    LIMIT 5
";

$stmt = $conn->prepare($sqlTopSetores);
$stmt->bind_param("i", $mes);
$stmt->execute();
$result = $stmt->get_result();

$setores = [];
$visitas = [];

while ($row = $result->fetch_assoc()) {
    $setores[] = $row['sigla_setor'];
    $visitas[] = (int)$row['total'];
}

echo json_encode([
    'labels' => $setores,
    'data' => $visitas
]);
