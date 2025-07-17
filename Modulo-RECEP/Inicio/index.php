<?php
// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start();

if (!isset($_SESSION['usuario_id'])) {
    header("Location: ../../Firewall/login.php");
    exit;
}

include '../../Firewall/Auth/autenticacao.php';
include '../../Firewall/Auth/controle_sessao.php';
include '../../conexao.php';
// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

// Pegar visitas de hoje
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
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Controle de Visitas - SEAD</title>
    <link rel="stylesheet" href="estilo.css">
</head>

<body>
    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a class="nav" href="../Inicio/index.php">Início</a>
            <a class="nav" href="../Registrar/nova_visita.php">Nova Visita</a>
            <a class="nav" href="../Ramais/ramais.php">Ramais SEAD</a>
            <a class="nav" href="../Repositorio/repositorio.php">Repositório</a>
            <a class="nav" href="../Ocorrencias/registro_ocorrencia.php">Ocorrências</a>
            <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <div class="topo-modulo">
                <h2 class="bem-vindo">Bem-vindo(a), <?= $_SESSION['nome']; ?>!</h2>

                <h2 class="titulo-centro">Visitas do Dia: <?= date('d/m/Y') ?></h2>

                <div class="controle-botoes">
                    <a href="../Registrar/nova_visita.php" class="btn-acao  bnt-nova">
                        <img src="../../Imagens/Icons/adicionar.png" alt="Nova Visita">
                        Nova Visita
                    </a>

                    <a href="leitor.php" class="btn-acao  bnt-qr">
                        <img src="../../Imagens/Icons/sair.png" alt="Registrar Saída">
                        Registrar Saída
                    </a>
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="text-center">Entrada</th>
                            <th>Nome do Visitante</th>
                            <th class="text-center">CPF</th>
                            <th>Setor Visitado</th>
                            <th>Servidor Visitado</th>
                            <th class="text-center">Saída</th>
                            <th class="text-center">Status</th>
                        </tr>
                    </thead>
                    <tbody id="visitas-tbody">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center"><?= $row['hora'] ?></td>
                                <td><?= !empty($row['social']) ? $row['social'] : $row['nome']; ?></td>
                                <td class="text-center">
                                    <?php
                                    $cpf = $row['cpf'];
                                    $partes = explode('.', $cpf);
                                    if (count($partes) === 3 && strpos($partes[2], '-') !== false) {
                                        $subpartes = explode('-', $partes[2]);
                                        $cpf_masked = '***.' . $partes[1] . '.' . $subpartes[0] . '-**';
                                    } else {
                                        $cpf_masked = 'CPF inválido';
                                    }
                                    echo $cpf_masked;
                                    ?>
                                </td>
                                <td><?= $row['nome_setor'] ?? '---' ?></td>
                                <td><?= $row['nome_servidor'] ?? '---' ?></td>
                                <td class="text-center"><?= $row['saida'] ? $row['saida'] : '---' ?></td>
                                <td class="text-center">
                                    <?= $row['saida'] ? '<span class="text-success">Encerrada</span>' : '<span class="text-warning">Em Andamento</span>'; ?>
                                </td>
                            </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
    </footer>

    <script>
        function maskCpf(cpf) {
            const partes = cpf.split('.');
            if (partes.length === 3 && partes[2].includes('-')) {
                const subpartes = partes[2].split('-');
                return '***.' + partes[1] + '.' + subpartes[0] + '-**';
            } else {
                return 'CPF inválido';
            }
        }

        function atualizarTabela() {
            fetch('listar_visitas.php')
                .then(response => response.json())
                .then(data => {
                    const tbody = document.getElementById('visitas-tbody');
                    tbody.innerHTML = ''; // Limpa tudo

                    data.forEach(visita => {
                        const tr = document.createElement('tr');

                        // Entrada
                        tr.innerHTML += `<td class="text-center">${visita.hora}</td>`;

                        // Nome ou social
                        const nome = visita.social && visita.social.trim() !== '' ? visita.social : visita.nome;
                        tr.innerHTML += `<td>${nome}</td>`;

                        // CPF mascarado
                        tr.innerHTML += `<td class="text-center">${maskCpf(visita.cpf)}</td>`;

                        // Setor
                        tr.innerHTML += `<td>${visita.nome_setor || '---'}</td>`;

                        // Servidor
                        tr.innerHTML += `<td>${visita.nome_servidor || '---'}</td>`;

                        // Saída
                        tr.innerHTML += `<td class="text-center">${visita.saida ? visita.saida : '---'}</td>`;

                        // Status
                        const status = visita.saida ?
                            '<span class="text-success">Encerrada</span>' :
                            '<span class="text-warning">Em Andamento</span>';
                        tr.innerHTML += `<td class="text-center">${status}</td>`;

                        tbody.appendChild(tr);
                    });
                })
                .catch(error => console.error('Erro ao carregar visitas:', error));
        }

        // Atualiza a tabela a cada 10 segundos
        setInterval(atualizarTabela, 10000);

        // Chama logo que a página carrega
        atualizarTabela();
    </script>
</body>

</html>