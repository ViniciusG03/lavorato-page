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

    $numero_guia = $_POST["numero_guia"];
    $status_guia = $_POST["status_guia"];
    $numero_lote = $_POST["numero_lote"];

    // Verificar se o número da guia existe no banco de dados
    $sql_select = "SELECT * FROM pacientes WHERE paciente_guia = '$numero_guia'";
    $result = $conn->query($sql_select);

    if ($result->num_rows > 0) {
        // Atualizar os campos se o número da guia existir
        $sql_update = "UPDATE pacientes SET paciente_status = '$status_guia', paciente_lote = '$numero_lote' WHERE paciente_guia = '$numero_guia'";

        if ($conn->query($sql_update) === TRUE) {
            echo "Atualização bem-sucedida";
        } else {
            echo "Erro ao atualizar: " . $conn->error;
        }
    } else {
        echo "Número da guia não encontrado";
    }

    $conn->close();
}
?>