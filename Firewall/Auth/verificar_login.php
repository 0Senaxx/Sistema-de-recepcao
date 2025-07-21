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

    if ($usuario['bloqueado']) {
        $_SESSION['erro'] = "<strong>Usuário bloqueado!</strong><br>Entre em contato com o DETI via e-mail <strong>deti@sead.am.gov.br</strong> ou pelo ramal <strong>3182-2801</strong>.";
        header("Location: ../login.php");
        exit;
    }

    if (!$usuario['ativo']) {
        $_SESSION['erro'] = "Usuário inativo. Procure o administrador do sistema.";
        header("Location: ../login.php");
        exit;
    }

    if (password_verify($senha, $usuario['senha'])) {
        // Zera tentativas após sucesso
        $sqlZera = "UPDATE usuarios SET tentativas_erradas = 0 WHERE id = ?";
        $stmtZera = $conn->prepare($sqlZera);
        $stmtZera->bind_param("i", $usuario['id']);
        $stmtZera->execute();

        $_SESSION['usuario_id'] = $usuario['id'];
        $_SESSION['nome'] = $usuario['nome'];
        $_SESSION['perfil'] = $usuario['perfil'];

        // Atualiza último login
        $sqlUpdate = "UPDATE usuarios SET ultimo_login = NOW() WHERE id = ?";
        $stmtUpdate = $conn->prepare($sqlUpdate);
        $stmtUpdate->bind_param("i", $usuario['id']);
        $stmtUpdate->execute();

        // Primeiro acesso
        if ($usuario['senha_temporaria']) {
            header("Location: ../primeiro_acesso.php");
            exit;
        }

        // Verifica expiração de senha
        if (!empty($usuario['data_ultima_troca'])) {
            $dataTroca = new DateTime($usuario['data_ultima_troca']);
            $hoje = new DateTime();
            $intervalo = $dataTroca->diff($hoje);

            if ($intervalo->m >= 2 || $intervalo->y >= 1) {
                header("Location: ../expirar_senha.php");
                exit;
            }
        }

        // Redirecionamento por perfil
        switch ($usuario['perfil']) {
            case 'ADM':
                header("Location: ../../Modulo-ADM/index.php");
                break;
            case 'GEPES':
                header("Location: ../../Modulo-GEPES/index.php");
                break;
            case 'GCP':
                header("Location: ../../Modulo-GCP/index.php");
                break;
            case 'Recepcionista':
            default:
                header("Location: ../../Modulo-RECEP/Inicio/index.php");
                break;
        }
        exit;
    } else {
        // Incrementa tentativas
        $tentativas = $usuario['tentativas_erradas'] + 1;
        $bloquear = $tentativas >= 5 ? 1 : 0;

        $sqlErro = "UPDATE usuarios SET tentativas_erradas = ?, bloqueado = ? WHERE id = ?";
        $stmtErro = $conn->prepare($sqlErro);
        $stmtErro->bind_param("iii", $tentativas, $bloquear, $usuario['id']);
        $stmtErro->execute();

        if ($bloquear) {
            $_SESSION['erro'] = "Usuário bloqueado após 5 tentativas. Contate o administrador.";
        } else {
            $_SESSION['erro'] = "Senha incorreta. Tentativas restantes: " . (5 - $tentativas);
        }

        header("Location: ../login.php");
        exit;
    }
} else {
    $_SESSION['erro'] = "Matrícula ou senha inválidos!";
    header("Location: ../login.php");
    exit;
}
