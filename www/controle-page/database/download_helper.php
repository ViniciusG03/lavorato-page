<?php
session_start();


if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}


if (isset($_GET['file'])) {
    $file = urldecode($_GET['file']);
    $filePath = "../controle-page/database/documents/" . $file;

    if (file_exists($filePath)) {
        header('Content-Description: File Transfer');
        header('Content-Type: application/octet-stream');
        header('Content-Disposition: attachment; filename="' . basename($filePath) . '"');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($filePath));
        readfile($filePath);
        exit;
    } else {
        echo "<p>Arquivo não encontrado.</p>";
    }
} else {
    echo "<p>Parâmetro 'file' não especificado.</p>";
}
?>