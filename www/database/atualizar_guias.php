<?php
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guias = $_POST['guias'];
    $status = $_POST['status'];

    if (empty($guias) || empty($status)) {
        echo "Dados inválidos!";
        exit;
    }

    $ids = implode(',', array_map('intval', $guias));

    $sql = "UPDATE pacientes SET paciente_status = ? WHERE id IN ($ids)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('s', $status);

    if ($stmt->execute()) {
        echo "Status atualizado com sucesso!";
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
