<?php
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: /lavorato-page/src/login/login.php");
  exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Controle</title>
  <link rel="shortcut icon" href="../src/assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
  <link rel="stylesheet" href="../src/stylesheet/style.css" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="/lavorato-page/src/bootstrap/css/bootstrap.min.css">
  <script src="/lavorato-page/src/bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="/lavorato-page/src/stylesheet/controle.css">
  <script src="/lavorato-page/src/index.js"></script>
  <link rel="shortcut icon" href="/lavorato-page/src/assets/Logo-Lavorato-alfa.png" type="image/x-icon">
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
          <li class="nav-item">
            <a class="nav-link" href="/lavorato-page/src/index.php">Home</a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <div class="container">
    <div class="image-logo">
      <img src="/lavorato-page/src/assets/Logo-Lavorato-alfa.png" width="1028px" height="364px" alt="Lavorato Logo" />
    </div>
    <div class="buttons">
      <button id="btn-cadastrar">Cadastrar</button>
      <button type="button" id="btn-listar">Listar</button>
      <button id="btn-documento">Documento</button>
    </div>
  </div>

  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalCadastroLabel">Cadastro de Paciente!</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/lavorato-page/src/controle-page/database/cadastrar_pacientes.php" method="post"
            enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="nome_goole" class="form-label">Nome Google:</label>
                <input type="text" id="nome_google" name="nome_google" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="email" class="form-label">Email:</label>
                <input type="text" id="email" name="email" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="data_inicio" class="form-label">Data Inicio:</label>
                <input type="text" id="data_inicio" name="data_inicio" class="form-control" autocomplete="off" />
              </div>
              <div class="col-md-6 mb-3">
                <label for="data_final" class="form-label">Data Final:</label>
                <input type="text" name="data_final" id="data_final" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="matricula" class="form-label">Matricula:</label>
                <input id="matricula" name="matricula" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="documento" class="form-label">Documento:</label>
                <input list="documentoTipo" type="text" id="documento" name="documento" class="form-control"
                  autocomplete="off" />
                <datalist id="documentoTipo">
                  <option>LD</option>
                  <option>PM</option>
                  <option>PT</option>
                  <option>RG</option>
                  <option>CPF</option>
                </datalist>
              </div>
              <div class="col-md-6 mb-3">
                <label for="documento_arquivo" class="form-label">Arquivo do Documento:</label>
                <input type="file" id="documento_arquivo" name="documento_arquivo" class="form-control"
                  accept="image/*,.pdf">
              </div>
              <div class="col-md-6 mb-3">
                <label for="especialidade_controle" class="form-label">Especialidade:</label>
                <input list="especialidadeList" id="especialidade_controle" name="especialidade_controle"
                  class="form-control" type="text" />
                <datalist id="especialidadeList">
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
                <label for="data_emissao" class="form-label">Data de Emissão:</label>
                <input type="text" name="data_emissao" id="data_emissao" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="data_validade" class="form-label">Data de Validade:</label>
                <input type="text" name="data_validade" id="data_validade" class="form-control" autocomplete="off">
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

  <div class="modal fade" id="modalDocumento" tabindex="-1" aria-labelledby="modalDocumentoLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalDocumentoLabel">Cadastro de Documento</h5>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="/lavorato-page/src/controle-page/database/cadastrar_documento.php" method="post"
            enctype="multipart/form-data">
            <div class="row">
              <div class="col-md-6 mb-3">
                <label for="paciente_matricula" class="form-label">Matricula:</label>
                <input type="text" id="paciente_matricula" name="paciente_matricula" class="form-control"
                  autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="documento" class="form-label">Documento:</label>
                <input list="documentoTipo" type="text" id="documento" name="documento" class="form-control"
                  autocomplete="off" />
                <datalist id="documentoTipo">
                  <option>LD</option>
                  <option>PM</option>
                  <option>PT</option>
                  <option>RG</option>
                  <option>CPF</option>
                </datalist>
              </div>
              <div class="col-md-6 mb-3">
                <label for="especialidade_documento" class="form-label">Especialidade:</label>
                <input list="especialidadeListDoc" id="especialidade_documento" name="especialidade_documento"
                  class="form-control" type="text" />
                <datalist id="especialidadeListDoc">
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
                <label for="data_emissao" class="form-label">Data de Emissão:</label>
                <input type="text" name="data_emissao" id="data_emissao" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="data_validade" class="form-label">Data de Validade:</label>
                <input type="text" name="data_validade" id="data_validade" class="form-control" autocomplete="off">
              </div>
              <div class="col-md-6 mb-3">
                <label for="documento_arquivo" class="form-label">Arquivo do Documento:</label>
                <input type="file" id="documento_arquivo" name="documento_arquivo" class="form-control"
                  accept="image/*,.pdf">
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

  <script>
    const btnListar = document.getElementById("btn-listar");
    btnListar.addEventListener("click", () => {
      window.location.href = "database/listar_pacientes.php";
    });

    const btnDocumento = document.getElementById('btn-documento');
    const modalDocumento = new bootstrap.Modal(document.getElementById('modalDocumento'));
    btnDocumento.addEventListener('click', () => {
      modalDocumento.show()
    })
  </script>
</body>

</html>