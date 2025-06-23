<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start(); 

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../01-Login/login.php");
  exit; 
}

include '../01-Login/Auth/autenticacao.php';
include '../01-Login/Auth/controle_sessao.php';
include '../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------

// Busca os documentos
$sql = "SELECT id, nome_arquivo, descricao, caminho, data_envio FROM documentos ORDER BY data_envio DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title>Documentos - Recepção</title>
  <link rel="stylesheet" href="../11-Repositorio/estilo.css" />
</head>

<body>
  <header class="cabecalho">
    <h1>Recepção SEAD</h1>
    <nav>
      <a class="nav" href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
      <a class="nav" href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">Nova Visita</a>
      <a class="nav" href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
      <a class="nav" href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
      <a class="nav" href="../12-Ocorrencias/registro_ocorrencia.php" onclick="fadeOut(event, this)">Ocorrências</a>
      <a class="nav" href="../01-Login/Auth/logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <section class="card">
      <div class="topo">
        <h2>📁 Repositório de Documentos</h2>
        <div>
          <label class="txt-label" for="filtro">Buscar:</label>
          <input class="campo-buscar" type="text" id="filtro" placeholder="🔍 Pesquisar documentos..." autocomplete="off" />
        </div>
      </div>

      <section class="document-list">
        <?php if ($result->num_rows > 0): ?>
          <?php while ($row = $result->fetch_assoc()):
            // Use htmlspecialchars para segurança e evitar XSS
            $nomeArquivo = htmlspecialchars($row['nome_arquivo']);
            $descricao = htmlspecialchars($row['descricao']);
            $dataEnvio = date('d/m/Y', strtotime($row['data_envio']));
            $caminho = htmlspecialchars($row['caminho']);
          ?>

            <div class="document-card">
              <div class="left-section">
                <h2> <svg viewBox="0 0 24 24" style="height: 14px" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <g id="SVGRepo_bgCarrier" stroke-width="0"></g>
                    <g id="SVGRepo_tracerCarrier" stroke-linecap="round" stroke-linejoin="round"></g>
                    <g id="SVGRepo_iconCarrier">
                      <path fill-rule="evenodd" clip-rule="evenodd" d="M9.29289 1.29289C9.48043 1.10536 9.73478 1 10 1H18C19.6569 1 21 2.34315 21 4V20C21 21.6569 19.6569 23 18 23H6C4.34315 23 3 21.6569 3 20V8C3 7.73478 3.10536 7.48043 3.29289 7.29289L9.29289 1.29289ZM18 3H11V8C11 8.55228 10.5523 9 10 9H5V20C5 20.5523 5.44772 21 6 21H18C18.5523 21 19 20.5523 19 20V4C19 3.44772 18.5523 3 18 3ZM6.41421 7H9V4.41421L6.41421 7ZM7 13C7 12.4477 7.44772 12 8 12H16C16.5523 12 17 12.4477 17 13C17 13.5523 16.5523 14 16 14H8C7.44772 14 7 13.5523 7 13ZM7 17C7 16.4477 7.44772 16 8 16H16C16.5523 16 17 16.4477 17 17C17 17.5523 16.5523 18 16 18H8C7.44772 18 7 17.5523 7 17Z" fill="#2c7be5"></path>
                    </g>
                  </svg> <?= $nomeArquivo ?></h2>
                <p><strong> Descrição:</strong> <?= $descricao ?></p>
              </div>
              <div class="right-section">
                <p class="data-envio"><strong>📅 Data de envio:</strong> <?= $dataEnvio ?></p>
                <div class="buttons">
                  <a href="download.php?id=<?= $row['id'] ?>" class="download-btn">⬇️ Baixar arquivo</a>

                </div>
              </div>
            </div>

          <?php endwhile; ?>
        <?php else: ?>
          <p style="text-align:center;">Nenhum documento disponível.</p>
        <?php endif; ?>
      </section>
    </section>
  </main>

  <footer class="rodape">
    2025 SEAD | Todos os direitos reservados
  </footer>

  <script>
    // Filtro de busca simples para os cards de documento
    const filtroInput = document.getElementById('filtro');
    filtroInput.addEventListener('input', function() {
      const termo = filtroInput.value.toLowerCase();
      document.querySelectorAll('.document-card').forEach(card => {
        const texto = card.innerText.toLowerCase();
        card.style.display = texto.includes(termo) ? '' : 'none';
      });
    });
  </script>
</body>

</html>