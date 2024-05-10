<!DOCTYPE html>
<html lang="pt-br">
  <head>
    <meta charset="UTF-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0" />
    <title>Lavorato</title>
    <link
      rel="shortcut icon"
      href="../src/assets/Logo-Lavorato-alfa.png"
      type="image/x-icon" />
    <link rel="stylesheet" href="../src/stylesheet/style.css" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet" />
  </head>
  <body>
    <div class="nav">
      <h1>Lavorato's System</h1>
    </div>
    <div class="container">
      <div class="image-logo">
        <img
          src="../src/assets/Logo-Lavorato-alfa.png"
          width="1028px"
          height="364px"
          alt="Lavorato Logo" />
      </div>
      <div class="buttons">
        <button id="btn-cadastrar">Cadastrar</button>
        <button id="btn-atualizar">Atualizar</button>
        <button type="button" id="btn-listar">Listar</button>
        <button type="button" id="btn-relatorio">Relatorio</button>
      </div>
    </div>

    <div class="modal">
      <div class="content">
        <h1>Cadastro de Guia!</h1>
        <form action="../src/database/cadastrar.php" method="post">
          <div class="container-form">
            <div class="form-left">
              <label for="nome">Nome:</label>
              <input type="text" id="nome" name="nome" autocomplete="off"/>
              <label for="convenio">Convênio:</label>
              <input type="text" id="convenio" name="convenio" autocomplete="off"/>
              <label for="numero_guia">Número da Guia:</label>
              <input type="text" id="numero_guia" name="numero_guia" autocomplete="off"/>
              <label for="status_guia">Status:</label>
              <input list="statusGuia" type="text" id="status_guia" name="status_guia" autocomplete="off"/>
              <datalist id="statusGuia">
                <option>Solicitado</option>
                <option>Emitido</option>
                <option>Descida</option>
                <option>Assinado</option>
                <option>Faturado</option>
              </datalist>
              <label for="numero_section">Número de sessões:</label>
              <input type="text" name="numero_section" id="numero_section" autocomplete="off">
            </div>
            <div class="form-right">
              <label for="especialidade">Especialidade:</label>
              <input id="especialidade" name="especialidade" list="listEspec" autocomplete="off">
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
              <label for="mes">Mês:</label>
              <input type="text" id="mes" name="mes" list="mesList">
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
              <label for="entrada">Entrada:</label>
              <input type="text" id="entrada" name="entrada" autocomplete="off">
              <label for="saida">Saída:</label>
              <input type="text" id="saida" name="saida" autocomplete="off">
            </div>
          </div>
          <div class="buttonCadastro">
            <button id="btnCadastrarModal">Cadastrar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal-atualizar">
      <div class="content-atualizar">
        <h1>Atualização de Guia!</h1>

        <form action="../src/database/atualizar.php" method="post">
          <label for="numero_guia">Número da Guia:</label>
          <input type="text" id="numero_guia" name="numero_guia" autocomplete="off"/>
          <label for="status_guia">Status:</label>
          <input
            type="text"
            id="status_guia"
            name="status_guia"
            list="statusGuia" 
            autocomplete="off"
            />

          <label for="numero_lote">Número do lote:</label>
          <input type="text" id="numero_lote" name="numero_lote" autocomplete="off"/>

          <datalist id="statusGuia">
            <option>Solicitado</option>
            <option>Emitido</option>
            <option>Descida</option>
            <option>Assinado</option>
            <option>Faturado</option>
          </datalist>

          <div class="f1-at">
            <label for="entrada">Entrada:</label>
            <input type="text" id="entrada" name="entrada" autocomplete="off">
            <label for="saida">Saída:</label>
            <input type="text" id="saida" name="saida" autocomplete="off">
          </div>

          <div class="buttonAtualizar">
            <button type="submit" id="btnAtualizarModal">Atualizar</button>
          </div>
        </form>
      </div>
    </div>

    <div class="modal-atualizar">
      <div class="content-atualizar">
        <form action="relatorio.php" method="post">
          <div class="form-group">
              <label for="data">Data:</label>
              <input type="date" id="data" name="data">
              <input type="text" id=status" name="status" placeholder="status...">
              <input type="text" id="hora" name="hora" placeholder="HH:MM">
              <select id="especialidade" name="especialidade">
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
              <select id="convenio" name="convenio">
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
              <select id="mes" name="mes">
                  <option value="todos">TODOS OS MESES</option>
                  <option value="todos">JANEIRO</option>
                  <option value="todos">FEVEREIRO</option>
                  <option value="todos">MARÇO</option>
                  <option value="todos">ABRIL</option>
                  <option value="todos">MAIO</option>
                  <option value="todos">JUNHO</option>
                  <option value="todos">JULHO</option>
                  <option value="todos">AGOSTO</option>
                  <option value="todos">SETEMBRO</option>
                  <option value="todos">OUTUBRO</option>
                  <option value="todos">NOVEMBRO</option>
                  <option value="todos">DEZEMBRO</option>
              </select>
              <select id="excluir_convenio" name="excluir_convenio[]" multiple>
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
          <div class="buttonclass">
              <button type="submit" id="relatorioButton">Relatorio</button>
          </div>
      </form>
      </div>
    </div>

    <script src="index.js"></script>
    <script>
      document.addEventListener('DOMContentLoaded', () => {
          const btnListar = document.getElementById('btn-listar');
          btnListar.addEventListener('click', () => {
            window.location.href = 'database/listar.php';
          });
      });
  </script>
  </body>
</html>

