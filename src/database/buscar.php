<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: /lavorato-page/src/login/login.php");
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
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../stylesheet/buscar.css">
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

    <h1 id="title">Resultados da Busca</h1>
    <div class="container">
        <?php
        $servername = "localhost";
        $username = "root";
        $password = "lavorato@admin2024";
        $database = "lavoratoDB";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Falha na conexão: " . $conn->connect_error);
        }

        if ($_SERVER["REQUEST_METHOD"] == "GET") {
            if (isset($_GET["categoria"]) && isset($_GET["termo"])) {
                $categoria = $_GET["categoria"];
                $termo = $_GET["termo"];

                $sql = "SELECT *, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes WHERE ";

                if (!empty($_GET["data"])) {
                    $data = $_GET["data"];
                    $sql .= "AND DATE_FORMAT(data_hora_insercao, '%Y-%m-%d') = ?";
                }

                if (empty($termo) && empty($data)) {
                    echo '<h1>O termo e a data não podem ser vazios!</h1><p>Clique em "Home" para voltar à página principal!</p>';
                } else {
                    switch ($categoria) {
                        case "paciente_nome":
                        case "paciente_convenio":
                        case "paciente_guia":
                        case "paciente_status":
                        case "paciente_especialidade":
                        case "paciente_mes":
                            $sql .= "$categoria LIKE ?";
                            break;
                        default:
                            die("Categoria inválida");
                    }

                    $stmt = $conn->prepare($sql);

                    if (!empty($data)) {
                        $termo .= "%";
                        $stmt->bind_param("ss", $termo, $data);
                    } else {
                        $termo = "%$termo%";
                        $stmt->bind_param("s", $termo);
                    }

                    $stmt->execute();

                    $result = $stmt->get_result();

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
                        echo "<th>Especialidade</th>";
                        echo "<th>Mês</th>";
                        echo "<th>Sessões</th>";
                        echo "<th>Entrada</th>";
                        echo "<th>Saída</th>";
                        echo "<th>Atualização</th>";
                        echo "</tr></thead>";
                        echo "<tbody>";

                        while ($row = $result->fetch_assoc()) {
                            echo "<tr>";
                            echo "<td>" . $row["id"] . "</td>";
                            echo "<td>" . $row["paciente_nome"] . "</td>";
                            echo "<td>" . $row["paciente_convenio"] . "</td>";
                            echo "<td>" . $row["paciente_guia"] . "</td>";
                            echo "<td>" . $row["paciente_status"] . "</td>";
                            echo "<td>" . $row["paciente_lote"] . "</td>";
                            echo "<td>" . $row["paciente_especialidade"] . "</td>";
                            echo "<td>" . $row["paciente_mes"] . "</td>";
                            echo "<td>" . $row["paciente_section"] . "</td>";
                            echo "<td>" . $row["paciente_entrada"] . "</td>";
                            echo "<td>" . $row["paciente_saida"] . "</td>";
                            echo "<td>" . $row["data_hora_formatada"] . "</td>";
                            echo "</tr>";
                        }

                        echo "</tbody>";
                        echo "</table>";
                        echo "</div>";
                    } else {
                        echo '<h1>Nenhum paciente encontrado</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                    }

                    $stmt->close();
                }
            } else {
                echo '<h1>Parâmetros não especificados</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
            }
        } else {
            echo '<h1>Método de requisição inválido</h1><br><p>Clique em "Home para voltar a página principal</p>';
        }

        $conn->close();
        ?>

    </div>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
            const btnListar = document.getElementById('homeButton');
            btnListar.addEventListener('click', () => {
                window.location.href = '../index.php';
            });
        });
    </script>
</body>

</html>