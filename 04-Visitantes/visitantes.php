<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/autenticacao.php';

include '../conexao.php';

$sql = "SELECT * FROM visitantes ORDER BY nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Visitantes</title>
    <link rel="stylesheet" href="visitantes.css">
</head>

<body class="container py-4">

    <header class="cabecalho">
        <h1>Recepção SEAD</h1>
        <nav>
            <a href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">+ Nova Visita</a>
            <a href="../05-Visitas/visitas.php" onclick="fadeOut(event, this)">Lista de Visitas</a>
            <a href="../04-Visitantes/visitantes.php" onclick="fadeOut(event, this)">Lista de Visitantes</a>
            <a href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a href="../01-Login/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="card">
            <div class="topo">
                <h2>Visitantes Cadastrados</h2>
                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" autocomplete="none" placeholder="Digite para buscar..."
                        onkeyup="filtrarTabela()">
                </div>
            </div>

            <table class="table table-bordered table-striped">
                <thead>
                    <tr>
                        <th>CPF</th>
                        <th>Nome</th>
                        <th>Órgão/Entidade</th>
                        <th class="text-center">Telefone</th>
                        <th class="text-center">Foto</th>
                        <th class="text-center">Ação</th>
                    </tr>
                </thead>
                <tbody id="tabela-corpo">
                    <?php while ($row = $result->fetch_assoc()): ?>
                    <tr>
                        <td>
                            <?= $row['cpf'] ?>
                        </td>
                        <td>
                            <?= $row['nome'] ?>
                        </td>
                        
                        <td>
                            <?= $row['orgao'] ?>
                        </td>
                        <td class="text-center">
                            <?= $row['telefone'] ?>
                        </td>

                        <td class="text-center">
                            <?php if ($row['foto']): ?>
                            <img src="<?= $row['foto'] ?>" alt="Foto" width="60">
                            <?php else: ?>
                            Sem foto
                            <?php endif; ?>
                        </td>
                        <td class="text-center">
                            <a href="editar_visitante.php?id=<?= $row['id'] ?>"
                                class="btn-editar"><svg class="svg" viewBox="0 0 512 512">
        <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path></svg>   Editar</a>
                        </td>
                    </tr>
                    <?php endwhile; ?>

                    <tr id="sem-resultados" style="display:none;">
                        <td colspan="7" style="text-align:center; font-style: italic;">Nenhum visitante encontrado.</td>
                    </tr>
                </tbody>
            </table>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>

    <script>
        
function filtrarTabela() {
    const filtro = document.getElementById('filtro').value.toLowerCase();
    const tabela = document.getElementById('tabela-corpo');
    const linhas = tabela.getElementsByTagName('tr');

    let encontrou = false;

    for (let i = 0; i < linhas.length; i++) {
        const linha = linhas[i];

        // Pula a linha da mensagem "Nenhum resultado"
        if (linha.id === 'sem-resultados') continue;

        const textoLinha = linha.textContent.toLowerCase();

        if (textoLinha.includes(filtro)) {
            linha.style.display = '';
            encontrou = true;
        } else {
            linha.style.display = 'none';
        }
    }

    // Mostrar ou esconder a linha de "Nenhum resultado"
    document.getElementById('sem-resultados').style.display = encontrou ? 'none' : '';
}
    </script>
</body>
</html>