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

    if (empty($numero_guia) || empty($status_guia)) {
        echo '<h1>Por favor, informe o número da guia e status!</h1><p>Clique em "Home" para voltar a página principal!</p>';
    } else {
  
        if ($entrada !== "" || $saida !== "" || $status_guia !== "" || $numero_lote !== "") {
            $sql_check = "SELECT * FROM pacientes WHERE paciente_lote = '$numero_lote' AND paciente_guia != '$numero_guia'";
            $result_check = $conn->query($sql_check);

            if ($result_check->num_rows > 0) {
                echo '<h1>Número de lote já existe!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
            } else {
            
                $sql_update = "UPDATE pacientes SET paciente_status = '$status_guia', ";
                if (!empty($numero_lote)) {
                    $sql_update .= "paciente_lote = '$numero_lote', ";
                }
                if (!empty($entrada)) {
                    $sql_update .= "paciente_entrada = '$entrada', ";
                }
                if (!empty($saida)) {
                    $sql_update .= "paciente_saida = '$saida', ";
                }
                
                $sql_update = rtrim($sql_update, ", ") . " WHERE paciente_guia = '$numero_guia'";

                if ($conn->query($sql_update) === TRUE) {
                    echo '<h1>Atualização bem-sucedida</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                } else {
                    echo "Erro ao atualizar: " . $conn->error;
                }
            }
        } else {
            echo "<h1>Nenhum campo para atualizar!</h1>";
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
