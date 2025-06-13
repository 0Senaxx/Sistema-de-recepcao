<?php
session_start();
include '../../conexao.php';

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

        // Primeiro acesso: senha temporária
        if ($usuario['senha_temporaria']) {
            header("Location: ../primeiro_acesso.php");
            exit;
        }

        // Verificar expiração de senha
        if (!empty($usuario['data_ultima_troca'])) {
            $dataTroca = new DateTime($usuario['data_ultima_troca']);
            $hoje = new DateTime();
            $intervalo = $dataTroca->diff($hoje);

            if ($intervalo->m >= 2 || $intervalo->y >= 1) {
                header("Location: ../expirar_senha.php");
                exit;
            }
        }

        // Login normal (sem senha expirada)
        switch ($usuario['perfil']) {
            case 'ADM':
                header("Location: ../../10-Administrador/index.php");
                break;
            case 'GEPES':
                header("Location: ../../08-Servidores/index.php");
                break;
            case 'GCP':
                header("Location: ../../09-Setores/index.php");
                break;
            case 'Recepcionista':
            default:
                header("Location: ../../02-Inicio/index.php");
                break;
        }
        exit;
    }
}

$_SESSION['erro'] = "Matrícula ou senha inválidos!";
header("Location: ../login.php");
exit;
