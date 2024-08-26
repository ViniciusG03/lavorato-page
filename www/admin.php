<?php

require_once 'database/database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inserir'])) {

    // Coleta os pacientes com status diferente de "Saiu" e "Cancelado"
    $sql = "SELECT * 
            FROM usuarios_teste
            WHERE paciente_status != 'Saiu'
            AND paciente_status != 'Cancelado' 
            AND paciente_convenio != 'CASSI' 
            AND paciente_convenio != 'UNIVIDA'
            AND paciente_saida IS NULL
            AND id IN (
                SELECT MAX(id)
                FROM usuarios_teste
                WHERE paciente_status != 'Saiu'
                AND paciente_status != 'Cancelado'
                AND paciente_convenio != 'CASSI' 
                AND paciente_convenio != 'UNIVIDA'
                AND paciente_saida IS NULL
                GROUP BY paciente_nome -- Ajuste conforme necessário
            );
            ";
    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Prepara a inserção dos pacientes com os campos especificados nulos e atualizando "paciente_mes"
    $insert_sql = 'INSERT INTO usuarios_teste (paciente_nome, paciente_convenio, paciente_especialidade, 
                paciente_mes, paciente_section, paciente_entrada)
                VALUES (:paciente_nome, :paciente_convenio, :paciente_especialidade, :paciente_mes,
                 :paciente_section, :paciente_entrada)';
    $insert_stmt = $db->prepare($insert_sql);

    foreach ($result as $row) {
        $insert_stmt->execute([
            ':paciente_nome' => $row['paciente_nome'],
            ':paciente_convenio' => $row['paciente_convenio'],
            ':paciente_especialidade' => $row['paciente_especialidade'],
            ':paciente_section' => $row['paciente_section'],
            ':paciente_entrada' => $row['paciente_entrada'],
            ':paciente_mes' => 'Setembro'
        ]);
    }


    $insert_stmt->closeCursor();
}

?>