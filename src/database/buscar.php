<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Resultados da Busca</title>
    <link
      rel="shortcut icon"
      href="../assets/Logo-Lavorato-alfa.png"
      type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/buscar.css">
</head>
<body>
    <div class="nav">
      <button id="homeButton">Home</button>
      <h1>Lavorato's System</h1>
    </div>
    <h1 id="title">Resultados da Busca</h1>
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

        $sql = "SELECT * FROM pacientes WHERE ";

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

        if ($stmt) {
            $termo = "%$termo%";
            
            $stmt->bind_param("s", $termo);
            $stmt->execute();

            $result = $stmt->get_result();

            if ($result->num_rows > 0) {
                echo "<table>";
                echo "<thead><tr>";
                echo "<th>ID</th>";
                echo "<th>Nome do Paciente</th>";
                echo "<th>Convênio</th>";
                echo "<th>Número da Guia</th>";
                echo "<th>Status</th>";
                echo "<th>Número de Lote</th>";
                echo "<th>Especialidade</th>";
                echo "<th>Mês</th>";
                echo "<th>Seções</th>";
                echo "<th>Entrada</th>";
                echo "<th>Saída</th>";
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
                    echo "</tr>";
                }
                
                echo "</tbody>";
                echo "</table>";
            } else {
                echo "Nenhum paciente encontrado";
            }

            $stmt->close();
        } else {
            echo "Erro na preparação da consulta: " . $conn->error;
        }
    } else {
        echo "Parâmetros não especificados";
    }
} else {
    echo "Método de requisição inválido";
}

$conn->close();
?>
    <script>
        document.addEventListener('DOMContentLoaded', () => {
          const btnListar = document.getElementById('homeButton');
          btnListar.addEventListener('click', () => {
            window.location.href = '../index.html';
          });
      });
    </script>
</body>
</html>
