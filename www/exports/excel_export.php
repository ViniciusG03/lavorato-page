<?php

/**
 * Função para exportar dados para Excel usando PhpSpreadsheet
 * 
 * @param array $dados Array associativo com os dados para exportar
 * @param string $titulo_relatorio Título do relatório
 * @param string $filename Nome do arquivo (opcional)
 */
function exportarParaExcel($dados, $titulo_relatorio, $filename = null)
{
    // Se o nome do arquivo não for especificado, gera um nome baseado no título
    if ($filename === null) {
        $filename = 'Relatorio_' . date('Y-m-d_H-i-s') . '.xlsx';
    }

    // Verifica se a extensão já existe no nome do arquivo
    if (strtolower(substr($filename, -5)) !== '.xlsx') {
        $filename .= '.xlsx';
    }

    require_once('../vendor/autoload.php');

    // Verifica se há dados para exportar
    if (empty($dados)) {
        throw new Exception('Nenhum dado disponível para exportação.');
    }

    $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
    $sheet = $spreadsheet->getActiveSheet();

    // Configurar propriedades do documento
    $spreadsheet->getProperties()
        ->setCreator('Lavorato System')
        ->setLastModifiedBy('Lavorato System')
        ->setTitle($titulo_relatorio)
        ->setSubject('Relatório de Guias')
        ->setDescription('Relatório gerado pelo sistema Lavorato')
        ->setKeywords('lavorato relatório guias')
        ->setCategory('Relatórios');

    // Configurar título do relatório
    $sheet->setCellValue('A1', $titulo_relatorio);
    $sheet->mergeCells('A1:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($dados[0]) - 1) . '1');

    // Estilizar o título
    $titleStyle = [
        'font' => [
            'bold' => true,
            'size' => 16,
            'color' => ['rgb' => '00B3FF'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
        ],
    ];
    $sheet->getStyle('A1')->applyFromArray($titleStyle);
    $sheet->getRowDimension(1)->setRowHeight(30);

    // Adicionar data do relatório
    $sheet->setCellValue('A2', 'Data do Relatório: ' . date('d/m/Y H:i:s'));
    $sheet->mergeCells('A2:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($dados[0]) - 1) . '2');

    // Adicionar total de registros
    $sheet->setCellValue('A3', 'Total de Registros: ' . count($dados));
    $sheet->mergeCells('A3:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count($dados[0]) - 1) . '3');

    // Estilizar informações adicionais
    $infoStyle = [
        'font' => [
            'size' => 10,
            'color' => ['rgb' => '666666'],
        ],
        'alignment' => [
            'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_LEFT,
        ],
    ];
    $sheet->getStyle('A2:A3')->applyFromArray($infoStyle);

    // Definir cabeçalhos da tabela na linha 5
    if (count($dados) > 0) {
        $column = 0;
        foreach (array_keys($dados[0]) as $header) {
            // Substituição do método setCellValueByColumnAndRow
            $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
            $sheet->setCellValue($colLetter . '5', $header);
            $column++;
        }

        // Estilizar cabeçalhos
        $headerStyle = [
            'font' => [
                'bold' => true,
                'color' => ['rgb' => 'FFFFFF'],
            ],
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => '00B3FF',
                ],
            ],
            'alignment' => [
                'horizontal' => \PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER,
            ],
        ];
        $sheet->getStyle('A5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1) . '5')->applyFromArray($headerStyle);

        // Adicionar dados a partir da linha 6
        $row = 6;
        foreach ($dados as $dataRow) {
            $column = 0;
            foreach ($dataRow as $value) {
                // Substituição do método setCellValueByColumnAndRow
                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
                $sheet->setCellValue($colLetter . $row, $value);
                $column++;
            }
            $row++;
        }

        // Adicionar estilo zebrado nas linhas de dados
        $zebraStyle = [
            'fill' => [
                'fillType' => \PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID,
                'startColor' => [
                    'rgb' => 'F2F2F2',
                ],
            ],
        ];

        for ($i = 6; $i < $row; $i += 2) {
            $sheet->getStyle('A' . $i . ':' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1) . $i)->applyFromArray($zebraStyle);
        }

        // Aplicar bordas finas em toda a tabela
        $borderStyle = [
            'borders' => [
                'allBorders' => [
                    'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                    'color' => ['rgb' => 'DDDDDD'],
                ],
            ],
        ];
        $sheet->getStyle('A5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1) . ($row - 1))->applyFromArray($borderStyle);

        // Auto-dimensionar colunas
        foreach (range('A', \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1)) as $col) {
            $sheet->getColumnDimension($col)->setAutoSize(true);
        }

        // Adicionar filtros aos cabeçalhos
        $sheet->setAutoFilter('A5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1) . '5');
    } else {
        // Se não houver dados, exibir mensagem
        $sheet->setCellValue('A5', 'Nenhum registro encontrado.');
        $sheet->mergeCells('A5:D5');
    }

    try {
        // Configurar o cabeçalho de saída
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment;filename="' . $filename . '"');
        header('Cache-Control: max-age=0');

        // Salvar o arquivo para saída
        $writer = \PhpOffice\PhpSpreadsheet\IOFactory::createWriter($spreadsheet, 'Xlsx');
        $writer->save('php://output');
    } catch (Exception $e) {
        // Lidar com exceções
        echo 'Erro ao gerar o arquivo Excel: ',  $e->getMessage();
    }

    exit();
}
