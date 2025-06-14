<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/Auth/autenticacao.php';
include '../01-Login/Auth/controle_sessao.php';
include '../conexao.php';

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
            <a href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">+ Nova Visita</a>
            <a href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a href="../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="card">

            <div class="cabecalho-visitas">
                <h2 class="bem-vindo">Bem-vindo(a),
                    <?= $_SESSION['nome']; ?>!
                </h2>
                <h2 class="titulo-centro">Visitas do Dia <br> <?= date('d/m/Y') ?>
                </h2>
                <a href="../03-Registrar/nova_visita.php" class="bnt-nova">Nova visita</a>
            </div><br>
            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>Entrada</th>
                        <th>Nome do Visitante</th>
                        <th>CPF</th>
                        <th>Setor Visitado</th>
                        <th>Servidor Visitado</th>
                        <th>Saída</th>
                        <th>Ação</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td><?= $row['hora'] ?></td>
                            <td>
                                <?= !empty($row['social']) ? $row['social'] : $row['nome']; ?>
                            </td>

                            <td>
                                <?php
                                $cpf = $row['cpf'];
                                $cpf_masked = substr($cpf, 0, 3) . '.' . '***.***-**';
                                echo $cpf_masked;
                                ?>
                            </td>
                            <td><?= $row['nome_setor'] ?? '---' ?></td>
                            <td><?= $row['nome_servidor'] ?? '---' ?></td>
                            <td><?= $row['saida'] ? $row['saida'] : '---' ?></td>
                            <td class="text-center">
                            <?php if (!$row['saida']): ?>
                            <form action="registrar_saida.php" method="POST" style="display:inline;">
                                <input type="hidden" name="visita_id" value="<?= $row['id'] ?>">
                                <button type="submit" class="btn btn-sm btn-danger">Registrar Saída</button>
                            </form>
                            <?php else: ?>
                            <span class="text-success">Encerrada</span>
                            <?php endif; ?>
                        </td>
                    </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>
</body>

</html>