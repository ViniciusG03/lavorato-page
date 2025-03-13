<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("HTTP/1.1 403 Forbidden");
    echo json_encode(['success' => false, 'message' => 'Acesso negado']);
    exit();
}

// Verificar se o ID do paciente foi fornecido
if (!isset($_GET['paciente_id']) || empty($_GET['paciente_id'])) {
    echo json_encode(['success' => false, 'message' => 'ID do paciente não fornecido']);
    exit();
}

$paciente_id = intval($_GET['paciente_id']);

// Conexão com o banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo json_encode(['success' => false, 'message' => 'Erro na conexão: ' . $conn->connect_error]);
    exit();
}

// Incluir funções de especialidades
require_once 'functions_especialidades.php';

// Obter especialidades
$especialidades = obter_especialidades_paciente($paciente_id, $conn);

// Retornar como JSON
echo json_encode([
    'success' => true,
    'especialidades' => $especialidades
]);

$conn->close();
?>