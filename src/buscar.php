<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$nome = $_GET["nome"] ?? "";
$numero_guia = $_GET["numero_guia"] ?? "";
$especialidade = $_GET["especialidade"] ?? "";
$mes = $_GET["mes"] ?? "";

$sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_lote, paciente_especialidade, paciente_mes, paciente_section, paciente_entrada, paciente_saida FROM pacientes WHERE paciente_nome LIKE '%$nome%' AND paciente_guia LIKE '%$numero_guia%' AND paciente_especialidade LIKE '%$especialidade%' AND paciente_mes LIKE '%$paciente_mes%'";
$result = $conn->query($sql);

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
</head>
<body>
    <h1>Resultados da Busca</h1>
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
</body>
</html>
