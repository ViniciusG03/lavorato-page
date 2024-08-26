<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../../login/login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
    <link rel="shortcut icon" href="../../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../../bootstrap/css/bootstrap.min.css">
    <script src="../../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../../stylesheet/buscar.css">
    <link rel="shortcut icon" href="../../assets/Logo-Lavorato-alfa.png" type="image/x-icon">
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
                        <a class="nav-link" href="../../controle.php">Controle</a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

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
                $categoria = $_GET["categoria"];
                $termo = $_GET["termo"];

                $sql = "SELECT p.id, p.Nome_paciente, p.Nome_google, p.Data_inicio, p.Data_final, p.Email, p.Matricula,
                d.Documento_tipo, d.Especialidade, d.Data_emissao, d.Data_validade
                FROM paciente p
                LEFT JOIN documento d ON p.id = d.Paciente_ID
                WHERE $categoria LIKE ?";

                if (empty($termo)) {
                    echo '<h1>O termo não pode ser vazio!</h1><p>Clique em "Home" para voltar à página principal!</p>';
                } else {
                    switch ($categoria) {
                        case "Nome_paciente":
                        case "Matricula":
                            break;
                        default:
                            die("Categoria inválida");
                    }


                    $stmt = $conn->prepare($sql);
                    $termo = "%$termo%";
                    $stmt->bind_param("s", $termo);

                    if ($stmt->execute()) {
                        $result = $stmt->get_result();

                        if ($result->num_rows > 0) {
                            $numRows = $result->num_rows;

                            echo "<p>Total de resultados encontrados: $numRows</p>";
                            echo "<div class='table-responsive mt-4'><div class='table-responsive-striped'>";
                            echo "<table>";
                            echo "<thead><tr>";
                            echo "<th>ID</th>";
                            echo "<th>Nome</th>";
                            echo "<th>Nome Google</th>";
                            echo "<th>Data Inicio</th>";
                            echo "<th>Data Final</th>";
                            echo "<th>Email</th>";
                            echo "<th>Matricula</th>";
                            echo "<th>Documento</th>";
                            echo "<th>Especialidade</th>";
                            echo "<th>Data Emisao</th>";
                            echo "<th>Data Validade</th>";
                            echo "</tr></thead>";
                            echo "<tbody>";

                            while ($row = $result->fetch_assoc()) {
                                echo "<tr>";
                                echo "<td>" . $row["id"] . "</td>";
                                echo "<td>" . $row["Nome_paciente"] . "</td>";
                                echo "<td>" . $row["Nome_google"] . "</td>";
                                echo "<td>" . $row["Data_inicio"] . "</td>";
                                echo "<td>" . $row["Data_final"] . "</td>";
                                echo "<td>" . $row["Email"] . "</td>";
                                echo "<td>" . $row["Matricula"] . "</td>";
                                echo "<td>" . $row["Documento_tipo"] . "</td>";
                                echo "<td>" . $row["Especialidade"] . "</td>";
                                echo "<td>" . $row["Data_emissao"] . "</td>";
                                echo "<td>" . $row["Data_validade"] . "</td>";
                                echo "</tr>";
                            }

                            echo "</tbody>";
                            echo "</table>";
                            echo "</div></div>";
                        } else {
                            echo '<h1>Nenhum paciente encontrado</h1><br><p>Clique em "Home" para voltar à página principal!</p>';
                        }
                    } else {
                        echo "<p>Erro ao executar a consulta: " . $stmt->error . "</p>";
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
                window.location.href = '../../index.php';
            });
        });
    </script>
</body>

</html>