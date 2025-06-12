<?php
session_start();
include '../conexao.php';

$matricula = $_POST['matricula'];
$senha = $_POST['senha'];

$sql = "SELECT * FROM usuarios WHERE matricula = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $matricula);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 1) {
    $usuario = $result->fetch_assoc();

    if (password_verify($senha, $usuario['senha'])) {
        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];

        // Redireciona de acordo com o perfil
        switch ($usuario['perfil']) {
            case 'ADM':
                header("Location: ../10-Administrador/index.php");
                break;
            case 'GEPES':
                header("Location: ../08-Servidores/index.php");
                break;
            case 'GCP':
                header("Location: ../09-Setores/index.php");
                break;
            case 'Recepcionista':
            default:
                header("Location: ../02-Inicio/index.php");
                break;
        }
        exit;
    }
}

$_SESSION['erro'] = "CPF ou senha inválidos!";
header("Location: login.php");
exit;
