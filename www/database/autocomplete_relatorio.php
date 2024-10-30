<?php
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if (isset($_POST['nome'])) {
    $nome = $conn->real_escape_string($_POST['nome']);
    $sql = "SELECT paciente_nome FROM pacientes WHERE paciente_nome LIKE '%$nome%' GROUP BY paciente_nome";
    $result = $conn->query($sql);

    $usuarios = [];
    if ($result->num_rows > 0) {
        while ($row = $result->fetch_assoc()) {
            $usuarios[] = $row;
        }
    }

    echo json_encode($usuarios);
} else {
    echo json_encode(['error' => 'Nenhum nome fornecido']);
}

$conn->close();
