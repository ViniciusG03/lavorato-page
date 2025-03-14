<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Conexão com o banco
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

// Buscar meses disponíveis
$sql_meses = "SELECT DISTINCT paciente_mes FROM pacientes ORDER BY 
    CASE 
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
    END";
$result_meses = $conn->query($sql_meses);

// Buscar status disponíveis
$sql_status = "SELECT DISTINCT paciente_status FROM pacientes ORDER BY paciente_status";
$result_status = $conn->query($sql_status);

// Buscar convênios disponíveis
$sql_convenios = "SELECT DISTINCT paciente_convenio FROM pacientes ORDER BY paciente_convenio";
$result_convenios = $conn->query($sql_convenios);

// Buscar especialidades disponíveis
$sql_especialidades = "SELECT DISTINCT paciente_especialidade FROM pacientes WHERE paciente_especialidade IS NOT NULL AND paciente_especialidade != '' ORDER BY paciente_especialidade";
$result_especialidades = $conn->query($sql_especialidades);
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatórios Personalizados - Lavorato System</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../stylesheet/base.css">
    <style>
        .filter-section {
            background-color: #f8f9fa;
            border-radius: 8px;
            padding: 20px;
            margin-bottom: 20px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .filter-title {
            margin-bottom: 15px;
            color: var(--main-color-btn);
            font-weight: 600;
        }

        .form-check-input:checked {
            background-color: var(--main-color-btn);
            border-color: var(--main-color-btn);
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

    <div class="container py-4">
        <h1 class="text-center mb-4">Relatórios Personalizados</h1>

        <form action="../database/gerar_relatorio_customizado.php" method="post" id="relatorioForm">
            <div class="row">
                <div class="col-md-6">
                    <div class="filter-section">
                        <h4 class="filter-title">Período</h4>
                        <div class="mb-3">
                            <label for="periodo_mes" class="form-label">Mês</label>
                            <select class="form-select" id="periodo_mes" name="periodo_mes">
                                <option value="">Todos os meses</option>
                                <?php while ($mes = $result_meses->fetch_assoc()): ?>
                                    <option value="<?php echo $mes['paciente_mes']; ?>"><?php echo $mes['paciente_mes']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4 class="filter-title">Status</h4>
                        <div class="mb-3">
                            <?php while ($status = $result_status->fetch_assoc()): ?>
                                <div class="form-check">
                                    <input class="form-check-input" type="checkbox" name="status[]" value="<?php echo $status['paciente_status']; ?>" id="status_<?php echo str_replace(' ', '_', $status['paciente_status']); ?>">
                                    <label class="form-check-label" for="status_<?php echo str_replace(' ', '_', $status['paciente_status']); ?>">
                                        <?php echo $status['paciente_status']; ?>
                                    </label>
                                </div>
                            <?php endwhile; ?>
                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="filter-section">
                        <h4 class="filter-title">Convênio</h4>
                        <div class="mb-3">
                            <select class="form-select" id="convenio" name="convenio">
                                <option value="">Todos os convênios</option>
                                <?php while ($convenio = $result_convenios->fetch_assoc()): ?>
                                    <option value="<?php echo $convenio['paciente_convenio']; ?>"><?php echo $convenio['paciente_convenio']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4 class="filter-title">Especialidade</h4>
                        <div class="mb-3">
                            <select class="form-select" id="especialidade" name="especialidade">
                                <option value="">Todas as especialidades</option>
                                <?php while ($especialidade = $result_especialidades->fetch_assoc()): ?>
                                    <option value="<?php echo $especialidade['paciente_especialidade']; ?>"><?php echo $especialidade['paciente_especialidade']; ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                    </div>

                    <div class="filter-section">
                        <h4 class="filter-title">Formato de Exportação</h4>
                        <div class="mb-3">
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="formato" id="formatoHTML" value="html" checked>
                                <label class="form-check-label" for="formatoHTML">
                                    <i class="fas fa-table me-2"></i>Visualizar na tela
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="formato" id="formatoExcel" value="excel">
                                <label class="form-check-label" for="formatoExcel">
                                    <i class="fas fa-file-excel me-2"></i>Excel
                                </label>
                            </div>
                            <div class="form-check">
                                <input class="form-check-input" type="radio" name="formato" id="formatoPDF" value="pdf">
                                <label class="form-check-label" for="formatoPDF">
                                    <i class="fas fa-file-pdf me-2"></i>PDF
                                </label>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="text-center mt-4">
                <button type="submit" class="btn btn-primary btn-lg">
                    <i class="fas fa-chart-bar me-2"></i>Gerar Relatório
                </button>
            </div>
        </form>
    </div>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
</body>

</html>
<?php
$conn->close();
?>