<?php
session_start();
if ($_SERVER['REQUEST_METHOD'] == "POST") {
    if (!isset($_SESSION['login'])) {
        header("Location: ../src/login/login.php");
        exit();
    }

    $servername = "mysql.lavoratoguias.kinghost.net";
    $username = "lavoratoguias";
    $password = "A3g7K2m9T5p8L4v6";
    $database = "lavoratoguias";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    } else {
        echo "<script language='javascript' type='text/javascript'>console.log('Conectado com sucesso');</script>";
    }

    $id_guia = $_POST['id_guia'];

    if (empty($id_guia)) {
        echo "<script language='javascript' type='text/javascript'>alert('Preencha todos os campos');window.location.href='../index.php';</script>";
        exit();
    } else {
        $stmt = $conn->prepare("DELETE FROM pacientes WHERE id = ?");
        if ($stmt === false) {
            die("Erro na preparação da consulta: " . $conn->error);
        }
        $stmt->bind_param("i", $id_guia);
        if (!$stmt->execute()) {
            echo "<script language='javascript' type='text/javascript'>alert('Erro ao remover: " . $stmt->error . "');window.location.href='../index.php';</script>";
            exit();
        } else {
            echo "<script language='javascript' type='text/javascript'>alert('Removido com sucesso');window.location.href='../index.php';</script>";
        }
        $stmt->close();
    }

    $conn->close();
}
?>