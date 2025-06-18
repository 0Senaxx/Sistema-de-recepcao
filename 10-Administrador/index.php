<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../login.php");
    exit;
}

include '../conexao.php';

// Visitas do dia
$sqlVisitasHoje = "SELECT COUNT(*) AS total FROM visitas WHERE data = CURDATE()";
$visitasHoje = $conn->query($sqlVisitasHoje)->fetch_assoc()['total'] ?? 0;

// Visitas Registradas
$sqlVisitasRegistradas = "SELECT COUNT(*) AS total FROM visitas";
$visitasRegistradas = $conn->query($sqlVisitasRegistradas)->fetch_assoc()['total'] ?? 0;

// Ocorrências do mês
$sqlOcorrencias = "SELECT COUNT(*) AS total FROM ocorrencias WHERE MONTH(data_hora) = MONTH(CURDATE())";
$ocorrenciasMes = $conn->query($sqlOcorrencias)->fetch_assoc()['total'] ?? 0;

// Total de servidores cadastrados
$sqlTotalServidores = "SELECT COUNT(*) AS total FROM servidores";
$totalServidores = $conn->query($sqlTotalServidores)->fetch_assoc()['total'] ?? 0;

// Setores totais
$sqlSetores = "SELECT COUNT(*) AS total FROM setores";
$setores = $conn->query($sqlSetores)->fetch_assoc()['total'] ?? 0;

// Setores mais visitados no mês (Top 5 por sigla)
$sqlTopSetores = "
    SELECT s.sigla AS sigla_setor, COUNT(*) AS total
    FROM visitas v
    JOIN setores s ON v.setor = s.id
    WHERE MONTH(v.data) = MONTH(CURDATE())
    GROUP BY s.sigla
    ORDER BY total DESC
    LIMIT 5
";
$resultTopSetores = $conn->query($sqlTopSetores);

// Preparar dados para o gráfico
$setoresSigla = [];
$visitasSetores = [];
while ($row = $resultTopSetores->fetch_assoc()) {
    $setoresSigla[] = $row['sigla_setor'];
    $visitasSetores[] = $row['total'];
}

// Última atualização em 'servidores'
$sqlUltimaAtualizacaoServidores = "
    SELECT DATE_FORMAT(updated_at, '%d/%m/%Y') AS ultima_data, updated_by
    FROM servidores
    ORDER BY updated_at DESC
    LIMIT 1
";
$rowServidor = $conn->query($sqlUltimaAtualizacaoServidores)->fetch_assoc();
$ultimaServidor = $rowServidor['ultima_data'] ?? 'N/A';
$usuarioServidor = $rowServidor['updated_by'] ?? 'Desconhecido';

// Última atualização em 'setores'
$sqlUltimaAtualizacaoSetores = "
    SELECT DATE_FORMAT(updated_at, '%d/%m/%Y') AS ultima_data, updated_by
    FROM setores
    ORDER BY updated_at DESC
    LIMIT 1
";
$rowSetor = $conn->query($sqlUltimaAtualizacaoSetores)->fetch_assoc();
$ultimaSetor = $rowSetor['ultima_data'] ?? 'N/A';
$usuarioSetor = $rowSetor['updated_by'] ?? 'Desconhecido';



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

// Visitas por mês (últimos 6 meses)
$sqlVisitasPorMes = "
    SELECT DATE_FORMAT(data, '%m/%Y') AS mes, COUNT(*) AS total
    FROM visitas
    GROUP BY mes
    ORDER BY mes ASC
";
$resultVisitasPorMes = $conn->query($sqlVisitasPorMes);
$mesesLabels = [];
$totaisMes = [];
while ($row = $resultVisitasPorMes->fetch_assoc()) {
    $mesesLabels[] = $row['mes']; // exemplo: "06/2025"
    $totaisMes[] = $row['total'];
}

$sqlTempoMedio = "
    SELECT 
        s.sigla, 
        ROUND(AVG(TIME_TO_SEC(TIMEDIFF(v.saida, v.hora)))) AS tempo_medio_segundos
    FROM visitas v
    JOIN setores s ON v.setor = s.id
    WHERE v.saida IS NOT NULL
    GROUP BY s.sigla
    ORDER BY tempo_medio_segundos DESC
    LIMIT 10
";

$resultTempoMedio = $conn->query($sqlTempoMedio);

$setoresTempo = [];
$temposMediosMinutos = [];
$temposMediosFormatados = [];

function formatarTempo($segundos)
{
    $horas = floor($segundos / 3600);
    $minutos = floor(($segundos % 3600) / 60);

    if ($horas > 0) {
        return "{$horas}h {$minutos}min";
    } else {
        return "{$minutos}min";
    }
}

while ($row = $resultTempoMedio->fetch_assoc()) {
    $setoresTempo[] = $row['sigla'];
    $temposMediosMinutos[] = round($row['tempo_medio_segundos'] / 60, 1);
    $temposMediosFormatados[] = formatarTempo($row['tempo_medio_segundos']);
}

?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <title>Dashboard do Administrador</title>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>

    <header class="cabecalho">
        <h1>Painel do Administrador</h1>
        <nav>
            <a class="nav" href="index.php">Início</a>
            <a class="nav" href="Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="../04-Visitantes/visitantes.php">Visitantes</a>
            <a class="nav" href="Setores/index.php">Setores</a>
            <a class="nav" href="Visitas/visitas.php">Visitas</a>
            <a class="nav" href="Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <div class="Topo">
            <div class="cards"><?= $visitasHoje ?><br><small>Visitas de Hoje</small></div>
            <div class="cards"><?= $visitasRegistradas ?><br><small>Visitas Registradas</small></div>
            <div class="cards"><?= $ocorrenciasMes ?><br><small>Ocorrências Registradas</small></div>
            <div class="cards"><?= $totalServidores ?><br><small>Total de Servidores</small></div>
            <div class="cards"><?= $setores ?><br><small>Setores</small></div>
        </div>

        <div class="painel">
            <!-- LADO ESQUERDO: GRÁFICO -->
            <div class="lado-esquerdo">
                <div class="box-info">
                    <h3>📈 Relatório de Visitas Mensal</h3>
                    <canvas id="graficoVisitas" height="200"></canvas>
                    <div class="botoes-grafico">
                        <button class="btn-grafico" onclick="mostrarDia()">Visitas por Dia</button>
                        <button class="btn-grafico" onclick="mostrarMes()">Visitas por Mês</button>
                    </div>
                </div>

                <div class="box-info">
                    <h3>⏱️ Tempo Médio de Permanência por Setor</h3>
                    <canvas id="graficoTempoMedio" height="200"></canvas>
                </div>
            </div>

            <!-- LADO DIREITO: DUAS TABELAS -->
            <div class="lado-direito">
                <div class="box-info">
                    <h3>🏆 Setores mais visitados</h3>
                    <canvas id="graficoSetores" height="200"></canvas>
                </div>

                <div class="box-info">
                    <h3>🕒 Últimas Atualizações</h3>
                    <ul style="list-style: none; padding-left: 0;">
                        <li><strong>Módulo de Gestão de Pessoal</strong> | Atualizado em <?= $ultimaServidor ?> por <?= htmlspecialchars($usuarioServidor) ?></li>
                        <li><strong>Módulo de Gestão Telefônica</strong> | Atualizado em <?= $ultimaSetor ?> por <?= htmlspecialchars($usuarioSetor) ?></li>
                    </ul>

                </div>
            </div>
        </div>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>

    <script>
        const diasLabels = <?= json_encode($datas) ?>;
        const diasData = <?= json_encode($quantidades) ?>;
        const mesesLabels = <?= json_encode($mesesLabels) ?>;
        const mesesData = <?= json_encode($totaisMes) ?>;

        const ctx = document.getElementById('graficoVisitas').getContext('2d');
        const grafico = new Chart(ctx, {
            type: 'line',
            data: {
                labels: diasLabels,
                datasets: [{
                        label: 'Visitas por Dia (Mês Atual)',
                        data: diasData,
                        borderColor: 'blue',
                        backgroundColor: 'lightblue',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y'
                    },
                    {
                        label: 'Visitas por Mês',
                        data: mesesData,
                        borderColor: 'green',
                        backgroundColor: 'lightgreen',
                        fill: true,
                        tension: 0.4,
                        yAxisID: 'y',
                        hidden: true
                    }
                ]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true
                    }
                }
            }
        });

        function mostrarDia() {
            grafico.data.labels = diasLabels;
            grafico.data.datasets[0].hidden = false;
            grafico.data.datasets[1].hidden = true;
            grafico.update();
        }

        function mostrarMes() {
            grafico.data.labels = mesesLabels;
            grafico.data.datasets[0].hidden = true;
            grafico.data.datasets[1].hidden = false;
            grafico.update();
        }


        const ctxSetores = document.getElementById('graficoSetores').getContext('2d');
        const graficoSetores = new Chart(ctxSetores, {
            type: 'bar',
            data: {
                labels: <?= json_encode($setoresSigla) ?>,
                datasets: [{
                    label: 'Visitas por Setor',
                    data: <?= json_encode($visitasSetores) ?>,
                    backgroundColor: 'rgba(54, 162, 235, 0.7)',
                    borderColor: 'rgba(54, 162, 235, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                responsive: true,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            stepSize: 1
                        }
                    }
                }
            }
        });

        const setores = <?= json_encode($setoresTempo) ?>;
        const temposMinutos = <?= json_encode($temposMediosMinutos) ?>;
        const temposFormatados = <?= json_encode($temposMediosFormatados) ?>;

        const ctxTempo = document.getElementById('graficoTempoMedio').getContext('2d');
        const graficoTempo = new Chart(ctxTempo, {
            type: 'bar',
            data: {
                labels: setores,
                datasets: [{
                    label: 'Tempo médio',
                    data: temposMinutos,
                    backgroundColor: 'rgba(255, 159, 64, 0.6)',
                    borderColor: 'rgba(255, 159, 64, 1)',
                    borderWidth: 1
                }]
            },
            options: {
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return formatarMinutos(value);
                            }
                        },
                        title: {
                            display: true,
                            text: 'Tempo médio'
                        }
                    }
                },
                plugins: {
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const index = context.dataIndex;
                                return 'Tempo médio: ' + temposFormatados[index];
                            }
                        }
                    }
                }
            }

        });

        function formatarMinutos(valorMinutos) {
            const horas = Math.floor(valorMinutos / 60);
            const minutos = Math.round(valorMinutos % 60);
            if (horas > 0) {
                return `${horas}h ${minutos}min`;
            }
            return `${minutos}min`;
        }
    </script>

</body>

</html>