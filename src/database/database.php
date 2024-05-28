<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratodb";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
} else {
    echo "Conexão bem-sucedida";
}

$conn->close();
?>