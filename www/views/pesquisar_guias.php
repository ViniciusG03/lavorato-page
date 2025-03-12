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

// Conectar ao banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Pesquisar Guia em Relatórios</title>
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
        <h1 class="text-center mb-4">Pesquisar Guia em Relatórios</h1>

        <div class="card">
            <div class="card-header">
                <h5 class="card-title mb-0">Pesquisar</h5>
            </div>
            <div class="card-body">
                <form method="get" action="">
                    <div class="row mb-3">
                        <div class="col-md-8">
                            <label for="numero_guia" class="form-label">Número da Guia:</label>
                            <input type="text" class="form-control" id="numero_guia" name="numero_guia" placeholder="Digite o número da guia" value="<?php echo isset($_GET['numero_guia']) ? htmlspecialchars($_GET['numero_guia']) : ''; ?>">
                        </div>
                        <div class="col-md-4 d-flex align-items-end">
                            <button type="submit" class="btn btn-primary">Pesquisar</button>
                        </div>
                    </div>
                </form>

                <?php
                if (isset($_GET['numero_guia']) && !empty($_GET['numero_guia'])) {
                    $numeroGuia = $_GET['numero_guia'];

                    // Consultar relatórios relacionados à guia
                    $sql = "SELECT rg.*, r.titulo, r.usuario_origem, r.usuario_destino, r.data_compartilhamento, r.status, r.dados_relatorio
                            FROM relatorios_guias rg
                            JOIN relatorios_compartilhados r ON rg.relatorio_id = r.relatorio_id
                            WHERE rg.paciente_guia = ?
                            ORDER BY r.data_compartilhamento DESC";

                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("s", $numeroGuia);
                    $stmt->execute();
                    $result = $stmt->get_result();

                    if ($result->num_rows > 0) {
                        echo '<h4 class="mt-4">Relatórios encontrados para a guia: ' . htmlspecialchars($numeroGuia) . '</h4>';
                        echo '<div class="table-responsive">';
                        echo '<table class="table table-striped">';
                        echo '<thead><tr>';
                        echo '<th>Título do Relatório</th>';
                        echo '<th>Enviado Por</th>';
                        echo '<th>Enviado Para</th>';
                        echo '<th>Data</th>';
                        echo '<th>Status</th>';
                        echo '<th>Ações</th>';
                        echo '</tr></thead>';
                        echo '<tbody>';

                        while ($row = $result->fetch_assoc()) {
                            echo '<tr>';
                            echo '<td>' . htmlspecialchars($row['titulo']) . '</td>';
                            echo '<td>' . ($usuarios[$row['usuario_origem']] ?? $row['usuario_origem']) . '</td>';
                            echo '<td>' . ($usuarios[$row['usuario_destino']] ?? $row['usuario_destino']) . '</td>';
                            echo '<td>' . date('d/m/Y H:i', strtotime($row['data_compartilhamento'])) . '</td>';
                            echo '<td>' . ucfirst($row['status']) . '</td>';
                            echo '<td>';
                            echo '<form action="../database/visualizar_relatorio.php" method="post" target="_blank">';
                            echo '<input type="hidden" name="relatorio_html" value="' . $row['dados_relatorio'] . '">';
                            echo '<button type="submit" class="btn btn-sm btn-info">Visualizar</button>';
                            echo '</form>';
                            echo '</td>';
                            echo '</tr>';
                        }

                        echo '</tbody></table>';
                        echo '</div>';
                    } else {
                        echo '<div class="alert alert-info mt-4">';
                        echo 'Nenhum relatório encontrado para a guia: ' . htmlspecialchars($numeroGuia);
                        echo '</div>';
                    }

                    $stmt->close();
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