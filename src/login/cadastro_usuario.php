<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $servername = "mysql.lavoratoguias.kinghost.net";
    $username = "lavoratoguias";
    $password = "A3g7K2m9T5p8L4v6";
    $database = "lavoratoguias";

    $conn = new mysqli($servername, $username, $password, $database);

    if ($conn->connect_error) {
        die("Erro na conexão: " . $conn->connect_error);
    }

    $login = trim($_POST['login']);
    $senha = md5($_POST['senha']);

    if (empty($login) || empty($senha)) {
        echo "<script language='javascript' type='text/javascript'>
          alert('Preencha os campos de login e senha!');
          window.location.href='cadastro.html';
        </script>";
        die();
    }

    $query_select = "SELECT login FROM usuarios WHERE login = '$login'";
    $select = $conn->query($query_select);

    if ($select->num_rows > 0) {
        echo "<script language='javascript' type='text/javascript'>
          alert('Login já cadastrado!');
          window.location.href='../login/cadastro.php';
        </script>";
    } else {
        $query = "INSERT INTO usuarios (login,senha) VALUES ('$login','$senha')";
        $insert = $conn->query($query);

        if ($insert) {
            echo "<script language='javascript' type='text/javascript'>
              alert('Usuário cadastrado com sucesso!');
              window.location.href='../login/login.php';  
            </script>";
        }

        if (!$insert) {
            echo "<p style='color: red;'>Erro ao cadastrar usuário: " . $conn->error . "</p>";
        }

    }

    $conn->close();

}
?>