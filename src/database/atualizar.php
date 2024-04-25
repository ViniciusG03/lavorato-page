<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Atualização de Guias</title>
    <link
      rel="shortcut icon"
      href="../assets/Logo-Lavorato-alfa.png"
      type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
      href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
      rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/atualizar.css">
</head>
<body>
    <div class="nav">
      <button id="homeButton">Home</button>
      <h1>Lavorato's System</h1>
    </div>
    <div class="container">
    <?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "localhost";
    $username = "root";
    $password = "lavorato@admin2024";
    $database = "lavoratoDB";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $numero_guia = $_POST["numero_guia"];
    $status_guia = $_POST["status_guia"];
    $numero_lote = $_POST["numero_lote"];
    $entrada = $_POST["entrada"];
    $saida = $_POST["saida"];

    // Verificar se algum dos campos obrigatórios está vazio
    if (empty($numero_guia) || empty($status_guia) || empty($numero_lote) || empty($entrada) || empty($saida)) {
        echo "<h1>Por favor, preencha todos os campos!</h1>";
    } else {
        $sql_select = "SELECT * FROM pacientes WHERE paciente_guia = '$numero_guia'";
        $result = $conn->query($sql_select);

        if ($result->num_rows > 0) {
            // Executar SQL de atualização apenas se os campos necessários estiverem preenchidos
            $sql_update = "UPDATE pacientes SET paciente_status = '$status_guia', paciente_lote = '$numero_lote', paciente_entrada = '$entrada', paciente_saida = '$saida' WHERE paciente_guia = '$numero_guia'";

            if ($conn->query($sql_update) === TRUE) {
                echo '<h1>Atualização bem-sucedida</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
            } else {
                echo "Erro ao atualizar: " . $conn->error;
            }
        } else {
            echo "<h1>Número da guia não encontrado</h1>";
        }
    }

    $conn->close();
}
?>
    </div>

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
