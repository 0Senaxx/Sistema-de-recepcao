<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    header("Location: ../../login.php");
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    include '../../conexao.php';

    $nome = $_POST['nome'];
    $cpf = $_POST['cpf'];
    $matricula = $_POST['matricula'];
    $senha = password_hash($_POST['senha'], PASSWORD_DEFAULT);
    $perfil = $_POST['perfil'];

    $sql = "INSERT INTO usuarios (nome, cpf, matricula, senha, perfil) VALUES (?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("sssss", $nome, $cpf, $matricula, $senha, $perfil);

    if ($stmt->execute()) {
        header("Location: usuarios.php");
        exit;
    } else {
        echo "Erro ao adicionar usuário: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
    <title>Adicionar Usuário</title>
</head>
<body>
    <h1>Adicionar Novo Usuário</h1>
    <form method="POST">
        <label>Nome:</label><br>
        <input type="text" name="nome" required><br><br>

        <label>CPF:</label><br>
        <input type="text" name="cpf" maxlength="14" oninput="aplicarMascaraCPF(this)" required><br><br>

        <label>Matrícula:</label><br>
        <input type="text" id="matricula" name="matricula" required><br><br>

        <label>Senha:</label><br>
        <input type="password" name="senha" required><br><br>

        <label>Perfil:</label><br>
        <select name="perfil" required>
            <option value="ADM">Administrador</option>
            <option value="Recepcionista">Recepcionista</option>
            <option value="GEPES">GEPES</option>
            <option value="GCP">GCP</option>
        </select><br><br>

        <button type="submit">Salvar</button>
    </form>
    <a href="usuarios.php">Voltar</a>
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
