<?php
include '../../../conexao.php';

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

// Consulta atualizada com joins para setor, servidor e usuário
$sql = "
    SELECT v.data, v.hora, v.saida,
           vis.nome AS nome_visitante, vis.cpf,
           s.sigla AS sigla_setor,
           srv.nome AS nome_servidor,
           u.nome AS nome_usuario_registro
    FROM visitas v
    JOIN visitantes vis ON v.visitante_id = vis.id
    LEFT JOIN setores s ON v.setor = s.id
    LEFT JOIN servidores srv ON v.servidor = srv.id
    LEFT JOIN usuarios u ON v.usuario_id = u.id
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
        <th>Duração</th>
        <th>Registrado por</th>
      </tr>";

while ($row = $result->fetch_assoc()) {
    // CPF Anonimizado
    $cpf = preg_replace('/\D/', '', $row['cpf']);
    $cpf_anon = '***.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-**';

    // Duração
    $duracao = '';
    if ($row['hora'] && $row['saida']) {
        $entrada_dt = new DateTime($row['hora']);
        $saida_dt = new DateTime($row['saida']);
        $intervalo = $entrada_dt->diff($saida_dt);
        $duracao = $intervalo->format('%H:%I:%S');
    }

    echo "<tr>
            <td>" . date('d/m/Y', strtotime($row['data'])) . "</td>
            <td>" . $row['hora'] . "</td>
            <td>" . htmlspecialchars($row['nome_visitante']) . "</td>
            <td>" . $cpf_anon . "</td>
            <td>" . htmlspecialchars($row['sigla_setor'] ?? '---') . "</td>
            <td>" . htmlspecialchars($row['nome_servidor'] ?? '---') . "</td>
            <td>" . ($row['saida'] ?: '---') . "</td>
            <td>" . ($duracao ?: '---') . "</td>
            <td>" . htmlspecialchars($row['nome_usuario_registro'] ?? '---') . "</td>
          </tr>";
}
echo "</table>";
?>
