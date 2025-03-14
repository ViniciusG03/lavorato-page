<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Incluir as funções de log e especialidades
require_once __DIR__ . '/log_functions.php';
require_once __DIR__ . '/functions_especialidades.php';

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
        // Capturar especialidades como array
        $especialidades = isset($_POST["especialidades"]) && is_array($_POST["especialidades"])
            ? $_POST["especialidades"]
            : [];
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
            if ($entrada !== "" || $saida !== "" || $status_guia !== "" || $numero_lote !== "" || !empty($especialidades)) {
                if ($checkbox_guia) {
                    $sql = "SELECT * FROM pacientes WHERE paciente_guia = ? AND paciente_mes = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("ss", $numero_guia, $mes);
                } else {
                    $sql = "SELECT * FROM pacientes WHERE id = ?";
                    $stmt = $conn->prepare($sql);
                    $stmt->bind_param("i", $numero_guia);
                }
                $stmt->execute();
                $result = $stmt->get_result();

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

                    // Iniciar transação
                    $conn->begin_transaction();

                    try {
                        // Primeira especialidade para manter compatibilidade
                        $especialidade_principal = !empty($especialidades) ? $especialidades[0] : $row['paciente_especialidade'];

                        $sql_update = "UPDATE pacientes SET paciente_status = ?, usuario_responsavel = ?";
                        $params = [$status_guia, $usuarioResponsavel];
                        $types = "ss";

                        if (!empty($numero_lote)) {
                            $sql_update .= ", paciente_lote = ?";
                            $params[] = $numero_lote;
                            $types .= "s";
                        }

                        if (!empty($entrada)) {
                            $sql_update .= ", paciente_entrada = ?";
                            $params[] = $entrada;
                            $types .= "s";
                        }

                        if (!empty($saida)) {
                            $sql_update .= ", paciente_saida = ?";
                            $params[] = $saida;
                            $types .= "s";
                        }

                        if (!empty($correcao_guia)) {
                            $sql_update .= ", paciente_guia = ?";
                            $params[] = $correcao_guia;
                            $types .= "s";
                            $guiaNumero = $correcao_guia; // Atualizar o número da guia para o log
                        }

                        if (!empty($valor_guia)) {
                            $sql_update .= ", paciente_valor = ?";
                            $params[] = $valor_guia;
                            $types .= "s";
                        }

                        if (!empty($data_remessa)) {
                            $sql_update .= ", paciente_data_remessa = ?";
                            $params[] = $data_remessa;
                            $types .= "s";
                        }

                        if (!empty($validade)) {
                            $sql_update .= ", paciente_validade = ?";
                            $params[] = $validade;
                            $types .= "s";
                        }

                        if (!empty($section)) {
                            $sql_update .= ", paciente_section = ?";
                            $params[] = $section;
                            $types .= "s";
                        }

                        if (!empty($especialidades)) {
                            // Atualizar especialidade principal para manter compatibilidade
                            $sql_update .= ", paciente_especialidade = ?";
                            $params[] = $especialidade_principal;
                            $types .= "s";
                        }

                        if (!empty($quantidadeFaturada)) {
                            $sql_update .= ", paciente_faturado = ?";
                            $params[] = $quantidadeFaturada;
                            $types .= "s";
                        }

                        // Adicionar condição WHERE
                        if ($checkbox_guia) {
                            $sql_update .= " WHERE paciente_guia = ? AND paciente_mes = ?";
                            $params[] = $numero_guia;
                            $params[] = $mes;
                            $types .= "ss";
                        } else {
                            $sql_update .= " WHERE id = ?";
                            $params[] = $numero_guia;
                            $types .= "i";
                        }

                        // Executar atualização
                        $stmt_update = $conn->prepare($sql_update);
                        $stmt_update->bind_param($types, ...$params);

                        if (!$stmt_update->execute()) {
                            throw new Exception("Erro ao atualizar: " . $stmt_update->error);
                        }

                        $stmt_update->close();

                        // Atualizar especialidades se fornecidas
                        if (!empty($especialidades)) {
                            if (!salvar_especialidades_paciente($guiaId, $especialidades, $conn)) {
                                throw new Exception("Erro ao atualizar especialidades");
                            }
                        }

                        // Registrar log de alteração se o status foi alterado
                        if ($statusAnterior != $status_guia) {
                            if (!registrar_alteracao_status(
                                $guiaId,
                                $guiaNumero,
                                $statusAnterior,
                                $status_guia,
                                $usuarioResponsavel,
                                "Atualização manual via formulário",
                                $conn
                            )) {
                                throw new Exception("Erro ao registrar log de alteração");
                            }
                        }

                        // Confirmar transação
                        $conn->commit();

                        // Mensagem de sucesso para o usuário
                        $mensagem = "Atualização bem-sucedida";

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
                    } catch (Exception $e) {
                        // Reverter transação em caso de erro
                        $conn->rollback();

                        $mensagem = "Erro ao atualizar: " . $e->getMessage();
                        if ($isAjax) {
                            header('Content-Type: application/json');
                            echo json_encode([
                                'success' => false,
                                'message' => $mensagem
                            ]);
                            exit();
                        } else {
                            echo "Erro ao atualizar: " . $e->getMessage();
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
