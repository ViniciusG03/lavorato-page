<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratodb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST["nome"])) {
    $nome = $_POST["nome"];

    $sql = "SELECT DISTINCT paciente_nome, paciente_convenio, paciente_entrada, paciente_saida FROM pacientes WHERE paciente_nome LIKE '%$nome%' LIMIT 10"; // Limita a 10 resultados para performance

    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        $pacientes = array();
        while ($row = $result->fetch_assoc()) {
            $pacientes[] = $row;
        }
        echo json_encode($pacientes);
    } else {
        echo json_encode(array('error' => 'Nenhum paciente encontrado.'));
    }
} else {
    echo json_encode(array('error' => 'Requisição inválida.'));
}

$conn->close();
?>
