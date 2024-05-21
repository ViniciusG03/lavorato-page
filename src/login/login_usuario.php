<?php
session_start();

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
        $row = $result->fetch_assoc();
        $_SESSION['login'] = $login;
        
        // Definir como admin se o login for "admin"
        $_SESSION['is_admin'] = ($login === 'admin');

        setcookie("login", $login, time() + (86400 * 30), "/"); // 86400 = 1 day
        header("Location: /lavorato-page/src/index.php");
        exit(); 
    } else {
        echo "<script language='javascript' type='text/javascript'>
                alert('Login e/ou senha incorretos');
                window.location.href='/lavorato-page/src/login/login.php';
              </script>";
        exit(); 
    }
}

$conn->close();
?>
