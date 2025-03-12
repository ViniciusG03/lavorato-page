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

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Processar ações se houver
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_POST['confirmar_relatorio'])) {
        $compartilhamento_id = $_POST['compartilhamento_id'];
        $status = $_POST['status']; // 'confirmado' ou 'rejeitado'
        $observacao_resposta = isset($_POST['observacao_resposta']) ? $_POST['observacao_resposta'] : '';

        $sql = "UPDATE relatorios_compartilhados SET status = ?, observacao_resposta = ?, data_confirmacao = NOW() WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("ssi", $status, $observacao_resposta, $compartilhamento_id);

        if ($stmt->execute()) {
            $mensagem = "Status do relatório atualizado com sucesso!";
            $tipo_mensagem = "success";
        } else {
            $mensagem = "Erro ao atualizar o status do relatório: " . $stmt->error;
            $tipo_mensagem = "danger";
        }

        $stmt->close();
    } elseif (isset($_POST['excluir_relatorio'])) {
        $compartilhamento_id = $_POST['compartilhamento_id'];

        // Verificar primeiro se o usuário é o remetente do relatório
        $check_sql = "SELECT usuario_origem FROM relatorios_compartilhados WHERE id = ?";
        $check_stmt = $conn->prepare($check_sql);
        $check_stmt->bind_param("i", $compartilhamento_id);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        $row = $check_result->fetch_assoc();
        $check_stmt->close();

        if ($row && $row['usuario_origem'] == $usuarioResponsavel) {
            // Apenas o remetente pode excluir
            $sql = "DELETE FROM relatorios_compartilhados WHERE id = ? AND usuario_origem = ?";
            $stmt = $conn->prepare($sql);
            $stmt->bind_param("is", $compartilhamento_id, $usuarioResponsavel);

            if ($stmt->execute()) {
                $mensagem = "Relatório excluído com sucesso!";
                $tipo_mensagem = "success";
            } else {
                $mensagem = "Erro ao excluir relatório: " . $stmt->error;
                $tipo_mensagem = "danger";
            }

            $stmt->close();
        } else {
            $mensagem = "Você não tem permissão para excluir este relatório.";
            $tipo_mensagem = "danger";
        }
    }
}

// Função para formatar status de forma mais amigável
function formatarStatus($status)
{
    switch ($status) {
        case 'pendente':
            return '<span class="badge bg-warning text-dark">Pendente</span>';
        case 'confirmado':
            return '<span class="badge bg-success">Confirmado</span>';
        case 'rejeitado':
            return '<span class="badge bg-danger">Rejeitado</span>';
        default:
            return '<span class="badge bg-secondary">Desconhecido</span>';
    }
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Compartilhados</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <style>
        :root {
            --main-bg-color: #b9d7d9;
            --main-color-btn: #00b3ffde;
            --main-nav-color: #00b3ffde;
        }

        body {
            background-color: var(--main-bg-color);
            font-family: "Poppins", sans-serif;
        }

        .container {
            padding-top: 20px;
            padding-bottom: 20px;
        }

        .card {
            margin-bottom: 20px;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        }

        .card-header {
            background-color: var(--main-color-btn);
            color: white;
            font-weight: 500;
            border-top-left-radius: 8px;
            border-top-right-radius: 8px;
        }

        .btn-primary {
            background-color: var(--main-color-btn);
            border-color: var(--main-color-btn);
        }

        .btn-primary:hover {
            background-color: #0099cc;
            border-color: #0099cc;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--main-color-btn);
            color: white;
            border-color: var(--main-color-btn);
        }

        .nav-tabs .nav-link {
            color: #333;
        }

        .table th {
            background-color: var(--main-color-btn);
            color: white;
        }

        .alert {
            border-radius: 8px;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Lavorato's System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
                aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Relatórios Compartilhados</h1>

        <?php if (isset($mensagem)): ?>
            <div class="alert alert-<?php echo $tipo_mensagem; ?> alert-dismissible fade show" role="alert">
                <?php echo $mensagem; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        <?php endif; ?>

        <ul class="nav nav-tabs mb-4" id="myTab" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="recebidos-tab" data-bs-toggle="tab" data-bs-target="#recebidos" type="button"
                    role="tab" aria-controls="recebidos" aria-selected="true">Recebidos</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="enviados-tab" data-bs-toggle="tab" data-bs-target="#enviados" type="button"
                    role="tab" aria-controls="enviados" aria-selected="false">Enviados</button>
            </li>
        </ul>

        <div class="tab-content" id="myTabContent">
            <!-- Aba de relatórios recebidos -->
            <div class="tab-pane fade show active" id="recebidos" role="tabpanel" aria-labelledby="recebidos-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Relatórios Recebidos</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>De</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Observação</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consultar relatórios recebidos pelo usuário
                                    $sql = "SELECT * FROM relatorios_compartilhados WHERE usuario_destino = ? ORDER BY data_compartilhamento DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $usuarioResponsavel);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($row['titulo']) . '</td>';
                                            echo '<td>' . $usuarios[$row['usuario_origem']] . '</td>';
                                            echo '<td>' . date('d/m/Y H:i', strtotime($row['data_compartilhamento'])) . '</td>';
                                            echo '<td>' . formatarStatus($row['status']) . '</td>';
                                            echo '<td>' . (empty($row['observacao']) ? '<em>Sem observação</em>' : htmlspecialchars($row['observacao'])) . '</td>';
                                            echo '<td>';

                                            // Botão para visualizar
                                            echo '<form action="../database/visualizar_relatorio.php" method="post" target="_blank" class="d-inline-block me-1">';
                                            echo '<input type="hidden" name="relatorio_html" value="' . $row['dados_relatorio'] . '">';
                                            echo '<button type="submit" class="btn btn-sm btn-info">Visualizar</button>';
                                            echo '</form>';

                                            // Botões de confirmação/rejeição (apenas para pendentes)
                                            if ($row['status'] == 'pendente') {
                                                echo '<div class="mt-2">';
                                                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" class="d-inline-block me-1">';
                                                echo '<input type="hidden" name="compartilhamento_id" value="' . $row['id'] . '">';
                                                echo '<input type="hidden" name="status" value="confirmado">';
                                                echo '<div class="mb-2">';
                                                echo '<input type="text" name="observacao_resposta" class="form-control form-control-sm" placeholder="Observação (opcional)">';
                                                echo '</div>';
                                                echo '<button type="submit" name="confirmar_relatorio" class="btn btn-sm btn-success">Confirmar</button>';
                                                echo '</form>';

                                                echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" class="d-inline-block me-1">';
                                                echo '<input type="hidden" name="compartilhamento_id" value="' . $row['id'] . '">';
                                                echo '<input type="hidden" name="status" value="rejeitado">';
                                                echo '<div class="mb-2">';
                                                echo '<input type="text" name="observacao_resposta" class="form-control form-control-sm" placeholder="Motivo da rejeição (opcional)">';
                                                echo '</div>';
                                                echo '<button type="submit" name="confirmar_relatorio" class="btn btn-sm btn-danger">Rejeitar</button>';
                                                echo '</form>';
                                                echo '</div>';
                                            }

                                            // Removido o botão de exclusão para relatórios recebidos

                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="6" class="text-center">Nenhum relatório recebido.</td></tr>';
                                    }
                                    $stmt->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Aba de relatórios enviados -->
            <div class="tab-pane fade" id="enviados" role="tabpanel" aria-labelledby="enviados-tab">
                <div class="card">
                    <div class="card-header">
                        <h5 class="card-title mb-0">Relatórios Enviados</h5>
                    </div>
                    <div class="card-body">
                        <div class="table-responsive">
                            <table class="table table-striped table-hover">
                                <thead>
                                    <tr>
                                        <th>Título</th>
                                        <th>Para</th>
                                        <th>Data</th>
                                        <th>Status</th>
                                        <th>Observação</th>
                                        <th>Resposta</th>
                                        <th>Ações</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php
                                    // Consultar relatórios enviados pelo usuário
                                    $sql = "SELECT * FROM relatorios_compartilhados WHERE usuario_origem = ? ORDER BY data_compartilhamento DESC";
                                    $stmt = $conn->prepare($sql);
                                    $stmt->bind_param("s", $usuarioResponsavel);
                                    $stmt->execute();
                                    $result = $stmt->get_result();

                                    if ($result->num_rows > 0) {
                                        while ($row = $result->fetch_assoc()) {
                                            echo '<tr>';
                                            echo '<td>' . htmlspecialchars($row['titulo']) . '</td>';
                                            echo '<td>' . $usuarios[$row['usuario_destino']] . '</td>';
                                            echo '<td>' . date('d/m/Y H:i', strtotime($row['data_compartilhamento'])) . '</td>';
                                            echo '<td>' . formatarStatus($row['status']) . '</td>';
                                            echo '<td>' . (empty($row['observacao']) ? '<em>Sem observação</em>' : htmlspecialchars($row['observacao'])) . '</td>';
                                            echo '<td>' . (empty($row['observacao_resposta']) ? '<em>Sem resposta</em>' : htmlspecialchars($row['observacao_resposta'])) . '</td>';
                                            echo '<td>';

                                            // Botão para visualizar
                                            echo '<form action="../database/visualizar_relatorio.php" method="post" target="_blank" class="d-inline-block me-1">';
                                            echo '<input type="hidden" name="relatorio_html" value="' . $row['dados_relatorio'] . '">';
                                            echo '<button type="submit" class="btn btn-sm btn-info">Visualizar</button>';
                                            echo '</form>';

                                            // Botão para excluir (apenas para remetentes)
                                            echo '<form action="' . $_SERVER['PHP_SELF'] . '" method="post" class="d-inline-block mt-2" onsubmit="return confirm(\'Tem certeza que deseja excluir este relatório?\');">';
                                            echo '<input type="hidden" name="compartilhamento_id" value="' . $row['id'] . '">';
                                            echo '<button type="submit" name="excluir_relatorio" class="btn btn-sm btn-secondary">Excluir</button>';
                                            echo '</form>';

                                            echo '</td>';
                                            echo '</tr>';
                                        }
                                    } else {
                                        echo '<tr><td colspan="7" class="text-center">Nenhum relatório enviado.</td></tr>';
                                    }
                                    $stmt->close();
                                    ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>