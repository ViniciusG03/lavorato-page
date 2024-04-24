<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_lote FROM pacientes";
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
</head>
<body>
    <h1>Lista de Pacientes</h1>
    <form action="buscar.php" method="get">
    <label for="nome">Nome do Paciente:</label>
    <input type="text" id="nome" name="nome">

    <label for="numero_guia">Número da Guia:</label>
    <input type="text" id="numero_guia" name="numero_guia">

    <button type="submit">Buscar</button>
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