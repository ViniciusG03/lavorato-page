<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Função auxiliar para remover um parâmetro da URL atual
function remove_query_param($param) {
    $params = $_GET;
    unset($params[$param]);
    return basename($_SERVER['PHP_SELF']) . '?' . http_build_query($params);
}

// Conexão com o banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Configurações de paginação
$itens_por_pagina = isset($_GET['itens_por_pagina']) ? (int)$_GET['itens_por_pagina'] : 10;
$pagina = isset($_GET['pagina']) ? (int)$_GET['pagina'] : 1;
$inicio = ($pagina - 1) * $itens_por_pagina;

// Filtros
$filtro_status = isset($_GET['filtro_status']) ? $_GET['filtro_status'] : '';
$filtro_convenio = isset($_GET['filtro_convenio']) ? $_GET['filtro_convenio'] : '';
$filtro_mes = isset($_GET['filtro_mes']) ? $_GET['filtro_mes'] : '';
$termo_busca = isset($_GET['termo_busca']) ? $_GET['termo_busca'] : '';

// Construção da query com base nos filtros
$where_conditions = [];
$params = [];
$types = '';

if (!empty($filtro_status)) {
    $where_conditions[] = "paciente_status = ?";
    $params[] = $filtro_status;
    $types .= 's';
}

if (!empty($filtro_convenio)) {
    $where_conditions[] = "paciente_convenio = ?";
    $params[] = $filtro_convenio;
    $types .= 's';
}

if (!empty($filtro_mes)) {
    $where_conditions[] = "paciente_mes = ?";
    $params[] = $filtro_mes;
    $types .= 's';
}

if (!empty($termo_busca)) {
    $where_conditions[] = "(paciente_nome LIKE ? OR paciente_guia LIKE ?)";
    $termo_busca_param = "%$termo_busca%";
    $params[] = $termo_busca_param;
    $params[] = $termo_busca_param;
    $types .= 'ss';
}

$where_clause = !empty($where_conditions) ? "WHERE " . implode(" AND ", $where_conditions) : "";

// Consulta para contar o total de registros com filtros
$sql_count = "SELECT COUNT(*) as total FROM pacientes $where_clause";
$stmt_count = $conn->prepare($sql_count);

if (!empty($types)) {
    $stmt_count->bind_param($types, ...$params);
}

$stmt_count->execute();
$result_count = $stmt_count->get_result();
$row_count = $result_count->fetch_assoc();
$total_registros = $row_count['total'];
$total_paginas = ceil($total_registros / $itens_por_pagina);
$stmt_count->close();

// Consulta para obter os registros com os filtros e paginação
$sql = "SELECT *, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada 
        FROM pacientes 
        $where_clause
        ORDER BY CASE 
            WHEN paciente_mes = 'Janeiro' THEN 1
            WHEN paciente_mes = 'Fevereiro' THEN 2
            WHEN paciente_mes = 'Março' THEN 3
            WHEN paciente_mes = 'Abril' THEN 4
            WHEN paciente_mes = 'Maio' THEN 5
            WHEN paciente_mes = 'Junho' THEN 6
            WHEN paciente_mes = 'Julho' THEN 7
            WHEN paciente_mes = 'Agosto' THEN 8
            WHEN paciente_mes = 'Setembro' THEN 9
            WHEN paciente_mes = 'Outubro' THEN 10
            WHEN paciente_mes = 'Novembro' THEN 11
            WHEN paciente_mes = 'Dezembro' THEN 12
            ELSE 13 
        END, paciente_nome ASC
        LIMIT ?, ?";

$stmt = $conn->prepare($sql);
$params[] = $inicio;
$params[] = $itens_por_pagina;
$types .= 'ii';

$stmt->bind_param($types, ...$params);
$stmt->execute();
$result = $stmt->get_result();

// Função para determinar a cor do badge com base no status
function getStatusColor($status) {
    switch ($status) {
        case 'Emitido':
            return 'success';
        case 'Subiu':
            return 'primary';
        case 'Cancelado':
            return 'danger';
        case 'Saiu':
            return 'warning';
        case 'Retornou':
            return 'purple';
        case 'Não Usou':
            return 'secondary';
        case 'Assinado':
            return 'info';
        case 'Faturado':
            return 'dark';
        case 'Enviado a BM':
            return 'pink';
        case 'Devolvido BM':
            return 'orange';
        default:
            return 'secondary';
    }
}

// Obter lista de convênios e meses para os filtros
$sql_convenios = "SELECT DISTINCT paciente_convenio FROM pacientes ORDER BY paciente_convenio ASC";
$result_convenios = $conn->query($sql_convenios);

$sql_meses = "SELECT DISTINCT paciente_mes FROM pacientes ORDER BY CASE 
                WHEN paciente_mes = 'Janeiro' THEN 1
                WHEN paciente_mes = 'Fevereiro' THEN 2
                WHEN paciente_mes = 'Março' THEN 3
                WHEN paciente_mes = 'Abril' THEN 4
                WHEN paciente_mes = 'Maio' THEN 5
                WHEN paciente_mes = 'Junho' THEN 6
                WHEN paciente_mes = 'Julho' THEN 7
                WHEN paciente_mes = 'Agosto' THEN 8
                WHEN paciente_mes = 'Setembro' THEN 9
                WHEN paciente_mes = 'Outubro' THEN 10
                WHEN paciente_mes = 'Novembro' THEN 11
                WHEN paciente_mes = 'Dezembro' THEN 12
                ELSE 13 END ASC";
$result_meses = $conn->query($sql_meses);
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Guias - Lavorato System</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../stylesheet/style.css">
    <style>
        :root {
            --main-bg-color: #b9d7d9;
            --main-color-btn: #00b3ffde;
            --main-nav-color: #00b3ffde;
            --text-color: #333333;
            --text-light: #6c757d;
            --text-white: #ffffff;
            --border-radius: 8px;
            --box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            --transition: all 0.3s ease;
            --success: #28a745;
            --warning: #ffc107;
            --danger: #dc3545;
            --info: #17a2b8;
        }

        .badge.bg-purple {
            background-color: #6f42c1;
        }
        
        .badge.bg-pink {
            background-color: #e83e8c;
        }
        
        .badge.bg-orange {
            background-color: #fd7e14;
        }
        
        .filters-container {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            margin-bottom: 20px;
        }
        
        .table-container {
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.05);
            padding: 0;
            overflow: hidden;
        }
        
        /* Melhora a responsividade da tabela em dispositivos móveis */
        @media (max-width: 768px) {
            .table-responsive {
                font-size: 0.85rem;
            }
            .status-badge {
                padding: 0.2rem 0.4rem;
                font-size: 0.7rem;
            }
        }
        
        /* Status badges distintos para melhor visualização */
        .status-badge {
            border-radius: 50rem;
            padding: 0.35em 0.65em;
            font-size: 0.75em;
            font-weight: 600;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        /* Melhoria para cabeçalhos fixos nas tabelas */
        .table-fixed-header {
            overflow-y: auto;
        }
        
        .table-fixed-header thead th {
            position: sticky;
            top: 0;
            z-index: 1;
        }

        /* Melhoria visual para os itens por página */
        .items-per-page {
            display: inline-block;
            width: auto;
        }
        
        /* Melhoria para paginação */
        .pagination-info {
            color: #6c757d;
            font-size: 0.875rem;
        }
        
        /* Destacar linhas ao passar o mouse */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 179, 255, 0.05);
        }
        
        /* Melhoria para os filtros */
        .filter-badge {
            background-color: #e9ecef;
            color: #495057;
            padding: 0.35em 0.65em;
            font-size: 0.85em;
            font-weight: 500;
            border-radius: 0.25rem;
            margin-right: 0.5rem;
        }
        
        .filter-badge .close {
            margin-left: 0.5rem;
            font-size: 0.85rem;
            cursor: pointer;
        }
        
        .filter-badge .close:hover {
            color: #dc3545;
        }
        
        /* Botões de ação */
        .btn-action {
            padding: 0.25rem 0.5rem;
            font-size: 0.8rem;
        }
        
        /* Cor do link na paginação */
        .page-link {
            color: var(--main-color-btn);
        }
        
        .page-item.active .page-link {
            background-color: var(--main-color-btn);
            border-color: var(--main-color-btn);
        }

        body, html {
    height: auto !important;
    overflow-y: auto !important;
}

.container, .card.table-container {
    height: auto !important;
    max-height: none !important;
}

/* Manter altura automática para todos os elementos principais */
.table-container, .card-body {
    height: auto !important;
}
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
        <div class="container-fluid">
            <a class="navbar-brand d-flex align-items-center" href="#">
                <img src="../assets/Logo-Lavorato-alfa.png" alt="Lavorato Logo" width="30" class="me-2">
                Lavorato's System
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
                    </li>
                    <?php if (!empty($_GET)): ?>
                    <li class="nav-item">
                        <a class="nav-link" href="listar.php">
                            <i class="fas fa-sync-alt me-1"></i> Limpar Filtros
                        </a>
                    </li>
                    <?php endif; ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#exportModal">
                            <i class="fas fa-file-export me-1"></i> Exportar
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container mt-4 mb-4">
        <div class="mb-4">
            <h1 class="h3">Lista de Guias</h1>
            <div class="d-flex">
            <button type="button" class="btn btn-sm btn-outline-primary me-2" data-bs-toggle="modal" data-bs-target="#filtersModal">
              <i class="fas fa-filter me-1"></i> Filtros Avançados
            </button>
             <a href="../index.php" class="btn btn-sm btn-primary">
                <i class="fas fa-arrow-left me-1"></i> Voltar
            </a>
        </div>
        </div>
        
        <!-- Barra de pesquisa rápida -->
        <div class="filters-container mb-4">
            <form action="listar.php" method="get" class="row g-3 align-items-end">
                <div class="col-md-4">
                    <label for="termo_busca" class="form-label">Busca Rápida</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-search"></i></span>
                        <input type="text" id="termo_busca" name="termo_busca" class="form-control" 
                            placeholder="Nome do paciente ou número da guia" 
                            value="<?php echo htmlspecialchars($termo_busca); ?>">
                    </div>
                </div>
                <div class="col-md-3">
                    <label for="filtro_status" class="form-label">Status</label>
                    <select id="filtro_status" name="filtro_status" class="form-select">
                        <option value="">Todos</option>
                        <option value="Emitido" <?php echo $filtro_status == 'Emitido' ? 'selected' : ''; ?>>Emitido</option>
                        <option value="Subiu" <?php echo $filtro_status == 'Subiu' ? 'selected' : ''; ?>>Subiu</option>
                        <option value="Cancelado" <?php echo $filtro_status == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                        <option value="Saiu" <?php echo $filtro_status == 'Saiu' ? 'selected' : ''; ?>>Saiu</option>
                        <option value="Retornou" <?php echo $filtro_status == 'Retornou' ? 'selected' : ''; ?>>Retornou</option>
                        <option value="Não Usou" <?php echo $filtro_status == 'Não Usou' ? 'selected' : ''; ?>>Não Usou</option>
                        <option value="Assinado" <?php echo $filtro_status == 'Assinado' ? 'selected' : ''; ?>>Assinado</option>
                        <option value="Faturado" <?php echo $filtro_status == 'Faturado' ? 'selected' : ''; ?>>Faturado</option>
                        <option value="Enviado a BM" <?php echo $filtro_status == 'Enviado a BM' ? 'selected' : ''; ?>>Enviado a BM</option>
                        <option value="Devolvido BM" <?php echo $filtro_status == 'Devolvido BM' ? 'selected' : ''; ?>>Devolvido BM</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label for="itens_por_pagina" class="form-label">Itens por página</label>
                    <select id="itens_por_pagina" name="itens_por_pagina" class="form-select">
                        <option value="10" <?php echo $itens_por_pagina == 10 ? 'selected' : ''; ?>>10</option>
                        <option value="25" <?php echo $itens_por_pagina == 25 ? 'selected' : ''; ?>>25</option>
                        <option value="50" <?php echo $itens_por_pagina == 50 ? 'selected' : ''; ?>>50</option>
                        <option value="100" <?php echo $itens_por_pagina == 100 ? 'selected' : ''; ?>>100</option>
                    </select>
                </div>
                <div class="col-md-2">
                    <button type="submit" class="btn btn-primary w-100">
                        <i class="fas fa-search me-1"></i> Buscar
                    </button>
                </div>
            </form>
        </div>
        
        <!-- Filtros ativos -->
        <?php if (!empty($filtro_status) || !empty($filtro_convenio) || !empty($filtro_mes) || !empty($termo_busca)): ?>
        <div class="mb-3">
            <div class="d-flex align-items-center flex-wrap">
                <span class="me-2">Filtros ativos:</span>
                
                <?php if (!empty($termo_busca)): ?>
                <span class="filter-badge mb-2">
                    <i class="fas fa-search me-1"></i> Busca: <?php echo htmlspecialchars($termo_busca); ?>
                    <a href="<?php echo remove_query_param('termo_busca'); ?>" class="close">&times;</a>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($filtro_status)): ?>
                <span class="filter-badge mb-2">
                    <i class="fas fa-check-circle me-1"></i> Status: <?php echo htmlspecialchars($filtro_status); ?>
                    <a href="<?php echo remove_query_param('filtro_status'); ?>" class="close">&times;</a>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($filtro_convenio)): ?>
                <span class="filter-badge mb-2">
                    <i class="fas fa-id-card me-1"></i> Convênio: <?php echo htmlspecialchars($filtro_convenio); ?>
                    <a href="<?php echo remove_query_param('filtro_convenio'); ?>" class="close">&times;</a>
                </span>
                <?php endif; ?>
                
                <?php if (!empty($filtro_mes)): ?>
                <span class="filter-badge mb-2">
                    <i class="fas fa-calendar-alt me-1"></i> Mês: <?php echo htmlspecialchars($filtro_mes); ?>
                    <a href="<?php echo remove_query_param('filtro_mes'); ?>" class="close">&times;</a>
                </span>
                <?php endif; ?>
                
                <a href="listar.php" class="btn btn-sm btn-outline-secondary ms-auto">
                    <i class="fas fa-times me-1"></i> Limpar todos
                </a>
            </div>
        </div>
        <?php endif; ?>
        
        <!-- Resultados da busca -->
        <div class="card table-container">
            <div class="card-header bg-white d-flex justify-content-between align-items-center py-3">
                <h5 class="mb-0">Guias Encontradas</h5>
                <span class="badge bg-primary"><?php echo $total_registros; ?> registros</span>
            </div>
            
            <?php if ($result->num_rows > 0): ?>
            <div class="table-fixed-header">
                <table class="table table-hover table-striped mb-0">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Nome</th>
                            <th>Convênio</th>
                            <th>Número</th>
                            <th>Status</th>
                            <th>Especialidade</th>
                            <th>Mês</th>
                            <th>Sessões</th>
                            <th>Valor</th>
                            <th>Atualização</th>
                            <th>Ações</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php while ($row = $result->fetch_assoc()): ?>
                        <tr>
                            <td><?php echo $row["id"]; ?></td>
                            <td><?php echo htmlspecialchars($row["paciente_nome"]); ?></td>
                            <td><?php echo htmlspecialchars($row["paciente_convenio"]); ?></td>
                            <td><?php echo htmlspecialchars($row["paciente_guia"]); ?></td>
                            <td>
                                <span class="badge bg-<?php echo getStatusColor($row["paciente_status"]); ?> status-badge">
                                    <?php echo htmlspecialchars($row["paciente_status"]); ?>
                                </span>
                            </td>
                            <td><?php echo htmlspecialchars($row["paciente_especialidade"]); ?></td>
                            <td><?php echo htmlspecialchars($row["paciente_mes"]); ?></td>
                            <td><?php echo htmlspecialchars($row["paciente_section"]); ?></td>
                            <td><?php echo !empty($row["paciente_valor"]) ? 'R$ ' . htmlspecialchars($row["paciente_valor"]) : '-'; ?></td>
                            <td><?php echo htmlspecialchars($row["data_hora_formatada"]); ?></td>
                            <td>
                                <div class="btn-group">
                                    <button type="button" class="btn btn-sm btn-outline-primary btn-action edit-button" 
                                        data-id="<?php echo $row["id"]; ?>"
                                        data-bs-toggle="tooltip" title="Editar">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    <button type="button" class="btn btn-sm btn-outline-info btn-action details-button"
                                        data-id="<?php echo $row["id"]; ?>"
                                        data-bs-toggle="tooltip" title="Detalhes">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <?php if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']): ?>
                                    <button type="button" class="btn btn-sm btn-outline-danger btn-action delete-button"
                                        data-id="<?php echo $row["id"]; ?>"
                                        data-bs-toggle="tooltip" title="Excluir">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    <?php endif; ?>
                                </div>
                            </td>
                        </tr>
                        <?php endwhile; ?>
                    </tbody>
                </table>
            </div>
            
            <div class="card-footer bg-white py-3">
                <div class="row align-items-center">
                    <div class="col-md-6">
                        <p class="pagination-info mb-md-0">Exibindo <?php echo min($total_registros, $itens_por_pagina); ?> de <?php echo $total_registros; ?> registros</p>
                    </div>
                    <div class="col-md-6">
                        <nav aria-label="Paginação">
                            <ul class="pagination justify-content-md-end mb-0">
                                <?php if ($pagina > 1): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=1<?php 
                                        echo !empty($termo_busca) ? '&termo_busca=' . urlencode($termo_busca) : '';
                                        echo !empty($filtro_status) ? '&filtro_status=' . urlencode($filtro_status) : '';
                                        echo !empty($filtro_convenio) ? '&filtro_convenio=' . urlencode($filtro_convenio) : '';
                                        echo !empty($filtro_mes) ? '&filtro_mes=' . urlencode($filtro_mes) : '';
                                        echo !empty($itens_por_pagina) ? '&itens_por_pagina=' . $itens_por_pagina : '';
                                    ?>">
                                        <i class="fas fa-angle-double-left"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?php echo $pagina - 1; ?><?php 
                                        echo !empty($termo_busca) ? '&termo_busca=' . urlencode($termo_busca) : '';
                                        echo !empty($filtro_status) ? '&filtro_status=' . urlencode($filtro_status) : '';
                                        echo !empty($filtro_convenio) ? '&filtro_convenio=' . urlencode($filtro_convenio) : '';
                                        echo !empty($filtro_mes) ? '&filtro_mes=' . urlencode($filtro_mes) : '';
                                        echo !empty($itens_por_pagina) ? '&itens_por_pagina=' . $itens_por_pagina : '';
                                    ?>">
                                        <i class="fas fa-angle-left"></i>
                                    </a>
                                </li>
                                <?php endif; ?>

                                <?php
                                // Mostrar 5 páginas (ou menos se não houver tantas)
                                $start_page = max(1, min($pagina - 2, $total_paginas - 4));
                                $end_page = min($total_paginas, max($pagina + 2, 5));
                                
                                for ($i = $start_page; $i <= $end_page; $i++):
                                ?>
                                <li class="page-item <?php echo ($i == $pagina) ? 'active' : ''; ?>">
                                    <a class="page-link" href="?pagina=<?php echo $i; ?><?php 
                                        echo !empty($termo_busca) ? '&termo_busca=' . urlencode($termo_busca) : '';
                                        echo !empty($filtro_status) ? '&filtro_status=' . urlencode($filtro_status) : '';
                                        echo !empty($filtro_convenio) ? '&filtro_convenio=' . urlencode($filtro_convenio) : '';
                                        echo !empty($filtro_mes) ? '&filtro_mes=' . urlencode($filtro_mes) : '';
                                        echo !empty($itens_por_pagina) ? '&itens_por_pagina=' . $itens_por_pagina : '';
                                    ?>"><?php echo $i; ?></a>
                                </li>
                                <?php endfor; ?>

                                <?php if ($pagina < $total_paginas): ?>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?php echo $pagina + 1; ?><?php 
                                        echo !empty($termo_busca) ? '&termo_busca=' . urlencode($termo_busca) : '';
                                        echo !empty($filtro_status) ? '&filtro_status=' . urlencode($filtro_status) : '';
                                        echo !empty($filtro_convenio) ? '&filtro_convenio=' . urlencode($filtro_convenio) : '';
                                        echo !empty($filtro_mes) ? '&filtro_mes=' . urlencode($filtro_mes) : '';
                                        echo !empty($itens_por_pagina) ? '&itens_por_pagina=' . $itens_por_pagina : '';
                                    ?>">
                                        <i class="fas fa-angle-right"></i>
                                    </a>
                                </li>
                                <li class="page-item">
                                    <a class="page-link" href="?pagina=<?php echo $total_paginas; ?><?php 
                                        echo !empty($termo_busca) ? '&termo_busca=' . urlencode($termo_busca) : '';
                                        echo !empty($filtro_status) ? '&filtro_status=' . urlencode($filtro_status) : '';
                                        echo !empty($filtro_convenio) ? '&filtro_convenio=' . urlencode($filtro_convenio) : '';
                                        echo !empty($filtro_mes) ? '&filtro_mes=' . urlencode($filtro_mes) : '';
                                        echo !empty($itens_por_pagina) ? '&itens_por_pagina=' . $itens_por_pagina : '';
                                    ?>">
                                        <i class="fas fa-angle-double-right"></i>
                                    </a>
                                </li>
                                <?php endif; ?>
                            </ul>
                        </nav>
                    </div>
                </div>
            </div>
            <?php else: ?>
            <div class="card-body text-center py-5">
                <div class="mb-3">
                    <i class="fas fa-search fa-3x text-muted"></i>
                </div>
                <h5 class="text-muted">Nenhum registro encontrado</h5>
                <p class="mb-0 text-muted">Tente modificar seus filtros de busca ou limpar os filtros.</p>
                <a href="listar.php" class="btn btn-outline-primary mt-3">
                    <i class="fas fa-sync-alt me-1"></i> Limpar filtros
                </a>
            </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Modal de filtros avançados -->
    <div class="modal fade" id="filtersModal" tabindex="-1" aria-labelledby="filtersModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="filtersModalLabel">Filtros Avançados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="listar.php" method="get" id="advancedFilterForm">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label for="termo_busca_modal" class="form-label">Nome do Paciente ou Número da Guia</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-search"></i></span>
                                    <input type="text" id="termo_busca_modal" name="termo_busca" class="form-control" 
                                        value="<?php echo htmlspecialchars($termo_busca); ?>">
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="filtro_status_modal" class="form-label">Status</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                                    <select id="filtro_status_modal" name="filtro_status" class="form-select">
                                        <option value="">Todos</option>
                                        <option value="Emitido" <?php echo $filtro_status == 'Emitido' ? 'selected' : ''; ?>>Emitido</option>
                                        <option value="Subiu" <?php echo $filtro_status == 'Subiu' ? 'selected' : ''; ?>>Subiu</option>
                                        <option value="Cancelado" <?php echo $filtro_status == 'Cancelado' ? 'selected' : ''; ?>>Cancelado</option>
                                        <option value="Saiu" <?php echo $filtro_status == 'Saiu' ? 'selected' : ''; ?>>Saiu</option>
                                        <option value="Retornou" <?php echo $filtro_status == 'Retornou' ? 'selected' : ''; ?>>Retornou</option>
                                        <option value="Não Usou" <?php echo $filtro_status == 'Não Usou' ? 'selected' : ''; ?>>Não Usou</option>
                                        <option value="Assinado" <?php echo $filtro_status == 'Assinado' ? 'selected' : ''; ?>>Assinado</option>
                                        <option value="Faturado" <?php echo $filtro_status == 'Faturado' ? 'selected' : ''; ?>>Faturado</option>
                                        <option value="Enviado a BM" <?php echo $filtro_status == 'Enviado a BM' ? 'selected' : ''; ?>>Enviado a BM</option>
                                        <option value="Devolvido BM" <?php echo $filtro_status == 'Devolvido BM' ? 'selected' : ''; ?>>Devolvido BM</option>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="filtro_convenio_modal" class="form-label">Convênio</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                                    <select id="filtro_convenio_modal" name="filtro_convenio" class="form-select">
                                        <option value="">Todos</option>
                                        <?php while ($convenio = $result_convenios->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($convenio['paciente_convenio']); ?>" 
                                                <?php echo $filtro_convenio == $convenio['paciente_convenio'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($convenio['paciente_convenio']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="filtro_mes_modal" class="form-label">Mês</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    <select id="filtro_mes_modal" name="filtro_mes" class="form-select">
                                        <option value="">Todos</option>
                                        <?php while ($mes = $result_meses->fetch_assoc()): ?>
                                            <option value="<?php echo htmlspecialchars($mes['paciente_mes']); ?>" 
                                                <?php echo $filtro_mes == $mes['paciente_mes'] ? 'selected' : ''; ?>>
                                                <?php echo htmlspecialchars($mes['paciente_mes']); ?>
                                            </option>
                                        <?php endwhile; ?>
                                    </select>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <label for="itens_por_pagina_modal" class="form-label">Itens por página</label>
                                <div class="input-group">
                                    <span class="input-group-text"><i class="fas fa-list-ol"></i></span>
                                    <select id="itens_por_pagina_modal" name="itens_por_pagina" class="form-select">
                                        <option value="10" <?php echo $itens_por_pagina == 10 ? 'selected' : ''; ?>>10</option>
                                        <option value="25" <?php echo $itens_por_pagina == 25 ? 'selected' : ''; ?>>25</option>
                                        <option value="50" <?php echo $itens_por_pagina == 50 ? 'selected' : ''; ?>>50</option>
                                        <option value="100" <?php echo $itens_por_pagina == 100 ? 'selected' : ''; ?>>100</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <a href="listar.php" class="btn btn-outline-secondary me-2">
                        <i class="fas fa-eraser me-1"></i> Limpar filtros
                    </a>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('advancedFilterForm').submit();">
                        <i class="fas fa-filter me-1"></i> Aplicar filtros
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de exportação -->
    <div class="modal fade" id="exportModal" tabindex="-1" aria-labelledby="exportModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exportModalLabel">Exportar Dados</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <form action="exportar.php" method="post" id="exportForm">
                        <input type="hidden" name="query" value="<?php echo htmlspecialchars($where_clause); ?>">
                        
                        <div class="mb-3">
                            <label class="form-label">Formato de Exportação</label>
                            <div class="d-flex">
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="formato" id="formatoExcel" value="excel" checked>
                                    <label class="form-check-label" for="formatoExcel">
                                        <i class="fas fa-file-excel text-success me-1"></i> Excel
                                    </label>
                                </div>
                                <div class="form-check me-3">
                                    <input class="form-check-input" type="radio" name="formato" id="formatoCsv" value="csv">
                                    <label class="form-check-label" for="formatoCsv">
                                        <i class="fas fa-file-csv text-primary me-1"></i> CSV
                                    </label>
                                </div>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" name="formato" id="formatoPdf" value="pdf">
                                    <label class="form-check-label" for="formatoPdf">
                                        <i class="fas fa-file-pdf text-danger me-1"></i> PDF
                                    </label>
                                </div>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label class="form-label">Opções de Exportação</label>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="incluir_cabecalho" id="incluirCabecalho" checked>
                                <label class="form-check-label" for="incluirCabecalho">
                                    Incluir cabeçalho
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" name="aplicar_filtros" id="aplicarFiltros" checked>
                                <label class="form-check-label" for="aplicarFiltros">
                                    Aplicar filtros atuais
                                </label>
                            </div>
                        </div>
                        
                        <div class="mb-3">
                            <label for="seleciona_campos" class="form-label">Campos a Exportar</label>
                            <select class="form-select" id="seleciona_campos" name="campos[]" multiple size="8">
                                <option value="id" selected>ID</option>
                                <option value="paciente_nome" selected>Nome</option>
                                <option value="paciente_convenio" selected>Convênio</option>
                                <option value="paciente_guia" selected>Número da Guia</option>
                                <option value="paciente_status" selected>Status</option>
                                <option value="paciente_especialidade" selected>Especialidade</option>
                                <option value="paciente_mes" selected>Mês</option>
                                <option value="paciente_section" selected>Sessões</option>
                                <option value="paciente_valor">Valor</option>
                                <option value="paciente_data_remessa">Data Remessa</option>
                                <option value="paciente_validade">Validade</option>
                                <option value="paciente_entrada">Entrada</option>
                                <option value="paciente_saida">Saída</option>
                                <option value="data_hora_insercao">Data de Atualização</option>
                            </select>
                            <small class="form-text text-muted">Segure CTRL para selecionar múltiplos campos.</small>
                        </div>
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-primary" onclick="document.getElementById('exportForm').submit();">
                        <i class="fas fa-download me-1"></i> Exportar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de detalhes -->
    <div class="modal fade" id="detailsModal" tabindex="-1" aria-labelledby="detailsModalLabel" aria-hidden="true">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="detailsModalLabel">Detalhes da Guia</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <div class="text-center">
                        <div class="spinner-border text-primary" role="status">
                            <span class="visually-hidden">Carregando...</span>
                        </div>
                        <p class="mt-2">Carregando detalhes...</p>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                    <button type="button" class="btn btn-primary btn-edit-details" style="display: none;">
                        <i class="fas fa-edit me-1"></i> Editar
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Modal de confirmação de exclusão -->
    <div class="modal fade" id="deleteConfirmModal" tabindex="-1" aria-labelledby="deleteConfirmModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-danger text-white">
                    <h5 class="modal-title" id="deleteConfirmModalLabel">Confirmar Exclusão</h5>
                    <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
                </div>
                <div class="modal-body">
                    <p><i class="fas fa-exclamation-triangle text-warning me-2"></i> Você está prestes a excluir a guia <strong id="deleteGuiaId"></strong>.</p>
                    <p>Esta ação <strong class="text-danger">não pode ser desfeita</strong>. Tem certeza que deseja continuar?</p>
                    <form id="deleteForm" action="../database/remover.php" method="post">
                        <input type="hidden" id="id_guia_delete" name="id_guia" value="">
                    </form>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                    <button type="button" class="btn btn-danger" id="confirmDelete">
                        <i class="fas fa-trash me-1"></i> Excluir
                    </button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script>
        document.addEventListener('DOMContentLoaded', function() {
            // Inicializar tooltips
            var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
            var tooltipList = tooltipTriggerList.map(function (tooltipTriggerEl) {
                return new bootstrap.Tooltip(tooltipTriggerEl);
            });
            
            // Botões de edição
            document.querySelectorAll('.edit-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    window.location.href = '../index.php?action=edit&id=' + id;
                });
            });
            
            // Botões de detalhes
            document.querySelectorAll('.details-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    const detailsModal = new bootstrap.Modal(document.getElementById('detailsModal'));
                    
                    // Resetar modal e mostrar loader
                    document.querySelector('#detailsModal .modal-body').innerHTML = `
                        <div class="text-center">
                            <div class="spinner-border text-primary" role="status">
                                <span class="visually-hidden">Carregando...</span>
                            </div>
                            <p class="mt-2">Carregando detalhes...</p>
                        </div>
                    `;
                    
                    // Esconder botão de edição
                    document.querySelector('.btn-edit-details').style.display = 'none';
                    
                    // Abrir modal
                    detailsModal.show();
                    
                    // Carregar detalhes via AJAX
                    fetch('get_detalhes_guia.php?id=' + id)
                        .then(response => response.text())
                        .then(html => {
                            document.querySelector('#detailsModal .modal-body').innerHTML = html;
                            document.querySelector('.btn-edit-details').style.display = 'block';
                            document.querySelector('.btn-edit-details').setAttribute('data-id', id);
                        })
                        .catch(error => {
                            document.querySelector('#detailsModal .modal-body').innerHTML = `
                                <div class="alert alert-danger">
                                    <i class="fas fa-exclamation-circle me-2"></i> Erro ao carregar detalhes. Tente novamente.
                                </div>
                            `;
                        });
                });
            });
            
            // Botão editar do modal de detalhes
            document.querySelector('.btn-edit-details')?.addEventListener('click', function() {
                const id = this.getAttribute('data-id');
                window.location.href = '../index.php?action=edit&id=' + id;
            });
            
            // Botões de exclusão
            document.querySelectorAll('.delete-button').forEach(button => {
                button.addEventListener('click', function() {
                    const id = this.getAttribute('data-id');
                    document.getElementById('id_guia_delete').value = id;
                    document.getElementById('deleteGuiaId').textContent = id;
                    const deleteModal = new bootstrap.Modal(document.getElementById('deleteConfirmModal'));
                    deleteModal.show();
                });
            });
            
            // Confirmar exclusão
            document.getElementById('confirmDelete')?.addEventListener('click', function() {
                document.getElementById('deleteForm').submit();
            });
        });
    </script>
</body>

</html>

<?php
$stmt->close();
$conn->close();
?>