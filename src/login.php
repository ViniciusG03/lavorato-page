<?php

$servername = "localhost";
$username = "root";
$password = "lavorato@admin2024";
$dbname = "lavoratoDB";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

if (isset($_POST["entrar"])) {
    $login = trim($_POST["login"]); 
    $senha = md5($_POST["senha"]); 

    $sql = "SELECT * FROM usuarios WHERE login = '$login' AND senha = '$senha'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        setcookie("login", $login);
        header("Location: index.php");
    } else {
        echo "<script language='javascript' type='text/javascript'>
                alert('Login e/ou senha incorretos');
                window.location.href='login.html';
            </script>";
    }
}

$conn->close();

?>