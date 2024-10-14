<?php
require 'vendor/autoload.php';
require_once 'database/database.php';

use PhpOffice\PhpSpreadsheet\Spreadsheet;
use PhpOffice\PhpSpreadsheet\Writer\Xlsx;

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inserir'])) {

    // Coleta os pacientes com status diferente de "Saiu" e "Cancelado"
    $sql = "SELECT DISTINCT paciente_nome, paciente_convenio, paciente_status, paciente_guia, paciente_especialidade, paciente_mes, paciente_section, paciente_entrada,
    paciente_saida, paciente_valor, paciente_data_remessa, paciente_validade, paciente_lote, paciente_faturado
    FROM pacientes
    WHERE paciente_status != 'Saiu'
    AND paciente_status != 'Cancelado' 
    AND paciente_convenio != 'CASSI' 
    AND paciente_convenio != 'UNIVIDA'
    AND paciente_saida IS NULL
    AND id IN (
        SELECT MAX(id)
        FROM pacientes
        WHERE paciente_status != 'Saiu'
        AND paciente_status != 'Cancelado'
        AND paciente_convenio != 'CASSI' 
        AND paciente_convenio != 'UNIVIDA'
        AND paciente_saida IS NULL
        GROUP BY paciente_nome
    )
    ";

    $stmt = $db->prepare($sql);
    $stmt->execute();
    $result = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $stmt->closeCursor();

    // Prepara a inserção dos pacientes com os campos especificados nulos e atualizando "paciente_mes"
    $insert_sql = 'INSERT INTO pacientes (paciente_nome, paciente_convenio, paciente_especialidade, 
                paciente_mes, paciente_section, paciente_entrada)
                VALUES (:paciente_nome, :paciente_convenio, :paciente_especialidade, :paciente_mes,
                 :paciente_section, :paciente_entrada)';
    $insert_stmt = $db->prepare($insert_sql);

    foreach ($result as $row) {
        $insert_stmt->execute([
            ':paciente_nome' => $row['paciente_nome'] ?? null,
            ':paciente_convenio' => $row['paciente_convenio'] ?? null,
            ':paciente_especialidade' => $row['paciente_especialidade'] ?? null,
            ':paciente_section' => $row['paciente_section'] ?? null,
            ':paciente_entrada' => $row['paciente_entrada'] ?? null,
            ':paciente_mes' => 'Outubro'
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leitorXML'])) {
    $xml = simplexml_load_file('LOTE_240904.xml');

    // Definir o namespace
    $namespaces = $xml->getNamespaces(true);
    $xml->registerXPathNamespace('ans', $namespaces['ans']);

    // Encontrar todas as tags <ans:senha> e extrair seus valores
    $senhas = $xml->xpath('//ans:senha');

    // Criar um novo Spreadsheet
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();
    $sheet->setCellValue('A1', 'Senha');

    // Adicionar todas as senhas ao Spreadsheet
    $rowNumber = 2;
    foreach ($senhas as $senha) {
        $sheet->setCellValue('A' . $rowNumber, (string) $senha);
        $rowNumber++;
    }

    // Salvar o Spreadsheet em um arquivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('senhas.xlsx');

    echo "Arquivo Excel criado com sucesso!";
}


?>