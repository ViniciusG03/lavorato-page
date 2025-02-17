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
    <title>Atualização de Guias</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
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
        $usuarioResponsavel = $_SESSION['login'];

        $servername = "mysql.lavoratoguias.kinghost.net";
        $username = "lavoratoguias";
        $password = "A3g7K2m9T5p8L4v6";
        $database = "lavoratoguias";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            die("Erro na conexão: " . $conn->connect_error);
        }

        require __DIR__ . '/../vendor/autoload.php'; // Certifique-se de que o caminho está correto
        
        use PhpOffice\PhpSpreadsheet\IOFactory;

        if ($_SERVER["REQUEST_METHOD"] == "POST") {

            if (isset($_FILES['excelFile']) && $_FILES['excelFile']['size'] > 0) {
                $fileName = $_FILES['excelFile']['tmp_name'];
                $spreadsheet = IOFactory::load($fileName);
                $sheet = $spreadsheet->getActiveSheet();
                $data = $sheet->toArray();

                foreach ($data as $row) {
                    $numeroGuia = $row[0];
                    $statusGuia = $row[1];
                    $valorGuia = $row[2];
                    $numeroLote = $row[3];
                    $dataRemessa = $row[4];
                    // $quantidadeFaturada = $row[5];
                    $mes = $_POST["mes"];

                    if (empty($statusGuia) && empty($valorGuia) && empty($numeroLote) && empty($dataRemessa) && empty($quantidadeFaturada)) {
                        $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numeroGuia' AND paciente_mes = '$mes'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 0) {
                            echo '<h1>NÚMERO NÃO ENCONTRADO!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                            exit();
                        }

                        $sqlUpdate = "UPDATE pacientes SET paciente_faturado=? WHERE paciente_guia=? AND paciente_mes =?";
                        $stmt = $conn->prepare($sqlUpdate);

                        if ($stmt === false) {
                            die('Prepare failed: ' . $conn->error);
                        }

                        $stmt->bind_param("sss", $quantidadeFaturada, $numeroGuia, $mes);

                        if (!$stmt->execute()) {
                            echo "Erro ao atualizar: " . $stmt->error;
                        }
                    } else if (empty($valor_guia) && empty($numeroLote) && empty($dataRemessa) && empty($quantidadeFaturada)) {
                        $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numeroGuia' AND paciente_mes = '$mes'";
                        $result = $conn->query($sql);

                        if ($result->num_rows == 0) {
                            echo '<h1>NÚMERO NÃO ENCONTRADO!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                            exit();
                        }

                        $sqlUpdate = "UPDATE pacientes SET paciente_status=? WHERE paciente_guia=? AND paciente_mes =?";
                        $stmt = $conn->prepare($sqlUpdate);

                        if ($stmt === false) {
                            die('Prepare failed: ' . $conn->error);
                        }

                        $stmt->bind_param("sss", $statusGuia, $numeroGuia, $mes);

                        if (!$stmt->execute()) {
                            echo "Erro ao atualizar: " . $stmt->error;
                        }
                    } else {
                        $sqlUpdate = "UPDATE pacientes SET paciente_status=?, paciente_valor=?, paciente_lote=?, paciente_data_remessa=?, paciente_faturado=? WHERE paciente_guia=? AND paciente_mes =?";
                        $stmt = $conn->prepare($sqlUpdate);

                        if ($stmt === false) {
                            die('Prepare failed: ' . $conn->error);
                        }

                        $stmt->bind_param("sssssss", $statusGuia, $valorGuia, $numeroLote, $dataRemessa, $quantidadeFaturada, $numeroGuia, $mes);

                        if (!$stmt->execute()) {
                            echo "Erro ao atualizar: " . $stmt->error;
                        }
                    }


                }

                echo '<h1>Atualização bem-sucedida</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                exit();
            } else {
                $numero_guia = $_POST["numero_guia"];
                $status_guia = $_POST["status_guia"];
                $correcao_guia = $_POST["correcao_guia"];
                $numero_lote = $_POST["numero_lote"];
                $entrada = $_POST["entrada"];
                $saida = $_POST["saida"];
                $valor_guia = $_POST["valor_guia"];
                $data_remessa = $_POST["data_remessa"];
                $validade = $_POST["validade"];
                $section = $_POST["section"];
                $especialidade = $_POST["especialidade"];
                $quantidadeFaturada = $_POST["qtd_faturada"];
                $checkbox_guia = isset($_POST['checkbox_guia']) ? $_POST['checkbox_guia'] : 0;
                $mes = $_POST["mes"];

                if (empty($numero_guia) || empty($status_guia)) {
                    echo '<h1>Por favor, informe o <strong>ID</strong> do paciente e status!</h1><p>Clique em "Home" para voltar a página principal!</p>';
                } else {
                    if ($entrada !== "" || $saida !== "" || $status_guia !== "" || $numero_lote !== "") {
                        if ($checkbox_guia) {
                            $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numero_guia' AND paciente_mes = '$mes'";
                        } else {
                            $sql = "SELECT * FROM pacientes WHERE id = '$numero_guia'";
                        }
                        $result = $conn->query($sql);

                        if ($result->num_rows == 0) {
                            echo '<h1>NÚMERO NÃO ENCONTRADO!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                        } else {
                            $sql_update = "UPDATE pacientes SET paciente_status = '$status_guia', usuario_responsavel = '$usuarioResponsavel'";
                            if (!empty($numero_lote)) {
                                $sql_update .= ", paciente_lote = '$numero_lote'";
                            }
                            if (!empty($entrada)) {
                                $sql_update .= ", paciente_entrada = '$entrada'";
                            }
                            if (!empty($saida)) {
                                $sql_update .= ", paciente_saida = '$saida'";
                            }
                            if (!empty($correcao_guia)) {
                                $sql_update .= ", paciente_guia = '$correcao_guia'";
                            }
                            if (!empty($valor_guia)) {
                                $sql_update .= ", paciente_valor = '$valor_guia'";
                            }
                            if (!empty($data_remessa)) {
                                $sql_update .= ", paciente_data_remessa = '$data_remessa'";
                            }
                            if (!empty($validade)) {
                                $sql_update .= ", paciente_validade = '$validade'";
                            }
                            if (!empty($section)) {
                                $sql_update .= ", paciente_section = '$section'";
                            }
                            if (!empty($especialidade)) {
                                $sql_update .= ", paciente_especialidade = '$especialidade'";
                            }
                            if (!empty($quantidadeFaturada)) {
                                $sql_update .= ", paciente_faturado = '$quantidadeFaturada'";
                            }

                            if ($checkbox_guia) {
                                $sql_update .= " WHERE paciente_guia = '$numero_guia'";
                            } else {
                                $sql_update .= " WHERE id = '$numero_guia'";
                            }

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
            }

            $conn->close();
        }
        ?>


        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const btnListar = document.getElementById('homeButton');
                btnListar.addEventListener('click', () => {
                    window.location.href = '../index.php';
                });
            });
        </script>

    </div>
</body>

</html>