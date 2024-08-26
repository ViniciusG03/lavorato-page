<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}
?>

<?php
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

$sql = "SELECT *, DATE_FORMAT(data_hora_insercao, '%d/%m/%Y %H:%i:%s') AS data_hora_formatada FROM pacientes";
$result = $conn->query($sql);

if ($result === false) {
    die("Erro na consulta: " . $conn->error);
}

$conn->close();
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lista de Pacientes</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <link rel="stylesheet" href="../stylesheet/listar.css">
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
        <h1 class="mb-4">Lista de Pacientes</h1>

        <!-- Formulário de busca usando Bootstrap -->
        <form action="buscar.php" method="get" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
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
                <div class="col-md-4">
                    <label for="termo" class="form-label">Termo de busca:</label>
                    <input type="text" id="termo" name="termo" class="form-control"
                        placeholder="Digite o termo de busca" autocomplete="off">
                </div>
                <div class="col-md-4 d-flex align-items-end">
                    <button type="submit" class="btn btn-primary">Buscar</button>
                </div>
            </div>
        </form>

        <!-- Tabela de pacientes usando Bootstrap -->
        <div class="table-responsive mt-4">
            <table>
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nome</th>
                        <th>Convênio</th>
                        <th>Número</th>
                        <th>Status</th>
                        <th>Lote</th>
                        <th>Especialidade</th>
                        <th>Mês</th>
                        <th>Sessões</th>
                        <th>Qtd. Faturada</th>
                        <th>Valor</th>
                        <th>Remessa</th>
                        <th>Validade</th>
                        <th>Entrada</th>
                        <th>Saida</th>
                        <th>Atualização</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
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
                            echo "<td>" . $row["paciente_faturado"] . "</td>";
                            echo "<td>" . $row["paciente_valor"] . "</td>";
                            echo "<td>" . $row["paciente_data_remessa"] . "</td>";
                            echo "<td>" . $row["paciente_validade"] . "</td>";
                            echo "<td>" . $row["paciente_entrada"] . "</td>";
                            echo "<td>" . $row["paciente_saida"] . "</td>";
                            echo "<td>" . $row["data_hora_formatada"] . "</td>";
                            echo "</tr>";
                        }
                    } else {
                        echo "<tr><td colspan='5'>Nenhum paciente encontrado</td></tr>";
                    }
                    ?>
                </tbody>
            </table>
        </div>
    </div>

    <script src="https://stackpath.bootstrapcdn.com/bootstrap/5.3.0/js/bootstrap.bundle.min.js"></script>
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