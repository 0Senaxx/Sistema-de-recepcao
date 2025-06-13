<?php
require_once '../01-Login/Auth/autenticacao.php';
require_once '../conexao.php'; // ajuste para seu arquivo de conexão

// Upload de novo documento
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_FILES['documento'])) {
    $nome = $_FILES['documento']['name'];
    $descricao = $_POST['descricao'] ?? '';
    $caminho = '../uploads/' . basename($nome);

    if (move_uploaded_file($_FILES['documento']['tmp_name'], $caminho)) {
        $stmt = $conn->prepare("INSERT INTO documentos (nome_arquivo, descricao, caminho, data_envio) VALUES (?, ?, ?, NOW())");
        $stmt->bind_param("sss", $nome, $descricao, $caminho);
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
    <title>Gerenciar Documentos</title>
</head>
<body>
    <h1>Gerenciar Documentos</h1>

    <!-- Mensagens de sucesso ou erro -->
    <?php if (isset($_GET['sucesso'])): ?>
        <p style="color: green;">Documento enviado com sucesso!</p>
    <?php elseif (isset($_GET['erro'])): ?>
        <p style="color: red;">Erro ao enviar o documento.</p>
    <?php elseif (isset($_GET['excluido'])): ?>
        <p style="color: green;">Documento excluído com sucesso!</p>
    <?php endif; ?>

    <h2>Enviar Novo Documento</h2>
    <form method="post" enctype="multipart/form-data">
        <label>Arquivo:</label>
        <input type="file" name="documento" required><br>
        <label>Descrição:</label>
        <textarea name="descricao" rows="4" cols="50"></textarea><br>
        <button type="submit">Enviar</button>
    </form>

    <h2>Documentos Cadastrados</h2>
    <table border="1">
        <thead>
            <tr>
                <th>Nome</th>
                <th>Descrição</th>
                <th>Data de Envio</th>
                <th>Ações</th>
            </tr>
        </thead>
        <tbody>
            <?php while($row = $result->fetch_assoc()): ?>
                <tr>
                    <td><?php echo htmlspecialchars($row['nome_arquivo']); ?></td>
                    <td><?php echo htmlspecialchars($row['descricao']); ?></td>
                    <td><?php echo date('d/m/Y H:i', strtotime($row['data_envio'])); ?></td>
                    <td>
                        <a href="<?php echo htmlspecialchars($row['caminho']); ?>" download>Baixar</a> |
                        <a href="?excluir=<?php echo $row['id']; ?>" onclick="return confirm('Tem certeza que deseja excluir este documento?');">Excluir</a>
                    </td>
                </tr>
            <?php endwhile; ?>
        </tbody>
    </table>
</body>
</html>
