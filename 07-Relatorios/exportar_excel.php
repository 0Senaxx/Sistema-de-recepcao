<?php
include '../conexao.php';

// Cabeçalhos com charset UTF-8
header("Content-Type: application/vnd.ms-excel; charset=UTF-8");
header("Content-Disposition: attachment; filename=relatorio_visitas.xls");
header("Pragma: no-cache");
header("Expires: 0");

// Força BOM UTF-8 para Excel reconhecer acentuação
echo "\xEF\xBB\xBF";

// Filtros recebidos por GET
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$setor = $_GET['setor'] ?? '';
$servidor = $_GET['servidor'] ?? '';

// Montagem da cláusula WHERE
$condicoes = [];
if ($data_inicio) $condicoes[] = "v.data >= '$data_inicio'";
if ($data_fim) $condicoes[] = "v.data <= '$data_fim'";
if ($setor) $condicoes[] = "v.setor = '$setor'";
if ($servidor) $condicoes[] = "v.servidor = '$servidor'";

$where = '';
if (!empty($condicoes)) {
    $where = 'WHERE ' . implode(' AND ', $condicoes);
}

// Consulta
$sql = "
    SELECT v.data, v.hora, vis.nome AS nome_visitante, vis.cpf, v.setor, v.servidor, v.saida
    FROM visitas v
    JOIN visitantes vis ON v.visitante_id = vis.id
    $where
    ORDER BY v.data DESC, v.hora DESC
";

$result = $conn->query($sql);

// Geração da tabela HTML
echo "<table border='1'>";
echo "<tr>
        <th>Data</th>
        <th>Entrada</th>
        <th>Nome do Visitante</th>
        <th>CPF</th>
        <th>Setor</th>
        <th>Servidor</th>
        <th>Saída</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td>" . date('d/m/Y', strtotime($row['data'])) . "</td>
            <td>" . $row['hora'] . "</td>
            <td>" . htmlspecialchars($row['nome_visitante']) . "</td>
            <td>" . $row['cpf'] . "</td>
            <td>" . htmlspecialchars($row['setor']) . "</td>
            <td>" . htmlspecialchars($row['servidor']) . "</td>
            <td>" . $row['saida'] . "</td>
          </tr>";
}
echo "</table>";
?>
