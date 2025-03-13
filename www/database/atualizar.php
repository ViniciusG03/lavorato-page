<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Incluir as funções de log
require_once __DIR__ . '/log_functions.php';

// Verificar se é uma solicitação AJAX
$isAjax = !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) == 'xmlhttprequest';

$usuarioResponsavel = $_SESSION['login'];

$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(['success' => false, 'message' => "Erro na conexão: " . $conn->connect_error]);
        exit();
    } else {
        die("Erro na conexão: " . $conn->connect_error);
    }
}

require __DIR__ . '/../vendor/autoload.php'; // Certifique-se de que o caminho está correto

use PhpOffice\PhpSpreadsheet\IOFactory;

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    if (isset($_FILES['excelFile']) && $_FILES['excelFile']['size'] > 0) {
        $fileName = $_FILES['excelFile']['tmp_name'];
        $spreadsheet = IOFactory::load($fileName);
        $sheet = $spreadsheet->getActiveSheet();
        $data = $sheet->toArray();

        $atualizacoes = 0;
        $erros = [];

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
                    $erros[] = "Número de guia não encontrado: $numeroGuia";
                    continue;
                }

                $sqlUpdate = "UPDATE pacientes SET paciente_faturado=? WHERE paciente_guia=? AND paciente_mes =?";
                $stmt = $conn->prepare($sqlUpdate);

                if ($stmt === false) {
                    $erros[] = "Erro ao preparar consulta: " . $conn->error;
                    continue;
                }

                $stmt->bind_param("sss", $quantidadeFaturada, $numeroGuia, $mes);

                if (!$stmt->execute()) {
                    $erros[] = "Erro ao atualizar guia $numeroGuia: " . $stmt->error;
                } else {
                    $atualizacoes++;
                }
            } else if (empty($valor_guia) && empty($numeroLote) && empty($dataRemessa) && empty($quantidadeFaturada)) {
                $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numeroGuia' AND paciente_mes = '$mes'";
                $result = $conn->query($sql);

                if ($result->num_rows == 0) {
                    $erros[] = "Número de guia não encontrado: $numeroGuia";
                    continue;
                }

                // Obter o status anterior para registro de log
                $row = $result->fetch_assoc();
                $statusAnterior = $row['paciente_status'];
                $guiaId = $row['id'];

                $sqlUpdate = "UPDATE pacientes SET paciente_status=? WHERE paciente_guia=? AND paciente_mes =?";
                $stmt = $conn->prepare($sqlUpdate);

                if ($stmt === false) {
                    $erros[] = "Erro ao preparar consulta: " . $conn->error;
                    continue;
                }

                $stmt->bind_param("sss", $statusGuia, $numeroGuia, $mes);

                if (!$stmt->execute()) {
                    $erros[] = "Erro ao atualizar guia $numeroGuia: " . $stmt->error;
                } else {
                    $atualizacoes++;
                    // Registrar o log de alteração de status se o status foi alterado
                    if ($statusAnterior != $statusGuia) {
                        registrar_alteracao_status(
                            $guiaId,
                            $numeroGuia,
                            $statusAnterior,
                            $statusGuia,
                            $usuarioResponsavel,
                            "Atualização via Excel: Mês $mes",
                            $conn
                        );
                    }
                }
            } else {
                // Obter o status anterior para registro de log
                $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numeroGuia' AND paciente_mes = '$mes'";
                $result = $conn->query($sql);

                if ($result->num_rows > 0) {
                    $row = $result->fetch_assoc();
                    $statusAnterior = $row['paciente_status'];
                    $guiaId = $row['id'];
                } else {
                    $erros[] = "Número de guia não encontrado: $numeroGuia";
                    continue;
                }

                $sqlUpdate = "UPDATE pacientes SET paciente_status=?, paciente_valor=?, paciente_lote=?, paciente_data_remessa=?, paciente_faturado=? WHERE paciente_guia=? AND paciente_mes =?";
                $stmt = $conn->prepare($sqlUpdate);

                if ($stmt === false) {
                    $erros[] = "Erro ao preparar consulta: " . $conn->error;
                    continue;
                }

                $stmt->bind_param("sssssss", $statusGuia, $valorGuia, $numeroLote, $dataRemessa, $quantidadeFaturada, $numeroGuia, $mes);

                if (!$stmt->execute()) {
                    $erros[] = "Erro ao atualizar guia $numeroGuia: " . $stmt->error;
                } else {
                    $atualizacoes++;
                    // Registrar o log de alteração de status se o status foi alterado
                    if ($statusAnterior != $statusGuia && $guiaId) {
                        registrar_alteracao_status(
                            $guiaId,
                            $numeroGuia,
                            $statusAnterior,
                            $statusGuia,
                            $usuarioResponsavel,
                            "Atualização completa via Excel: Mês $mes",
                            $conn
                        );
                    }
                }
            }
        }

        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode([
                'success' => (count($erros) == 0),
                'message' => "Atualização concluída. $atualizacoes guias atualizadas.",
                'errors' => $erros
            ]);
            exit();
        } else {
            echo '<h1>Atualização bem-sucedida</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
            if (count($erros) > 0) {
                echo '<h2>Erros encontrados:</h2>';
                foreach ($erros as $erro) {
                    echo "<p>$erro</p>";
                }
            }
            exit();
        }
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

        $mensagem = "";

        if (empty($numero_guia) || empty($status_guia)) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode([
                    'success' => false,
                    'message' => "Por favor, informe o ID do paciente e status!"
                ]);
                exit();
            } else {
                echo '<h1>Por favor, informe o <strong>ID</strong> do paciente e status!</h1><p>Clique em "Home" para voltar a página principal!</p>';
            }
        } else {
            if ($entrada !== "" || $saida !== "" || $status_guia !== "" || $numero_lote !== "") {
                if ($checkbox_guia) {
                    $sql = "SELECT * FROM pacientes WHERE paciente_guia = '$numero_guia' AND paciente_mes = '$mes'";
                } else {
                    $sql = "SELECT * FROM pacientes WHERE id = '$numero_guia'";
                }
                $result = $conn->query($sql);

                if ($result->num_rows == 0) {
                    if ($isAjax) {
                        header('Content-Type: application/json');
                        echo json_encode([
                            'success' => false,
                            'message' => "NÚMERO NÃO ENCONTRADO!"
                        ]);
                        exit();
                    } else {
                        echo '<h1>NÚMERO NÃO ENCONTRADO!</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                    }
                } else {
                    // Obter o status anterior para registro de log
                    $row = $result->fetch_assoc();
                    $statusAnterior = $row['paciente_status'];
                    $guiaId = $row['id'];
                    $guiaNumero = $row['paciente_guia'];

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
                        $guiaNumero = $correcao_guia; // Atualizar o número da guia para o log
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
                        $mensagem = "Atualização bem-sucedida";

                        // Registrar o log de alteração de status se o status foi alterado
                        if ($statusAnterior != $status_guia) {
                            registrar_alteracao_status(
                                $guiaId,
                                $guiaNumero,
                                $statusAnterior,
                                $status_guia,
                                $usuarioResponsavel,
                                "Atualização manual via formulário",
                                $conn
                            );
                        }

                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => true,
                                'message' => $mensagem
                            ]);
                            exit();
                        } else {
                            echo '<h1>' . $mensagem . '</h1><br><p>Clique em "Home" para voltar a página principal!</p>';
                        }
                    } else {
                        $mensagem = "Erro ao atualizar: " . $conn->error;
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => $mensagem
                            ]);
                            exit();
                        } else {
                            echo "Erro ao atualizar: " . $conn->error;
                        }
                    }
                }
            } else {
                $mensagem = "Nenhum campo para atualizar!";
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode([
                        'success' => false,
                        'message' => $mensagem
                    ]);
                    exit();
                } else {
                    echo "<h1>Nenhum campo para atualizar!</h1>";
                }
            }
        }

        $conn->close();
    }
}
?>