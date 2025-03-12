<?php

/**
 * Funções para registro de logs de alteração de status
 */

/**
 * Registra uma alteração de status de guia no histórico
 * 
 * @param int|string $guia_id ID da guia
 * @param string $paciente_guia Número da guia
 * @param string $status_anterior Status anterior da guia
 * @param string $status_novo Novo status da guia
 * @param string $usuario_responsavel Usuário que realizou a alteração
 * @param string $observacao Observações adicionais (opcional)
 * @param mysqli $conn Conexão com o banco de dados (opcional)
 * @return bool True se o registro foi bem-sucedido, False caso contrário
 */
function registrar_alteracao_status($guia_id, $paciente_guia, $status_anterior, $status_novo, $usuario_responsavel, $observacao = '', $conn = null)
{
    // Se não for fornecida uma conexão, criar uma nova
    $conexao_local = false;
    if ($conn === null) {
        $conexao_local = true;
        $servername = "mysql.lavoratoguias.kinghost.net";
        $username = "lavoratoguias";
        $password = "A3g7K2m9T5p8L4v6";
        $database = "lavoratoguias";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            error_log("Erro na conexão ao registrar log: " . $conn->connect_error);
            return false;
        }
    }

    try {
        // Preparar a consulta SQL
        $sql = "INSERT INTO guias_status_logs (guia_id, paciente_guia, status_anterior, status_novo, usuario_responsavel, observacao) 
                VALUES (?, ?, ?, ?, ?, ?)";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Erro ao preparar consulta para log: " . $conn->error);
            return false;
        }

        $stmt->bind_param("isssss", $guia_id, $paciente_guia, $status_anterior, $status_novo, $usuario_responsavel, $observacao);

        $resultado = $stmt->execute();

        if ($resultado === false) {
            error_log("Erro ao executar inserção de log: " . $stmt->error);
        }

        $stmt->close();

        // Fechar a conexão se foi criada localmente
        if ($conexao_local) {
            $conn->close();
        }

        return $resultado;
    } catch (Exception $e) {
        error_log("Exceção ao registrar log: " . $e->getMessage());

        // Fechar a conexão se foi criada localmente
        if ($conexao_local) {
            $conn->close();
        }

        return false;
    }
}

/**
 * Obtém o status atual de uma guia pelo ID ou número
 * 
 * @param int|string $id_or_number ID ou número da guia
 * @param bool $is_guia_number True se o parâmetro é o número da guia, False se é o ID
 * @param mysqli $conn Conexão com o banco de dados (opcional)
 * @return string|null O status atual da guia ou null se não encontrada
 */
function obter_status_atual($id_or_number, $is_guia_number = false, $conn = null)
{
    // Se não for fornecida uma conexão, criar uma nova
    $conexao_local = false;
    if ($conn === null) {
        $conexao_local = true;
        $servername = "mysql.lavoratoguias.kinghost.net";
        $username = "lavoratoguias";
        $password = "A3g7K2m9T5p8L4v6";
        $database = "lavoratoguias";

        $conn = new mysqli($servername, $username, $password, $database);

        if ($conn->connect_error) {
            error_log("Erro na conexão ao obter status: " . $conn->connect_error);
            return null;
        }
    }

    try {
        // Determinar qual campo usar para busca
        $campo = $is_guia_number ? "paciente_guia" : "id";

        // Preparar a consulta SQL
        $sql = "SELECT paciente_status FROM pacientes WHERE $campo = ?";

        $stmt = $conn->prepare($sql);

        if ($stmt === false) {
            error_log("Erro ao preparar consulta para obter status: " . $conn->error);
            return null;
        }

        $stmt->bind_param($is_guia_number ? "s" : "i", $id_or_number);
        $stmt->execute();
        $stmt->bind_result($status);

        // Verificar se encontrou um resultado
        if ($stmt->fetch()) {
            $stmt->close();

            // Fechar a conexão se foi criada localmente
            if ($conexao_local) {
                $conn->close();
            }

            return $status;
        }

        $stmt->close();

        // Fechar a conexão se foi criada localmente
        if ($conexao_local) {
            $conn->close();
        }

        return null;
    } catch (Exception $e) {
        error_log("Exceção ao obter status: " . $e->getMessage());

        // Fechar a conexão se foi criada localmente
        if ($conexao_local) {
            $conn->close();
        }

        return null;
    }
}
