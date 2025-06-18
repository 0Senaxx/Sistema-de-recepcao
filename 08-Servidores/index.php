<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
  // Se não estiver logado, redireciona para a página de login
  header("Location: ../01-Login/login.php");
  exit;
}

include '../01-Login/Auth/autenticacao.php';
include '../01-Login/Auth/controle_sessao.php';
include '../conexao.php';

// Filtro por setor (opcional)
$setor_filter = $_GET['setor'] ?? '';

// Busca (opcional)
$busca = $_GET['busca'] ?? '';

// Consulta SQL com filtro e busca
$sql = "SELECT s.*, se.nome AS setor_nome FROM servidores s LEFT JOIN setores se ON s.setor_id = se.id WHERE 1=1";

if ($setor_filter !== '') {
  $sql .= " AND s.setor_id = " . intval($setor_filter);
}

if ($busca !== '') {
  $busca_esc = $conn->real_escape_string($busca);
  $sql .= " AND (s.nome LIKE '%$busca_esc%' OR s.matricula LIKE '%$busca_esc%')";
}

$sql .= " ORDER BY s.nome ASC";

$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
  <meta charset="UTF-8" />
  <link rel="stylesheet" href="index.css">
  <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/choices.js/public/assets/styles/choices.min.css" />
  <script src="https://cdn.jsdelivr.net/npm/choices.js/public/assets/scripts/choices.min.js"></script>
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/jquery.mask/1.14.16/jquery.mask.min.js"></script>
  <title>Lista de Servidores</title>
</head>

<body>
  <header class="cabecalho">
    <h1>Recepção SEAD</h1>
    <nav>
      <a href="../01-Login/Auth/logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <section class="Modulo">
      <h1>Módulo de Gestão de Pessoal - SEAD</h1>
    </section>

    <section class="card">

      <div class="cabecalho-visitas">
        <form method="GET" class="filtro-form">

          <!-- CAMPO DE BUSCA POR matricula OU NOME -->
          <input class="campo-buscar" type="text" name="busca" placeholder="Buscar por nome ou matricula"
            value="<?php echo htmlspecialchars($busca); ?>" />

          <!-- FILTRO POR SETOR -->
          <!-- filtro -->
          <select name="setor" id="setor_filtro" class="choices-select" placeholder="tets">
            <option value="">Todos os setores</option>
            <?php
            $sqlSetores = "SELECT id, nome FROM setores ORDER BY nome";
            $resSetores = $conn->query($sqlSetores);
            while ($setor = $resSetores->fetch_assoc()) {
              $selected = ($setor_filter == $setor['id']) ? "selected" : "";
              echo "<option value='{$setor['id']}' $selected>" . htmlspecialchars($setor['nome']) . "</option>";
            }
            ?>
          </select>

          <button class="bnt-Filtro" type="submit">Filtrar</button>
          <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="bnt-NoFiltro">Limpar filtro</a>
        </form>

        <button id="btnAbrirModal" class="bnt-nova">Adicionar Servidor</button>
      </div>
      </div>
      <table>
        <thead>
          <tr>
            <th>Nome</th>
            <th class="text-center">Matrícula</th>
            <th class="text-center">Status</th>
            <th>Setor</th>
            <th class="text-center">Última Atualização</th>
            <th class="text-center">Ações</th>
          </tr>
        </thead>
        <tbody id="tabela-corpo">
          <?php if ($result && $result->num_rows > 0): ?>
            <?php while ($row = $result->fetch_assoc()): ?>
              <tr>
                <td>
                  <?php echo htmlspecialchars($row['nome']); ?>
                </td>
                <td class="text-center">
                  <?php echo htmlspecialchars($row['matricula']); ?>
                </td>
                <td class="text-center">
                  <?php echo htmlspecialchars($row['status']); ?>
                </td>
                <td>
                  <?php echo htmlspecialchars($row['setor_nome'] ?? ''); ?>
                </td>
                <td class="text-center">
                  <?php
                  if (!empty($row['updated_at']) && !empty($row['updated_by'])) {
                    echo date('d/m/Y', strtotime($row['updated_at']));
                  } else {
                    echo '---';
                  }
                  ?>
                </td>

                <td class="text-center">
                  <a href="#" class="btnEditar" onclick="editarServidor(<?php echo $row['id']; ?>); return false;">Editar</a> |
                  <a href="excluir.php?id=<?php echo $row['id']; ?>" class="btnExcluir" onclick="return confirm('Confirma exclusão do servidor?');">Excluir</a>
                </td>
              </tr>
            <?php endwhile; ?>
          <?php else: ?>
            <tr>
              <td colspan="6">Nenhum servidor encontrado.</td>
            </tr>
          <?php endif; ?>
        </tbody>
      </table>
    </section>
  </main>

  <!-- Modal (inicialmente escondido) -->
  <div id="modalServidor" class="modal">
    <div class="modal-conteudo">
      <h2 id="modalTitulo">Adicionar Servidor</h2><br>

      <form id="formServidor">

        <input type="hidden" name="id" id="servidor_id" value="0" />

        <div class="form-campo">
          <label>Nome:</label>
          <input type="text" name="nome" id="nome" required autocomplete="off" />
        </div>

        <div class="form-campo">
          <label>Matrícula:</label>
          <input type="text" name="matricula" id="matricula" maxlength="10" required autocomplete="off" />
        </div>

        <div class="form-lista">
          <label>Status:</label>
          <select name="status" id="status">
            <option value="Disponível" selected>Disponível</option>
            <option value="Indisponível">Indisponível</option>
          </select>
        </div>

        <div class="form-lista">
          <label>Setor:</label>
          <select name="setor_id" id="setor_modal" required>
            <option value="">--Selecione o setor--</option>
            <?php
            $sqlSetores = "SELECT id, nome FROM setores ORDER BY nome";
            $resSetores = $conn->query($sqlSetores);
            while ($setor = $resSetores->fetch_assoc()):
            ?>
              <option value="<?php echo $setor['id']; ?>"><?php echo htmlspecialchars($setor['nome']); ?></option>
            <?php endwhile; ?>
          </select>
        </div>

        <div class="modal-botoes">
          <button type="submit" class="btnSalvar">Salvar</button>
          <button type="button" class="btnCancelar" id="btnFecharModal">Cancelar</button>
        </div>
      </form>
    </div>
  </div>

  <footer class="rodape">
    2025 SEAD | Todos os direitos reservados
  </footer>
  <script src="script.js"></script>
  </script>

</body>


</html>