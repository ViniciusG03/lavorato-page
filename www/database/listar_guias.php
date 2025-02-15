<?php
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$busca = isset($_GET['busca']) ? $_GET['busca'] : '';

// Modificar a query para permitir busca dinâmica
$sql = "SELECT id, paciente_guia, paciente_nome, paciente_status FROM pacientes";
if (!empty($busca)) {
    $sql .= " WHERE paciente_guia LIKE '%$busca%'";
}
$result = $conn->query($sql);

// Gerar HTML com as guias filtradas
while ($row = $result->fetch_assoc()) {
    echo "<tr>
            <td><input type='checkbox' class='checkbox-guia' value='{$row['id']}'></td>
            <td>{$row['paciente_guia']}</td>
            <td>{$row['paciente_nome']}</td>
            <td>{$row['paciente_status']}</td>
          </tr>";
}

$conn->close();
