<?php
session_start();
require_once __DIR__ . '/functions_especialidades.php';

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../stylesheet/buscar.css">
    <style>
        
    </style>
</head>

<body>
    <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
        <div class="container">
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

    <div class="container mt-4">
        <form action="buscar.php" method="get" class="mb-4">
            <div class="row g-3">
                <div class="col-md-6">
                    <label for="busca" class="form-label">Buscar por:</label>
                    <select id="busca" name="categoria" class="form-select">
                        <option value="paciente_nome">Nome do Paciente</option>
                        <option value="paciente_convenio">Convênio</option>
                        <option value="paciente_guia">Número da Guia</option>
                        <option value="paciente_status">Status</option>
                        <option value="paciente_especialidade">Especialidade</option>
                        <option value="paciente_mes">Mês</option>
                    </select>
                </div>
                <div class="col-md-6">
                    <label for="termo" class="form-label">Termo de busca:</label>
                    <input type="text" id="termo" name="termo" class="form-control"
                        placeholder="Digite o termo de busca" autocomplete="off">
                </div>
                <div class="col-md-6 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>
    </div>

    <h1 id="title">Resultados da Busca</h1>
    <div class="container">
        <?php
        $servername = "mysql.lavoratoguias.kinghost.net";
        $username = "lavoratoguias";
        $password = "A3g7K2m9T5p8L4v6";
        $database = "lavoratoguias";

        $conn = new mysqli($servername, $username, $password, $database);
        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET["categoria"]) && isset($_GET["termo"])) {
                $categoria = isset($_GET['categoria']) ? $_GET['categoria'] : '';
                $termo = isset($_GET['termo']) ? $_GET['termo'] : '';
                $filtro_adicional = isset($_GET['filtro_adicional']) ? $_GET['filtro_adicional'] : '';
                $termo_adicional = isset($_GET['termo_adicional']) ? $_GET['termo_adicional'] : '';

                $sql = "SELECT *, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes WHERE 1=1";

                // Adiciona o primeiro filtro se existir
                if ($categoria && $termo) {
                    $termo = $conn->real_escape_string($termo);
                    $sql .= " AND $categoria LIKE '%$termo%'";
                }

                // Adiciona o filtro adicional se existir
                if ($filtro_adicional && $termo_adicional) {
                    $termo_adicional = $conn->real_escape_string($termo_adicional);
                    $sql .= " AND $filtro_adicional LIKE '%$termo_adicional%'";
                }

                $sql .= " ORDER BY CASE 
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
                            ELSE 13 END";

                $result = $conn->query($sql);

                if ($result === false) {
                    die("Erro na consulta: " . $conn->error);
                }

                if ($result->num_rows > 0) {
                    $numRows = $result->num_rows;
                    echo "<p>Total de resultados encontrados: $numRows</p>";
                    echo "<div class='table-responsive mt-4'>";
                    echo "<table>";
                    echo "<thead><tr>";
                    echo "<th>ID</th>";
                    echo "<th>Nome</th>";
                    echo "<th>Convênio</th>";
                    echo "<th>Número</th>";
                    echo "<th>Status</th>";
                    echo "<th>Lote</th>";
                    echo "<th>Especialidades</th>";
                    echo "<th>Mês</th>";
                    echo "<th>Sessões</th>";
                    echo "<th>Qtd. Faturada</th>";
                    echo "<th>Valor</th>";
                    echo "<th>Remessa</th>";
                    echo "<th>Validade</th>";
                    echo "<th>Entrada</th>";
                    echo "<th>Saída</th>";
                    echo "<th>Atualização</th>";
                    echo "</tr></thead>";
                    echo "<tbody>";

                    while ($row = $result->fetch_assoc()) {
                        $especialidades = obter_especialidades_paciente($row["id"], $conn);

                        echo "<tr>";
                        echo "<td>" . htmlspecialchars($row["id"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_nome"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_convenio"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_guia"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_status"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_lote"]) . "</td>";
                        echo "<td>" . formatar_especialidades($especialidades) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_mes"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_section"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_faturado"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_valor"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_data_remessa"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_validade"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_entrada"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["paciente_saida"]) . "</td>";
                        echo "<td>" . htmlspecialchars($row["data_hora_formatada"]) . "</td>";
                        echo "</tr>";
                    }

                    echo "</tbody>";
                    echo "</table>";
                    echo "</div>";
                } else {
                    echo '<h1>Nenhum paciente encontrado</h1><br><p>Clique em "Home" para voltar à página principal!</p>';
                }

            } else {
                echo '<h1>Parâmetros não especificados</h1><br><p>Clique em "Home" para voltar à página principal!</p>';
            }
        } else {
            echo '<h1>Método de requisição inválido</h1><br><p>Clique em "Home" para voltar à página principal!</p>';
        }

        $conn->close();
        ?>

    </div>
</body>

</html>