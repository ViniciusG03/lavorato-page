<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_lote, paciente_especialidade, paciente_mes, paciente_section, paciente_entrada, paciente_saida FROM pacientes";
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
    <!-- <form action="buscar.php" method="get">
        <div class="form-group">
            <label for="nome">Nome do Paciente:</label>
            <input type="text" id="nome" name="nome">
        </div>
        <div class="form-group">
            <label for="numero_guia">Número da Guia:</label>
            <input type="text" id="numero_guia" name="numero_guia">
        </div>
        <div class="form-group">
            <label for="especialidade">Especialidade:</label>
            <input type="text" name="especialidade" id="especialidade" list="listEspec">
                <datalist id="listEspec">
                    <option>Fonoaudiologia</option>
                    <option>Psicologia</option>
                    <option>ABA</option>
                    <option>Psicomotriciade</option>
                    <option>Músico Terapia</option>
                    <option>Fisioterapia</option>
                    <option>Nutrição</option>
                    <option>Arte Terapia</option>
                    <option>Psicologia</option>
                    <option>Consulta</option>
                </datalist>
        </div>

        <div class="form-group">
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
        </div>
    <button type="submit">Buscar</button>
</form> -->
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
            <input type="text" id="termo" name="termo" placeholder="Digite o termo de busca">
        </div>
        <div class="buttonclass">
            <button type="submit">Buscar</button>
        </div>
    </form>
    <table>
        <thead>
            <tr>
                <th>ID</th>
                <th>Nome do Paciente</th>
                <th>Convênio</th>
                <th>Número da Guia</th>
                <th>Status da Guia</th>
                <th>Número de Lote</th>
                <th>Especialidade</th>
                <th>Mês</th>
                <th>Seções</th>
                <th>Entrada</th>
                <th>Saida</th>
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