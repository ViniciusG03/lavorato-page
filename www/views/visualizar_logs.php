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

// Paginação
$registros_por_pagina = 20;
$pagina_atual = isset($_GET['pagina']) ? intval($_GET['pagina']) : 1;
$inicio = ($pagina_atual - 1) * $registros_por_pagina;

// Filtros
$filtro_guia = isset($_GET['filtro_guia']) ? $_GET['filtro_guia'] : '';
$filtro_usuario = isset($_GET['filtro_usuario']) ? $_GET['filtro_usuario'] : '';
$filtro_data_inicio = isset($_GET['filtro_data_inicio']) ? $_GET['filtro_data_inicio'] : '';
$filtro_data_fim = isset($_GET['filtro_data_fim']) ? $_GET['filtro_data_fim'] : '';

// Construir a consulta SQL com base nos filtros
$where_conditions = [];
$params = [];
$types = '';

if (!empty($filtro_guia)) {
    $where_conditions[] = "l.paciente_guia LIKE ?";
    $params[] = "%$filtro_guia%";
    $types .= 's';
}

if (!empty($filtro_usuario)) {
    $where_conditions[] = "l.usuario_responsavel = ?";
    $params[] = $filtro_usuario;
    $types .= 's';
}

if (!empty($filtro_data_inicio)) {
    $where_conditions[] = "l.data_alteracao >= ?";
    $params[] = $filtro_data_inicio . ' 00:00:00';
    $types .= 's';
}

if (!empty($filtro_data_fim)) {
    $where_conditions[] = "l.data_alteracao <= ?";
    $params[] = $filtro_data_fim . ' 23:59:59';
    $types .= 's';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Consulta para contar o total de registros
$sql_count = "SELECT COUNT(*) as total FROM guias_status_logs l $where_clause";
$stmt_count = $conn->prepare($sql_count);

if (!empty($types)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_registros = $row_count['total'];
$total_paginas = ceil($total_registros / $registros_por_pagina);
$stmt_count->close();

// Consulta para buscar os logs com paginação
$sql_logs = "SELECT l.*, p.paciente_nome 
             FROM guias_status_logs l
             LEFT JOIN pacientes p ON l.guia_id = p.id
             $where_clause
             ORDER BY l.data_alteracao DESC
             LIMIT ?, ?";

$stmt_logs = $conn->prepare($sql_logs);
$params[] = $inicio;
$params[] = $registros_por_pagina;
$types .= 'ii';

$stmt_logs->bind_param($types, ...$params);
$stmt_logs->execute();
$result_logs = $stmt_logs->get_result();
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Histórico de Alterações de Status</title>
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

        .pagination .page-link {
            color: var(--main-color-btn);
        }

        .pagination .page-item.active .page-link {
            background-color: var(--main-color-btn);
            border-color: var(--main-color-btn);
            color: white;
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
                </ul>
            </div>
        </div>
    </nav>

    <div class="container">
        <h1 class="text-center mb-4">Histórico de Alterações de Status</h1>

        <div class="card mb-4">
            <div class="card-header">
                <h5 class="mb-0">Filtros</h5>
            </div>
            <div class="card-body">
                <form action="" method="GET" class="row g-3">
                    <div class="col-md-3">
                        <label for="filtro_guia" class="form-label">Número da Guia:</label>
                        <input type="text" class="form-control" id="filtro_guia" name="filtro_guia" value="<?php echo htmlspecialchars($filtro_guia); ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_usuario" class="form-label">Usuário:</label>
                        <select class="form-select" id="filtro_usuario" name="filtro_usuario">
                            <option value="">Todos</option>
                            <?php foreach ($usuarios as $login => $nome): ?>
                                <option value="<?php echo $login; ?>" <?php echo $filtro_usuario === $login ? 'selected' : ''; ?>>
                                    <?php echo $nome; ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_data_inicio" class="form-label">Data Inicial:</label>
                        <input type="date" class="form-control" id="filtro_data_inicio" name="filtro_data_inicio" value="<?php echo $filtro_data_inicio; ?>">
                    </div>
                    <div class="col-md-3">
                        <label for="filtro_data_fim" class="form-label">Data Final:</label>
                        <input type="date" class="form-control" id="filtro_data_fim" name="filtro_data_fim" value="<?php echo $filtro_data_fim; ?>">
                    </div>
                    <div class="col-12 d-flex justify-content-end">
                        <button type="submit" class="btn btn-primary me-2">Filtrar</button>
                        <a href="visualizar_logs.php" class="btn btn-secondary">Limpar Filtros</a>
                    </div>
                </form>
            </div>
        </div>

        <div class="card">
            <div class="card-header d-flex justify-content-between align-items-center">
                <h5 class="mb-0">Registros de Alterações</h5>
                <span>Total: <?php echo $total_registros; ?> registros</span>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-striped table-hover">
                        <thead>
                            <tr>
                                <th>Data/Hora</th>
                                <th>Guia</th>
                                <th>Paciente</th>
                                <th>Status Anterior</th>
                                <th>Novo Status</th>
                                <th>Usuário</th>
                                <th>Observação</th>
                                <th>Ações</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if ($result_logs->num_rows > 0): ?>
                                <?php while ($log = $result_logs->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo date('d/m/Y H:i:s', strtotime($log['data_alteracao'])); ?></td>
                                        <td><?php echo htmlspecialchars($log['paciente_guia']); ?></td>
                                        <td><?php echo htmlspecialchars($log['paciente_nome'] ?? 'N/A'); ?></td>
                                        <td>
                                            <?php if (empty($log['status_anterior'])): ?>
                                                <span class="text-muted">Não definido</span>
                                            <?php else: ?>
                                                <span class="status-badge" style="background-color: #f0f0f0; color: #333;">
                                                    <?php echo htmlspecialchars($log['status_anterior']); ?>
                                                </span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
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
                                        </td>
                                        <td><?php echo isset($usuarios[$log['usuario_responsavel']]) ? $usuarios[$log['usuario_responsavel']] : $log['usuario_responsavel']; ?></td>
                                        <td><?php echo !empty($log['observacao']) ? htmlspecialchars($log['observacao']) : '<span class="text-muted">Nenhuma</span>'; ?></td>
                                        <td>
                                            <a href="historico_guia.php?guia=<?php echo urlencode($log['paciente_guia']); ?>" class="btn btn-sm btn-info">
                                                Ver Histórico
                                            </a>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            <?php else: ?>
                                <tr>
                                    <td colspan="8" class="text-center">Nenhum registro encontrado.</td>
                                </tr>
                            <?php endif; ?>
                        </tbody>
                    </table>
                </div>

                <?php if ($total_paginas > 1): ?>
                    <nav aria-label="Paginação">
                        <ul class="pagination justify-content-center">
                            <li class="page-item <?php echo $pagina_atual <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=1<?php echo !empty($filtro_guia) ? '&filtro_guia=' . urlencode($filtro_guia) : ''; ?><?php echo !empty($filtro_usuario) ? '&filtro_usuario=' . urlencode($filtro_usuario) : ''; ?><?php echo !empty($filtro_data_inicio) ? '&filtro_data_inicio=' . urlencode($filtro_data_inicio) : ''; ?><?php echo !empty($filtro_data_fim) ? '&filtro_data_fim=' . urlencode($filtro_data_fim) : ''; ?>">Primeira</a>
                            </li>
                            <li class="page-item <?php echo $pagina_atual <= 1 ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?><?php echo !empty($filtro_guia) ? '&filtro_guia=' . urlencode($filtro_guia) : ''; ?><?php echo !empty($filtro_usuario) ? '&filtro_usuario=' . urlencode($filtro_usuario) : ''; ?><?php echo !empty($filtro_data_inicio) ? '&filtro_data_inicio=' . urlencode($filtro_data_inicio) : ''; ?><?php echo !empty($filtro_data_fim) ? '&filtro_data_fim=' . urlencode($filtro_data_fim) : ''; ?>">Anterior</a>
                            </li>

                            <?php for ($i = max(1, $pagina_atual - 2); $i <= min($pagina_atual + 2, $total_paginas); $i++): ?>
                                <li class="page-item <?php echo $i == $pagina_atual ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?><?php echo !empty($filtro_guia) ? '&filtro_guia=' . urlencode($filtro_guia) : ''; ?><?php echo !empty($filtro_usuario) ? '&filtro_usuario=' . urlencode($filtro_usuario) : ''; ?><?php echo !empty($filtro_data_inicio) ? '&filtro_data_inicio=' . urlencode($filtro_data_inicio) : ''; ?><?php echo !empty($filtro_data_fim) ? '&filtro_data_fim=' . urlencode($filtro_data_fim) : ''; ?>"><?php echo $i; ?></a>
                                </li>
                            <?php endfor; ?>

                            <li class="page-item <?php echo $pagina_atual >= $total_paginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?><?php echo !empty($filtro_guia) ? '&filtro_guia=' . urlencode($filtro_guia) : ''; ?><?php echo !empty($filtro_usuario) ? '&filtro_usuario=' . urlencode($filtro_usuario) : ''; ?><?php echo !empty($filtro_data_inicio) ? '&filtro_data_inicio=' . urlencode($filtro_data_inicio) : ''; ?><?php echo !empty($filtro_data_fim) ? '&filtro_data_fim=' . urlencode($filtro_data_fim) : ''; ?>">Próxima</a>
                            </li>
                            <li class="page-item <?php echo $pagina_atual >= $total_paginas ? 'disabled' : ''; ?>">
                                <a class="page-link" href="?pagina=<?php echo $total_paginas; ?><?php echo !empty($filtro_guia) ? '&filtro_guia=' . urlencode($filtro_guia) : ''; ?><?php echo !empty($filtro_usuario) ? '&filtro_usuario=' . urlencode($filtro_usuario) : ''; ?><?php echo !empty($filtro_data_inicio) ? '&filtro_data_inicio=' . urlencode($filtro_data_inicio) : ''; ?><?php echo !empty($filtro_data_fim) ? '&filtro_data_fim=' . urlencode($filtro_data_fim) : ''; ?>">Última</a>
                            </li>
                        </ul>
                    </nav>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$stmt_logs->close();
$conn->close();
?>