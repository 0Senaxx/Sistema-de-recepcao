<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

include '../../conexao.php';

if (!isset($_GET['id'])) {
    header("Location: index.php");
    exit;
}

$id = $_GET['id'];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $nome = $_POST['nome'];
    $matricula = $_POST['matricula'];
    $perfil = $_POST['perfil'];

    if (!empty($_POST['senha'])) {
        $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, matricula=?, senha=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $matricula, $senha, $perfil, $id);
    } else {
        $sql = "UPDATE usuarios SET nome=?, matricula=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssi", $nome, $matricula, $perfil, $id);
    }

    if ($stmt->execute()) {
        header("Location: ../index.php");
        exit;
    } else {
        echo "Erro ao atualizar usuário: " . $conn->error;
    }
} else {
    // Buscar dados do usuário
    $sql = "SELECT * FROM usuarios WHERE id=?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $id);
    $stmt->execute();
    $result = $stmt->get_result();
    $usuario = $result->fetch_assoc();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Editar Usuário</title>
</head>
<body>
    <h1>Editar Usuário</h1>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" value="<?= htmlspecialchars($usuario['nome']) ?>" required><br><br>

        <label>Matricula:</label><br>
        <input type="text" name="matricula" value="<?= htmlspecialchars($usuario['matricula']) ?>" required><br><br>

        <label>Senha (deixe em branco para não alterar):</label><br>
        <input type="password" name="senha"><br><br>

        <label>Perfil:</label><br>
        <select name="perfil" required>
            <option value="ADM" <?= $usuario['perfil'] == 'ADM' ? 'selected' : '' ?>>Administrador</option>
            <option value="Recepcionista" <?= $usuario['perfil'] == 'Recepcionista' ? 'selected' : '' ?>>Recepcionista</option>
            <option value="GEPES" <?= $usuario['perfil'] == 'GEPES' ? 'selected' : '' ?>>GEPES</option>
            <option value="GCP" <?= $usuario['perfil'] == 'GCP' ? 'selected' : '' ?>>GCP</option>
        </select><br><br>

        <button type="submit">Salvar Alterações</button>
    </form>
    <a href="../index.php">Voltar</a>
</body>
    <script>
        function aplicarMascaraCPF(input) {
            let value = input.value.replace(/\D/g, '');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d)/, '$1.$2');
            value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
            input.value = value;
        }

          $('#matricula').mask('000.000-0 A', {
            translation: {
                'A': { pattern: /[A-Za-z]/ }
                }
            });
    </script>
</html>
