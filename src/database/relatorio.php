<?php
require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dataSelecionada = $_POST['data'];
    
    if (isset($_POST['especialidade'])) {
        $especialidadeSelecionada = $_POST['especialidade'];
    } else {
        $especialidadeSelecionada = 'todas'; 
    }

    $dataFormatada = date('d/m/Y', strtotime($dataSelecionada));
    $dataAtual = date('Y-m-d');
    $dataAtualFormatada = date('d/m/Y', strtotime($dataAtual)); 

    $tituloRelatorio = "<h1>Relatório de Guias Emitidas</h1>";
    $subtituloRelatorio = "<h3>Data: $dataAtualFormatada a $dataFormatada</h3>";

    $servername = "localhost";
    $username = "root";
    $password = "lavorato@admin2024";
    $database = "lavoratoDB";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_especialidade, paciente_mes, paciente_section, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes WHERE DATE(data_hora_insercao) = ?";

    if ($especialidadeSelecionada !== 'todas') {
        $sql .= " AND paciente_especialidade = ?";
    }

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        if ($especialidadeSelecionada !== 'todas') {
            $stmt->bind_param("ss", $dataAtual, $especialidadeSelecionada);
        } else {
            $stmt->bind_param("s", $dataAtual);
        }
    
        $stmt->execute();
    
        $result = $stmt->get_result();
    
        if ($result->num_rows > 0) {
            $tabelaHTML = "<table style='border-collapse: collapse; border: 1px solid black;'>
                <thead>
                    <tr>
                        <th style='border: 1px solid black;'>Nome</th>
                        <th style='border: 1px solid black;'>Convênio</th>
                        <th style='border: 1px solid black;'>Número</th>
                        <th style='border: 1px solid black;'>Status</th>
                        <th style='border: 1px solid black;'>Especialidade</th>
                        <th style='border: 1px solid black;'>Mês</th>
                        <th style='border: 1px solid black;'>Sessões</th>
                        <th style='border: 1px solid black;'>Atualização</th>
                    </tr>
                </thead>
                <tbody>";

        while ($row = $result->fetch_assoc()) {
            $tabelaHTML .= "<tr>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_nome"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_convenio"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_guia"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_status"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_especialidade"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_mes"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["paciente_section"] . "</td>";
            $tabelaHTML .= "<td style='border: 1px solid black;'>" . $row["data_hora_formatada"] . "</td>";
            $tabelaHTML .= "</tr>";
        }

        $tabelaHTML .= "</tbody></table>";
        } else {
            echo '<h1>Nenhum registro encontrado!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
        }

        $stmt->close();
    } else {
        echo "Erro na preparação da consulta: " . $conn->error;
    }

    $dompdf = new Dompdf();

    $html = "<!DOCTYPE html>
            <html lang='pt-BR'>
            <head>
                <meta charset='UTF-8'>
                <title>Relatório</title>
                <style>
                    h1, h3 {
                        color: black;
                        margin-bottom: 10px;
                        text-align: center;
                    }

                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                
                    th, td {
                        border: 1px solid black;
                        padding: 8px;
                        text-align: center; 
                        vertical-align: middle; 
                    }
                </style>
            </head>
            <body>
                <h1>$tituloRelatorio</h1>
                <h3>$subtituloRelatorio</h3>
                $tabelaHTML
            </body>
            </html>";

    $dompdf->loadHtml($html); 

    $dompdf->render();

    $dompdf->stream('relatorio.pdf', array('Attachment' => 0));

    $conn->close();
} else {
    header("Location: index.html");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link
      rel="shortcut icon"
      href="../src/assets/Logo-Lavorato-alfa.png"
      type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/relatorio.css">
</head>
<style>
    h1, h3 {
        color: white;
        margin-bottom: 10px;
        text-align: center;
    }
</style>
<body>
    <div class="nav">
        <button id="homeButton">Home</button>
        <button id="gerarPDF">PDF</button>
        <h1>Lavorato's System</h1>
    </div>
    <div class="container">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            echo $tituloRelatorio;
            echo $subtituloRelatorio;
            echo $tabelaHTML;
        } else {
            echo "<p>Nenhum relatório gerado. Por favor, envie o formulário para gerar o relatório.</p>";
        }
        ?>
    </div>

    <script>
        const btnListar = document.getElementById('homeButton');
        btnListar.addEventListener('click', () => {
            window.location.href = '../index.html';
        });

        const btnGerarPDF = document.getElementById('gerarPDF');
        btnGerarPDF.addEventListener('click', () => {
            window.location.href = '<?php echo $_SERVER["PHP_SELF"]; ?>';
        });
    </script>
</body>
</html>


