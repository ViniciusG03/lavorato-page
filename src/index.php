<?php
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: ../src/login/login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lavorato</title>
  <link rel="shortcut icon" href="../src/assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
  <link rel="stylesheet" href="../src/stylesheet/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <script src="bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../src/stylesheet/style.css" />
  <script src="../src/scripts/index.js"></script>
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
                echo '<li><a class="dropdown-item" href="../src/login/cadastro.php">Cadastro</a></li>';
              }
              ?>
              <li><a class="dropdown-item" href="../src/login/logout.php">Logout</a></li>
            </ul>
          </li>
        </ul>
      </div>
    </div>
  </nav>
  <div class="container">
    <div class="image-logo">
      <img src="../src/assets/Logo-Lavorato-alfa.png" width="1028px" height="364px" alt="Lavorato Logo" />
    </div>
    <div class="buttons">
      <button id="btn-cadastrar">Cadastrar</button>
      <button id="btn-atualizar">Atualizar</button>
      <button type="button" id="btn-listar">Listar</button>
      <button type="button" id="btn-relatorio">Relatorio</button>
      <?php
      if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
        echo '<button type="button" id="btn-remover">Remover</button>';
      }
      ?>
    </div>
  </div>
  <!-- Modal para cadastro de guias -->
  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroLabel">Cadastro de Guia!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../src/database/cadastrar.php" method="post">
            <div class="row">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" class="form-control" list="nome-list" autocomplete="off" />
                <datalist id="nome-list" style="display: none;">
                  <select id="nome-dropdown"></select>
                </datalist>

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
                  <option>Solicitado</option>
                  <option>Emitido</option>
                  <option>Descida</option>
                  <option>Assinado</option>
                  <option>Faturado</option>
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
                  <option>SESSÃO DE ARTETERAPIA</option>
                  <option>SESSÃO DE EQUOTERAPIA</option>
                  <option>SESSÃO DE FISIOTERAPIA</option>
                  <option>SESSÃO DE FONOAUDIOLOGIA</option>
                  <option>SESSÃO DE FONOAUDIOLOGIA EM GRUPO</option>
                  <option>SESSÃO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                  <option>SESSÃO DE MUSICOTERAPIA</option>
                  <option>SESSÃO DE NUTRIÇÃO</option>
                  <option>SESSÃO DE PSICOLOGIA DE CASAL</option>
                  <option>SESSÃO DE PSICOMOTRICIDADE</option>
                  <option>SESSÃO DE PSICOPEDAGOGIA</option>
                  <option>SESSÃO DE PSICOTERAPIA</option>
                  <option>SESSÃO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                  <option>SESSÃO DE TERAPIA OCUPACIONAL</option>
                  <option>SESSÃO DE TERAPIA OCUPACIONAL EM GRUPO</option>
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

  <!-- Script para autocomplete dos cadastros -->
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="../src/scripts/autocomplete.js"></script>

  <div class="modal fade" id="modalAtualizacao" tabindex="-1" aria-labelledby="modalAtualizacaoLabel"
    aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalAtualizacaoLabel">Atualização de Guias!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form id="updateForm" action="../src/database/atualizar.php" method="post" enctype="multipart/form-data">
            <div class="mb-3">
              <label for="numero_guia" class="form-label">Número da Guia:</label>
              <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" />
            </div>
            <div class="mb-3">
              <label for="correcao_guia" class="form-label">Correção da Guia:</label>
              <input type="text" id="correcao_guia" name="correcao_guia" class="form-control" autocomplete="off" />
            </div>
            <div class="mb-3">
              <label for="status_guia" class="form-label">Status:</label>
              <input type="text" id="status_guia" name="status_guia" list="statusGuia" class="form-control"
                autocomplete="off" />
              <datalist id="statusGuia">
                <option>Solicitado</option>
                <option>Emitido</option>
                <option>Descida</option>
                <option>Assinado</option>
                <option>Faturado</option>
              </datalist>
            </div>
            <div class="mb-3">
              <label for="excelFile" class="form-label">Importar arquivo Excel:</label>
              <input type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls" class="form-control" />
            </div>
            <div class="mb-3">
              <label for="numero_lote" class="form-label">Número do lote:</label>
              <input type="text" id="numero_lote" name="numero_lote" class="form-control" autocomplete="off" />
            </div>
            <div class="row mb-3">
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

  <div class="modal fade" id="modalRelatorio" tabindex="-1" aria-labelledby="modalRelatorioLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-scrollable">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRelatorioLabel">Emissão de Relatório</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../src/database/relatorio.php" method="post">
            <div class="mb-3">
              <label for="data" class="form-label">Data:</label>
              <input type="date" id="data" name="data" class="form-control">
            </div>
            <div class="mb-3">
              <label for="status" class="form-label">Status:</label>
              <input type="text" id="status" name="status" class="form-control" placeholder="Emitido..."
                list="statusList">
              <datalist id="statusList">
                <option>Solicitado</option>
                <option>Emitido</option>
                <option>Descida</option>
                <option>Assinado</option>
                <option>Faturado</option>
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
                <option>SESSÃO DE ARTETERAPIA</option>
                <option>SESSÃO DE EQUOTERAPIA</option>
                <option>SESSÃO DE FISIOTERAPIA</option>
                <option>SESSÃO DE FONOAUDIOLOGIA</option>
                <option>SESSÃO DE FONOAUDIOLOGIA EM GRUPO</option>
                <option>SESSÃO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                <option>SESSÃO DE MUSICOTERAPIA</option>
                <option>SESSÃO DE NUTRIÇÃO</option>
                <option>SESSÃO DE PSICOLOGIA DE CASAL</option>
                <option>SESSÃO DE PSICOMOTRICIDADE</option>
                <option>SESSÃO DE PSICOPEDAGOGIA</option>
                <option>SESSÃO DE PSICOTERAPIA</option>
                <option>SESSÃO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                <option>SESSÃO DE TERAPIA OCUPACIONAL</option>
                <option>SESSÃO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                <option>TRATAMENTO SERIADO</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="convenio" class="form-label">Convênio:</label>
              <select id="convenio" name="convenio" class="form-select">
                <option value="todos">TODOS OS CONVÊNIOS</option>
                <option value="CASSI">CASSI</option>
                <option value="FUSEX">FUSEX</option>
                <option value="CBMDF">CBMDF</option>
                <option value="ASMEPRO">ASMEPRO</option>
                <option value="ASMCH">ASMCH</option>
                <option value="AMHPDF">AMHPDF</option>
                <option value="AMAI">AMAI</option>
                <option value="BRB">BRB</option>
                <option value="BRBSAUDE">BRBSAUDE</option>
                <option value="FUSEX(PNE)">FUSEX(PNE)</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="mes" class="form-label">Mês:</label>
              <select id="mes" name="mes" class="form-select">
                <option value="todos">TODOS OS MESES</option>
                <option value="Janeiro">JANEIRO</option>
                <option value="Fevereiro">FEVEREIRO</option>
                <option value="Março">MARÇO</option>
                <option value="Abril">ABRIL</option>
                <option value="Maio">MAIO</option>
                <option value="Junho">JUNHO</option>
                <option value="Julho">JULHO</option>
                <option value="Agosto">AGOSTO</option>
                <option value="Setembro">SETEMBRO</option>
                <option value="Outubro">OUTUBRO</option>
                <option value="Novembro">NOVEMBRO</option>
                <option value="Dezembro">DEZEMBRO</option>
              </select>
            </div>
            <div class="mb-3">
              <label for="excluir_convenio" class="form-label">Convênios para Excluir:</label>
              <select id="excluir_convenio" name="excluir_convenio[]" class="form-select" multiple>
                <option value="CASSI">CASSI</option>
                <option value="FUSEX">FUSEX</option>
                <option value="CBMDF">CBMDF</option>
                <option value="ASMEPRO">ASMEPRO</option>
                <option value="ASMCH">ASMCH</option>
                <option value="AMHPDF">AMHPDF</option>
                <option value="AMAI">AMAI</option>
                <option value="BRB">BRB</option>
                <option value="BRBSAUDE">BRBSAUDE</option>
                <option value="FUSEX(PNE)">FUSEX(PNE)</option>
              </select>
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

  <div class="modal fade" id="modalRemover" tabindex="-1" aria-labelledby="modalRemoverLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRemoverLabel">Remoção de Guias!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="../src/database/remover.php" method="post">
            <div class="mb-3">
              <label for="numero_guia" class="form-label">Número da Guia:</label>
              <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" />
            </div>
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
      window.location.href = "../src/database/listar.php";
    });

    var idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minuto (60000 milissegundos)

    document.addEventListener("mousemove", function () {
      idleTime = 0;
    });

    function timerIncrement() {
      idleTime++;
      if (idleTime >= 15) { // 15 minutos
        window.location.href = "../src/login/logout.php";
      }
    }
  </script>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>