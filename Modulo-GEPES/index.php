<?php

// ------[ ÁREA DE PARAMETROS DE SEGURANÇA ]------
session_start();

if (!isset($_SESSION['usuario_id'])) {
  header("Location: ../Firewall/login.php");
  exit;
}

include '../Firewall/Auth/autenticacao.php';
include '../Firewall/Auth/controle_sessao.php';
include '../conexao.php';

// ------[ FIM DA ÁREA DE PARAMETROS DE SEGURANÇA ]------


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
      <a href="../Firewall/Auth/logout.php">Sair</a>
    </nav>
  </header>

  <main>
    <section class="Modulo">
      <h1>Módulo de Gestão de Servidores</h1>
    </section>

    <section class="card">

      <div class="cabecalho-visitas">
        <form method="GET" class="filtro-form">

          <!-- CAMPO DE BUSCA POR matricula OU NOME -->
          <input class="campo-buscar" type="text" autocomplete="off" name="busca" placeholder="Buscar por nome ou matricula"
            value="<?php echo htmlspecialchars($busca); ?>" />

          <!-- FILTRO POR SETOR -->
          <select name="setor" id="setor_filtro" aria-label="Filtrar por setor" class="form-select">
            <option value="" disabled <?php echo (empty($setor_filter)) ? "selected" : ""; ?>> Buscar por setor </option>
            <option value="">Todos os setores</option>

            <?php
            $sqlSetores = "SELECT id, nome FROM setores ORDER BY nome";
            $resSetores = $conn->query($sqlSetores);
            while ($setor = $resSetores->fetch_assoc()) {
              $selected = ($setor_filter == $setor['id']) ? "selected" : "";
              echo "<option value='" . htmlspecialchars($setor['id']) . "' $selected>" . htmlspecialchars($setor['nome']) . "</option>";
            }
            ?>
          </select>

          <button class="btn-acao bnt-Filtro" type="submit">
            <img src="../Imagens/Icons/filtro.png" alt="Filtrar">
            Filtrar
          </button>
          <a href="<?php echo strtok($_SERVER["REQUEST_URI"], '?'); ?>" class="btn-acao bnt-NoFiltro">
            <img src="../Imagens/Icons/apagador.png" alt="Limpar">
            Limpar filtro
          </a>
        </form>

        <button id="btnAbrirModal" class="btn-acao bnt-nova">
          <img src="../Imagens/Icons/adicionar-usuario.png" alt="Limpar">
          Novo Servidor
        </button>
      </div>

      </div>
      <div class="table-container">
        <table>
          <thead>
            <tr>
              <th class="col-servidor">Nome</th>
              <th class="text-center">Matrícula</th>
              <th class="text-center">Status</th>
              <th class="col-setor">Setor</th>
              <th class="text-center col-atualizacao">Atualização</th>
              <th class="text-center">Ações</th>
            </tr>
          </thead>
          <tbody id="tabela-corpo">
            <?php if ($result && $result->num_rows > 0): ?>
              <?php while ($row = $result->fetch_assoc()): ?>
                <tr>
                  <td class="col-servidor">
                    <?php echo htmlspecialchars($row['nome']); ?>
                  </td>
                  <td class="text-center">
                    <?php echo htmlspecialchars($row['matricula']); ?>
                  </td>
                  <td class="text-center">
                    <?php echo htmlspecialchars($row['status']); ?>
                  </td>
                  <td class="col-setor">
                    <?php echo htmlspecialchars($row['setor_nome'] ?? ''); ?>
                  </td>
                  <td class="text-center col-atualizacao">
                    <?php
                    if (!empty($row['updated_at']) && !empty($row['updated_by'])) {
                      echo date('d/m/Y', strtotime($row['updated_at']));
                    } else {
                      echo '---';
                    }
                    ?>
                  </td>

                  <td class="text-center">
                    <a href="#" class="btn-acao btnEditar" onclick="editarServidor(<?php echo $row['id']; ?>); return false;">
                      <img src="../Imagens/Icons/editar.png" alt="Editar">
                      Editar
                    </a>
                    |
                    <a href="excluir.php?id=<?php echo $row['id']; ?>" class="btn-acao btnExcluir" onclick="return confirm('Confirma exclusão do servidor?');">
                      <img src="../Imagens/Icons/excluir.png" alt="Excluir">
                      Excluir
                    </a>

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
      </div>
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
          <input type="text" name="nome" id="nome" required autocomplete="off" placeholder="Insira o nome completo" />
        </div>

        <div class="form-campo">
          <label>Matrícula:</label>
          <input type="text" name="matricula" id="matricula" maxlength="10" required autocomplete="off" placeholder="Insira a matrícula" />
        </div>

        <div class="form-lista">
          <label>Status:</label>
          <select name="status" id="status" required>  
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
          <button type="submit" class="btn-acao  btnSalvar">
            <img src="../Imagens/Icons/salve.png" alt="Salvar">
            Salvar
          </button>

          <button type="button" class="btn-acao btnCancelar" id="btnFecharModal">
            <img src="../Imagens/Icons/cancelar.png" alt="Salvar">
            Cancelar
          </button>
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