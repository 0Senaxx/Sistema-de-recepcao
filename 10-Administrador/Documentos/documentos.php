<?php
require_once '../../01-Login/Auth/autenticacao.php';
require_once '../../conexao.php'; // ajuste para seu arquivo de conexão

// Upload de novo documento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $nome_original = $_FILES['documento']['name'];
    $nome_arquivo = $_POST['nome_arquivo'] ?? pathinfo($nome_original, PATHINFO_FILENAME);
    $descricao = $_POST['descricao'] ?? '';
    $caminho = '../../uploads/' . basename($nome_original);


    if (move_uploaded_file($_FILES['documento']['tmp_name'], $caminho)) {
        $stmt = $conn->prepare("INSERT INTO documentos (nome_arquivo, nome_original, descricao, caminho, data_envio) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $nome_arquivo, $nome_original, $descricao, $caminho);

        $stmt->execute();
        // Redireciona para evitar reenvio no refresh
        header("Location: documentos.php?sucesso=1");
        exit;
    } else {
        // Redireciona com erro
        header("Location: documentos.php?erro=1");
        exit;
    }
}

// Exclusão de documento
if (isset($_GET['excluir'])) {
    $id = intval($_GET['excluir']);
    // Buscando caminho do arquivo
    $stmt = $conn->prepare("SELECT caminho FROM documentos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $stmt->bind_result($caminho);
    if ($stmt->fetch()) {
        unlink($caminho); // Apaga o arquivo físico
    }
    $stmt->close();

    // Exclui o registro no banco
    $stmt = $conn->prepare("DELETE FROM documentos WHERE id = ?");
    $stmt->bind_param("i", $id);
    $stmt->execute();
    header("Location: documentos.php?excluido=1");
    exit;
}

// Lista de documentos
$sql = "SELECT id, nome_arquivo, descricao, caminho, data_envio FROM documentos ORDER BY data_envio DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html>

<head>
    <meta charset="UTF-8">
    <link rel="stylesheet" href="documentos.css">
    <title>Gerenciar Documentos</title>
</head>

<body>
    <header class="cabecalho">
        <h1>Painel do Administrador</h1>
        <nav>
            <a class="nav" href="../index.php">Início</a>
            <a class="nav" href="../Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="../../04-Visitantes/visitantes.php">Visitantes</a>
            <a class="nav" href="../Setores/index.php">Setores</a>
            <a class="nav" href="../Visitas/visitas.php">Visitas</a>
            <a class="nav" href="../Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../../01-Login/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="Modulo">
            <div class="topo-modulo">
                <h1>Gerenciador Documentos</h1>
                <button onclick="abrirModal()" class="bntSalvar">Adicionar Novo Documento</button>
            </div>
        </section>

        <section class="card">
            <h2>Documentos Cadastrados</h2>
            <table border="1">
                <thead>
                    <tr>
                        <th>Nome</th>
                        <th style="width: 850px;">Descrição</th>
                        <th class="text-center">Data de Envio</th>
                        <th class="text-center">Ações</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo htmlspecialchars($row['nome_arquivo']); ?></td>
                            <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                            <td class="text-center"><?php echo date('d/m/Y', strtotime($row['data_envio'])); ?></td>
                            <td class="text-center">
                                <a class="btnBaixar" href="<?php echo htmlspecialchars($row['caminho']); ?>" download>Baixar</a> |
                                <a class="btnExcluir" href="?excluir=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </section>
    </main>

    <!-- Modal -->
    <div id="modalUpload" class="modal">
        <div class="modal-content">
            <span class="fechar" onclick="fecharModal()">&times;</span>
            <h2>Enviar Novo Documento</h2>
            <form method="post" enctype="multipart/form-data">
                <label for="nome_arquivo">Nome do Arquivo:</label>
                <input type="text" id="nome_arquivo" name="nome_arquivo" required>

                <label for="descricao">Descrição:</label>
                <textarea id="descricao" name="descricao" rows="3"></textarea>

                <label for="documento">Selecionar Arquivo:</label>
                <input type="file" id="documento" name="documento" required>

                <button type="submit">Enviar</button>
            </form>
        </div>
    </div>


</body>
<script>
    function abrirModal() {
        document.getElementById('modalUpload').style.display = 'block';
    }

    function fecharModal() {
        document.getElementById('modalUpload').style.display = 'none';
    }

    // Fecha o modal ao clicar fora dele
    window.onclick = function(event) {
        const modal = document.getElementById('modalUpload');
        if (event.target == modal) {
            modal.style.display = "none";
        }
    }
</script>

</html>