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

if(isset ($_POST["status_guia"])) {
    $status_guia = $_POST["status_guia"];
    echo "$status_guia";
}

    $numero_guia = $_POST["numero_guia"];
    $numero_lote = $_POST["numero_lote"];

    echo "$numero_guia <br>";
    echo "$status_guia";
    echo "$numero_lote";

    $conn->close();
}
?>