<?php
session_start();
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon">
    <link rel="stylesheet" href="../stylesheet/login.css">
    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
</head>
<body>
    <header class="cabecalho">
        <nav class="cabecalho__nav">
        <button class="registro">Registro</button>
            <button class="login">Login</button>
            <script>
                document.querySelector('.login').addEventListener('click', () => {
                    window.location.href = './login.php';
                });
                document.querySelector('.registro').addEventListener('click', () => {
                    window.location.href = './cadastro.php';
                });
            </script>
        </nav>
    </header>
    <main class="container">
        <div class="img"></div>
        <div class="logo">
            <img src="../assets/Lavorato.jpg" alt="Logo">
            <form action="login_usuario.php" method="POST" class="form-login">

                <label for="login">Usuário</label>
                <input type="text" name="login" id="login" placeholder="Usuário" required>

                <label for="senha">Senha</label>
                <input type="password" name="senha" id="senha" placeholder="********" required>

                <button type="submit" id="entrar" name="entrar">Login</button>
                <a href="esqueci_senha.php">Esqueceu a senha?</a>
            </form>
        </div>
    </main>
    <footer class="rodape">
        <p>Desenvolvido por @Lavorato Tech</p>
    </footer>
</body>
</html>