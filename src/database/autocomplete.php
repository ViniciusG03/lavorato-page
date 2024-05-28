<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$dbname = "lavoratodb";

// Criar conexão
$conn = new mysqli($servername, $username, $password, $dbname);

// Verificar conexão
if ($conn->connect_error) {
    die(json_encode(["error" => "Falha na conexão: " . $conn->connect_error]));
}

if (isset($_POST['nome'])) {
    $nome = $_POST['nome'];
    $sql = "SELECT paciente_nome, paciente_convenio, paciente_entrada, paciente_saida FROM pacientes WHERE paciente_nome LIKE ?";
    $stmt = $conn->prepare($sql);
    
    if (!$stmt) {
        die(json_encode(["error" => "Erro na preparação da consulta: " . $conn->error]));
    }

    $searchTerm = "%" . $nome . "%";
    $stmt->bind_param("s", $searchTerm);
    
    if (!$stmt->execute()) {
        die(json_encode(["error" => "Erro na execução da consulta: " . $stmt->error]));
    }

    $result = $stmt->get_result();

    if ($result->num_rows > 0) {
        $usuario = $result->fetch_assoc();
        echo json_encode($usuario);
    } else {
        echo json_encode(null);
    }

    $stmt->close();
} else {
    echo json_encode(["error" => "Nome não fornecido"]);
}

$conn->close();
?>
