<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}
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
        session_start();

        if ($_SERVER["REQUEST_METHOD"] == "POST") {
            $servername = "localhost";
            $username = "root";
            $password = "lavorato@admin2024";
            $database = "lavoratodb";

            $conn = new mysqli($servername, $username, $password, $database);

            if ($conn->connect_error) {
                die("Erro na conexão: " . $conn->connect_error);
            }

            $nome = $_POST["nome"];
            $nome_google = $_POST["nome_google"];
            $email = $_POST["email"];
            $data_inicio = $_POST["data_inicio"];
            $data_final = $_POST["data_final"];
            $matricula = $_POST["matricula"];
            $documento = $_POST["documento"];
            $especialidade = $_POST["especialidade_controle"];
            $data_emissao = $_POST["data_emissao"];
            $data_validade = $_POST["data_validade"];

            $uploadDir = "../controle-page/database/documents/";

            if (empty($nome) || empty($nome_google) || empty($email) || empty($data_inicio) || empty($matricula) || empty($documento) || empty($especialidade) || empty($data_emissao) || empty($data_validade)) {
                echo "<h1>Todos os campos devem ser preenchidos, exceto 'Data Final'!</h1>";
            } else {
                $verifica_sql = "SELECT COUNT(*) as count FROM paciente WHERE Nome_paciente = '$nome'";
                $resultado_verificacao = $conn->query($verifica_sql);

                $documentID = $conn->insert_id;

                if ($resultado_verificacao) {
                    $row = $resultado_verificacao->fetch_assoc();
                    $numero_de_registros = $row['count'];

                    if ($numero_de_registros > 0) {
                        echo "<h2>Erro: O paciente já está cadastrado!</h2>";
                    } else {
                        $sql = "INSERT INTO paciente (Nome_paciente, Nome_google, Data_inicio, Data_final, Email, Matricula) VALUES ('$nome', '$nome_google', '$data_inicio', '$data_final', '$email', '$matricula')";
                        if ($conn->query($sql) === TRUE) {
                            $pacienteID = $conn->insert_id;
                            $sqlDoc = "INSERT INTO documento (Documento_tipo, Especialidade, Data_emissao, Data_validade, Paciente_ID) VALUES ('$documento', '$especialidade', '$data_emissao', '$data_validade', '$pacienteID')";
                            $conn->query($sqlDoc);

                            if (isset($_FILES['documento_arquivo'])) {
                                $fileError = $_FILES['documento_arquivo']['error'];

                                if ($fileError === UPLOAD_ERR_OK) {
                                    $fileTmpPath = $_FILES['documento_arquivo']['tmp_name'];
                                    $fileName = $_FILES['documento_arquivo']['name'];
                                    $fileNameCmps = explode(".", $fileName);
                                    $fileExtension = strtolower(end($fileNameCmps));

                                    $newFileName = $documento . $matricula . '.' . $fileExtension;
                                    $dest_path = $uploadDir . $newFileName;

                                    if (move_uploaded_file($fileTmpPath, $dest_path)) {
                                        $_SESSION['download_file'] = $dest_path;
                                        $_SESSION['documento'] = $documento;

                                        header("Location: download.php?documento=$documento");
                                        exit;

                                    } else {
                                        echo "<p>Erro ao mover o arquivo para o diretório de destino.</p>";
                                    }
                                }
                            } else {
                                switch ($fileError) {
                                    case UPLOAD_ERR_INI_SIZE:
                                    case UPLOAD_ERR_FORM_SIZE:
                                        echo "<p>O arquivo é muito grande.</p>";
                                        break;
                                    case UPLOAD_ERR_PARTIAL:
                                        echo "<p>O upload do arquivo foi feito parcialmente.</p>";
                                        break;
                                    case UPLOAD_ERR_NO_FILE:
                                        echo "<p>Nenhum arquivo foi enviado.</p>";
                                        break;
                                    case UPLOAD_ERR_NO_TMP_DIR:
                                        echo "<p>Pasta temporária ausente.</p>";
                                        break;
                                    case UPLOAD_ERR_CANT_WRITE:
                                        echo "<p>Falha em escrever o arquivo no disco.</p>";
                                        break;
                                    case UPLOAD_ERR_EXTENSION:
                                        echo "<p>Upload de arquivo interrompido por uma extensão PHP.</p>";
                                        break;
                                    default:
                                        echo "<p>Erro desconhecido no upload do arquivo.</p>";
                                        break;
                                }
                            }

                            echo "<h1> Paciente cadastrado com sucesso!</h1>";
                            echo "<h2>Nome do paciente:</h2> <p>$nome</p>";
                            echo "<h2>Email:</h2> <p>$email</p>";
                            echo "<h2>Matricula:</h2> <p>$matricula</p>";
                            echo "<h2>Documento:</h2> <p>$documento</p>";
                            echo "<h2>Data de Inicio:</h2> <p>$data_inicio</p>";
                            echo "<h2>Especialidade:</h2> <p>$especialidade</p>";
                            echo "<h2>Data de Emissão:</h2> <p>$data_emissao</p>";
                            echo "<h2>Data de Validade:</h2> <p>$data_validade</p>";
                        } else {
                            echo "<p>Erro ao cadastrar a guia: </p>" . $conn->error;
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