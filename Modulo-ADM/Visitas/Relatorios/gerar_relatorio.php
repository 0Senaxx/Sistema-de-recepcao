<?php
require('../../../libs/fpdf/fpdf.php');
include '../../../conexao.php';

class PDF extends FPDF
{
    function Header()
    {
        // Imagem de fundo (timbrado)
        $this->Image('../../../Imagens/Timbrado-SEAD-Paisagem.png', 0, 0, 297, 210); // A4 horizontal
        $this->SetY(40);

        // Título
        $this->SetFont('Arial', 'B', 12);
        $this->Cell(0, 10, utf8_decode('Relatório de Visitas - SEAD'), 0, 1, 'C');
        $this->Ln(5);

        // Cabeçalhos da tabela
        $this->SetFont('Arial', 'B', 8);
        $this->SetFillColor(215, 215, 215);
        $this->SetTextColor(0, 0, 0);

        $this->Cell(20, 8, utf8_decode('Data'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('Entrada'), 1, 0, 'C', true);
        $this->Cell(60, 8, utf8_decode('Visitante'), 1, 0, 'C', true);
        $this->Cell(25, 8, 'CPF', 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('Setor'), 1, 0, 'C', true);
        $this->Cell(70, 8, utf8_decode('Servidor'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('Saída'), 1, 0, 'C', true);
        $this->Cell(20, 8, utf8_decode('Duração'), 1, 0, 'C', true);
        $this->Cell(25, 8, utf8_decode('Registrado por'), 1, 1, 'C', true);
    }

    function Footer()
    {
        $this->SetY(-9);
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
SELECT 
    v.data, v.hora, v.saida, v.servidor,
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

$pdf = new PDF('L');
$pdf->SetMargins(5, 100, 5);
$pdf->SetAutoPageBreak(true, 30);
$pdf->AddPage();


// Resetar cores para o conteúdo
$pdf->SetFillColor(255, 255, 255); // fundo branco normal
$pdf->SetTextColor(0, 0, 0); // texto preto


// Linhas
$pdf->SetFont('Arial', '', 8);
while ($row = $result->fetch_assoc()) {
    // Anonimizar CPF (pegando 4° a 9° caracteres)
    $cpf = preg_replace('/\D/', '', $row['cpf']); // remove pontos e traços
    $cpf_anon = '***.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-**';

    // Duração
    $hora = $row['hora'];
    $saida = $row['saida'];
    $duracao = '';
    if ($hora && $saida) {
        $entrada_dt = new DateTime($hora);
        $saida_dt = new DateTime($saida);
        $intervalo = $entrada_dt->diff($saida_dt);
        $duracao = $intervalo->format('%H:%I:%M');
    }

    $nome_servidor = utf8_decode($row['nome_servidor'] ?? '---');
    if (strlen($nome_servidor) > 35) {
        $nome_servidor = substr($nome_servidor, 0, 32) . '...';
    }

    $pdf->Cell(20, 7, date('d/m/Y', strtotime($row['data'])), 1, 0, 'C'); // Data centralizado
    $pdf->Cell(20, 7, $hora, 1, 0, 'C');                                   // Entrada centralizado
    $pdf->Cell(60, 7, utf8_decode($row['nome_visitante']), 1);            // Visitante (alinhamento padrão à esquerda)
    $pdf->Cell(25, 7, $cpf_anon, 1, 0, 'C');                               // CPF centralizado
    $pdf->Cell(25, 7, utf8_decode($row['sigla_setor']), 1, 0, 'C');        // Setor centralizado
    $pdf->Cell(70, 7, $nome_servidor, 1);                                  // Servidor (alinhamento padrão à esquerda)
    $pdf->Cell(20, 7, $saida ?: '---', 1, 0, 'C');                         // Saída centralizado
    $pdf->Cell(20, 7, $duracao ?: '---', 1, 0, 'C');                       // Duração centralizado
    $nome_usuario_registro = utf8_decode($row['nome_usuario_registro'] ?? '---');
    if (strlen($nome_usuario_registro) > 30) {
        $nome_usuario_registro = substr($nome_usuario_registro, 0, 27) . '...';
    }
    $pdf->Cell(25, 7, $nome_usuario_registro, 1, 0, 'C');

    $pdf->Ln();
}

$pdf->Output();
