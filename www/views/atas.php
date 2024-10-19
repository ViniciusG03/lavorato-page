<?php
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: ../login/login.php");
  exit();
}

$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

$sql = "SELECT *, DATE_FORMAT(validade, '%d/%m/%y') AS data_hora_formatada, validade FROM Atas";
$result = $conn->query($sql);

if ($result === false) {
  die("Erro na consulta: " . $conn->error);
}

?>
<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>ATAS/PM</title>
  <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
  <script src="../bootstrap/js/bootstrap.min.js"></script>
  <link rel="stylesheet" href="../stylesheet/atas.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="index.js"></script>
</head>

<body>

  <!-- Barra de navegação com bootstrap -->
  <nav class="navbar navbar-expand-lg navbar-dark" style="background-color: #00b3ffde;">
    <div class="container-fluid">
      <a class="navbar-brand" href="#">Lavorato's System</a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ml-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              Opções
            </a>
            <ul class="dropdown-menu" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="controle-page\controle.php">Controle</a></li>
              <?php
              if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                echo '<li><a class="dropdown-item" href="../login/cadastro.php">Cadastro</a></li>';
              }
              ?>
              <li><a class="dropdown-item" href="../login/logout.php">Logout</a></li>
              <li><a class="dropdown-item" href="atas.php">ATAS</a></li>
            </ul>
          </li>
        </ul>
      </div>
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
    <!-- Tabela de atas usando Bootstrap -->
    <div class="table-responsive mt-4">
      <table>
        <thead>
          <tr>
            <th>ID</th>
            <th>Nome</th>
            <th>Convênio</th>
            <th>Status</th>
            <th>Validade</th>
            <th>Tipo</th>
            <th>E-mail</th>
          </tr>
        </thead>
        <tbody>
          <?php
          if ($result->num_rows > 0) {
              while ($row = $result->fetch_assoc()) {
                  $validade = new DateTime($row["validade"]);
                  $hoje = new DateTime();
                  $intervalo = $hoje->diff($validade);
                  $dias_restantes = (int)$intervalo->format("%r%a");

                  // Definir status baseado na data de validade
                  if ($dias_restantes < 0) {
                      $status = "Vencido";
                  } elseif ($dias_restantes <= 15) {
                      $status = "Perto de vencer";
                  } else {
                      $status = "Válido";
                  }

                  echo "<tr>";
                  echo "<td>" . $row["id"] . "</td>";
                  echo "<td>" . $row["nome"] . "</td>";
                  echo "<td>" . $row["convenio"] . "</td>";
                  echo "<td>" . $status . "</td>";
                  echo "<td>" . $row["data_hora_formatada"] . "</td>";
                  echo "<td>" . $row["tipo"] . "</td>";
                  echo "<td>" . $row["email"] . "</td>";
                  echo "</tr>";

                  $sql = "UPDATE Atas SET status = '$status' WHERE id = " . $row["id"];
                  $stmt = $conn->prepare($sql);
                  $stmt->execute();
              }
          } else {
              echo "<tr><td colspan='7'>Nenhum paciente encontrado</td></tr>";
          }
          ?>
        </tbody>
      </table>
    </div>

    <div class="buttons">
      <button id="btn-cadastrar">Cadastrar</button>
    </div>

    <div class="modal fade" id="modalCadastrarAta" tabindex="-1" aria-labelledby="modalCadastrarAtaLabel"
      aria-hidden="true">
      <div class="modal-dialog">
        <div class="modal-content">
          <div class="modal-header">
            <h5 class="modal-title" id="modalCadastrarAtaLabel">Cadastar</h5>
            <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
          </div>
          <div class="modal-body">
            <form action="../database/cadastrar_ata.php" method="post">
              <div class="mb-3">
                <label for="nome" class="form-label">Nome:</label>
                <input type="text" id="nome" name="nome" class="form-control" autocomplete="off" />
                <label for="convenio" class="form-label">Convênio:</label>
                <input type="text" id="convenio" name="convenio" class="form-control" autocomplete="off" />
                <label for="email" class="form-label">E-mail:</label>
                <input type="email" id="email" name="email" class="form-control" autocomplete="off" required="true"/>
                <label for="validade" class="form-label">Validade:</label>
                <input type="date" id="validade" name="validade" class="form-control" autocomplete="off" />
                <label for="tipo" class="form-label">Tipo:</label>
                <select name="tipo" id="tipo" class="form-select">
                  <option value="Pedido Medico">PEDIDO MEDICO</option>
                  <option value="ATA">ATA</option>
                </select>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Fechar</button>
                <button type="submit" class="btn btn-primary">Salvar</button>
              </div>
            </form>
          </div>
        </div>
      </div>
    </div>
    <script src="atas.js"></script>

  </div>
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
</body>

</html>

<?php
$conn->close();
?>
