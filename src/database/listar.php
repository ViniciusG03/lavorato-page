<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_lote, paciente_especialidade, paciente_mes, paciente_section, paciente_entrada, paciente_saida, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link
      rel="shortcut icon"
      href="../assets/Logo-Lavorato-alfa.png"
      type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/listar.css">
</head>
<body>
    <div class="nav">
        <button id="homeButton">Home</button>
        <h1>Lavorato's System</h1>
    </div>
    <h1 id="title">Lista de Pacientes</h1>

    <div class=form-container>
        <form action="buscar.php" method="get">
        <div class="form-group">
            <label for="busca">Buscar por:</label>
            <select id="busca" name="categoria">
                <option value="paciente_nome">Nome do Paciente</option>
                <option value="paciente_convenio">Convênio</option>
                <option value="paciente_guia">Número da Guia</option>
                <option value="paciente_status">Status</option>
                <option value="paciente_especialidade">Especialidade</option>
                <option value="paciente_mes">Mês</option>
            </select>
            <input type="text" id="termo" name="termo" placeholder="Digite o termo de busca" autocomplete="off">
            <div class="buttonclass">
                <button type="submit" id="buscarButton">Buscar</button>
            </div>
        </div>
    </form>
    
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

    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome</th>
                <th>Convênio</th>
                <th>Número</th>
                <th>Status</th>
                <th>Lote</th>
                <th>Especialidade</th>
                <th>Mês</th>
                <th>Sessões</th>
                <th>Entrada</th>
                <th>Saida</th>
                <th>Atualização</th>
            </tr>
        </thead>
        <tbody>
            <?php
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    echo "<tr>";
                    echo "<td>" . $row["id"] . "</td>";
                    echo "<td>" . $row["paciente_nome"] . "</td>";
                    echo "<td>" . $row["paciente_convenio"] . "</td>";
                    echo "<td>" . $row["paciente_guia"] . "</td>";
                    echo "<td>" . $row["paciente_status"] . "</td>";
                    echo "<td>" . $row["paciente_lote"] . "</td>";
                    echo "<td>" . $row["paciente_especialidade"] . "</td>";
                    echo "<td>" . $row["paciente_mes"] . "</td>";
                    echo "<td>" . $row["paciente_section"] . "</td>";
                    echo "<td>" . $row["paciente_entrada"] . "</td>";
                    echo "<td>" . $row["paciente_saida"] . "</td>";
                    echo "<td>" . $row["data_hora_formatada"] . "</td>";
                    echo "</tr>";
                }
            } else {
                echo "<tr><td colspan='5'>Nenhum paciente encontrado</td></tr>";
            }
            ?>
        </tbody>
    </table>

    <script>
        document.addEventListener('DOMContentLoaded', () => {
          const btnListar = document.getElementById('homeButton');
          btnListar.addEventListener('click', () => {
            window.location.href = '../index.html';
          });
      });
    </script>
</body>
</html>
