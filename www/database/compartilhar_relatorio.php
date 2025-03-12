// Arquivo: database/compartilhar_relatorio.php
<?php
session_start();

if (!isset($_SESSION['login']) || !isset($_SESSION['relatorio_html'])) {
    header("Location: ../login/login.php");
    exit();
}

$usuarioResponsavel = $_SESSION['login'];
$html = $_SESSION['relatorio_html'];

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['compartilhar_relatorio'])) {
    $relatorio_id = $_POST['relatorio_id'];
    $usuario_destino = $_POST['usuario_destino'];
    $observacao_compartilhamento = $_POST['observacao_compartilhamento'];
    $titulo_relatorio = $_POST['titulo_relatorio'];
    $dados_relatorio = base64_encode($html);

    // Inserir o compartilhamento na tabela
    $sql = "INSERT INTO relatorios_compartilhados 
            (relatorio_id, usuario_origem, usuario_destino, data_compartilhamento, status, observacao, dados_relatorio, titulo) 
            VALUES (?, ?, ?, NOW(), 'pendente', ?, ?, ?)";

    $stmt = $conn->prepare($sql);
    $stmt->bind_param("ssssss", $relatorio_id, $usuarioResponsavel, $usuario_destino, $observacao_compartilhamento, $dados_relatorio, $titulo_relatorio);

    if ($stmt->execute()) {
        echo "<script>alert('Relatório compartilhado com sucesso!');</script>";
    } else {
        echo "<script>alert('Erro ao compartilhar o relatório: " . $stmt->error . "');</script>";
    }

    $stmt->close();
    $conn->close();
}

// Redirecionar para a página inicial
echo "<script>window.location.href = '../index.php';</script>";
exit;
?>