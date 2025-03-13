<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verifica se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
    <link rel="preconnect" href="https://fonts.googleapis.com" />
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
        rel="stylesheet" />
    <link rel="stylesheet" href="../stylesheet/cadastro.css">
</head>

<body>
    <div class="nav">
        <button id="homeButton">Home</button>
        <h1>Lavorato's System</h1>
    </div>
    <div class="box">
        <?php
        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $servername = "mysql.lavoratoguias.kinghost.net";
            $username = "lavoratoguias";
            $password = "A3g7K2m9T5p8L4v6";
            $database = "lavoratoguias";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(["success" => false, "message" => "Erro na conexão: " . $conn->connect_error]);
                    exit;
                } else {
                    die("Erro na conexão: " . $conn->connect_error);
                }
            }

            $nome = $_POST["nome"];
            $convenio = $_POST["convenio"];
            $numero_guia = $_POST["numero_guia"];
            $status_guia = $_POST["status_guia"];
            $especialidade = $_POST["especialidade"];
            $mes = $_POST["mes"];
            $entrada = $_POST["entrada"];
            $saida = $_POST["saida"];
            $section = $_POST["numero_section"];
            $validade = $_POST["validade"];

            if (empty($nome) || empty($convenio) || empty($numero_guia) || empty($status_guia) || empty($especialidade) || empty($mes) || empty($entrada) || empty($section)) {
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(["success" => false, "message" => "Todos os campos devem ser preenchidos, exceto 'Saída'!"]);
                    exit;
                } else {
                    echo "<h1>Todos os campos devem ser preenchidos, exceto 'Saída'!</h1>";
                }
            } else {
                $verifica_sql = "SELECT COUNT(*) as count FROM pacientes WHERE paciente_guia = '$numero_guia' AND paciente_mes = '$mes'";
                $resultado_verificacao = $conn->query($verifica_sql);

                if ($resultado_verificacao) {
                    $row = $resultado_verificacao->fetch_assoc();
                    $numero_de_registros = $row['count'];

                    if ($numero_de_registros > 0) {
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode(["success" => false, "message" => "Erro: A guia já está cadastrada!"]);
                            exit;
                        } else {
                            echo "<h2>Erro: A guia já está cadastrada!</h2>";
                        }
                    } else {
                        $sql = "INSERT INTO pacientes (paciente_nome, paciente_convenio, paciente_guia, paciente_status, paciente_especialidade, paciente_mes, paciente_entrada, paciente_saida, paciente_section, paciente_validade) VALUES ('$nome', '$convenio', '$numero_guia', '$status_guia', '$especialidade', '$mes', '$entrada', '$saida', '$section', '$validade')";

                        if ($conn->query($sql) === TRUE) {
                            // Dados para apresentar no resumo
                            $resumo = [
                                "nome" => $nome,
                                "convenio" => $convenio,
                                "numero_guia" => $numero_guia,
                                "section" => $section,
                                "status_guia" => $status_guia,
                                "especialidade" => $especialidade,
                                "validade" => $validade,
                                "mes" => $mes,
                                "entrada" => $entrada,
                                "saida" => $saida
                            ];
                            
                            if ($isAjax) {
                                header('Content-Type: application/json');
                                echo json_encode(["success" => true, "message" => "Guia cadastrada com sucesso!", "data" => $resumo]);
                                exit;
                            } else {
                                echo "<h1> Guia cadastrada com sucesso!</h1>";
                                echo "<h2>Nome do paciente:</h2> <p>$nome</p>";
                                echo "<h2>Convênio do paciente:</h2> <p>$convenio</p>";
                                echo "<h2>Número da Guia:</h2> <p>$numero_guia</p>";
                                echo "<h2>Número de Seções:</h2> <p>$section</p>";
                                echo "<h2>Status da Guia:</h2> <p>$status_guia</p>";
                                echo "<h2>Especialidade:</h2> <p>$especialidade</p>";
                                echo "<h2>Validade:</h2> <p>$validade</p>";
                                echo "<h2>Mês:</h2> <p>$mes</p>";
                                echo "<h2>Entrada:</h2> <p>$entrada</p>";
                                echo "<h2>Saida:</h2> <p>$saida</p>";
                            }
                        } else {
                            if ($isAjax) {
                                header('Content-Type: application/json');
                                echo json_encode(["success" => false, "message" => "Erro ao cadastrar a guia: " . $conn->error]);
                                exit;
                            } else {
                                echo "<p>Erro ao cadastrar a guia: </p>" . $conn->error;
                            }
                        }
                    }
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
                window.location.href = '../index.php';
            });
        });
    </script>
</body>

</html>