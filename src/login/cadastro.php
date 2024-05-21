<?php
session_start();
if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
    header('Location: /lavorato-page/src/index.php');
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="/lavorato-page/src/assets/Logo-Lavorato-alfa.png" type="image/x-icon">
    <link rel="stylesheet" href="/lavorato-page/src/bootstrap/css/bootstrap.min.css">
    <script src="/lavorato-page/src/bootstrap/js/bootstrap.min.js"></script>
    <style>
        body, html {
            height: 100%;
        }
        .container {
            height: 100%;
            width: 100%;
            display: flex;
            align-items: center;
            justify-content: center;
        } 
        #cadastrar{
            margin-top: 10px;
        }
    </style>
</head>
<body>
    <div class="container">
        <div class="row justify-content-center">
            <div class="col-md-6">
                <form method="POST" action="cadastro_usuario.php" class="mt-5">
                    <div class="form-group">
                        <label for="login">Login:</label>
                        <input
                            type="text"
                            class="form-control"
                            name="login"
                            id="login"
                            autocomplete="off" />
                    </div>
                    <div class="form-group">
                        <label for="senha">Senha:</label>
                        <input
                            type="password"
                            class="form-control"
                            name="senha"
                            id="senha"
                            autocomplete="off" />
                    </div>
                    <button
                        type="submit"
                        class="btn btn-primary"
                        id="cadastrar"
                        name="cadastrar">
                        Cadastrar
                    </button>
                </form>
            </div>
        </div>
    </div>


    <script src="https://code.jquery.com/jquery-3.5.1.slim.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/@popperjs/core@2.9.2/dist/umd/popper.min.js"></script>
    <script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.5.2/js/bootstrap.min.js"></script>
  </body>
</html>