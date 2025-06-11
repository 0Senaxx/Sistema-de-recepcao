<?php

session_start();

// Verifica se o usuário está logado, ou seja, se a sessão 'usuario_id' existe
if (!isset($_SESSION['usuario_id'])) {
    // Se não estiver logado, redireciona para a página de login
    header("Location: ../01-Login/login.php");
    exit;
}

include '../01-Login/autenticacao.php';

include '../conexao.php';


// Consulta para obter os setores
$sql = "SELECT id, nome FROM setores ORDER BY nome ASC";
$result = $conn->query($sql);

// Consulta para obter os servidores
$sqlServidores = "SELECT id, nome, status FROM servidores ORDER BY nome ASC";
$resultServidores = $conn->query($sqlServidores);

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
            <a href="../02-Inicio/index.php" onclick="fadeOut(event, this)">Início</a>
            <a href="../03-Registrar/nova_visita.php" onclick="fadeOut(event, this)">+ Nova Visita</a>
            <a href="../06-Ramais/ramais.php" onclick="fadeOut(event, this)">Ramais SEAD</a>
            <a href="../11-Repositorio/repositorio.php" onclick="fadeOut(event, this)">Repositório</a>
            <a href="../01-Login/logout.php">Sair</a>
        </nav>
  </header>


  <main>
    <section class="card">
      <h2>Registro de visita</h2>
      <form class="formulario" action="salvar_visita.php" method="POST" enctype="multipart/form-data">

        <!-- Campos principais -->
        <div class="form-esquerdo">

          <!-- 1ª linha: Tipo doc • Documento • Data -->
          <div class="primeira-linha">
            
            <div class="campo-documento">
              <label for="cpf">Documento (CPF) <span class="required">*</span></label>
              <div class="input-with-icon">
                <input type="text" id="cpf" name="cpf" placeholder="000.000.000-00" autocomplete="off" required>
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
                  <option value="SECRETARIA DE ESTADO DA CASA MILITAR">
                  <option value="SECRETARIA DE GOVERNO – SEGOV">
                  <option value="PROCURADORIA GERAL DO ESTADO – PGE">
                  <option value="CONTROLADORIA GERAL DO ESTADO – CGE">
                  <option value="SECRETARIA DE ESTADO DE COMUNICAÇÃO SOCIAL – SECOM">
                  <option value="SECRETARIA DE ESTADO DE RELAÇÕES FEDERATIVAS E INTERNACIONAIS – SERFI">
                  <option value="ESCRITÓRIO DE REPRESENTAÇÃO DO GOVERNO, EM SÃO PAULO – ERGSP">
                  <option value="UNIDADE DE GESTÃO INTEGRADA – UGI">
                  <option value="SECRETARIA DE ESTADO DA FAZENDA – SEFAZ">
                  <option value="SECRETARIA DE ESTADO DE DESENVOLVIMENTO ECONÔMICO, CIÊNCIA, TECNOLOGIA E INOVAÇÃO – SEDECTI">
                  <option value="SECRETARIA DE ESTADO DE ADMINISTRAÇÃO E GESTÃO – SEAD">
                  <option value="SECRETARIA DE ESTADO DE JUSTIÇA, DIREITOS HUMANOS E CIDADANIA – SEJUSC">
                  <option value="SECRETARIA DE ESTADO DE SAÚDE – SES/AM">
                  <option value="SECRETARIA DE ESTADO DE EDUCAÇÃO E DESPORTO ESCOLAR – SEDUC">
                  <option value="SECRETARIA DE ESTADO DE INFRAESTRUTURA – SEINFRA">
                  <option value="SECRETARIA DE ESTADO DE ENERGIA, MINERAÇÃO E GÁS – SEMIG">
                  <option value="SECRETARIA DE ESTADO DE DESENVOLVIMENTO URBANO E METROPOLITANO – SEDURB">
                  <option value="SECRETARIA DE ESTADO DO DESPORTO E LAZER – SEDEL">
                  <option value="SECRETARIA DE ESTADO DE SEGURANÇA PÚBLICA – SSP">
                  <option value="SECRETARIA DE ESTADO DA ASSISTÊNCIA SOCIAL E COMBATE À FOME – SEAS">
                  <option value="SECRETARIA DE ESTADO DE CULTURA E ECONOMIA CRIATIVA – SEC">
                  <option value="SECRETARIA DE ESTADO DO MEIO AMBIENTE – SEMA">
                  <option value="SECRETARIA DE ESTADO DAS CIDADES E TERRITÓRIOS – SECT">
                  <option value="SECRETARIA DE ESTADO DOS DIREITOS DA PESSOA COM DEFICIÊNCIA- SEPCD">
                  <option value="SECRETARIA DE ESTADO DE PESCA E AQUICULTURA- SEPA">
                  <option value="SECRETARIA DE ESTADO DE PROTEÇÃO ANIMAL – SEPET">
                  <option value="SECRETARIA DE ESTADO DE PRODUÇÃO RURAL – SEPROR">
                  <option value="SECRETARIA DE ESTADO DE ADMINISTRAÇÃO PENITENCIÁRIA – SEAP">
                  <option value="CONSELHO DE DESENVOLVIMENTO DO ESTADO DO AMAZONAS – CODAM">
                  <option value="CENTRO DE SERVIÇOS COMPARTILHADOS – CSC">
                  <option value="POLÍCIA CIVIL DO ESTADO – PC">
                  <option value="POLÍCIA MILITAR DO AMAZONAS – PMAM">
                  <option value="CORPO DE BOMBEIROS MILITAR DO ESTADO DO AMAZONAS – CBMAM">
                  <option value="SECRETARIA EXECUTIVA DO FUNDO DE PROMOÇÃO SOCIAL E ERRADICAÇÃO DA POBREZA – FPS">
                  <option value="DEFESA CIVIL DO AMAZONAS">
                  <option value="UNIDADE GESTORA DE PROJETOS ESPECIAIS – UGPE (Vinculada à Secretaria de Estado de Desenvolvimento Urbano e Metropolitano)">
                  <option value="UNIDADE DE GERENCIAMENTO DO PROGRAMA DE ACELERAÇÃO DO DESENVOLVIMENTO DA EDUCAÇÃO DO AMAZONAS (UGP-PADEAM)">
                  <option value="IMPRENSA OFICIAL DO ESTADO DO AMAZONAS – IOA">
                  <option value="DEPARTAMENTO ESTADUAL DE TRÂNSITO – DETRAN">
                  <option value="JUNTA COMERCIAL DO ESTADO DO AMAZONAS – JUCEA">
                  <option value="SUPERINTENDÊNCIA DE HABITAÇÃO – SUHAB">
                  <option value="INSTITUTO DE PESOS E MEDIDAS – IPEM">
                  <option value="INSTITUTO DE PROTEÇÃO AMBIENTAL DO AMAZONAS – IPAAM">
                  <option value="INSTITUTO DE DESENVOLVIMENTO AGROPECUÁRIO E FLORESTAL SUSTENTÁVEL DO ESTADO DO AMAZONAS – IDAM">
                  <option value="CENTRO DE EDUCAÇÃO TECNOLÓGICA DO AMAZONAS – CETAM">
                  <option value="SUPERINTENDÊNCIA ESTADUAL DE NAVEGAÇÃO, PORTOS E HIDROVIAS – SNPH">
                  <option value="INSTITUTO DE DEFESA DO CONSUMIDOR – PROCON/AM (Vinculado à SEJUSC)">
                  <option value="AGÊNCIA REGULADORA DOS SERVIÇOS PÚBLICOS DELEGADOS E CONTRATADOS DO ESTADO DO AMAZONAS – ARSEPAM">
                  <option value="AGÊNCIA DE DEFESA AGROPECUÁRIA E FLORESTAL DO ESTADO DO AMAZONAS – ADAF">
                  <option value="FUNDAÇÃO DE MEDICINA TROPICAL “DOUTOR HEITOR VIEIRA DOURADO – FMT-AM">
                  <option value="FUNDAÇÃO HOSPITALAR DE DERMATOLOGIA TROPICAL E VENEREOLOGIA “ALFREDO DA MATTA” – FUHAM">
                  <option value="FUNDAÇÃO CENTRO DE CONTROLE DE ONCOLOGIA DO ESTADO DO AMAZONAS – FCECON">
                  <option value="FUNDAÇÃO HOSPITALAR E HEMATOLOGIA E HEMOTERAPIA DO AMAZONAS – FHEMOAM">
                  <option value="FUNDAÇÃO HOSPITAL “ADRIANO JORGE” – FHAJ (Vinculada à SES/AM)">
                  <option value="FUNDAÇÃO HOSPITAL DO CORAÇÃO “FRANCISCA MENDES” – FHCFM">
                  <option value="FUNDAÇÃO DE VIGILÂNCIA EM SAÚDE DO ESTADO DO AMAZONAS DRA. ROSEMARY COSTA PINTO- FVS/RCP">
                  <option value="FUNDAÇÃO TELEVISÃO E RÁDIO CULTURA DO AMAZONAS – FUNTEC">
                  <option value="FUNDAÇÃO DE AMPARO À PESQUISA DO ESTADO DO AMAZONAS – FAPEAM">
                  <option value="FUNDAÇÃO FUNDO PREVIDENCIÁRIO DO ESTADO DO AMAZONAS – AMAZONPREV">
                  <option value="UNIVERSIDADE DO ESTADO DO AMAZONAS – UEA">
                  <option value="FUNDAÇÃO ESTADUAL DOS POVOS INDÍGENAS DO AMAZONAS – FEPIAM">
                  <option value="FUNDAÇÃO UNIVERSIDADE ABERTA DA TERCEIRA IDADE – FUNATI">
                  <option value="EMPRESA ESTADUAL DE TURISMO – AMAZONASTUR">
                  <option value="AGÊNCIA DE DESENVOLVIMENTO E FOMENTO DO ESTADO DO AMAZONAS – AFEAM">
                  <option value="AGÊNCIA DE DESENVOLVIMENTO SUSTENTÁVEL DO AMAZONAS – ADS">
                  <option value="COMPANHIA AMAZONENSE DE DESENVOLVIMENTO E MOBILIZAÇÃO DE ATIVOS – CADA">
                  <option value="AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO ECONÔMICO, SOCIAL E AMBIENTAL – AADESAM">
                  <option value="AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO CULTURAL – AADC">
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
            <div class="form-control">
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

            <div class="form-control">
              <label>Status do Servidor</label>
              <div class="servidorStatus">
                <span id="servidorStatus">--</span>
              </div>
            </div>
          </div>

          <!-- Botões de ação -->
          <div class="form-actions">
            <button type="submit" class="btn salvar" id="btnSalvar" disabled>
              <span>Salvar</span>
            </button>
            <button type="button" id="btnLimpar" class="btn Limpar">
              <span>Limpar</span>
            </button>

            <a class="btn cancelar" href="../02-Inicio/index.php">Cancelar</a>
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
            <button type="button" class="btn foto" id="btn-foto" onclick="alternarCameraOuCaptura()">Registrar</button>
          </div>
        </div>
        <input type="hidden" name="foto_base64" id="foto_base64">

      </form>
    </section>
  </main>
  <div id="popupToast" class="Aviso"></div>


  <footer class="rodape">
    2025 SEAD | Todos os direitos reservados
  </footer>

  <script src="script.js"></script>
</body>

</html>