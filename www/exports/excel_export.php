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

    // Remove caracteres inválidos do nome do arquivo
    $filename = preg_replace('/[^a-zA-Z0-9_\-\.]/', '_', $filename);

    require_once('../vendor/autoload.php');

    // Verifica se há dados para exportar
    if (empty($dados)) {
        throw new Exception('Nenhum dado disponível para exportação.');
    }

    try {
        // Cria uma nova instância do Spreadsheet
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

        // Verificar se $dados[0] existe e é array antes de tentar acessar suas chaves
        $numColunas = 5; // Valor padrão se não houver dados
        if (!empty($dados) && isset($dados[0]) && is_array($dados[0])) {
            $numColunas = count($dados[0]);
        }

        $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($numColunas - 1);
        $sheet->mergeCells('A1:' . $lastCol . '1');

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
        $sheet->mergeCells('A2:' . $lastCol . '2');

        // Adicionar total de registros
        $sheet->setCellValue('A3', 'Total de Registros: ' . count($dados));
        $sheet->mergeCells('A3:' . $lastCol . '3');

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
                // Formatar cabeçalhos para remover prefixo "paciente_"
                $header_formatado = str_replace('paciente_', '', $header);
                $header_formatado = ucwords(str_replace('_', ' ', $header_formatado));

                $colLetter = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column);
                $sheet->setCellValue($colLetter . '5', $header_formatado);
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
                'borders' => [
                    'allBorders' => [
                        'borderStyle' => \PhpOffice\PhpSpreadsheet\Style\Border::BORDER_THIN,
                        'color' => ['rgb' => 'FFFFFF'],
                    ],
                ],
            ];
            $sheet->getStyle('A5:' . \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1) . '5')->applyFromArray($headerStyle);

            // Adicionar dados a partir da linha 6
            $row = 6;
            foreach ($dados as $dataRow) {
                $column = 0;
                foreach ($dataRow as $value) {
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

        // Limpar os buffers de saída antes de enviar cabeçalhos
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Configurar os cabeçalhos HTTP
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Cache-Control: max-age=0');
        header('Expires: 0');
        header('Pragma: public');

        // Criar o escritor Xlsx
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);

        // Desabilitar recursos que podem causar problemas
        $writer->setPreCalculateFormulas(false);

        // Salvar diretamente para saída
        $writer->save('php://output');
    } catch (Exception $e) {
        // Em caso de erro, fornecer detalhes
        header('Content-Type: text/html; charset=utf-8');
        echo '<h2>Erro ao gerar o arquivo Excel</h2>';
        echo '<p>Ocorreu um erro ao tentar gerar o arquivo. Detalhes do erro:</p>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>Por favor, tente novamente ou entre em contato com o suporte técnico.</p>';
        echo '<p><a href="javascript:history.back()">Voltar</a></p>';

        // Registrar o erro em um arquivo de log para debug
        error_log('Erro na exportação Excel: ' . $e->getMessage() . "\n" . $e->getTraceAsString(), 0);
    }

    exit();
}
