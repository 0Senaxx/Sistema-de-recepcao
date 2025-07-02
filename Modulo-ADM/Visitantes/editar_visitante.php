<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start(); 

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../Firewall/login.php");
  exit; 
}

include '../../Firewall/Auth/autenticacao.php';
include '../../Firewall/Auth/controle_sessao.php';
include '../../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

if (!isset($_GET['id'])) {
    echo "ID do visitante não informado.";
    exit;
}

$id = $_GET['id'];

$sql = "SELECT * FROM visitantes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if (!$result || $result->num_rows == 0) {
    echo "Visitante não encontrado.";
    exit;
}

$visitante = $result->fetch_assoc();
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <title>Editar Visitante</title>
    <link rel="stylesheet" href="editar_visitante.css">
</head>

<body class="container py-4">

    <header class="cabecalho">
        <h1>Painel de Gestão</h1>
        <nav>
            <a class="nav" href="../index.php">Início</a>
            <a class="nav" href="../Usuarios/usuarios.php">Usuários</a>
            <a class="nav" href="#.php">Visitantes</a>
            <a class="nav" href="../Setores/index.php">Setores</a>
            <a class="nav" href="../Visitas/visitas.php">Visitas</a>
            <a class="nav" href="../Documentos/documentos.php">Repositório</a>
            <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
        </nav>
    </header>

    <main>
        <section class="card">
            <h2>Editar Visitante</h2>
            <form action="atualizar_visitante.php" method="POST" enctype="multipart/form-data">
                <input type="hidden" name="id" value="<?= $visitante['id'] ?>">

                <div class="form-container">

                    <div class="info">
                        <div class="campo">
                            <label>CPF:</label>
                            <input type="text" name="cpf" value="<?= $visitante['cpf'] ?>" required readonly tabindex="-1">
                        </div>

                        <div class="campo">
                            <label>Nome:</label>
                            <input type="text" name="nome" value="<?= $visitante['nome'] ?>" required>
                        </div>

                        <div class="campo">
                            <label>Nome Social (opcional):</label>
                            <input type="text" name="social" value="<?= $visitante['social'] ?? '' ?>">
                        </div>


                        <div class="campo">
                            <label>Órgão/Entidade:</label>
                            <input type="text" name="orgao" value="<?= $visitante['orgao'] ?>">
                        </div>

                        <div class="form-actions">
                            <button type="submit">Atualizar</button>
                            <a class="bnt-voltar" href="visitantes.php">Voltar</a>
                        </div>
                    </div>

                    <div class="foto">
                        <label>Foto Atual:</label>
                        <?php if ($visitante['foto']): ?>
                            <img id="fotoCapturada" src="<?= $visitante['foto'] ?>" style="border: 2px solid green;">
                        <?php else: ?>

                            <img id="fotoCapturada" src="" style="display:none; border: 2px solid green;">
                            <p id="semFoto">Sem foto</p>
                        <?php endif; ?>

                        <div class="camera-area">
                            <video id="camera" autoplay playsinline style="display: none;"></video>
                            <canvas id="canvas" style="display: none;"></canvas>
                        </div>

                        <input type="hidden" name="foto_base64" id="foto_base64">

                        <div class="foto-actions">
                            <button type="button" id="btnAbrirCamera">📷 Abrir câmera</button>
                            <button type="button" id="btnCapturarFoto" style="display:none;">📸 Capturar foto</button>
                            <button type="button" id="btnRepetirFoto" style="display:none;">🔄 Repetir foto</button>
                        </div>
                    </div>
                </div>
            </form>

        </section>
    </main>

    <footer class="rodape">
        2025 SEAD | Todos os direitos reservados
    </footer>

    <script src="editar_visitante.js"></script>
</body>

</html>