<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verificar se é um POST
if ($_SERVER["REQUEST_METHOD"] != "POST") {
    header("Location: ../views/relatorios_customizados.php");
    exit();
}

// Conexão com o banco
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Recuperar parâmetros do formulário
$periodo_mes = isset($_POST['periodo_mes']) ? $_POST['periodo_mes'] : '';
$status_array = isset($_POST['status']) ? $_POST['status'] : [];
$convenio = isset($_POST['convenio']) ? $_POST['convenio'] : '';
$especialidade = isset($_POST['especialidade']) ? $_POST['especialidade'] : '';
$formato = isset($_POST['formato']) ? $_POST['formato'] : 'html';

// Construir a consulta SQL com base nos filtros
$sql = "SELECT 
            id, 
            paciente_nome, 
            paciente_convenio, 
            paciente_guia, 
            paciente_status, 
            paciente_especialidade, 
            paciente_mes, 
            paciente_section, 
            paciente_valor, 
            paciente_lote, 
            paciente_data_remessa, 
            DATE_FORMAT(data_hora_insercao, '%d/%m/%Y') AS data_formatada 
        FROM 
            pacientes 
        WHERE 1=1";

$params = [];
$types = "";

// Adicionar filtros à consulta
if (!empty($periodo_mes)) {
    $sql .= " AND paciente_mes = ?";
    $params[] = $periodo_mes;
    $types .= "s";
}

if (!empty($status_array)) {
    $placeholders = array_fill(0, count($status_array), '?');
    $sql .= " AND paciente_status IN (" . implode(',', $placeholders) . ")";
    foreach ($status_array as $status) {
        $params[] = $status;
        $types .= "s";
    }
}

if (!empty($convenio)) {
    $sql .= " AND paciente_convenio = ?";
    $params[] = $convenio;
    $types .= "s";
}

if (!empty($especialidade)) {
    $sql .= " AND paciente_especialidade = ?";
    $params[] = $especialidade;
    $types .= "s";
}

// Adicionar ordenação
$sql .= " ORDER BY 
    CASE 
        WHEN paciente_mes = 'Janeiro' THEN 1
        WHEN paciente_mes = 'Fevereiro' THEN 2
        WHEN paciente_mes = 'Março' THEN 3
        WHEN paciente_mes = 'Abril' THEN 4
        WHEN paciente_mes = 'Maio' THEN 5
        WHEN paciente_mes = 'Junho' THEN 6
        WHEN paciente_mes = 'Julho' THEN 7
        WHEN paciente_mes = 'Agosto' THEN 8
        WHEN paciente_mes = 'Setembro' THEN 9
        WHEN paciente_mes = 'Outubro' THEN 10
        WHEN paciente_mes = 'Novembro' THEN 11
        WHEN paciente_mes = 'Dezembro' THEN 12
    END, paciente_nome";

// Preparar e executar a consulta
$stmt = $conn->prepare($sql);

if (!empty($types)) {
    $stmt->bind_param($types, ...$params);
}

$stmt->execute();
$result = $stmt->get_result();
$dados = $result->fetch_all(MYSQLI_ASSOC);

// Fechar a conexão e libertar recursos
$stmt->close();
$conn->close();

// Definir título do relatório
$titulo_relatorio = "Relatório de Guias";

if (!empty($periodo_mes)) {
    $titulo_relatorio .= " - $periodo_mes";
}

if (!empty($status_array)) {
    $titulo_relatorio .= " - Status: " . implode(", ", $status_array);
}

if (!empty($convenio)) {
    $titulo_relatorio .= " - Convênio: $convenio";
}

if (!empty($especialidade)) {
    $titulo_relatorio .= " - Especialidade: $especialidade";
}

// Processar o formato de saída
if ($formato === 'excel') {
    // Limpar qualquer saída anterior
    require_once('../exports/excel_export.php');
    exportarParaExcel($dados, $titulo_relatorio, $filename);
} elseif ($formato === 'pdf') {
    require_once('../exports/pdf_export.php');
    exportarParaPDF($dados, $titulo_relatorio);
} else {
    // Formato HTML (visualização na tela)
    require_once('../views/visualizar_relatorio.php');
}
