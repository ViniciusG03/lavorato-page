<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../src/login/login.php");
    exit();
}

$usuarioResponsavel = $_SESSION['login'];

$usuarios = [
    'admin' => 'Vinicius Oliveira',
    'talita' => 'Talita Ruiz',
    'gustavoramos' => 'Gustavo Ramos',
    'nicole' => 'Nicole Santos',
    'kaynnanduraes' => 'Kaynnan Durães'
];

$usuarioResponsavelFormatado = $usuarios[$usuarioResponsavel] ?? 'None';


require_once '../vendor/autoload.php';

use Dompdf\Dompdf;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $dataSelecionada = $_POST['data'];
    $status = isset($_POST['status']) ? $_POST['status'] : '';

    if (isset($_POST['especialidade'])) {
        $especialidadeSelecionada = $_POST['especialidade'];
    } else {
        $especialidadeSelecionada = 'todas';
    }

    if (isset($_POST['convenio'])) {
        $convenioSelecionado = $_POST['convenio'];
    } else {
        $convenioSelecionado = 'todos';
    }

    if (isset($_POST['mes'])) {
        $mesSelecionado = $_POST['mes'];
    } else {
        $mesSelecionado = 'todos';
    }

    if (isset($_POST['hora']) && preg_match('/^(0[0-9]|1[0-9]|2[0-3]):[0-5][0-9]$/', $_POST['hora'])) {
        $horaSelecionada = $_POST['hora'];
    } else {
        $horaSelecionada = null;
    }

    $dataFormatada = date('d/m/Y', strtotime($dataSelecionada));

    $tituloRelatorio = "<h1>Relatório</h1>";
    $horaDeGeração = date('H:i:s');
    $subtituloRelatorio = "<h3>Emitido por $usuarioResponsavelFormatado</h3>";

    $servername = "mysql.lavoratoguias.kinghost.net";
    $username = "lavoratoguias";
    $password = "A3g7K2m9T5p8L4v6";
    $database = "lavoratoguias";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $sql = "SELECT id, paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_especialidade, paciente_mes, paciente_section, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes WHERE DATE(data_hora_insercao) = ? AND usuario_responsavel = ?";

    if ($especialidadeSelecionada !== 'todas') {
        $sql .= " AND paciente_especialidade = ?";
    }

    if ($convenioSelecionado !== 'todos') {
        $sql .= " AND paciente_convenio = ?";
    }

    if ($mesSelecionado !== 'todos') {
        $sql .= " AND MONTH(data_hora_insercao) = ?";
    }

    if (!empty($status)) {
        $sql .= " AND paciente_status = ?";
    }

    if ($horaSelecionada !== null) {
        $sql .= " AND TIME(data_hora_insercao) >= ?";
    }

    if (isset($_POST['excluir_convenio'])) {
        $excluirConvenios = $_POST['excluir_convenio'];
        $excluirConveniosStr = "'" . implode("','", $excluirConvenios) . "'";
        $sql .= " AND paciente_convenio NOT IN ($excluirConveniosStr)";
    }

    $sql .= " ORDER BY paciente_nome";

    $stmt = $conn->prepare($sql);

    if ($stmt) {
        $bindValues = [$dataSelecionada, $usuarioResponsavel];

        $bindTypes = 'ss';

        if ($especialidadeSelecionada !== 'todas') {
            $bindValues[] = $especialidadeSelecionada;
            $bindTypes .= 's';
        }
        if ($convenioSelecionado !== 'todos') {
            $bindValues[] = $convenioSelecionado;
            $bindTypes .= 's';
        }
        if ($mesSelecionado !== 'todos') {
            $bindValues[] = $mesSelecionado;
            $bindTypes .= 's';
        }
        if (!empty($status)) {
            $bindValues[] = $status;
            $bindTypes .= 's';
        }

        if ($horaSelecionada !== null) {
            $bindValues[] = $horaSelecionada;
            $bindTypes .= 's';
        }

        $stmt->bind_param($bindTypes, ...$bindValues);

        $stmt->execute();

        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $tabelaHTML = "<table style='border-collapse: collapse; width: 100%;'>
                <thead>
                    <tr>
                        <th style='border: 1px solid black; padding: 5px;'>Nome</th>
                        <th style='border: 1px solid black; padding: 5px;'>Convênio</th>
                        <th style='border: 1px solid black; padding: 5px;'>Número</th>
                        <th style='border: 1px solid black; padding: 5px;'>Status</th>
                        <th style='border: 1px solid black; padding: 5px;'>Especialidade</th>
                        <th style='border: 1px solid black; padding: 5px;'>Mês</th>
                        <th style='border: 1px solid black; padding: 5px;'>Sessões</th>
                        <th style='border: 1px solid black; padding: 5px;'>Atualização</th>
                    </tr>
                </thead>
                <tbody>";

            while ($row = $result->fetch_assoc()) {
                $tabelaHTML .= "<tr>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_nome"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_convenio"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_guia"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_status"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_especialidade"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_mes"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["paciente_section"] . "</td>";
                $tabelaHTML .= "<td style='border: 1px solid black; padding: 5px;'>" . $row["data_hora_formatada"] . "</td>";
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
                    body {
                        margin: 10mm; 
                        font-size: 10pt; 
                    }
                    table {
                        border-collapse: collapse;
                        width: 100%;
                    }
                    th, td {
                        border: 1px solid black;
                        padding: 5px; 
                        text-align: center;
                    }
                </style>
            </head>
            <body>
                <h1>$tituloRelatorio</h1>
                <h2>Emitido por: $usuarioResponsavelFormatado</h2>
                <h2>Em $dataFormatada as $horaDeGeração</h2>
                $tabelaHTML
            </body>
            </html>";

    $dompdf->loadHtml($html);

    $dompdf->setPaper('A4', 'portrait');

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
    <link rel="shortcut icon" href="../src/assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../src/stylesheet/relatorio.css">
    <link rel="shortcut icon" href="../src/assets/Logo-Lavorato-alfa.png" type="image/x-icon">
</head>
<style>
    h1,
    h3 {
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
        <?php echo $tituloRelatorio; ?>
        <?php echo $subtituloRelatorio; ?>
        <?php echo isset($tabelaHTML) ? $tabelaHTML : "<p>Nenhum relatório gerado. Por favor, envie o formulário para gerar o relatório.</p>"; ?>
    </div>

    <script>
        const btnListar = document.getElementById('homeButton');
        btnListar.addEventListener('click', () => {
            window.location.href = '../src/index.php';
        });

        const btnGerarPDF = document.getElementById('gerarPDF');
        btnGerarPDF.addEventListener('click', () => {
            window.location.href = '<?php echo $_SERVER["PHP_SELF"]; ?>';
        });
    </script>
</body>

</html>