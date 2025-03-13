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
            <title>Relatório - Lavorato System</title>
            <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon">
            <link rel="preconnect" href="https://fonts.googleapis.com">
            <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
            <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
            <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
            <link rel="stylesheet" href="../stylesheet/base.css">
            <style>
                :root {
                    --main-bg-color: #b9d7d9;
                    --main-color-btn: #00b3ffde;
                    --main-nav-color: #00b3ffde;
                }
                
                body {
                    background-color: var(--main-bg-color);
                    font-family: "Poppins", sans-serif;
                    min-height: 100vh;
                    display: flex;
                    flex-direction: column;
                }
                
                .navbar {
                    background-color: var(--main-nav-color) !important;
                    box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
                }
                
                .page-header {
                    background-color: #fff;
                    border-radius: 0.5rem;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                    margin-bottom: 1.5rem;
                    padding: 1.5rem;
                }
                
                .btn-primary {
                    background-color: var(--main-color-btn);
                    border-color: var(--main-color-btn);
                }
                
                .btn-primary:hover {
                    background-color: #0099cc;
                    border-color: #0099cc;
                }
                
                .btn-outline-primary {
                    color: var(--main-color-btn);
                    border-color: var(--main-color-btn);
                }
                
                .btn-outline-primary:hover {
                    background-color: var(--main-color-btn);
                    border-color: var(--main-color-btn);
                }
                
                .pdf-container {
                    width: 100%;
                    height: 600px;
                    border: 1px solid #ddd;
                    border-radius: 0.5rem;
                    margin-bottom: 1.5rem;
                    background-color: #fff;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                
                .card {
                    border: none;
                    border-radius: 0.5rem;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                    margin-bottom: 1.5rem;
                }
                
                .card-header {
                    background-color: var(--main-color-btn);
                    color: white;
                    border-radius: 0.5rem 0.5rem 0 0 !important;
                    padding: 1rem 1.25rem;
                }
                
                .message-container {
                    width: 100%;
                    background-color: #f8f9fa;
                    border: 1px solid #ddd;
                    border-radius: 0.5rem;
                    padding: 2rem;
                    margin-bottom: 1.5rem;
                    text-align: center;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                
                .compartilhar-panel {
                    background-color: white;
                    padding: 1.5rem;
                    border-radius: 0.5rem;
                    margin-top: 1.5rem;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                
                .compartilhar-panel .card-header {
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .form-group {
                    margin-bottom: 1rem;
                }
                
                .notifications {
                    background-color: white;
                    border-radius: 0.5rem;
                    padding: 1.5rem;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.075);
                }
                
                .notifications h3 {
                    color: var(--main-color-btn);
                    font-size: 1.25rem;
                    margin-bottom: 1rem;
                    display: flex;
                    align-items: center;
                    gap: 0.5rem;
                }
                
                .alert {
                    border-left: 4px solid #ffc107;
                    background-color: #fff8e1;
                    box-shadow: 0 0.125rem 0.25rem rgba(0, 0, 0, 0.05);
                }
                
                .footer {
                    margin-top: auto;
                    background-color: var(--main-nav-color);
                    color: white;
                    padding: 1rem 0;
                }
                
                @media (max-width: 768px) {
                    .pdf-container {
                        height: 400px;
                    }
                    
                    .page-header {
                        padding: 1rem;
                    }
                }
            </style>
        </head>
        <body>
            <nav class="navbar navbar-expand-lg navbar-dark">
                <div class="container">
                    <a class="navbar-brand d-flex align-items-center" href="#">
                        Lavorato System
                    </a>
                    <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" 
                        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                        <span class="navbar-toggler-icon"></span>
                    </button>
                    <div class="collapse navbar-collapse" id="navbarNav">
                        <ul class="navbar-nav ms-auto">
                            <li class="nav-item">
                                <a class="nav-link" href="../index.php">
                                    <i class="fas fa-home me-1"></i> Home
                                </a>
                            </li>';
                            
        if ($temRegistros) {
            echo '<li class="nav-item">
                    <a class="nav-link" href="gerar_pdf.php" id="gerarPDF">
                        <i class="fas fa-file-pdf me-1"></i> PDF Completo
                    </a>
                </li>';
        }
        
        echo '</ul>
                    </div>
                </div>
            </nav>

            <div class="container my-4">
                <div class="page-header">
                    <div class="d-flex justify-content-between align-items-center">
                        <div>
                            <h1 class="h3 mb-0">Visualização de Relatório</h1>
                            <p class="mb-0 text-muted">Gerado em ' . $dataFormatada . ' às ' . $horaDeGeração . ' por ' . $usuarioResponsavelFormatado . '</p>
                        </div>
                        <div>';
                        
        if ($temRegistros) {
            echo '<a href="gerar_pdf.php" class="btn btn-outline-primary">
                    <i class="fas fa-download me-1"></i> Baixar PDF
                </a>';
        }
        
        echo '</div>
                    </div>
                </div>';

        if ($temRegistros) {
            // Se tem registros, mostra o iframe com o PDF
            echo '<div class="card">
                    <div class="card-header">
                        <i class="fas fa-file-alt me-2"></i> Relatório
                    </div>
                    <div class="card-body p-0">
                        <iframe src="gerar_pdf.php" class="pdf-container"></iframe>
                    </div>
                </div>';

            // Painel de compartilhamento apenas se tem registros
            echo '<div class="card compartilhar-panel">
                    <div class="card-header">
                        <i class="fas fa-share-alt me-2"></i> Compartilhar Relatório
                    </div>
                    <div class="card-body">
                        <form action="' . $_SERVER["PHP_SELF"] . '" method="post">
                            <input type="hidden" name="relatorio_id" value="' . $relatorio_id . '">
                            <input type="hidden" name="titulo_relatorio" value="Relatório de ' . $usuarioResponsavelFormatado . ' - ' . $dataFormatada . '">
                            <input type="hidden" name="dados_relatorio" value="' . base64_encode($html) . '">
                            
                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="usuario_destino" class="form-label">Compartilhar com:</label>
                                        <select name="usuario_destino" id="usuario_destino" class="form-select" required>';

            foreach ($usuarios as $login => $nome) {
                if ($login != $usuarioResponsavel) { // Não mostrar o usuário atual na lista
                    echo '<option value="' . $login . '">' . $nome . '</option>';
                }
            }

            echo '</select>
                                    </div>
                                </div>
                                
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <label for="observacao_compartilhamento" class="form-label">Observação:</label>
                                        <textarea name="observacao_compartilhamento" id="observacao_compartilhamento" class="form-control" rows="1"></textarea>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="form-group mt-3">
                                <button type="submit" name="compartilhar_relatorio" class="btn btn-primary">
                                    <i class="fas fa-share me-1"></i> Compartilhar
                                </button>
                            </div>
                        </form>
                    </div>
                </div>';
        } else {
            // Se não tem registros, mostra a mensagem diretamente
            echo '<div class="message-container">
                    <div class="text-center mb-4">
                        <i class="fas fa-search fa-3x text-muted mb-3"></i>
                        <h2 class="h4" style="color: #6c757d; margin-bottom: 10px;">Nenhum registro encontrado</h2>
                        <p style="color: #6c757d;">Não foram encontrados registros correspondentes aos critérios de busca selecionados.</p>
                        <p style="color: #6c757d;">Tente modificar os filtros ou selecionar outro período.</p>
                    </div>
                    <button class="btn btn-primary mt-3" id="voltarButton">
                        <i class="fas fa-arrow-left me-1"></i> Voltar para os filtros
                    </button>
                </div>';
        }

        // Mostrar notificações de relatórios compartilhados
        echo '<div class="notifications mt-4">
                <h3><i class="fas fa-bell me-2"></i> Notificações de Relatórios</h3>';

        // Consultar relatórios compartilhados com o usuário atual
        $sql_notificacoes = "SELECT * FROM relatorios_compartilhados WHERE usuario_destino = ? AND status = 'pendente' ORDER BY data_compartilhamento DESC LIMIT 5";
        $stmt_notificacoes = $conn->prepare($sql_notificacoes);
        $stmt_notificacoes->bind_param("s", $usuarioResponsavel);
        $stmt_notificacoes->execute();
        $result_notificacoes = $stmt_notificacoes->get_result();

        if ($result_notificacoes->num_rows > 0) {
            while ($notificacao = $result_notificacoes->fetch_assoc()) {
                echo '<div class="alert alert-warning">
                        <div class="d-flex justify-content-between align-items-center">
                            <div>
                                <h5 class="alert-heading">' . htmlspecialchars($notificacao['titulo']) . '</h5>
                                <p class="mb-1"><strong>De:</strong> ' . $usuarios[$notificacao['usuario_origem']] . '</p>
                                <p class="mb-1"><strong>Data:</strong> ' . date('d/m/Y H:i', strtotime($notificacao['data_compartilhamento'])) . '</p>';

                if ($notificacao['observacao']) {
                    echo '<p class="mb-0"><strong>Observação:</strong> ' . htmlspecialchars($notificacao['observacao']) . '</p>';
                }

                echo '</div>
                            <div>
                                <a href="../views/relatorios_compartilhados.php" class="btn btn-sm btn-info">
                                    <i class="fas fa-eye me-1"></i> Visualizar
                                </a>
                            </div>
                        </div>
                    </div>';
            }
            
            echo '<div class="text-center mt-3">
                    <a href="../views/relatorios_compartilhados.php" class="btn btn-outline-primary">
                        <i class="fas fa-list me-1"></i> Ver Todos os Relatórios
                    </a>
                </div>';
        } else {
            echo '<div class="alert alert-info">
                    <div class="d-flex align-items-center">
                        <i class="fas fa-info-circle me-3 fa-lg"></i>
                        <p class="mb-0">Você não tem notificações de relatórios pendentes.</p>
                    </div>
                </div>';
        }

        echo '</div>
            </div>

            <footer class="footer">
                <div class="container text-center">
                    <p class="mb-0">© 2025 Lavorato Tech. Todos os direitos reservados.</p>
                </div>
            </footer>

            <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
            <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
            <script>
                document.addEventListener("DOMContentLoaded", function() {';

        if (!$temRegistros) {
            echo 'const btnVoltar = document.getElementById("voltarButton");
                    if (btnVoltar) {
                        btnVoltar.addEventListener("click", () => {
                            window.history.back();
                        });
                    }';
        }

        echo '});
            </script>
        </body>
        </html>';

        // Fechar conexão aqui
        $conn->close();
        exit;
    }
}
?>