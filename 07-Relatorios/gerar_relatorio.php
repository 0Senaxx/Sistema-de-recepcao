<?php
require('../libs/fpdf/fpdf.php');
include '../conexao.php';

class PDF extends FPDF
{
    function Header()
    {
        // Papel timbrado ocupando toda a página
        $this->Image('../uploads/Timbrado-SEAD.png', 0, 0, 297, 210); // A4 horizontal
        $this->SetY(40); // Espaço para o timbrado no topo
    }

    function Footer()
    {
        // Rodapé com número da página
        $this->SetY(-15);
        $this->SetFont('Arial', 'I', 8);
        $this->Cell(0, 10, utf8_decode('Página ') . $this->PageNo(), 0, 0, 'C');
    }
}

// Filtros
$data_inicio = $_GET['data_inicio'] ?? '';
$data_fim = $_GET['data_fim'] ?? '';
$setor = $_GET['setor'] ?? '';
$servidor = $_GET['servidor'] ?? '';

$condicoes = [];
if ($data_inicio) $condicoes[] = "v.data >= '$data_inicio'";
if ($data_fim) $condicoes[] = "v.data <= '$data_fim'";
if ($setor) $condicoes[] = "v.setor = '$setor'";
if ($servidor) $condicoes[] = "v.servidor = '$servidor'";

$where = '';
if (!empty($condicoes)) {
    $where = 'WHERE ' . implode(' AND ', $condicoes);
}

$sql = "
    SELECT v.data, v.hora, v.saida, v.servidor,
           vis.nome AS nome_visitante, vis.cpf,
           s.sigla AS sigla_setor,
           srv.nome AS nome_servidor
    FROM visitas v
    JOIN visitantes vis ON v.visitante_id = vis.id
    LEFT JOIN setores s ON v.setor = s.id
    LEFT JOIN servidores srv ON v.servidor = srv.id
    $where
    ORDER BY v.data DESC, v.hora DESC
";

$result = $conn->query($sql);

$pdf = new PDF('L');
$pdf->SetMargins(10, 100, 10); // Margens esquerda, topo e direita
$pdf->SetAutoPageBreak(true, 20);
$pdf->AddPage();

// Título do relatório
$pdf->SetFont('Arial', 'B', 12);
$pdf->Cell(0, 10, utf8_decode('Relatório de Visitas - SEAD'), 0, 1, 'C');
$pdf->Ln(5);

// Cabeçalho da tabela com fonte menor
$pdf->SetFont('Arial', 'B', 8);
$pdf->Cell(30, 8, utf8_decode('Data'), 1);
$pdf->Cell(25, 8, utf8_decode('Entrada'), 1);
$pdf->Cell(55, 8, utf8_decode('Visitante'), 1);
$pdf->Cell(35, 8, 'CPF', 1);
$pdf->Cell(30, 8, utf8_decode('Setor'), 1);  // reduzido de 45 para 30
$pdf->Cell(55, 8, utf8_decode('Servidor'), 1); // aumentei o servidor para 55
$pdf->Cell(25, 8, utf8_decode('Saída'), 1);
$pdf->Ln();

// Dados da tabela com fonte menor e tratamento do nome do servidor
$pdf->SetFont('Arial', '', 8);
while ($row = $result->fetch_assoc()) {
    $pdf->Cell(30, 7, date('d/m/Y', strtotime($row['data'])), 1);
    $pdf->Cell(25, 7, $row['hora'], 1);
    $pdf->Cell(55, 7, utf8_decode($row['nome_visitante']), 1);
    $pdf->Cell(35, 7, $row['cpf'], 1);
    $pdf->Cell(30, 7, utf8_decode($row['sigla_setor']), 1); // reduzido
    // Nome servidor com limite maior agora
    $nome_servidor = utf8_decode($row['nome_servidor'] ?? '---');
    if (strlen($nome_servidor) > 40) {
        $nome_servidor = substr($nome_servidor, 0, 37) . '...';
    }
    $pdf->Cell(55, 7, $nome_servidor, 1);
    $pdf->Cell(25, 7, $row['saida'], 1);
    $pdf->Ln();
}
$pdf->Output();
?>
