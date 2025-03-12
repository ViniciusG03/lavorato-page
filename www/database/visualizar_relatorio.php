<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

require_once '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Verifica se recebeu os dados do relatório via POST
if (isset($_POST['relatorio_html'])) {
    $html = base64_decode($_POST['relatorio_html']);

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
    $dompdf->stream("relatorio_compartilhado.pdf", array("Attachment" => false));
    exit();
} else {
    echo "Erro: Nenhum relatório para visualizar.";
    echo "<p><a href='../index.php'>Voltar para a página inicial</a></p>";
}
