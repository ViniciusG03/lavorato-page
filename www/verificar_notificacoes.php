<?php
// Arquivo: verificar_notificacoes.php
session_start();

if (!isset($_SESSION['login'])) {
    // Retornar erro em formato JSON
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Usuário não autenticado']);
    exit();
}

$usuarioResponsavel = $_SESSION['login'];

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    header('Content-Type: application/json');
    echo json_encode(['erro' => 'Erro na conexão com o banco de dados']);
    exit();
}

// Consultar notificações pendentes
$sql = "SELECT COUNT(*) as quantidade, MAX(data_compartilhamento) as ultima_data FROM relatorios_compartilhados 
        WHERE usuario_destino = ? AND status = 'pendente'";
$stmt = $conn->prepare($sql);
$stmt->bind_param("s", $usuarioResponsavel);
$stmt->execute();
$result = $stmt->get_result();
$row = $result->fetch_assoc();

// Verificar se há novas notificações desde a última verificação
$novasNotificacoes = false;
if (isset($_SESSION['ultima_verificacao_notificacoes']) && !empty($row['ultima_data'])) {
    $ultimaVerificacao = new DateTime($_SESSION['ultima_verificacao_notificacoes']);
    $ultimaNotificacao = new DateTime($row['ultima_data']);

    if ($ultimaNotificacao > $ultimaVerificacao) {
        $novasNotificacoes = true;
    }
}

// Atualizar timestamp da última verificação
$_SESSION['ultima_verificacao_notificacoes'] = date('Y-m-d H:i:s');

// Preparar resposta
$resposta = [
    'temNovos' => ($row['quantidade'] > 0),
    'quantidade' => $row['quantidade'],
    'novasNotificacoes' => $novasNotificacoes
];

// Enviar resposta em formato JSON
header('Content-Type: application/json');
echo json_encode($resposta);

$stmt->close();
$conn->close();
