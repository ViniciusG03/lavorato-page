<?php
require_once 'database.php';

$database = new Database();
$conn = $database->connect();

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $nome = $_POST['nome'];
    $convenio = $_POST['convenio'];
    $validade = $_POST['validade'];
    $tipo = $_POST['tipo'];
    $e = $_POST['email'];

    $dataFormatada = date('Y-m-d', strtotime($validade));

    $sql = 'INSERT INTO Atas (nome, convenio, validade, tipo, email) VALUES (:nome, :convenio, :validade, :tipo, :email)';
    $stmt = $conn->prepare($sql);
    $stmt->bindParam(':nome', $nome);
    $stmt->bindParam(':convenio', $convenio);
    $stmt->bindParam(':validade', $dataFormatada);
    $stmt->bindParam(':tipo', $tipo);
    $stmt->bindParam(':email', $e);

    if ($stmt->execute()) {
        header('Location: ../views/atas.php');
    } else {
        echo 'Erro ao cadastrar a ata';
    }
}

