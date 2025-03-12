// Arquivo: database/gerar_pdf.php
<?php
session_start();

if (!isset($_SESSION['login']) || !isset($_SESSION['relatorio_html'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

$html = $_SESSION['relatorio_html'];

// Configurar as opções do Dompdf
$options = new Options();
$options->set('isRemoteEnabled', true);
$dompdf = new Dompdf($options);

// Carregar o HTML
$dompdf->loadHtml($html);

// Definir o tamanho do papel e a orientação
$dompdf->setPaper('A4', 'portrait');

// Renderizar o PDF
$dompdf->render();

// Enviar o PDF para o navegador
$dompdf->stream("relatorio.pdf", array("Attachment" => 0));
exit();
?>