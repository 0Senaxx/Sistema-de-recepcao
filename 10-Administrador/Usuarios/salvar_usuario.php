<?php
session_start();
if (!isset($_SESSION['usuario_id']) || $_SESSION['perfil'] != 'ADM') {
    http_response_code(403);
    echo "Acesso negado.";
    exit;
}

include '../../conexao.php';

// Recebe dados do POST
$id = isset($_POST['id']) && $_POST['id'] !== '' ? intval($_POST['id']) : null;
$nome = trim($_POST['nome'] ?? '');
$cpf = trim($_POST['cpf'] ?? '');
$matricula = trim($_POST['matricula'] ?? '');
$perfil = $_POST['perfil'] ?? '';
$senha = $_POST['senha'] ?? '';

// Validações simples (pode melhorar)
if ($nome === '' || $matricula === '' || $perfil === '') {
    echo "Por favor, preencha todos os campos obrigatórios.";
    exit;
}

// Evitar SQL Injection usando prepared statements
if ($id) {
    // Atualizar usuário
    if ($senha !== '') {
        // Atualiza senha também
        $hashSenha = password_hash($senha, PASSWORD_DEFAULT);
        $sql = "UPDATE usuarios SET nome=?, cpf=?, matricula=?, perfil=?, senha=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("sssssi", $nome, $cpf, $matricula, $perfil, $hashSenha, $id);
    } else {
        // Atualiza sem senha
        $sql = "UPDATE usuarios SET nome=?, cpf=?, matricula=?, perfil=? WHERE id=?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssssi", $nome, $cpf, $matricula, $perfil, $id);
    }

    if ($stmt->execute()) {
        echo "Usuário atualizado com sucesso.";
    } else {
        echo "Erro ao atualizar usuário: " . $stmt->error;
    }
} else {
    // Inserir novo usuário, senha é obrigatória para novo cadastro
    if ($senha === '') {
        echo "A senha é obrigatória para novo usuário.";
        exit;
    }

    $hashSenha = password_hash($senha, PASSWORD_DEFAULT);

    $sql = "INSERT INTO usuarios (nome, matricula, perfil, senha, ativo) VALUES (?, ?, ?, ?, 1)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssss", $nome, $matricula, $perfil, $hashSenha);


    if ($stmt->execute()) {
        echo "Usuário adicionado com sucesso.";
    } else {
        echo "Erro ao adicionar usuário: " . $stmt->error;
    }
}
