<?php
session_start();

$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

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
        header("Location: ../index.php");
        exit();
    } else {
        echo "<script language='javascript' type='text/javascript'>
                alert('Login e/ou senha incorretos');
                window.location.href='../login/login.php';
              </script>";
        exit();
    }
}

$conn->close();
?>