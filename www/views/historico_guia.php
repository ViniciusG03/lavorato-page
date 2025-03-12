<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verifica se o usuário tem permissão para visualizar logs
function hasPermission($roles)
{
    return (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) || in_array($_SESSION['login'], $roles);
}

// Lista de usuários com permissão para visualizar logs
$allowed_users = ['admin', 'gustavoramos', 'talita', 'kaynnanduraes', 'will', 'eviny', 'tulio'];

if (!hasPermission($allowed_users)) {
    header("Location: ../index.php");
    exit();
}

// Verificar se foi fornecido o número da guia
if (!isset($_GET['guia']) || empty($_GET['guia'])) {
    header("Location: visualizar_logs.php");
    exit();
}

$numeroGuia = $_GET['guia'];

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Mapeamento de usuários para nomes formatados
$usuarios = [
    'admin' => 'Vinicius Oliveira',
    'talita' => 'Talita Ruiz',
    'gustavoramos' => 'Gustavo Ramos',
    'kaynnanduraes' => 'Kaynnan Durães',
    'eviny' => 'Eviny Santos',
    'tulio' => 'Tulio Uler',
    'will' => 'Williams Licar'
];

// Obter informações básicas da guia
$sql_guia = "SELECT * FROM pacientes WHERE paciente_guia = ?";
$stmt_guia = $conn->prepare($sql_guia);
$stmt_guia->bind_param("s", $numeroGuia);
$stmt_guia->execute();
$result_guia = $stmt_guia->get_result();

if ($result_guia->num_rows === 0) {
    // Guia não encontrada
    $guia_info = null;
} else {
    $guia_info = $result_guia->fetch_assoc();
}

$stmt_guia->close();

// Buscar histórico de alterações
$sql_logs = "SELECT * FROM guias_status_logs WHERE paciente_guia = ? ORDER BY data_alteracao DESC";
$stmt_logs = $conn->prepare($sql_logs);
$stmt_logs->bind_param("s", $numeroGuia);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Alterações da Guia</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap" rel="stylesheet" />
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

        .table th {
            background-color: var(--main-color-btn);
            color: white;
        }

        .status-badge {
            display: inline-block;
            padding: 0.25em 0.4em;
            font-size: 85%;
            font-weight: 700;
            line-height: 1;
            text-align: center;
            white-space: nowrap;
            vertical-align: baseline;
            border-radius: 0.25rem;
        }

        .timeline {
            position: relative;
            padding: 0;
            list-style: none;
        }

        .timeline::before {
            content: '';
            position: absolute;
            top: 0;
            bottom: 0;
            left: 20px;
            width: 2px;
            background-color: var(--main-color-btn);
        }

        .timeline>li {
            position: relative;
            margin-bottom: 20px;
        }

        .timeline>li::before,
        .timeline>li::after {
            content: " ";
            display: table;
        }

        .timeline>li::after {
            clear: both;
        }

        .timeline-badge {
            position: absolute;
            top: 20px;
            left: 10px;
            width: 20px;
            height: 20px;
            border-radius: 50%;
            background-color: var(--main-color-btn);
            z-index: 1;
        }

        .timeline-panel {
            position: relative;
            margin-left: 50px;
            padding: 20px;
            background-color: #fff;
            border-radius: 8px;
            box-shadow: 0 1px 3px rgba(0, 0, 0, 0.12), 0 1px 2px rgba(0, 0, 0, 0.24);
        }

        .timeline-date {
            display: block;
            margin-bottom: 10px;
            font-size: 0.85em;
            color: #777;
        }

        .timeline-title {
            margin-top: 0;
            font-weight: 600;
        }
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
        <div class="container-fluid">
            <a class="navbar-brand" href="#">Lavorato's System</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav" aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="../index.php">Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="visualizar_logs.php">Voltar</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Histórico de Alterações da Guia</h1>

        <?php if ($guia_info === null): ?>
            <div class="alert alert-warning">
                <h4 class="alert-heading">Guia não encontrada!</h4>
                <p>Não foi possível encontrar informações sobre a guia <strong><?php echo htmlspecialchars($numeroGuia); ?></strong>.</p>
                <hr>
                <p class="mb-0">Verifique se o número da guia está correto e tente novamente.</p>
            </div>
        <?php else: ?>
            <div class="card mb-4">
                <div class="card-header">
                    <h5 class="mb-0">Informações da Guia #<?php echo htmlspecialchars($numeroGuia); ?></h5>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Paciente:</strong> <?php echo htmlspecialchars($guia_info['paciente_nome']); ?></p>
                            <p><strong>Convênio:</strong> <?php echo htmlspecialchars($guia_info['paciente_convenio']); ?></p>
                            <p><strong>Especialidade:</strong> <?php echo htmlspecialchars($guia_info['paciente_especialidade']); ?></p>
                            <p><strong>Mês:</strong> <?php echo htmlspecialchars($guia_info['paciente_mes']); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Status Atual:</strong>
                                <span class="status-badge"
                                    style="background-color: <?php
                                                                $status = $guia_info['paciente_status'];
                                                                if ($status == 'Emitido') echo '#28a745';
                                                                else if ($status == 'Subiu') echo '#007bff';
                                                                else if ($status == 'Cancelado') echo '#dc3545';
                                                                else if ($status == 'Saiu') echo '#fd7e14';
                                                                else if ($status == 'Retornou') echo '#6f42c1';
                                                                else if ($status == 'Não Usou') echo '#6c757d';
                                                                else if ($status == 'Assinado') echo '#20c997';
                                                                else if ($status == 'Faturado') echo '#17a2b8';
                                                                else if ($status == 'Enviado a BM') echo '#e83e8c';
                                                                else if ($status == 'Devolvido BM') echo '#ff9800';
                                                                else echo '#6c757d';
                                                                ?>; color: white;">
                                    <?php echo htmlspecialchars($status); ?>
                                </span>
                            </p>
                            <p><strong>Data de Entrada:</strong> <?php echo !empty($guia_info['paciente_entrada']) ? htmlspecialchars($guia_info['paciente_entrada']) : '<em>Não definida</em>'; ?></p>
                            <p><strong>Data de Saída:</strong> <?php echo !empty($guia_info['paciente_saida']) ? htmlspecialchars($guia_info['paciente_saida']) : '<em>Não definida</em>'; ?></p>
                            <p><strong>Última Atualização:</strong> <?php echo !empty($guia_info['data_hora_insercao']) ? date('d/m/Y H:i:s', strtotime($guia_info['data_hora_insercao'])) : '<em>Desconhecida</em>'; ?></p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h5 class="mb-0">Histórico de Alterações de Status</h5>
                </div>
                <div class="card-body">
                    <?php if ($result_logs->num_rows > 0): ?>
                        <ul class="timeline">
                            <?php while ($log = $result_logs->fetch_assoc()): ?>
                                <li>
                                    <div class="timeline-badge"></div>
                                    <div class="timeline-panel">
                                        <div class="timeline-heading">
                                            <span class="timeline-date"><?php echo date('d/m/Y H:i:s', strtotime($log['data_alteracao'])); ?> por <?php echo isset($usuarios[$log['usuario_responsavel']]) ? $usuarios[$log['usuario_responsavel']] : $log['usuario_responsavel']; ?></span>
                                            <h5 class="timeline-title">
                                                Status alterado:
                                                <?php if (!empty($log['status_anterior'])): ?>
                                                    <span class="status-badge" style="background-color: #f0f0f0; color: #333;">
                                                        <?php echo htmlspecialchars($log['status_anterior']); ?>
                                                    </span>
                                                <?php else: ?>
                                                    <span class="status-badge" style="background-color: #f0f0f0; color: #333;">
                                                        Não definido
                                                    </span>
                                                <?php endif; ?>
                                                &rarr;
                                                <span class="status-badge"
                                                    style="background-color: <?php
                                                                                if ($log['status_novo'] == 'Emitido') echo '#28a745';
                                                                                else if ($log['status_novo'] == 'Subiu') echo '#007bff';
                                                                                else if ($log['status_novo'] == 'Cancelado') echo '#dc3545';
                                                                                else if ($log['status_novo'] == 'Saiu') echo '#fd7e14';
                                                                                else if ($log['status_novo'] == 'Retornou') echo '#6f42c1';
                                                                                else if ($log['status_novo'] == 'Não Usou') echo '#6c757d';
                                                                                else if ($log['status_novo'] == 'Assinado') echo '#20c997';
                                                                                else if ($log['status_novo'] == 'Faturado') echo '#17a2b8';
                                                                                else if ($log['status_novo'] == 'Enviado a BM') echo '#e83e8c';
                                                                                else if ($log['status_novo'] == 'Devolvido BM') echo '#ff9800';
                                                                                else echo '#6c757d';
                                                                                ?>; color: white;">
                                                    <?php echo htmlspecialchars($log['status_novo']); ?>
                                                </span>
                                            </h5>
                                        </div>
                                        <?php if (!empty($log['observacao'])): ?>
                                            <div class="timeline-body">
                                                <p><?php echo htmlspecialchars($log['observacao']); ?></p>
                                            </div>
                                        <?php endif; ?>
                                    </div>
                                </li>
                            <?php endwhile; ?>
                        </ul>
                    <?php else: ?>
                        <div class="alert alert-info">
                            <p class="mb-0">Nenhum registro de alteração de status foi encontrado para esta guia.</p>
                        </div>
                    <?php endif; ?>
                </div>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$stmt_logs->close();
$conn->close();
?>