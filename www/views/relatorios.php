<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

if ($_SESSION['login'] == 'consulta') {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Relatorios</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <script src="../bootstrap/js/bootstrap.min.js"></script>
    <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
    <script src="../js/autocomplete_relatorio.js"></script>
    <link rel="stylesheet" href="../stylesheet/relatorios.css">
</head>

<body>
    <div class="container-fluid">
        <div class="row">
            <!-- Sidebar -->
            <nav id="sidebar" class="col-md-3 col-lg-2 d-md-block bg-dark    sidebar collapse">
                <div class="position-sticky">
                    <ul class="nav flex-column">
                        <li class="nav-item">
                            <a class="nav-link active" href="../index.php">
                                <span data-feather="home"></span>
                                Home
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="book"></span>
                                Guias
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="file"></span>
                                Atas
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" href="#">
                                <span data-feather="bar-chart-2"></span>
                                Relatórios
                            </a>
                        </li>
                    </ul>
                </div>
            </nav>

            <!-- Conteúdo principal -->
            <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
                <div
                    class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                    <h1 class="h2">Painel</h1>
                </div>

                <p>Escolha uma opção!</p>

                <!-- Botões relatorio e fichas -->
                <div class="mb-4">
                    <button class="btn btn-secondary me-2" type="button">Relatórios</button>
                    <button class="btn btn-secondary" type="button" data-bs-toggle="modal"
                        data-bs-target="#fichasModal">Fichas</button>
                </div>

                <!-- Modal para selação das fichas -->
                <div class="modal fade" id="fichasModal" tabindex="-1" aria-labelledby="fichasModalLabel"
                    aria-hidden="true">
                    <div class="modal-dialog">
                        <div class="modal-content bg-dark text-light">
                            <div class="modal-header">
                                <h5 class="modal-title" id="fichasModalLabel">Selecionar Ficha</h5>
                                <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal"
                                    aria-label="Close"></button>
                            </div>
                            <div class="modal-body">
                                <button class="btn btn-secondary w-100 my-2 ficha-option"
                                    data-option="fusex_individual_tipico">Ficha Fusex Individual Tipico</button>
                                <?php
                                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || $_SESSION['login'] == 'gustavoramos' || $_SESSION['login'] == 'eviny') {
                                    echo '<button class="btn btn-secondary w-100 my-2 ficha-option" data-option="fusex_tipico">Ficha
                                    Fusex Tipico</button>';
                                }
                                ?>
                                <button class="btn btn-secondary w-100 my-2 ficha-option"
                                    data-option="fusex_individual_pne">Ficha Fusex Individual PNE</button>
                                <?php
                                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin'] || $_SESSION['login'] == 'gustavoramos' || $_SESSION['login'] == 'eviny') {
                                    echo '<button class="btn btn-secondary w-100 my-2 ficha-option" data-option="fusex_pne">Ficha
                                    Fusex PNE</button>';
                                }
                                ?>
                                <button class="btn btn-secondary w-100 my-2 ficha-option"
                                    data-option="cbmdf_individual">Ficha CBMDF Individual</button>
                                <?php
                                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                                    echo '<button class="btn btn-secondary w-100 my-2 ficha-option" data-option="cbmdf">Ficha
                                    CBMDF</button>';
                                }
                                ?>
                                <?php
                                if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                                    echo ' <button class="btn btn-secondary w-100 my-2 ficha-option"
                                    data-option="particular_individual">Ficha Particular Individual</button>
                                    <button class="btn btn-secondary w-100 my-2 ficha-option"
                                    data-option="particular">Ficha Particular</button>';
                                }
                                ?>
                            </div>
                        </div>
                    </div>
                </div>

                <div id="formContainer" class="mt-4"></div>
            </main>
        </div>
    </div>
    <script src="https://cdnjs.cloudflare.com/ajax/libs/feather-icons/4.28.0/feather.min.js"></script>
    <script>
        feather.replace();
    </script>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script src="../js/relatorio.js"></script>
</body>

</html>