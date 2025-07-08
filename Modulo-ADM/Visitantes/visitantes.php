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


$sql = "SELECT v.*, u.nome AS nome_usuario 
        FROM visitantes v
        LEFT JOIN usuarios u ON v.atualizado_por = u.id
        ORDER BY v.nome";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Lista de Visitantes</title>
    <link rel="stylesheet" href="visitantes.css">
</head>

<body>
    <header class="cabecalho">
        <h1>Painel de Gestão</h1>
        <nav>
            <a class="nav" href="../index.php">Início</a>
            <a class="nav" href="../Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="../Visitantes/visitantes.php">Visitantes</a>
            <a class="nav" href="../Setores/index.php">Setores</a>
            <a class="nav" href="../Servidores/index.php">Servidores</a>
            <a class="nav" href="../Visitas/visitas.php">Visitas</a>
            <a class="nav" href="../Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <div class="topo-modulo">
                <h1>Visitantes Cadastrados</h1>

                <div>
                    <label class="txt-label">Buscar: </label>
                    <input class="campo-buscar" type="text" id="filtro" autocomplete="off" placeholder="Digite para buscar..." onkeyup="filtrarTabela()">
                </div>
            </div>
        </section>

        <section class="card">
            <div class="table-container">
                <table>
                    <thead>
                        <tr>
                            <th class="col-cpf text-center">CPF</th>
                            <th class="col-nome">Nome</th>
                            <th class="col-orgao">Órgão/Entidade</th>
                            <th class="col-update text-center">Atualizado</th>
                            <th class="col-foto text-center">Foto</th>
                            <th class="col-acao text-center">Ação</th>
                        </tr>
                    </thead>

                    <tbody id="tabela-corpo">
                        <?php while ($row = $result->fetch_assoc()): ?>
                            <tr>
                                <td class="text-center">
                                    <?php
                                    $cpf = $row['cpf']; // Ex: "123.456.789-00"

                                    // Quebra o CPF pela máscara
                                    $partes = explode('.', $cpf); // $partes[0] = "123", $partes[1] = "456", etc.

                                    if (count($partes) === 3 && strpos($partes[2], '-') !== false) {
                                        $subpartes = explode('-', $partes[2]); // $subpartes[0] = "789", $subpartes[1] = "00"
                                        $cpf_masked = '***.' . $partes[1] . '.' . $subpartes[0] . '-**';
                                    } else {
                                        $cpf_masked = 'CPF inválido';
                                    }

                                    echo $cpf_masked;
                                    ?>
                                </td>
                                <td>
                                    <?= !empty($row['social']) ? $row['social'] : $row['nome'] ?>
                                </td>

                                <td>
                                    <?= $row['orgao'] ?>
                                </td>
                                <td class="text-center"><?= $row['nome_usuario'] ?? '---' ?></td>
                                <td class="text-center">
                                    <?php if ($row['foto']): ?>
                                        <img src="<?= $row['foto'] ?>" alt="Foto" width="60" class="foto-visitante">
                                    <?php else: ?>
                                        Sem foto
                                    <?php endif; ?>
                                </td>
                                <td class="text-center">
                                    <a href="editar_visitante.php?id=<?= $row['id'] ?>"
                                        class="btn-editar"><svg class="svg" viewBox="0 0 512 512">
                                            <path d="M410.3 231l11.3-11.3-33.9-33.9-62.1-62.1L291.7 89.8l-11.3 11.3-22.6 22.6L58.6 322.9c-10.4 10.4-18 23.3-22.2 37.4L1 480.7c-2.5 8.4-.2 17.5 6.1 23.7s15.3 8.5 23.7 6.1l120.3-35.4c14.1-4.2 27-11.8 37.4-22.2L387.7 253.7 410.3 231zM160 399.4l-9.1 22.7c-4 3.1-8.5 5.4-13.3 6.9L59.4 452l23-78.1c1.4-4.9 3.8-9.4 6.9-13.3l22.7-9.1v32c0 8.8 7.2 16 16 16h32zM362.7 18.7L348.3 33.2 325.7 55.8 314.3 67.1l33.9 33.9 62.1 62.1 33.9 33.9 11.3-11.3 22.6-22.6 14.5-14.5c25-25 25-65.5 0-90.5L453.3 18.7c-25-25-65.5-25-90.5 0zm-47.4 168l-144 144c-6.2 6.2-16.4 6.2-22.6 0s-6.2-16.4 0-22.6l144-144c6.2-6.2 16.4-6.2 22.6 0s6.2 16.4 0 22.6z"></path>
                                        </svg> Editar</a>
                                </td>
                            </tr>
                        <?php endwhile; ?>

                        <tr id="sem-resultados" style="display:none;">
                            <td class="nenhum" colspan="7">Nenhum visitante encontrado.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | EPP. Todos os direitos reservados
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