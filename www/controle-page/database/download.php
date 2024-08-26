<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}


if (isset($_GET['documento'])) {
    $documento = $_GET['documento'];
    $uploadDir = "../controle-page/database/documents/";

    if (isset($_SESSION['download_file']) && file_exists($_SESSION['download_file'])) {
        $filePath = $_SESSION['download_file'];
        $fileName = basename($filePath);

        $downloadUrl = "download_helper.php?file=" . urlencode($fileName);


        switch ($documento) {
            case "RG":
                $redirectUrl = "https://drive.google.com/drive/u/1/folders/1KCT5rULPHRPswa5HffhUhtnfoJIfoB5z";
                break;
            case "CPF":
                $redirectUrl = "https://drive.google.com/drive/u/1/folders/1MaYCk7Xop7RLHSG3cyLlbmlQ-OCzCFQA";
                break;
            case "LD":
                $redirectUrl = "https://drive.google.com/drive/u/1/folders/1hz1E-sIeRS_7Hykwg8LBQywtmkCp7r4G";
                break;
            case "PM":
                $redirectUrl = "https://drive.google.com/drive/u/1/folders/1fmumaZvHgWGjHR7xdzyu_Fzk9Gx06E0_";
                break;
            case "PT":
                $redirectUrl = "https://drive.google.com/drive/u/1/folders/1G8WhmHTANhZ8OzN0bnSdb48t8nLgorb8";
                break;
            default:
                echo "<p>Documento inválido.</p>";
                exit;
        }


        echo "<!DOCTYPE html>
        <html>
        <head>
            <meta http-equiv='refresh' content='0;url=$redirectUrl'>
            <script>
                window.onload = function() {
                    window.location.href = '$redirectUrl';
                    var a = document.createElement('a');
                    a.href = '$downloadUrl';
                    a.download = '$fileName';
                    document.body.appendChild(a);
                    a.click();
                    document.body.removeChild(a);
                }
            </script>
        </head>
        <body>
        </body>
        </html>";
    } else {
        echo "<p>Arquivo para download não encontrado.</p>";
    }
} else {
    echo "<p>Parâmetro 'documento' não especificado.</p>";
}
?>