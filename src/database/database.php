<?php
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
} else {
    echo "Conexão bem-sucedida";
}

$conn->close();
?>