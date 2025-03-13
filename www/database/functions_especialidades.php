<?php
/**
 * Arquivo: functions_especialidades.php
 * Funções para gerenciar especialidades de pacientes
 */

/**
 * Obtém todas as especialidades de um paciente por ID
 * 
 * @param int $paciente_id ID do paciente
 * @param mysqli $conn Conexão com o banco de dados
 * @return array Lista de especialidades
 */
function obter_especialidades_paciente($paciente_id, $conn) {
    $especialidades = [];
    
    // Primeiro verifica se a guia está no formato novo (com múltiplas especialidades)
    $sql = "SELECT COUNT(*) as total FROM paciente_especialidades WHERE paciente_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("i", $paciente_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    $stmt->close();
    
    // Se existem registros na tabela de especialidades, busca nela
    if ($row['total'] > 0) {
        $sql = "SELECT especialidade FROM paciente_especialidades WHERE paciente_id = ? ORDER BY id";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        while ($row = $result->fetch_assoc()) {
            $especialidades[] = $row['especialidade'];
        }
        
        $stmt->close();
    } else {
        // Caso contrário, mantém compatibilidade e busca na tabela principal
        $sql = "SELECT paciente_especialidade FROM pacientes WHERE id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $paciente_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($row = $result->fetch_assoc()) {
            if (!empty($row['paciente_especialidade'])) {
                $especialidades[] = $row['paciente_especialidade'];
            }
        }
        
        $stmt->close();
    }
    
    return $especialidades;
}

/**
 * Salva as especialidades de um paciente
 * 
 * @param int $paciente_id ID do paciente
 * @param array $especialidades Lista de especialidades
 * @param mysqli $conn Conexão com o banco de dados
 * @return bool Resultado da operação
 */
function salvar_especialidades_paciente($paciente_id, $especialidades, $conn) {
    // Verificar se há especialidades para salvar
    if (empty($especialidades) || !is_array($especialidades)) {
        return false;
    }
    
    // Iniciar transação
    $conn->begin_transaction();
    
    try {
        // Remover especialidades existentes
        $sql_delete = "DELETE FROM paciente_especialidades WHERE paciente_id = ?";
        $stmt_delete = $conn->prepare($sql_delete);
        $stmt_delete->bind_param("i", $paciente_id);
        $stmt_delete->execute();
        $stmt_delete->close();
        
        // Inserir novas especialidades
        $sql_insert = "INSERT INTO paciente_especialidades (paciente_id, especialidade) VALUES (?, ?)";
        $stmt_insert = $conn->prepare($sql_insert);
        
        foreach ($especialidades as $especialidade) {
            if (!empty($especialidade)) {
                $stmt_insert->bind_param("is", $paciente_id, $especialidade);
                $stmt_insert->execute();
            }
        }
        
        $stmt_insert->close();
        
        // Atualizar especialidade principal na tabela pacientes para manter compatibilidade
        if (!empty($especialidades[0])) {
            $especialidade_principal = $especialidades[0];
            $sql_update = "UPDATE pacientes SET paciente_especialidade = ? WHERE id = ?";
            $stmt_update = $conn->prepare($sql_update);
            $stmt_update->bind_param("si", $especialidade_principal, $paciente_id);
            $stmt_update->execute();
            $stmt_update->close();
        }
        
        // Confirmar transação
        $conn->commit();
        return true;
    } catch (Exception $e) {
        // Reverter em caso de erro
        $conn->rollback();
        error_log("Erro ao salvar especialidades: " . $e->getMessage());
        return false;
    }
}

/**
 * Formata especialidades para exibição
 * 
 * @param array $especialidades Lista de especialidades
 * @param bool $html Se deve retornar HTML formatado ou texto simples
 * @return string Especialidades formatadas
 */
function formatar_especialidades($especialidades, $html = true) {
    if (empty($especialidades)) {
        return $html ? '<em>Não definida</em>' : 'Não definida';
    }
    
    // Se for apenas uma especialidade ou não for HTML
    if (count($especialidades) == 1 || !$html) {
        return count($especialidades) == 1 
            ? htmlspecialchars($especialidades[0]) 
            : htmlspecialchars(implode(", ", $especialidades));
    }
    
    // Formatar múltiplas especialidades como lista
    $output = '<ul class="list-unstyled mb-0 small">';
    foreach ($especialidades as $esp) {
        $output .= '<li>' . htmlspecialchars($esp) . '</li>';
    }
    $output .= '</ul>';
    
    return $output;
}