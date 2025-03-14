<?php
// Arquivo: views/visualizar_relatorio.php
// Este arquivo é incluído pelo gerar_relatorio.php quando o formato é HTML

// Variáveis esperadas: $dados, $titulo_relatorio, $periodo_mes, $status_array, $convenio, $especialidade

// Prevenir acesso direto
if (!isset($dados)) {
    header("Location: relatorios_customizados.php");
    exit();
}

// Garantir que $status_array seja sempre um array
if (!isset($status_array) || !is_array($status_array)) {
    $status_array = [];
}
?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($titulo_relatorio); ?> - Lavorato System</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="https://cdn.datatables.net/1.11.5/css/dataTables.bootstrap5.min.css">
    <link rel="stylesheet" href="../stylesheet/base.css">
    <style>
        .relatorio-header {
            background-color: #f8f9fa;
            padding: 15px;
            border-radius: 8px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .filtro-badge {
            display: inline-block;
            background-color: var(--main-color-btn);
            color: white;
            font-size: 0.8rem;
            padding: 0.3rem 0.6rem;
            border-radius: 30px;
            margin-right: 5px;
            margin-bottom: 5px;
        }

        .table th {
            background-color: var(--main-color-btn);
            color: white;
            white-space: nowrap;
        }

        .export-buttons {
            margin-bottom: 20px;
        }

        /* Cores alternadas para linhas da tabela */
        .table-striped tbody tr:nth-of-type(odd) {
            background-color: rgba(0, 0, 0, 0.05);
        }

        /* Hover sobre as linhas da tabela */
        .table-hover tbody tr:hover {
            background-color: rgba(0, 179, 255, 0.1);
        }

        /* Garantir que a tabela role horizontalmente */
        .table-responsive {
            overflow-x: auto;
            -webkit-overflow-scrolling: touch;
            width: 100%;
        }

        /* Ajustar o layout dos controles do DataTables */
        div.dataTables_wrapper div.dataTables_length {
            margin-bottom: 15px;
            display: flex;
            align-items: center;
            gap: 8px;
            /* Espaço entre os elementos */
        }

        div.dataTables_wrapper div.dataTables_length select {
            padding-right: 30px;
            /* Espaço para o ícone não sobrepor o texto */
            min-width: 80px;
        }

        div.dataTables_wrapper div.dataTables_length label {
            display: flex;
            align-items: center;
            gap: 8px;
            margin: 0;
        }

        /* Responsividade para dispositivos móveis */
        @media (max-width: 768px) {
            .col-md-6.text-end {
                text-align: left !important;
                margin-top: 1rem;
            }

            .export-buttons {
                text-align: center !important;
            }

            .export-buttons .btn {
                width: 100%;
                margin-bottom: 0.5rem;
            }
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
                    <li class="nav-item">
                        <a class="nav-link" href="../views/relatorios_customizados.php">Voltar aos Filtros</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <div class="container py-4">
        <h1 class="text-center mb-4"><?php echo htmlspecialchars($titulo_relatorio); ?></h1>

        <div class="relatorio-header">
            <div class="row">
                <div class="col-md-6">
                    <h5>Filtros Aplicados:</h5>
                    <div>
                        <?php if (!empty($periodo_mes)): ?>
                            <span class="filtro-badge">Mês: <?php echo htmlspecialchars($periodo_mes); ?></span>
                        <?php else: ?>
                            <span class="filtro-badge">Todos os meses</span>
                        <?php endif; ?>

                        <?php if (!empty($status_array)): ?>
                            <span class="filtro-badge">Status: <?php echo htmlspecialchars(implode(', ', $status_array)); ?></span>
                        <?php else: ?>
                            <span class="filtro-badge">Todos os status</span>
                        <?php endif; ?>

                        <?php if (!empty($convenio)): ?>
                            <span class="filtro-badge">Convênio: <?php echo htmlspecialchars($convenio); ?></span>
                        <?php else: ?>
                            <span class="filtro-badge">Todos os convênios</span>
                        <?php endif; ?>

                        <?php if (!empty($especialidade)): ?>
                            <span class="filtro-badge">Especialidade: <?php echo htmlspecialchars($especialidade); ?></span>
                        <?php else: ?>
                            <span class="filtro-badge">Todas as especialidades</span>
                        <?php endif; ?>
                    </div>
                </div>
                <div class="col-md-6 text-end">
                    <p><strong>Data do Relatório:</strong> <?php echo date('d/m/Y H:i:s'); ?></p>
                    <p><strong>Total de Registros:</strong> <?php echo count($dados); ?></p>
                </div>
            </div>
        </div>

        <div class="export-buttons text-end">
            <form action="../database/gerar_relatorio_customizados.php" method="post" class="d-inline-block me-2 mb-2">
                <!-- Repassar os mesmos parâmetros do filtro -->
                <input type="hidden" name="periodo_mes" value="<?php echo htmlspecialchars($periodo_mes ?? ''); ?>">
                <?php foreach ($status_array as $status): ?>
                    <input type="hidden" name="status[]" value="<?php echo htmlspecialchars($status); ?>">
                <?php endforeach; ?>
                <input type="hidden" name="convenio" value="<?php echo htmlspecialchars($convenio ?? ''); ?>">
                <input type="hidden" name="especialidade" value="<?php echo htmlspecialchars($especialidade ?? ''); ?>">
                <input type="hidden" name="formato" value="excel">
                <button type="submit" class="btn btn-success">
                    <i class="fas fa-file-excel me-1"></i> Exportar para Excel
                </button>
            </form>

            <form action="../database/gerar_relatorio_customizados.php" method="post" class="d-inline-block mb-2">
                <!-- Repassar os mesmos parâmetros do filtro -->
                <input type="hidden" name="periodo_mes" value="<?php echo htmlspecialchars($periodo_mes ?? ''); ?>">
                <?php foreach ($status_array as $status): ?>
                    <input type="hidden" name="status[]" value="<?php echo htmlspecialchars($status); ?>">
                <?php endforeach; ?>
                <input type="hidden" name="convenio" value="<?php echo htmlspecialchars($convenio ?? ''); ?>">
                <input type="hidden" name="especialidade" value="<?php echo htmlspecialchars($especialidade ?? ''); ?>">
                <input type="hidden" name="formato" value="pdf">
                <button type="submit" class="btn btn-danger">
                    <i class="fas fa-file-pdf me-1"></i> Exportar para PDF
                </button>
            </form>
        </div>

        <?php if (!empty($dados)): ?>
            <div class="table-responsive">
                <table id="relatorioTable" class="table table-striped table-hover">
                    <thead>
                        <tr>
                            <?php if (isset($dados[0]) && is_array($dados[0])): ?>
                                <?php foreach (array_keys($dados[0]) as $coluna): ?>
                                    <th>
                                        <?php
                                        // Remove o prefixo "paciente_" do nome da coluna
                                        $coluna_formatada = str_replace('paciente_', '', $coluna);
                                        // Capitaliza a primeira letra de cada palavra
                                        $coluna_formatada = ucwords(str_replace('_', ' ', $coluna_formatada));
                                        echo htmlspecialchars($coluna_formatada);
                                        ?>
                                    </th>
                                <?php endforeach; ?>
                            <?php endif; ?>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($dados as $linha): ?>
                            <tr>
                                <?php foreach ($linha as $valor): ?>
                                    <td><?php echo htmlspecialchars($valor); ?></td>
                                <?php endforeach; ?>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <div class="alert alert-info text-center">
                <h4>Nenhum registro encontrado</h4>
                <p>Tente modificar os filtros para obter resultados.</p>
            </div>
        <?php endif; ?>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/jquery.dataTables.min.js"></script>
    <script src="https://cdn.datatables.net/1.11.5/js/dataTables.bootstrap5.min.js"></script>
    <script>
        $(document).ready(function() {
            // Inicializa o DataTables apenas se a tabela existir e tiver dados
            if ($("#relatorioTable").length && $("#relatorioTable tbody tr").length > 0) {
                $('#relatorioTable').DataTable({
                    "language": {
                        "url": "//cdn.datatables.net/plug-ins/1.11.5/i18n/pt-BR.json",
                        "lengthMenu": "Exibir _MENU_ resultados por página"
                    },
                    "pageLength": 25,
                    "order": [],
                    "scrollX": true,
                    "responsive": false,
                    "dom": '<"top"lf>rt<"bottom"ip><"clear">',
                    "lengthMenu": [
                        [10, 25, 50, 100, -1],
                        [10, 25, 50, 100, "Todos"]
                    ],
                    "drawCallback": function(settings) {
                        // Aplicar estilos adicionais após carregar a tabela
                        $('.dataTables_length select').addClass('form-select form-select-sm');
                        if ($(window).width() < 768) {
                            $('.dataTables_length, .dataTables_filter').addClass('w-100');
                        }
                    }
                });
            }
        });
    </script>
</body>

</html>