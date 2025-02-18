<?php
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: ../login/login.php");
  exit();
}

function hasPermission($roles) {
  return (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) || in_array($_SESSION['login'], $roles);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lavorato</title>
  <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
  <link rel="stylesheet" href="../stylesheet/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../stylesheet/style.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../scripts/autocomplete.js"></script>
  <script src="index.js"></script>
</head>

<body>
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Lavorato's System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              Opções
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="controle-page\controle.php">Controle</a></li>
              <?php
              if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                echo '<li><a class="dropdown-item" href="../login/cadastro.php">Cadastro</a></li>';
              }
              ?>
              <li><a class="dropdown-item" href="../login/logout.php">Logout</a></li>
              <li><a class="dropdown-item" href="views/atas.php">ATAS</a></li>
              <?php
              if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])) {
                echo '<li><a class="dropdown-item" href="views/relatorios.php">Relatorios</a></li>';
              }
              ?>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="image-logo">
      <img src="../assets/Logo-Lavorato-alfa.png" width="1028px" height="364px" alt="Lavorato Logo" />
    </div>
    <div class="buttons">
      <?php
      if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])) {
        echo '<button id="btn-cadastrar" class="btn btn-primary data-bs-toggle="modal">Cadastrar</button>';
      }
      ?>
      <?php
      if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
        echo '<button id="btn-atualizar" class="btn btn-primary data-bs-toggle="modal" data-bs-target=#modalAtualizacao>Atualizar</button>';
      }
      ?>
      <?php
      if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
        echo '<button id="btn-atualizarEmMassa" class="btn btn-primary data-bs-toggle="modal">Atualizar em massa</button>';
      }
      ?>
      <button type="button" id="btn-listar" target="_blank" class="btn btn-primary data-bs-toggle=modal">Listar</button>
      <?php
      if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
        echo '<button type="button" id="btn-relatorio" class="btn btn-primary data-bs-toggle="modal">Relatorio</button>';
      }
      ?>
      <?php
      if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])) {
        echo '<button type="button" id="btn-remover" class="btn btn-primary data-bs-toggle="modal">Remover</button>';
      }
      ?>
    </div>
  </div>

  <!-- Modal de cadastro -->
  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroLabel">Cadastro de Guia!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../database/cadastrar.php" method="post">
            <div class="row">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" class="form-control" autocomplete="off" />
                <div id="nome-suggestions" class="dropdown-menu"></div>
              </div>
              <div class="col-md-6 mb-3">
                <label for="convenio" class="form-label">Convênio:</label>
                <input type="text" id="convenio" name="convenio" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="numero_guia" class="form-label">Número da Guia:</label>
                <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="status_guia" class="form-label">Status:</label>
                <input list="statusGuia" type="text" id="status_guia" name="status_guia" class="form-control"
                  autocomplete="off" />
                <datalist id="statusGuia">
                <option>Emitido</option>
                <option>Subiu</option>
                <option>Cancelado</option>
                <option>Saiu</option>
                <option>Não Usou</option>
                <option>Assinado</option>
                <option>Faturado</option>
                <option>Enviado a BM</option>
                </datalist>
              </div>
              <div class="col-md-6 mb-3">
                <label for="numero_section" class="form-label">Número de sessões:</label>
                <input type="text" name="numero_section" id="numero_section" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="especialidade" class="form-label">Especialidade:</label>
                <input id="especialidade" name="especialidade" list="listEspec" class="form-control" autocomplete="off">
                <datalist id="listEspec">
                  <option>AVALIACAO NEUROPSICOLOGICA</option>
                  <option>SESSAO DE ARTETERAPIA</option>
                  <option>SESSAO DE EQUOTERAPIA</option>
                  <option>SESSAO DE FISIOTERAPIA</option>
                  <option>SESSAO DE FONOAUDIOLOGIA</option>
                  <option>SESSAO DE FONOAUDIOLOGIA EM GRUPO</option>
                  <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                  <option>SESSAO DE MUSICOTERAPIA</option>
                  <option>SESSAO DE NUTRIÇÃO</option>
                  <option>SESSAO DE PSICOLOGIA DE CASAL</option>
                  <option>SESSAO DE PSICOMOTRICIDADE</option>
                  <option>SESSAO DE PSICOPEDAGOGIA</option>
                  <option>SESSAO DE PSICOTERAPIA</option>
                  <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                  <option>SESSAO DE TERAPIA OCUPACIONAL</option>
                  <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                  <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                  <option>TRATAMENTO SERIADO</option>
                </datalist>
              </div>
              <div class="col-md-6 mb-3">
                <label for="mes" class="form-label">Mês:</label>
                <input type="text" id="mes" name="mes" list="mesList" class="form-control" autocomplete="off">
                <datalist id="mesList">
                  <option>Janeiro</option>
                  <option>Fevereiro</option>
                  <option>Março</option>
                  <option>Abril</option>
                  <option>Maio</option>
                  <option>Junho</option>
                  <option>Julho</option>
                  <option>Agosto</option>
                  <option>Setembro</option>
                  <option>Outubro</option>
                  <option>Novembro</option>
                  <option>Dezembro</option>
                </datalist>
              </div>
              <div class="mb-3">
                <label for="validade" class="form-label">Validade:</label>
                <input type="text" id="validade" name="validade" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="entrada" class="form-label">Entrada:</label>
                <input type="text" id="entrada" name="entrada" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="saida" class="form-label">Saída:</label>
                <input type="text" id="saida" name="saida" class="form-control" autocomplete="off">
              </div>
            </div>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="submit" class="btn btn-primary">Salvar</button>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de atualização -->
  <div class="modal fade" id="modalAtualizacao" tabindex="-1" aria-labelledby="modalAtualizacaoLabel"
    aria-hidden="true">
    <div class="modal-dialog modal-xl modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAtualizacaoLabel">Atualização de Guias!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="updateForm" action="../database/atualizar.php" method="post" enctype="multipart/form-data">
            <div class="row mb-3">
              <div class="col-md-6 mb-3">
                <label for="numero_guia" class="form-label">ID:</label>
                <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" />
                <label for="checkbox_guia" class="form-label">Usar numeração da guia:</label>
                <input type="checkbox" id="checkbox_guia" name="checkbox_guia" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="correcao_guia" class="form-label">Corrigir número da guia:</label>
                <input type="text" id="correcao_guia" name="correcao_guia" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="status_guia" class="form-label">Status:</label>
                <input type="text" id="status_guia" name="status_guia" list="statusGuia" class="form-control"
                  autocomplete="off" />
                <datalist id="statusGuia">
                <option>Emitido</option>
                <option>Subiu</option>
                <option>Cancelado</option>
                <option>Saiu</option>
                <option>Não Usou</option>
                <option>Assinado</option>
                <option>Faturado</option>
                <option>Enviado a BM</option>
                </datalist>
              </div>
              <div class="col-md-6 mb-3">
                <label for="valor_guia" class="form-label">Valor da Guia:</label>
                <input type="text" id="valor_guia" name="valor_guia" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="qtd_faturada" class="form-label">Quantidade faturada:</label>
                <input type="text" id="qtd_faturada" name="qtd_faturada" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="validade" class="form-label">Data da Remessa:</label>
                <input type="text" id="data_remessa" name="data_remessa" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="validade" class="form-label">Validade:</label>
                <input type="text" id="validade" name="validade" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="section" class="form-label">Seção:</label>
                <input type="text" id="section" name="section" class="form-control" autocomplete="off" />
              </div>
              <div class="mb-3">
                <label for="especialidade" class="form-label">Especialidade:</label>
                <input type="text" id="especialidade" name="especialidade" class="form-control" list="EspecList"
                  autocomplete="off" />
                <datalist id="EspecList">
                  <option>AVALIACAO NEUROPSICOLOGICA</option>
                  <option>SESSAO DE ARTETERAPIA</option>
                  <option>SESSAO DE EQUOTERAPIA</option>
                  <option>SESSAO DE FISIOTERAPIA</option>
                  <option>SESSAO DE FONOAUDIOLOGIA</option>
                  <option>SESSAO DE FONOAUDIOLOGIA EM GRUPO</option>
                  <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                  <option>SESSAO DE MUSICOTERAPIA</option>
                  <option>SESSAO DE NUTRIÇÃO</option>
                  <option>SESSAO DE PSICOLOGIA DE CASAL</option>
                  <option>SESSAO DE PSICOMOTRICIDADE</option>
                  <option>SESSAO DE PSICOPEDAGOGIA</option>
                  <option>SESSAO DE PSICOTERAPIA</option>
                  <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                  <option>SESSAO DE TERAPIA OCUPACIONAL</option>
                  <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                  <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                  <option>TRATAMENTO SERIADO</option>
                </datalist>
              </div>
            </div>
            <div class="mb-3">
              <label for="excelFile" class="form-label">Importar arquivo Excel:</label>
              <input type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls" class="form-control" />
            </div>
            <div class="row mb-3">
              <div class="col-md-6">
                <label for="mes" class="form-label">Mês de atualização:</label>
                <input type="text" id="mes" name="mes" list="mesList" class="form-control" autocomplete="off" />
                <datalist id="mesList">
                  <option>Janeiro</option>
                  <option>Fevereiro</option>
                  <option>Março</option>
                  <option>Abril</option>
                  <option>Maio</option>
                  <option>Junho</option>
                  <option>Julho</option>
                  <option>Agosto</option>
                  <option>Setembro</option>
                  <option>Outubro</option>
                  <option>Novembro</option>
                  <option>Dezembro</option>
                </datalist>
              </div>
              <div class="col-md-6">
                <label for="numero_lote" class="form-label">Número do lote:</label>
                <input type="text" id="numero_lote" name="numero_lote" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6">
                <label for="entrada" class="form-label">Entrada:</label>
                <input type="text" id="entrada" name="entrada" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6">
                <label for="saida" class="form-label">Saída:</label>
                <input type="text" id="saida" name="saida" class="form-control" autocomplete="off" />
              </div>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal relatorio -->
  <div class="modal fade" id="modalRelatorio" tabindex="-1" aria-labelledby="modalRelatorioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRelatorioLabel">Emissão de Relatório</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../database/relatorio.php" method="post">
            <div class="mb-3">
              <label for="data" class="form-label">Data:</label>
              <input type="date" id="data" name="data" class="form-control">
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status:</label>
              <input type="text" id="status" name="status" class="form-control" placeholder="Emitido..."
                list="statusList">
              <datalist id="statusList">
                <option>Emitido</option>
                <option>Subiu</option>
                <option>Cancelado</option>
                <option>Saiu</option>
                <option>Não Usou</option>
                <option>Assinado</option>
                <option>Faturado</option>
                <option>Enviado a BM</option>
              </datalist>
            </div>
            <div class="mb-3">
              <label for="hora" class="form-label">Hora:</label>
              <input type="text" id="hora" name="hora" class="form-control" placeholder="HH:MM">
            </div>
            <div class="mb-3">
              <label for="especialidade" class="form-label">Especialidade:</label>
              <select id="especialidade" name="especialidade" class="form-select">
                <option value="todas">Todas as Especialidades</option>
                <option>AVALIACAO NEUROPSICOLOGICA</option>
                <option>SESSAO DE ARTETERAPIA</option>
                <option>SESSAO DE EQUOTERAPIA</option>
                <option>SESSAO DE FISIOTERAPIA</option>
                <option>SESSAO DE FONOAUDIOLOGIA</option>
                <option>SESSAO DE FONOAUDIOLOGIA EM GRUPO</option>
                <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                <option>SESSAO DE MUSICOTERAPIA</option>
                <option>SESSAO DE NUTRIÇÃO</option>
                <option>SESSAO DE PSICOLOGIA DE CASAL</option>
                <option>SESSAO DE PSICOMOTRICIDADE</option>
                <option>SESSAO DE PSICOPEDAGOGIA</option>
                <option>SESSAO DE PSICOTERAPIA</option>
                <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                <option>SESSAO DE TERAPIA OCUPACIONAL</option>
                <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                <option>TRATAMENTO SERIADO</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="convenio" class="form-label">Convênio:</label>
              <select id="convenio" name="convenio" class="form-select">
                <option value="todos">TODOS OS CONVÊNIOS</option>
                <option value="CASSI">CASSI</option>
                <option value="CAIXA">CAIXA</option>
                <option value="TRF">TRF</option>
                <option value="SERPRO">SERPRO</option>
                <option value="TST">TST</option>
                <option value="POSTAL SAUDE">POSTAL SAUDE</option>
                <option value="TJDFT">TJDFT</option>
                <option value="PARTICULAR">PARTICULAR</option>
                <option value="FUSEX">FUSEX</option>
                <option value="FUSEX(PNE)">FUSEX(PNE)</option>
                <option value="CBMDF">CBMDF</option>
                <option value="CAIXA">CAIXA</option>
                <option value="SIS SENADO">SIS SENADO</option>
                <option value="PLAN ASSISTE">PLAN ASSISTE</option>
                <option value="FASCAL">FASCAL</option>
                <option value="BACEN">BACEN</option>
                <option value="STM">STM</option>
                <option value="E-VIDA">E-VIDA</option>
                <option value="BRB">BRB</option>
                <option value="UNIVIDA">UNIVIDA</option>
                <option value="PORTO">PORTO</option>
                <option value="CASEMBRAPA">CASEMBRAPA</option>
                <option value="TRT">TRT</option>
                <option value="GEAP">GEAP</option>
                <option value="OMINT">OMINT</option>
                <option value="STF">STF</option>
                <option value="BRADESCO">BRADESCO</option>
                <option value="CAMARA DOS DEPUTADOS">CAMARA DOS DEPUTADOS</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="mes" class="form-label">Mês:</label>
              <select id="mes" name="mes" class="form-select">
                <option value="todos">TODOS OS MESES</option>
                <option value="Janeiro">Janeiro</option>
                <option value="Fevereiro">Fevereiro</option>
                <option value="Março">Março</option>
                <option value="Abril">Abril</option>
                <option value="Maio">Maio</option>
                <option value="Junho">Junho</option>
                <option value="Julho">Julho</option>
                <option value="Agosto">Agosto</option>
                <option value="Setembro">Setembro</option>
                <option value="Outubro">Outubro</option>
                <option value="Novembro">Novembro</option>
                <option value="Dezembro">Dezembro</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="excluir_convenio" class="form-label">Convênios para Excluir:</label>
              <select id="excluir_convenio" name="excluir_convenio[]" class="form-select" multiple>
                <option value="CASSI">CASSI</option>
                <option value="CAIXA">CAIXA</option>
                <option value="TRF">TRF</option>
                <option value="SERPRO">SERPRO</option>
                <option value="TST">TST</option>
                <option value="POSTAL SAUDE">POSTAL SAUDE</option>
                <option value="TJDFT">TJDFT</option>
                <option value="PARTICULAR">PARTICULAR</option>
                <option value="FUSEX">FUSEX</option>
                <option value="CBMDF">CBMDF</option>
                <option value="CAIXA">CAIXA</option>
                <option value="SIS SENADO">SIS SENADO</option>
                <option value="PLAN ASSISTE">PLAN ASSISTE</option>
                <option value="FASCAL">FASCAL</option>
                <option value="BACEN">BACEN</option>
                <option value="STM">STM</option>
                <option value="E-VIDA">E-VIDA</option>
                <option value="BRB">BRB</option>
                <option value="UNIVIDA">UNIVIDA</option>
                <option value="PORTO">PORTO</option>
                <option value="CASEMBRAPA">CASEMBRAPA</option>
                <option value="TRT">TRT</option>
                <option value="GEAP">GEAP</option>
                <option value="OMINT">OMINT</option>
                <option value="STF">STF</option>
                <option value="BRADESCO">BRADESCO</option>
                <option value="CAMARA DOS DEPUTADOS">CAMARA DOS DEPUTADOS</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="observacao" id="observacao" class="form-label">Observação:</label>
              <textarea name="observacao" id="observacao" placeholder="Digite sua observação..." class="form-control"></textarea>
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAtualizar" tabindex="-1" aria-labelledby="modalAtualizarLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAtualizarLabel">Atualizar Guias em Massa</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <!-- Campo de busca -->
          <input type="text" id="buscaGuia" class="form-control mb-3" placeholder="Buscar guia pelo número...">

          <!-- Lista de guias filtradas -->
          <table class="table">
            <thead>
              <tr>
                <th>Selecionar</th>
                <th>Número da Guia</th>
                <th>Paciente</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody id="listaGuias">
              <!-- Guias serão carregadas aqui via AJAX -->
            </tbody>
          </table>

          <!-- Seleção do novo status -->
          <label for="novoStatus">Novo Status:</label>
          <select class="form-control" id="novoStatus">
            <option value="Emitido">Emitido</option>
            <option value="Subiu">Subiu</option>
            <option value="Cancelado">Cancelado</option>
            <option value="Saiu">Saiu</option>
            <option value="Não Usou">Não Usou</option>
            <option value="Assinado">Assinado</option>
            <option value="Enviado a BM">Enviado a BM</option>
            <option value="Faturado">Faturado</option>
          </select>
        </div>
        <div class="modal-footer">
          <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
          <button type="button" class="btn btn-success" id="confirmarAtualizacao">Atualizar</button>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal remoção -->
  <div class="modal fade" id="modalRemover" tabindex="-1" aria-labelledby="modalRemoverLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRemoverLabel">Remoção de Guias!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../database/remover.php" method="post">
            <div class="mb-3">
              <label for="id_guia" class="form-label">ID da Guia:</label>
              <input type="text" id="id_guia" name="id_guia" class="form-control" autocomplete="off" />
            </div>
            <div class="modal-footer">
              <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
              <button type="submit" class="btn btn-primary">Salvar</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <script>
    const btnListar = document.getElementById("btn-listar");
    btnListar.addEventListener("click", () => {
      window.location.href = "../database/listar.php";
    });

    var idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minuto (60000 milissegundos)

    document.addEventListener("mousemove", function() {
      idleTime = 0;
    });

    function timerIncrement() {
      idleTime++;
      if (idleTime >= 15) { // 15 minutos
        window.location.href = "../login/logout.php";
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>