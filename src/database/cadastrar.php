<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "lavorato@admin2024";
    $database = "lavoratoDB";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $nome = $_POST["nome"];
    $convenio = $_POST["convenio"];
    $numero_guia = $_POST["numero_guia"];
    $status_guia = $_POST["status_guia"];

    $sql = "INSERT INTO pacientes (paciente_nome, paciente_convenio, paciente_guia, paciente_status) VALUES ('$nome', '$convenio', '$numero_guia', '$status_guia')";

    if($conn->query($sql) === TRUE){
        echo "<h1> Guia Cadastrada com sucesso!</h1><br>";
        echo "<p>Nome do paciente: $nome</p><br>";
        echo "<p>Convênio do paciente: $convenio</p><br>";
        echo "<p>Número da Guia: $numero_guia</p><br>";
        echo "<p>Status da Guia: $status_guia</p><br>";
    } else {
        echo "Erro ao cadastrar a guia: " . conn->error;
    }


    $conn->close();
}
?>
