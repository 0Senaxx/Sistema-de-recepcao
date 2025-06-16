<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

// Visitas do dia
$sqlVisitasHoje = "SELECT COUNT(*) AS total FROM visitas WHERE data = CURDATE()";
$visitasHoje = $conn->query($sqlVisitasHoje)->fetch_assoc()['total'] ?? 0;

// Ocorrências do mês
$sqlOcorrencias = "SELECT COUNT(*) AS total FROM ocorrencias WHERE MONTH(data_hora) = MONTH(CURDATE())";
$ocorrenciasMes = $conn->query($sqlOcorrencias)->fetch_assoc()['total'] ?? 0;

// Servidores disponíveis
$sqlServidores = "SELECT COUNT(*) AS total FROM servidores WHERE status = 'Ativo'";
$servidores = $conn->query($sqlServidores)->fetch_assoc()['total'] ?? 0;

// Setores totais
$sqlSetores = "SELECT COUNT(*) AS total FROM setores";
$setores = $conn->query($sqlSetores)->fetch_assoc()['total'] ?? 0;

// Setores mais visitados no mês
$sqlTopSetores = "
    SELECT s.nome AS nome_setor, COUNT(*) AS total
    FROM visitas v
    JOIN setores s ON v.setor = s.id
    WHERE MONTH(v.data) = MONTH(CURDATE())
    GROUP BY s.nome
    ORDER BY total DESC
    LIMIT 5
";
$resultTopSetores = $conn->query($sqlTopSetores);

// Últimas atualizações de servidores
$sqlAtualizacoesServidores = "
    SELECT nome, DATE_FORMAT(updated_at, '%d/%m/%y') as data
    FROM servidores
    ORDER BY updated_at DESC
    LIMIT 5
";
$resultAtualizacoes = $conn->query($sqlAtualizacoesServidores);

// Relatório de visitas por dia no mês
$sqlVisitasMes = "
    SELECT DATE(data) as dia, COUNT(*) as total
    FROM visitas
    WHERE MONTH(data) = MONTH(CURDATE())
    GROUP BY dia
    ORDER BY dia ASC
";
$resultVisitasMes = $conn->query($sqlVisitasMes);
$datas = [];
$quantidades = [];
while ($row = $resultVisitasMes->fetch_assoc()) {
    $datas[] = $row['dia'];
    $quantidades[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <title>Dashboard do Administrador</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="estilo.css">
</head>
<body>

<h1>📊 Painel do Administrador</h1>

        <section class="Modulo">
            <div class="Topo">
                <div class="card"><?= $visitasHoje ?><br><small>Visitas de Hoje</small></div>
                <div class="card"><?= $ocorrenciasMes ?><br><small>Ocorrências Registradas</small></div>
                <div class="card"><?= $servidores ?><br><small>Servidores Disponíveis</small></div>
                <div class="card"><?= $setores ?><br><small>Setores</small></div>
            </div>
        </section>

        <section class="Modulo">
  <div class="painel">
    <!-- LADO ESQUERDO: GRÁFICO -->
    <div class="lado-esquerdo">
      <h3>📈 Relatório de Visitas Mensal</h3>
      <canvas id="graficoVisitas" height="250"></canvas>
    </div>

    <!-- LADO DIREITO: DUAS TABELAS -->
    <div class="lado-direito">
      <div class="box-info">
        <h3>🏆 Setores mais visitados</h3>
        <table>
            <tr><th>Setor</th><th>Visitas</th></tr>
                <?php while ($setor = $resultTopSetores->fetch_assoc()) : ?>
            <tr>
                <td><?= htmlspecialchars($setor['nome_setor']) ?></td>
                <td><?= $setor['total'] ?></td>
            </tr>
            <?php endwhile; ?>
        </table>
      </div>

      <div class="box-info">
        <h3>🕒 Últimas atualizações de servidores</h3>
                    <table>
                        <tr><th>Servidor</th><th>Data</th></tr>
                        <?php while ($atual = $resultAtualizacoes->fetch_assoc()) : ?>
                            <tr>
                                <td><?= htmlspecialchars($atual['nome']) ?></td>
                                <td><?= $atual['data'] ?></td>
                            </tr>
                        <?php endwhile; ?>
                    </table>
      </div>
    </div>
  </div>
</section>


<script>
    const ctx = document.getElementById('graficoVisitas').getContext('2d');
    const grafico = new Chart(ctx, {
        type: 'line',
        data: {
            labels: <?= json_encode($datas) ?>,
            datasets: [{
                label: 'Visitas por dia',
                data: <?= json_encode($quantidades) ?>,
                borderColor: 'blue',
                backgroundColor: 'lightblue',
                fill: true,
                tension: 0.4
            }]
        },
        options: {
            scales: {
                y: { beginAtZero: true }
            }
        }
    });
</script>

</body>
</html>
