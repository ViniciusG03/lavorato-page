<?php
$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$database = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

$sql = "SELECT p.id, p.Nome_paciente, p.Nome_google, p.Data_inicio, p.Data_final, p.Email, p.Matricula,
        d.Documento_tipo, d.Especialidade, d.Data_emissao, d.Data_validade
        FROM paciente p
        LEFT JOIN documento d ON p.id = d.Paciente_ID"; // Ajuste o nome da coluna da chave estrangeira conforme necessário

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
        <form action="buscar_paciente.php" method="get" class="mb-4">
            <div class="row g-3">
                <div class="col-md-4">
                    <label for="busca" class="form-label">Buscar por:</label>
                    <select id="busca" name="categoria" class="form-select">
                        <option value="Nome_paciente">Nome do Paciente</option>
                        <option value="Matricula">Matricula</option>
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
                        <th>Nome Google</th>
                        <th>Data Inicio</th>
                        <th>Data Final</th>
                        <th>Email</th>
                        <th>Matricula</th>
                        <th>Documento</th>
                        <th>Especialidade</th>
                        <th>Data de Emissao</th>
                        <th>Data de Validade</th>
                    </tr>
                </thead>
                <tbody>
                    <?php
                    if ($result->num_rows > 0) {
                            while($row = $result->fetch_assoc()) {
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
                    } else {
                        echo "<tr><td colspan='6'>Nenhum paciente encontrado</td></tr>";
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
