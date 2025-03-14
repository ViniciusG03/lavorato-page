<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verificar se o usuário tem permissão para acessar esta página
// Apenas admin e usuários específicos podem acessar
if (!($_SESSION['login'] == 'admin' || in_array($_SESSION['login'], ['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita']))) {
    header("Location: ../index.php");
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

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Parâmetros de filtro para a visualização
$filtro_status = isset($_GET['status']) ? $_GET['status'] : '';
$filtro_usuario = isset($_GET['usuario']) ? $_GET['usuario'] : '';
$filtro_data_inicio = isset($_GET['data_inicio']) ? $_GET['data_inicio'] : '';
$filtro_data_fim = isset($_GET['data_fim']) ? $_GET['data_fim'] : '';

// Função para formatar status
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
    <title>Administração de Relatórios Compartilhados</title>
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

        .filtro-container {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
        }

        .estatisticas-container {
            display: flex;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 20px;
        }

        .estatistica-card {
            flex: 1;
            min-width: 200px;
            padding: 15px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .estatistica-numero {
            font-size: 24px;
            font-weight: bold;
            color: var(--main-color-btn);
        }

        .estatistica-titulo {
            font-size: 14px;
            color: #6c757d;
        }

        .pagination-container {
            display: flex;
            justify-content: center;
            margin-top: 20px;
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
        <h1 class="text-center mb-4">Administração de Relatórios Compartilhados</h1>

        <!-- Filtros -->
        <div class="filtro-container">
            <form method="get" action="" class="row g-3">
                <div class="col-md-3">
                    <label for="status" class="form-label">Status:</label>
                    <select name="status" id="status" class="form-select">
                        <option value="" <?php echo $filtro_status == '' ? 'selected' : ''; ?>>Todos</option>
                        <option value="pendente" <?php echo $filtro_status == 'pendente' ? 'selected' : ''; ?>>Pendentes</option>
                        <option value="confirmado" <?php echo $filtro_status == 'confirmado' ? 'selected' : ''; ?>>Confirmados</option>
                        <option value="rejeitado" <?php echo $filtro_status == 'rejeitado' ? 'selected' : ''; ?>>Rejeitados</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="usuario" class="form-label">Usuário:</label>
                    <select name="usuario" id="usuario" class="form-select">
                        <option value="" <?php echo $filtro_usuario == '' ? 'selected' : ''; ?>>Todos</option>
                        <?php foreach ($usuarios as $login => $nome): ?>
                            <option value="<?php echo $login; ?>" <?php echo $filtro_usuario == $login ? 'selected' : ''; ?>><?php echo $nome; ?></option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="data_inicio" class="form-label">Data Início:</label>
                    <input type="date" class="form-control" id="data_inicio" name="data_inicio" value="<?php echo $filtro_data_inicio; ?>">
                </div>
                <div class="col-md-3">
                    <label for="data_fim" class="form-label">Data Fim:</label>
                    <input type="date" class="form-control" id="data_fim" name="data_fim" value="<?php echo $filtro_data_fim; ?>">
                </div>
                <div class="col-12 text-center">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                    <a href="admin_relatorios.php" class="btn btn-secondary ms-2">Limpar Filtros</a>
                </div>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="estatisticas-container">
            <?php
            // Total de relatórios
            $sql_total = "SELECT COUNT(*) as total FROM relatorios_compartilhados";
            $result_total = $conn->query($sql_total);
            $total_relatorios = $result_total->fetch_assoc()['total'];

            // Total pendentes
            $sql_pendentes = "SELECT COUNT(*) as total FROM relatorios_compartilhados WHERE status = 'pendente'";
            $result_pendentes = $conn->query($sql_pendentes);
            $total_pendentes = $result_pendentes->fetch_assoc()['total'];

            // Total confirmados
            $sql_confirmados = "SELECT COUNT(*) as total FROM relatorios_compartilhados WHERE status = 'confirmado'";
            $result_confirmados = $conn->query($sql_confirmados);
            $total_confirmados = $result_confirmados->fetch_assoc()['total'];

            // Total rejeitados
            $sql_rejeitados = "SELECT COUNT(*) as total FROM relatorios_compartilhados WHERE status = 'rejeitado'";
            $result_rejeitados = $conn->query($sql_rejeitados);
            $total_rejeitados = $result_rejeitados->fetch_assoc()['total'];
            ?>

            <div class="estatistica-card">
                <div class="estatistica-numero"><?php echo $total_relatorios; ?></div>
                <div class="estatistica-titulo">Total de Relatórios</div>
            </div>

            <div class="estatistica-card">
                <div class="estatistica-numero"><?php echo $total_pendentes; ?></div>
                <div class="estatistica-titulo">Pendentes</div>
            </div>

            <div class="estatistica-card">
                <div class="estatistica-numero"><?php echo $total_confirmados; ?></div>
                <div class="estatistica-titulo">Confirmados</div>
            </div>

            <div class="estatistica-card">
                <div class="estatistica-numero"><?php echo $total_rejeitados; ?></div>
                <div class="estatistica-titulo">Rejeitados</div>
            </div>
        </div>

        <!-- Lista de Relatórios -->
        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Relatórios Compartilhados</h5>
            </div>
            <div class="card-body">
                <?php
                // Configurar paginação
                $registros_por_pagina = 20;
                $pagina_atual = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
                $offset = ($pagina_atual - 1) * $registros_por_pagina;

                // Construir a query com base nos filtros
                $sql = "SELECT r.*, 
                        DATE_FORMAT(r.data_compartilhamento, '%d/%m/%Y %H:%i') as data_formatada,
                        DATE_FORMAT(r.data_confirmacao, '%d/%m/%Y %H:%i') as confirmacao_formatada
                        FROM relatorios_compartilhados r
                        WHERE 1=1";

                $params = [];
                $types = "";

                if (!empty($filtro_status)) {
                    $sql .= " AND r.status = ?";
                    $params[] = $filtro_status;
                    $types .= "s";
                }

                if (!empty($filtro_usuario)) {
                    $sql .= " AND (r.usuario_origem = ? OR r.usuario_destino = ?)";
                    $params[] = $filtro_usuario;
                    $params[] = $filtro_usuario;
                    $types .= "ss";
                }

                if (!empty($filtro_data_inicio)) {
                    $sql .= " AND DATE(r.data_compartilhamento) >= ?";
                    $params[] = $filtro_data_inicio;
                    $types .= "s";
                }

                if (!empty($filtro_data_fim)) {
                    $sql .= " AND DATE(r.data_compartilhamento) <= ?";
                    $params[] = $filtro_data_fim;
                    $types .= "s";
                }

                // Contar o total de registros para a paginação
                $sql_count = "SELECT COUNT(*) as total FROM relatorios_compartilhados r WHERE 1=1";

                // Adicionando as mesmas condições de filtro à consulta de contagem
                if (!empty($filtro_status)) {
                    $sql_count .= " AND r.status = ?";
                }

                if (!empty($filtro_usuario)) {
                    $sql_count .= " AND (r.usuario_origem = ? OR r.usuario_destino = ?)";
                }

                if (!empty($filtro_data_inicio)) {
                    $sql_count .= " AND DATE(r.data_compartilhamento) >= ?";
                }

                if (!empty($filtro_data_fim)) {
                    $sql_count .= " AND DATE(r.data_compartilhamento) <= ?";
                }

                $stmt_count = $conn->prepare($sql_count);
                if (!empty($types)) {
                    $stmt_count->bind_param($types, ...$params);
                }
                $stmt_count->execute();
                $result_count = $stmt_count->get_result();
                $row_count = $result_count->fetch_assoc();
                $total_registros = $row_count['total'];
                $total_paginas = ceil($total_registros / $registros_por_pagina);

                $sql .= " ORDER BY r.data_compartilhamento DESC LIMIT ?, ?";
                $params[] = $offset;
                $params[] = $registros_por_pagina;
                $types .= "ii";

                $stmt = $conn->prepare($sql);
                if (!empty($types)) {
                    $stmt->bind_param($types, ...$params);
                }
                $stmt->execute();
                $result = $stmt->get_result();

                if ($result->num_rows > 0) {
                ?>
                    <div class="table-responsive">
                        <table class="table table-striped table-hover">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Título</th>
                                    <th>De</th>
                                    <th>Para</th>
                                    <th>Data Envio</th>
                                    <th>Data Confirmação</th>
                                    <th>Status</th>
                                    <th>Observação</th>
                                    <th>Resposta</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php while ($row = $result->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $row['id']; ?></td>
                                        <td><?php echo htmlspecialchars($row['titulo']); ?></td>
                                        <td><?php echo $usuarios[$row['usuario_origem']] ?? $row['usuario_origem']; ?></td>
                                        <td><?php echo $usuarios[$row['usuario_destino']] ?? $row['usuario_destino']; ?></td>
                                        <td><?php echo $row['data_formatada']; ?></td>
                                        <td><?php echo $row['confirmacao_formatada'] ?: '-'; ?></td>
                                        <td><?php echo formatarStatus($row['status']); ?></td>
                                        <td><?php echo empty($row['observacao']) ? '-' : htmlspecialchars($row['observacao']); ?></td>
                                        <td><?php echo empty($row['observacao_resposta']) ? '-' : htmlspecialchars($row['observacao_resposta']); ?></td>
                                        <td>
                                            <form action="../database/visualizar_relatorio.php" method="post" target="_blank">
                                                <input type="hidden" name="relatorio_html" value="<?php echo $row['dados_relatorio']; ?>">
                                                <button type="submit" class="btn btn-sm btn-info">Visualizar</button>
                                            </form>
                                        </td>
                                    </tr>
                                <?php endwhile; ?>
                            </tbody>
                        </table>
                    </div>

                    <!-- Paginação -->
                    <div class="pagination-container">
                        <nav aria-label="Navegação entre páginas">
                            <ul class="pagination">
                                <?php if ($pagina_atual > 1): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=1<?php
                                                                            echo (!empty($filtro_status)) ? '&status=' . $filtro_status : '';
                                                                            echo (!empty($filtro_usuario)) ? '&usuario=' . $filtro_usuario : '';
                                                                            echo (!empty($filtro_data_inicio)) ? '&data_inicio=' . $filtro_data_inicio : '';
                                                                            echo (!empty($filtro_data_fim)) ? '&data_fim=' . $filtro_data_fim : '';
                                                                            ?>" aria-label="Primeira">
                                            <span aria-hidden="true">&laquo;&laquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_atual - 1; ?><?php
                                                                                                            echo (!empty($filtro_status)) ? '&status=' . $filtro_status : '';
                                                                                                            echo (!empty($filtro_usuario)) ? '&usuario=' . $filtro_usuario : '';
                                                                                                            echo (!empty($filtro_data_inicio)) ? '&data_inicio=' . $filtro_data_inicio : '';
                                                                                                            echo (!empty($filtro_data_fim)) ? '&data_fim=' . $filtro_data_fim : '';
                                                                                                            ?>" aria-label="Anterior">
                                            <span aria-hidden="true">&laquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>

                                <?php
                                $range = 2; // Exibir 2 páginas antes e depois da atual
                                $start_page = max(1, $pagina_atual - $range);
                                $end_page = min($total_paginas, $pagina_atual + $range);

                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                    <li class="page-item <?php echo ($i == $pagina_atual) ? 'active' : ''; ?>">
                                        <a class="page-link" href="?pagina=<?php echo $i; ?><?php
                                                                                            echo (!empty($filtro_status)) ? '&status=' . $filtro_status : '';
                                                                                            echo (!empty($filtro_usuario)) ? '&usuario=' . $filtro_usuario : '';
                                                                                            echo (!empty($filtro_data_inicio)) ? '&data_inicio=' . $filtro_data_inicio : '';
                                                                                            echo (!empty($filtro_data_fim)) ? '&data_fim=' . $filtro_data_fim : '';
                                                                                            ?>"><?php echo $i; ?></a>
                                    </li>
                                <?php endfor; ?>

                                <?php if ($pagina_atual < $total_paginas): ?>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $pagina_atual + 1; ?><?php
                                                                                                            echo (!empty($filtro_status)) ? '&status=' . $filtro_status : '';
                                                                                                            echo (!empty($filtro_usuario)) ? '&usuario=' . $filtro_usuario : '';
                                                                                                            echo (!empty($filtro_data_inicio)) ? '&data_inicio=' . $filtro_data_inicio : '';
                                                                                                            echo (!empty($filtro_data_fim)) ? '&data_fim=' . $filtro_data_fim : '';
                                                                                                            ?>" aria-label="Próxima">
                                            <span aria-hidden="true">&raquo;</span>
                                        </a>
                                    </li>
                                    <li class="page-item">
                                        <a class="page-link" href="?pagina=<?php echo $total_paginas; ?><?php
                                                                                                        echo (!empty($filtro_status)) ? '&status=' . $filtro_status : '';
                                                                                                        echo (!empty($filtro_usuario)) ? '&usuario=' . $filtro_usuario : '';
                                                                                                        echo (!empty($filtro_data_inicio)) ? '&data_inicio=' . $filtro_data_inicio : '';
                                                                                                        echo (!empty($filtro_data_fim)) ? '&data_fim=' . $filtro_data_fim : '';
                                                                                                        ?>" aria-label="Última">
                                            <span aria-hidden="true">&raquo;&raquo;</span>
                                        </a>
                                    </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>

                    <div class="text-center">
                        <p>Exibindo <?php echo min($total_registros, $registros_por_pagina); ?> de <?php echo $total_registros; ?> resultados - Página <?php echo $pagina_atual; ?> de <?php echo $total_paginas; ?></p>
                    </div>
                <?php
                } else {
                    echo '<div class="alert alert-info">Nenhum relatório encontrado com os filtros selecionados.</div>';
                }
                ?>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>