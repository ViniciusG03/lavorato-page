<?php
session_start();

$usuarioResponsavel = $_SESSION['login'];

// Incluir as funções de log
require_once __DIR__ . '/log_functions.php';

$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    die("Erro na conexão: " . $conn->connect_error);
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $guias = $_POST['guias'];
    $status = $_POST['status'];

    if (empty($guias) || empty($status)) {
        echo "Dados inválidos!";
        exit;
    }

    $ids = implode(',', array_map('intval', $guias));

    // Primeiro, vamos buscar os registros para obter os status anteriores
    $sql_select = "SELECT id, paciente_guia, paciente_status FROM pacientes WHERE id IN ($ids)";
    $result = $conn->query($sql_select);

    $registros_para_log = [];
    if ($result) {
        while ($row = $result->fetch_assoc()) {
            // Armazenar os dados de cada registro para posterior registro nos logs
            $registros_para_log[] = [
                'id' => $row['id'],
                'paciente_guia' => $row['paciente_guia'],
                'status_anterior' => $row['paciente_status']
            ];
        }
    }

    // Atualizar os registros
    $sql = "UPDATE pacientes SET paciente_status = ?, usuario_responsavel = ? WHERE id IN ($ids)";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('ss', $status, $usuarioResponsavel);

    $success = true;
    if ($stmt->execute()) {
        // Agora, registramos os logs para cada guia alterada
        foreach ($registros_para_log as $registro) {
            // Verificar se o status realmente mudou
            if ($registro['status_anterior'] != $status) {
                $log_success = registrar_alteracao_status(
                    $registro['id'],
                    $registro['paciente_guia'],
                    $registro['status_anterior'],
                    $status,
                    $usuarioResponsavel,
                    "Atualização em massa via sistema",
                    $conn
                );

                if (!$log_success) {
                    error_log("Erro ao registrar log para a guia ID: " . $registro['id']);
                    $success = false;
                }
            }
        }

        if ($success) {
            echo "Status atualizado com sucesso!";
        } else {
            echo "Status atualizado, mas houve problema no registro de alguns logs.";
        }
    } else {
        echo "Erro ao atualizar: " . $stmt->error;
    }

    $stmt->close();
    $conn->close();
}
