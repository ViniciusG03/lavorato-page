<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

$usuarioResponsavel = $_SESSION['login'];

$usuarios = [
    'admin' => 'Vinicius Oliveira',
    'talita' => 'Talita Ruiz',
    'gustavoramos' => 'Gustavo Ramos',
    'kaynnanduraes' => 'Kaynnan Durães',
    'eviny' => 'Eviny Santos',
    'tulio' => 'Tulio Uler',
    'will' => 'Williams Licar'
];

$usuarioResponsavelFormatado = $usuarios[$usuarioResponsavel] ?? 'None';


require_once '../vendor/autoload.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "mysql.lavoratoguias.kinghost.net";
    $username = "lavoratoguias";
    $password = "A3g7K2m9T5p8L4v6";
    $database = "lavoratoguias";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    // Se o formulário for para compartilhar um relatório
    if (isset($_POST['compartilhar_relatorio'])) {
        $relatorio_id = $_POST['relatorio_id'];
        $usuario_destino = $_POST['usuario_destino'];
        $observacao_compartilhamento = $_POST['observacao_compartilhamento'];
        $dados_relatorio = $_POST['dados_relatorio'];
        $titulo_relatorio = $_POST['titulo_relatorio'];

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

        // Redirecionar para a página inicial
        echo "<script>window.location.href = '../index.php';</script>";
        exit;
    } else {
        $observacao = $_POST["observacao"];
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

            // Verifique se há resultados e defina a variável $tabelaHTML
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
                $temRegistros = true;

                // Gerar um ID único para o relatório
                $relatorio_id = uniqid('rel_');

                // Reiniciar o cursor de resultados para processar novamente
                $result->data_seek(0);

                // Armazenar as guias associadas ao relatório
                while ($row = $result->fetch_assoc()) {
                    if (!empty($row["paciente_guia"])) {
                        $sql_guia = "INSERT INTO relatorios_guias (relatorio_id, paciente_guia, paciente_nome) VALUES (?, ?, ?)";
                        $stmt_guia = $conn->prepare($sql_guia);
                        $stmt_guia->bind_param("sss", $relatorio_id, $row["paciente_guia"], $row["paciente_nome"]);
                        $stmt_guia->execute();
                        $stmt_guia->close();
                    }
                }
            } else {
                // Definir uma mensagem amigável quando não há registros
                $tabelaHTML = "<div style='text-align: center; padding: 20px; background-color: #f8f9fa; border-radius: 8px; margin: 20px 0;'>
                    <h2 style='color: #6c757d; margin-bottom: 10px;'>Nenhum registro encontrado</h2>
                    <p style='color: #6c757d;'>Não foram encontrados registros correspondentes aos critérios de busca selecionados.</p>
                    <p style='color: #6c757d;'>Tente modificar os filtros ou selecionar outro período.</p>
                </div>";
                $temRegistros = false;

                // Como não há registros, vamos gerar um ID único mesmo assim para manter consistência
                $relatorio_id = uniqid('rel_empty_');
            }

            $stmt->close();
        } else {
            echo "Erro na preparação da consulta: " . $conn->error;
            exit;
        }

        // Código HTML para o relatório
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
                    .title {
                        text-align: center;
                    }
                    .header-table {
                        width: 100%;
                        border-collapse: collapse;
                    }
                    .header-table td {
                        width: 50%;
                        vertical-align: middle;
                        font-size: 9pt; 
                    }
                    .header-table td:first-child {
                        text-align: left;
                    }
                    .header-table td:last-child {
                        text-align: right;
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
                    p {
                        font-size: 12pt; 
                    }
                </style>
            </head>
            <body>
                <h1 style='font-size: 12pt;' class='title'>$tituloRelatorio</h1>
                <table class='header-table'>
                    <tr>
                        <td>Emitido por: $usuarioResponsavelFormatado</td>
                        <td>em $dataFormatada às $horaDeGeração</td>
                    </tr>
                </table>
                <p>Observação: $observacao</p>
                $tabelaHTML
            </body>
            </html>";

        // Armazenar HTML na sessão
        $_SESSION['relatorio_html'] = $html;
        $_SESSION['tem_registros'] = $temRegistros;

        // Em vez de enviar o PDF diretamente, exibir a página com o PDF incorporado em um iframe
        echo '<!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Relatório</title>
            <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="../stylesheet/relatorio.css">
            <style>
                .pdf-container {
                    width: 100%;
                    height: 600px;
                    border: 1px solid #ddd;
                    margin-bottom: 20px;
                }
                .message-container {
                    width: 100%;
                    background-color: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 8px;
                    padding: 20px;
                    margin-bottom: 20px;
                    text-align: center;
                }
                .compartilhar-panel {
                    background-color: #00b3ffde;
                    padding: 15px;
                    border-radius: 8px;
                    margin-top: 20px;
                    color: white;
                }
                .form-group {
                    margin-bottom: 15px;
                }
                .form-group label {
                    display: block;
                    margin-bottom: 5px;
                }
                .form-group select, .form-group textarea {
                    width: 100%;
                    padding: 8px;
                    border: 1px solid #ccc;
                    border-radius: 4px;
                }
                .btn-primary {
                    background-color: #0099cc;
                    border: none;
                    padding: 8px 15px;
                    color: white;
                    border-radius: 4px;
                    cursor: pointer;
                }
            </style>
        </head>
        <body>
            <div class="nav">
                <button id="homeButton">Home</button>
                ';
        if ($temRegistros) {
            echo '<button id="gerarPDF">PDF Completo</button>';
        }
        echo '
                <h1>Lavorato\'s System</h1>
            </div>
            <div class="container">';

        if ($temRegistros) {
            // Se tem registros, mostra o iframe com o PDF
            echo '<iframe src="gerar_pdf.php" class="pdf-container"></iframe>';

            // Painel de compartilhamento apenas se tem registros
            echo '<div class="compartilhar-panel">
                        <h3>Compartilhar Relatório</h3>
                        <form action="' . $_SERVER["PHP_SELF"] . '" method="post">
                            <input type="hidden" name="relatorio_id" value="' . $relatorio_id . '">
                            <input type="hidden" name="titulo_relatorio" value="Relatório de ' . $usuarioResponsavelFormatado . ' - ' . $dataFormatada . '">
                            <input type="hidden" name="dados_relatorio" value="' . base64_encode($html) . '">
                            
                            <div class="form-group">
                                <label for="usuario_destino">Compartilhar com:</label>
                                <select name="usuario_destino" id="usuario_destino" class="form-control" required>';

            foreach ($usuarios as $login => $nome) {
                if ($login != $usuarioResponsavel) { // Não mostrar o usuário atual na lista
                    echo '<option value="' . $login . '">' . $nome . '</option>';
                }
            }

            echo '</select>
                            </div>
                            
                            <div class="form-group">
                                <label for="observacao_compartilhamento">Observação:</label>
                                <textarea name="observacao_compartilhamento" id="observacao_compartilhamento" class="form-control" rows="3"></textarea>
                            </div>
                            
                            <div class="form-group">
                                <button type="submit" name="compartilhar_relatorio" class="btn btn-primary">Compartilhar</button>
                            </div>
                        </form>
                    </div>';
        } else {
            // Se não tem registros, mostra a mensagem diretamente
            echo '<div class="message-container">
                        <h2 style="color: #6c757d; margin-bottom: 10px;">Nenhum registro encontrado</h2>
                        <p style="color: #6c757d;">Não foram encontrados registros correspondentes aos critérios de busca selecionados.</p>
                        <p style="color: #6c757d;">Tente modificar os filtros ou selecionar outro período.</p>
                        <button class="btn btn-primary mt-3" id="voltarButton">Voltar para os filtros</button>
                    </div>';
        }

        // Mostrar notificações de relatórios compartilhados
        echo '<div class="notifications mt-4">
                    <h3 style="color: #333;">Notificações de Relatórios</h3>';

        // Consultar relatórios compartilhados com o usuário atual
        $sql_notificacoes = "SELECT * FROM relatorios_compartilhados WHERE usuario_destino = ? AND status = 'pendente' ORDER BY data_compartilhamento DESC LIMIT 5";
        $stmt_notificacoes = $conn->prepare($sql_notificacoes);
        $stmt_notificacoes->bind_param("s", $usuarioResponsavel);
        $stmt_notificacoes->execute();
        $result_notificacoes = $stmt_notificacoes->get_result();

        if ($result_notificacoes->num_rows > 0) {
            while ($notificacao = $result_notificacoes->fetch_assoc()) {
                echo '<div class="alert alert-warning">
                                <p><strong>Relatório:</strong> ' . htmlspecialchars($notificacao['titulo']) . '</p>
                                <p><strong>De:</strong> ' . $usuarios[$notificacao['usuario_origem']] . '</p>
                                <p><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($notificacao['data_compartilhamento'])) . '</p>';

                if ($notificacao['observacao']) {
                    echo '<p><strong>Observação:</strong> ' . htmlspecialchars($notificacao['observacao']) . '</p>';
                }

                echo '<div class="mt-2">
                                <a href="../views/relatorios_compartilhados.php" class="btn btn-sm btn-info">Ver Todos</a>
                            </div>
                            </div>';
            }
        } else {
            echo '<p>Você não tem notificações de relatórios pendentes.</p>';
        }

        echo '</div>
            </div>

            <script>
                const btnListar = document.getElementById("homeButton");
                btnListar.addEventListener("click", () => {
                    window.location.href = "../index.php";
                });';

        if ($temRegistros) {
            echo 'const btnGerarPDF = document.getElementById("gerarPDF");
                    btnGerarPDF.addEventListener("click", () => {
                        window.location.href = "gerar_pdf.php";
                    });';
        } else {
            echo 'const btnVoltar = document.getElementById("voltarButton");
                    btnVoltar.addEventListener("click", () => {
                        window.history.back();
                    });';
        }

        echo '</script>
        </body>
        </html>';

        // Fechar conexão aqui
        $conn->close();
        exit;
    }
}
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatório</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/relatorio.css">
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon">
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
        <?php echo isset($tituloRelatorio) ? $tituloRelatorio : "<h1>Relatório</h1>"; ?>
        <?php echo isset($subtituloRelatorio) ? $subtituloRelatorio : "<h3>Sistema Lavorato</h3>"; ?>
        <?php echo isset($tabelaHTML) ? $tabelaHTML : "<p>Nenhum relatório gerado. Por favor, envie o formulário para gerar o relatório.</p>"; ?>
    </div>

    <script>
        const btnListar = document.getElementById('homeButton');
        btnListar.addEventListener('click', () => {
            window.location.href = '../index.php';
        });

        const btnGerarPDF = document.getElementById('gerarPDF');
        btnGerarPDF.addEventListener('click', () => {
            window.location.href = '<?php echo $_SERVER["PHP_SELF"]; ?>';
        });
    </script>
</body>

</html>