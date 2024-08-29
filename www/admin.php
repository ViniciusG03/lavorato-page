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
            ':paciente_mes' => 'Setembro'
        ]);
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['leitorXML'])) {
    $xml = simplexml_load_file('LOTE_240805.xml', 'SimpleXMLElement', LIBXML_NOCDATA);

    // Criar uma nova planilha
    $spreadsheet = new Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar cabeçalhos da planilha
    $cabecalhos = [
        'Tipo Transação',
        'Sequencial Transação',
        'Data Registro Transação',
        'Hora Registro Transação',
        'Código Prestador',
        'Registro ANS',
        'Número Lote',
        'Número Guia Operadora',
        'Data Autorização',
        'Senha',
        'Data Validade Senha',
        'Número Carteira',
        'Atendimento RN',
        'CNPJ Contratado',
        'Nome Contratado',
        'CNES',
        'Sequencial Item',
        'Data Execução',
        'Código Tabela',
        'Código Procedimento',
        'Descrição Procedimento',
        'Quantidade Executada',
        'Valor Unitário',
        'Valor Total',
        'CPF Profissional',
        'Nome Profissional',
        'Conselho',
        'Número Conselho Profissional',
        'UF',
        'CBOS'
    ];

    $col = 1;
    foreach ($cabecalhos as $cabecalho) {
        $sheet->setCellValue([$col++, 1], $cabecalho);
    }

    // Preencher a planilha com dados do XML
    $row = 2; // Inicia na segunda linha

    foreach ($xml->xpath('//ans:guiaSP-SADT') as $guia) {
        $col = 1;
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:tipoTransacao')[0]);
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:sequencialTransacao')[0]);
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:dataRegistroTransacao')[0]);
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:horaRegistroTransacao')[0]);
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:codigoPrestadorNaOperadora')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:registroANS')[0]);
        $sheet->setCellValue([$col++, $row], (string) $xml->xpath('//ans:numeroLote')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:numeroGuiaOperadora')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:dataAutorizacao')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:senha')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:dataValidadeSenha')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:numeroCarteira')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:atendimentoRN')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:cnpjContratado')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:nomeContratado')[0]);
        $sheet->setCellValue([$col++, $row], (string) $guia->xpath('.//ans:CNES')[0]);

        foreach ($guia->xpath('.//ans:procedimentoExecutado') as $procedimento) {
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:sequencialItem')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:dataExecucao')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:codigoTabela')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:codigoProcedimento')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:descricaoProcedimento')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:quantidadeExecutada')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:valorUnitario')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:valorTotal')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:cpfContratado')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:nomeProf')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:conselho')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:numeroConselhoProfissional')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:UF')[0]);
            $sheet->setCellValue([$col++, $row], (string) $procedimento->xpath('.//ans:CBOS')[0]);

            $row++; // Mover para a próxima linha para o próximo procedimento
        }
    }

    // Salvar o arquivo Excel
    $writer = new Xlsx($spreadsheet);
    $writer->save('saida.xlsx');
}

?>