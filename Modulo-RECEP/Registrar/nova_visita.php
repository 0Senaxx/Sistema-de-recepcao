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

// Consulta para obter os setores
$sql = "SELECT id, nome FROM setores ORDER BY nome ASC";
$result = $conn->query($sql);

// Consulta para obter os servidores
$sqlServidores = "SELECT id, nome, status FROM servidores ORDER BY nome ASC";
$resultServidores = $conn->query($sqlServidores);

// Consulta para obter crachás disponíveis
$sqlCrachas = "SELECT id, codigo FROM crachas WHERE status = 'disponivel' ORDER BY codigo ASC";
$resultCrachas = $conn->query($sqlCrachas);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <link rel="icon" href="http://servicos.sead.am.gov.br/wp-content/uploads/2020/06/cropped-favicon-192x192.png"
    sizes="192x192">
  <link rel="stylesheet" href="style.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <title>Recepção SEAD</title>
</head>

<body class="container py-4">

  <header class="cabecalho">
    <h1>Recepção SEAD</h1>
    <nav>
      <a class="nav" href="../Inicio/index.php">Início</a>
      <a class="nav" href="../Registrar/nova_visita.php">Nova Visita</a>
      <a class="nav" href="../Ramais/ramais.php">Ramais SEAD</a>
      <a class="nav" href="../Repositorio/repositorio.php">Repositório</a>
      <a class="nav" href="../Ocorrencias/registro_ocorrencia.php">Ocorrências</a>
      <a class="nav" href="../../Firewall/Auth/logout.php">Sair</a>
    </nav>
  </header>


  <main>
    <section class="card">
      <h2>Registrar visita</h2>
      <form class="formulario" action="salvar_visita.php" method="POST" enctype="multipart/form-data">

        <!-- Campos principais -->
        <div class="form-esquerdo">

          <!-- 1ª linha: Tipo doc • Documento • Data -->
          <div class="primeira-linha">

            <div class="campo-documento">
              <label for="cpf">Documento (CPF) <span class="required">*</span></label>
              <div class="input-with-icon">
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" autocomplete="off" required onblur="validarCPF()">
                <button type="button" id="btnBuscarCPF" title="Pesquisar">🔍</button>
              </div>
            </div>

            <div class="campo-data">
              <label for="data-entrada">Data de entrada <span class="required">*</span></label>
              <input type="date" id="data-entrada" name="data" required readonly tabindex="-1">
            </div>

            <div class="campo-hora">
              <label for="hora-entrada">Hora de entrada <span class="required">*</span></label>
              <input type="time" id="hora-entrada" name="hora" required readonly tabindex="-1">
            </div>
          </div>

          <!-- 2ª linha: Nome -->
          <div class="segunda-linha">
            <div class="form-control nome">
              <label for="nome">Nome completo <span class="required">*</span></label>
              <input type="text" id="nome" name="nome" placeholder="Nome do visitante" autocomplete="off" required>
            </div>
            <div class="form-control contato">
              <label for="social">Nome social (opcional)</label>
              <input type="text" id="social" name="social" placeholder="Informe o nome social, se desejar" autocomplete="off">
            </div>
          </div>

          <!-- 3ª linha: Órgão • Setor -->
          <div class="terceira-linha">
            <div class="form-control">
              <label for="orgao">Órgão/Entidade</label>
              <input list="orgaos" id="orgao" name="orgao" autocomplete="off" placeholder="Selecione um órgão...">
              <datalist id="orgaos">
                <option value="SECRETARIA DE ESTADO DA CASA CIVIL">

              </datalist>

            </div>

            <div class="form-control">
              <label for="setor_id">Setor a ser visitado <span class="required">*</span></label>
              <select id="setor_id" name="setor_id" class="choices-select" required>
                <option value="">Selecione um setor</option>
                <?php while ($row = $result->fetch_assoc()): ?>
                  <option value="<?= $row['id'] ?>"><?= htmlspecialchars($row['nome']) ?></option>
                <?php endwhile; ?>
              </select>
            </div>

          </div>

          <!-- 4ª linha: Servidor e Status -->
          <div class="quarta-linha">
            <div class="campo-servidor">
              <label for="servidorInput">Servidor a ser visitado<span class="required">*</span></label>
              <select id="servidorInput" name="servidor" class="choices-select" required>
                <option value="">Selecione um servidor...</option>
                <?php while ($row = $resultServidores->fetch_assoc()): ?>
                  <option value="<?= htmlspecialchars($row['id']) ?>" data-status="<?= htmlspecialchars($row['status']) ?>">
                    <?= htmlspecialchars($row['nome']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>

            <div class="campo-status">
              <label>Status do Servidor</label>
              <div class="servidorStatus">
                <span id="servidorStatus">--</span>
              </div>
            </div>

            <!-- Crachá para visita -->
            <div class="campo-cracha">
              <label for="cracha_id">Crachá para visita <span class="required">*</span></label>
              <select id="cracha_id" name="cracha_id" class="choices-select" required>
                <option value="">Selecione o crachá</option>
                <?php while ($cracha = $resultCrachas->fetch_assoc()): ?>
                  <option value="<?= $cracha['id'] ?>">
                    Crachá <?= htmlentities($cracha['codigo']) ?>
                  </option>
                <?php endwhile; ?>
              </select>
            </div>
          </div>


          <!-- Botões de ação -->
          <div class="form-actions">
            <button type="submit" class="btn-acao  btn salvar" id="btnSalvar" disabled>
              <img src="../../Imagens/Icons/salve.png" alt="Editar">
              <span>Salvar</span>
            </button>

            <button type="button" id="btnLimpar" class="btn-acao btn Limpar">
              <img src="../../Imagens/Icons/apagador.png" alt="Editar">
              <span>Limpar</span>
            </button>

            <a class="btn-acao  btn cancelar" href="../Inicio/index.php">
              <img src="../../Imagens/Icons/cancelar.png" alt="Editar">
              Cancelar
            </a>
          </div>
        </div>

        <!-- Seção de foto -->
        <div class="form-direito">
          <h4>Registro fotográfico <span class="required">*</span></h4>

          <div class="foto-box" id="foto-box">
            <span id="foto-texto">Foto do visitante</span>
            <video id="video" autoplay playsinline
              style="display: none; width: 100%; height: 100%; object-fit: cover;"></video>
            <canvas id="canvas" style="display: none;"></canvas>
          </div>

          <div class="form-actions">
            <button type="button" class="btn-acao   btn foto" id="btn-foto" onclick="alternarCameraOuCaptura()">
              <img src="../../Imagens/Icons/camera.png" alt="Editar">
              Registrar
            </button>
          </div>
        </div>
        <input type="hidden" name="foto_base64" id="foto_base64">

      </form>
    </section>
  </main>
  <div id="popupToast"></div>


  <footer class="rodape">
    2025 SEAD | Todos os direitos reservados
  </footer>

  <script src="script.js"></script>
</body>

</html>