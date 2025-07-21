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
              <label for="orgao">Órgão/Empresa</label>
              <input list="orgaos" id="orgao" name="orgao" autocomplete="off" placeholder="Informer o órgão ou empresa...">
              <datalist id="orgaos">
                <option value="SECRETARIA DE ESTADO DA CASA CIVIL">SECRETARIA DE ESTADO DA CASA CIVIL</option>
                <option value="SECRETARIA DE ESTADO DA CASA MILITAR">SECRETARIA DE ESTADO DA CASA MILITAR</option>
                <option value="SECRETARIA DE GOVERNO – SEGOV">SECRETARIA DE GOVERNO – SEGOV</option>
                <option value="SECRETARIA DE ESTADO DE COMUNICAÇÃO SOCIAL – SECOM">SECRETARIA DE ESTADO DE COMUNICAÇÃO SOCIAL – SECOM</option>
                <option value="SECRETARIA DE ESTADO DE RELAÇÕES FEDERATIVAS E INTERNACIONAIS – SERFI">SECRETARIA DE ESTADO DE RELAÇÕES FEDERATIVAS E INTERNACIONAIS – SERFI</option>
                <option value="SECRETARIA DE ESTADO DA FAZENDA – SEFAZ">SECRETARIA DE ESTADO DA FAZENDA – SEFAZ</option>
                <option value="SECRETARIA DE ESTADO DE DESENVOLVIMENTO ECONÔMICO, CIÊNCIA, TECNOLOGIA E INOVAÇÃO – SEDECTI">SECRETARIA DE ESTADO DE DESENVOLVIMENTO ECONÔMICO, CIÊNCIA, TECNOLOGIA E INOVAÇÃO – SEDECTI</option>
                <option value="SECRETARIA DE ESTADO DE ADMINISTRAÇÃO E GESTÃO – SEAD">SECRETARIA DE ESTADO DE ADMINISTRAÇÃO E GESTÃO – SEAD</option>
                <option value="SECRETARIA DE ESTADO DE JUSTIÇA, DIREITOS HUMANOS E CIDADANIA – SEJUSC">SECRETARIA DE ESTADO DE JUSTIÇA, DIREITOS HUMANOS E CIDADANIA – SEJUSC</option>
                <option value="SECRETARIA DE ESTADO DE SAÚDE – SES/AM">SECRETARIA DE ESTADO DE SAÚDE – SES/AM</option>
                <option value="SECRETARIA DE ESTADO DE EDUCAÇÃO E DESPORTO ESCOLAR – SEDUC">SECRETARIA DE ESTADO DE EDUCAÇÃO E DESPORTO ESCOLAR – SEDUC</option>
                <option value="SECRETARIA DE ESTADO DE INFRAESTRUTURA – SEINFRA">SECRETARIA DE ESTADO DE INFRAESTRUTURA – SEINFRA</option>
                <option value="SECRETARIA DE ESTADO DE ENERGIA, MINERAÇÃO E GÁS – SEMIG">SECRETARIA DE ESTADO DE ENERGIA, MINERAÇÃO E GÁS – SEMIG</option>
                <option value="SECRETARIA DE ESTADO DE DESENVOLVIMENTO URBANO E METROPOLITANO – SEDURB">SECRETARIA DE ESTADO DE DESENVOLVIMENTO URBANO E METROPOLITANO – SEDURB</option>
                <option value="SECRETARIA DE ESTADO DO DESPORTO E LAZER – SEDEL">SECRETARIA DE ESTADO DO DESPORTO E LAZER – SEDEL</option>
                <option value="SECRETARIA DE ESTADO DE SEGURANÇA PÚBLICA – SSP">SECRETARIA DE ESTADO DE SEGURANÇA PÚBLICA – SSP</option>
                <option value="SECRETARIA DE ESTADO DA ASSISTÊNCIA SOCIAL E COMBATE À FOME – SEAS">SECRETARIA DE ESTADO DA ASSISTÊNCIA SOCIAL E COMBATE À FOME – SEAS</option>
                <option value="SECRETARIA DE ESTADO DE CULTURA E ECONOMIA CRIATIVA – SEC">SECRETARIA DE ESTADO DE CULTURA E ECONOMIA CRIATIVA – SEC</option>
                <option value="SECRETARIA DE ESTADO DO MEIO AMBIENTE – SEMA">SECRETARIA DE ESTADO DO MEIO AMBIENTE – SEMA</option>
                <option value="SECRETARIA DE ESTADO DAS CIDADES E TERRITÓRIOS – SECT">SECRETARIA DE ESTADO DAS CIDADES E TERRITÓRIOS – SECT</option>
                <option value="SECRETARIA DE ESTADO DOS DIREITOS DA PESSOA COM DEFICIÊNCIA- SEPCD">SECRETARIA DE ESTADO DOS DIREITOS DA PESSOA COM DEFICIÊNCIA- SEPCD</option>
                <option value="SECRETARIA DE ESTADO DE PESCA E AQUICULTURA- SEPA">SECRETARIA DE ESTADO DE PESCA E AQUICULTURA- SEPA</option>
                <option value="SECRETARIA DE ESTADO DE PROTEÇÃO ANIMAL – SEPET">SECRETARIA DE ESTADO DE PROTEÇÃO ANIMAL – SEPET</option>
                <option value="SECRETARIA DE ESTADO DE PRODUÇÃO RURAL – SEPROR">SECRETARIA DE ESTADO DE PRODUÇÃO RURAL – SEPROR</option>
                <option value="SECRETARIA DE ESTADO DE ADMINISTRAÇÃO PENITENCIÁRIA – SEAP">SECRETARIA DE ESTADO DE ADMINISTRAÇÃO PENITENCIÁRIA – SEAP</option>
                <option value="ESCRITÓRIO DE REPRESENTAÇÃO DO GOVERNO, EM SÃO PAULO – ERGSP">ESCRITÓRIO DE REPRESENTAÇÃO DO GOVERNO, EM SÃO PAULO – ERGSP</option>
                <option value="UNIDADE DE GESTÃO INTEGRADA – UGI">UNIDADE DE GESTÃO INTEGRADA – UGI</option>
                <option value="PROCURADORIA GERAL DO ESTADO – PGE">PROCURADORIA GERAL DO ESTADO – PGE</option>
                <option value="CONTROLADORIA GERAL DO ESTADO – CGE">CONTROLADORIA GERAL DO ESTADO – CGE</option>
                <option value="CONSELHO DE DESENVOLVIMENTO DO ESTADO DO AMAZONAS – CODAM">CONSELHO DE DESENVOLVIMENTO DO ESTADO DO AMAZONAS – CODAM</option>
                <option value="CENTRO DE SERVIÇOS COMPARTILHADOS – CSC">CENTRO DE SERVIÇOS COMPARTILHADOS – CSC</option>
                <option value="POLÍCIA CIVIL DO ESTADO – PC">POLÍCIA CIVIL DO ESTADO – PC</option>
                <option value="POLÍCIA MILITAR DO AMAZONAS – PMAM">POLÍCIA MILITAR DO AMAZONAS – PMAM</option>
                <option value="CORPO DE BOMBEIROS MILITAR DO ESTADO DO AMAZONAS – CBMAM">CORPO DE BOMBEIROS MILITAR DO ESTADO DO AMAZONAS – CBMAM</option>
                <option value="SECRETARIA EXECUTIVA DO FUNDO DE PROMOÇÃO SOCIAL E ERRADICAÇÃO DA POBREZA – FPS">SECRETARIA EXECUTIVA DO FUNDO DE PROMOÇÃO SOCIAL E ERRADICAÇÃO DA POBREZA – FPS</option>
                <option value="DEFESA CIVIL DO AMAZONAS">DEFESA CIVIL DO AMAZONAS</option>
                <option value="UNIDADE GESTORA DE PROJETOS ESPECIAIS – UGPE">UNIDADE GESTORA DE PROJETOS ESPECIAIS – UGPE</option>
                <option value="UNIDADE DE GERENCIAMENTO DO PROGRAMA DE ACELERAÇÃO DO DESENVOLVIMENTO DA EDUCAÇÃO DO AMAZONAS (UGP-PADEAM)">UNIDADE DE GERENCIAMENTO DO PROGRAMA DE ACELERAÇÃO DO DESENVOLVIMENTO DA EDUCAÇÃO DO AMAZONAS (UGP-PADEAM)</option>
                <option value="IMPRENSA OFICIAL DO ESTADO DO AMAZONAS – IOA">IMPRENSA OFICIAL DO ESTADO DO AMAZONAS – IOA</option>
                <option value="DEPARTAMENTO ESTADUAL DE TRÂNSITO – DETRAN">DEPARTAMENTO ESTADUAL DE TRÂNSITO – DETRAN</option>
                <option value="JUNTA COMERCIAL DO ESTADO DO AMAZONAS – JUCEA">JUNTA COMERCIAL DO ESTADO DO AMAZONAS – JUCEA</option>
                <option value="SUPERINTENDÊNCIA DE HABITAÇÃO – SUHAB">SUPERINTENDÊNCIA DE HABITAÇÃO – SUHAB</option>
                <option value="INSTITUTO DE PESOS E MEDIDAS – IPEM">INSTITUTO DE PESOS E MEDIDAS – IPEM</option>
                <option value="INSTITUTO DE PROTEÇÃO AMBIENTAL DO AMAZONAS – IPAAM">INSTITUTO DE PROTEÇÃO AMBIENTAL DO AMAZONAS – IPAAM</option>
                <option value="INSTITUTO DE DESENVOLVIMENTO AGROPECUÁRIO E FLORESTAL SUSTENTÁVEL DO ESTADO DO AMAZONAS – IDAM">INSTITUTO DE DESENVOLVIMENTO AGROPECUÁRIO E FLORESTAL SUSTENTÁVEL DO ESTADO DO AMAZONAS – IDAM</option>
                <option value="CENTRO DE EDUCAÇÃO TECNOLÓGICA DO AMAZONAS – CETAM">CENTRO DE EDUCAÇÃO TECNOLÓGICA DO AMAZONAS – CETAM</option>
                <option value="SUPERINTENDÊNCIA ESTADUAL DE NAVEGAÇÃO, PORTOS E HIDROVIAS – SNPH">SUPERINTENDÊNCIA ESTADUAL DE NAVEGAÇÃO, PORTOS E HIDROVIAS – SNPH</option>
                <option value="INSTITUTO DE DEFESA DO CONSUMIDOR – PROCON">INSTITUTO DE DEFESA DO CONSUMIDOR – PROCON</option>
                <option value="AGÊNCIA REGULADORA DOS SERVIÇOS PÚBLICOS DELEGADOS E CONTRATADOS DO ESTADO DO AMAZONAS – ARSEPAM">AGÊNCIA REGULADORA DOS SERVIÇOS PÚBLICOS DELEGADOS E CONTRATADOS DO ESTADO DO AMAZONAS – ARSEPAM</option>
                <option value="AGÊNCIA DE DEFESA AGROPECUÁRIA E FLORESTAL DO ESTADO DO AMAZONAS – ADAF">AGÊNCIA DE DEFESA AGROPECUÁRIA E FLORESTAL DO ESTADO DO AMAZONAS – ADAF</option>
                <option value="FUNDAÇÃO DE MEDICINA TROPICAL “DOUTOR HEITOR VIEIRA DOURADO” – FMT-AM">FUNDAÇÃO DE MEDICINA TROPICAL “DOUTOR HEITOR VIEIRA DOURADO” – FMT-AM</option>
                <option value="FUNDAÇÃO HOSPITALAR DE DERMATOLOGIA TROPICAL E VENEREOLOGIA “ALFREDO DA MATTA” – FUHAM">FUNDAÇÃO HOSPITALAR DE DERMATOLOGIA TROPICAL E VENEREOLOGIA “ALFREDO DA MATTA” – FUHAM</option>
                <option value="FUNDAÇÃO CENTRO DE CONTROLE DE ONCOLOGIA DO ESTADO DO AMAZONAS – FCECON">FUNDAÇÃO CENTRO DE CONTROLE DE ONCOLOGIA DO ESTADO DO AMAZONAS – FCECON</option>
                <option value="FUNDAÇÃO HOSPITALAR E HEMATOLOGIA E HEMOTERAPIA DO AMAZONAS – FHEMOAM">FUNDAÇÃO HOSPITALAR E HEMATOLOGIA E HEMOTERAPIA DO AMAZONAS – FHEMOAM</option>
                <option value="FUNDAÇÃO HOSPITAL ADRIANO JORGE – FHAJ">FUNDAÇÃO HOSPITAL ADRIANO JORGE – FHAJ</option>
                <option value="FUNDAÇÃO HOSPITAL DO CORAÇÃO FRANCISCA MENDES – FHCFM">FUNDAÇÃO HOSPITAL DO CORAÇÃO FRANCISCA MENDES – FHCFM</option>
                <option value="FUNDAÇÃO DE VIGILÂNCIA EM SAÚDE DO ESTADO DO AMAZONAS DRA. ROSEMARY COSTA PINTO- FVS/RCP">FUNDAÇÃO DE VIGILÂNCIA EM SAÚDE DO ESTADO DO AMAZONAS DRA. ROSEMARY COSTA PINTO- FVS/RCP</option>
                <option value="FUNDAÇÃO TELEVISÃO E RÁDIO CULTURA DO AMAZONAS – FUNTEC">FUNDAÇÃO TELEVISÃO E RÁDIO CULTURA DO AMAZONAS – FUNTEC</option>
                <option value="FUNDAÇÃO DE AMPARO À PESQUISA DO ESTADO DO AMAZONAS – FAPEAM">FUNDAÇÃO DE AMPARO À PESQUISA DO ESTADO DO AMAZONAS – FAPEAM</option>
                <option value="FUNDAÇÃO FUNDO PREVIDENCIÁRIO DO ESTADO DO AMAZONAS – AMAZONPREV">FUNDAÇÃO FUNDO PREVIDENCIÁRIO DO ESTADO DO AMAZONAS – AMAZONPREV</option>
                <option value="UNIVERSIDADE DO ESTADO DO AMAZONAS – UEA">UNIVERSIDADE DO ESTADO DO AMAZONAS – UEA</option>
                <option value="FUNDAÇÃO ESTADUAL DOS POVOS INDÍGENAS DO AMAZONAS – FEPIAM">FUNDAÇÃO ESTADUAL DOS POVOS INDÍGENAS DO AMAZONAS – FEPIAM</option>
                <option value="FUNDAÇÃO UNIVERSIDADE ABERTA DA TERCEIRA IDADE – FUNATI">FUNDAÇÃO UNIVERSIDADE ABERTA DA TERCEIRA IDADE – FUNATI</option>
                <option value="EMPRESA ESTADUAL DE TURISMO – AMAZONASTUR">EMPRESA ESTADUAL DE TURISMO – AMAZONASTUR</option>
                <option value="AGÊNCIA DE DESENVOLVIMENTO E FOMENTO DO ESTADO DO AMAZONAS – AFEAM">AGÊNCIA DE DESENVOLVIMENTO E FOMENTO DO ESTADO DO AMAZONAS – AFEAM</option>
                <option value="AGÊNCIA DE DESENVOLVIMENTO SUSTENTÁVEL DO AMAZONAS – ADS">AGÊNCIA DE DESENVOLVIMENTO SUSTENTÁVEL DO AMAZONAS – ADS</option>
                <option value="COMPANHIA AMAZONENSE DE DESENVOLVIMENTO E MOBILIZAÇÃO DE ATIVOS – CADA">COMPANHIA AMAZONENSE DE DESENVOLVIMENTO E MOBILIZAÇÃO DE ATIVOS – CADA</option>
                <option value="AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO ECONÔMICO, SOCIAL E AMBIENTAL – AADESAM">AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO ECONÔMICO, SOCIAL E AMBIENTAL – AADESAM</option>
                <option value="AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO CULTURAL – AADC">AGÊNCIA AMAZONENSE DE DESENVOLVIMENTO CULTURAL – AADC</option>
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
  <footer class="rodape">
    2025 SEAD | EPP. Todos os direitos reservados
  </footer>
    <div id="popupToast"></div>

  <script src="script.js"></script>
  
</body>

</html>