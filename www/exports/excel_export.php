<?php

/**
 * Função para exportar dados para Excel usando PhpSpreadsheet
 * Versão que usa arquivo temporário para evitar problemas com ZipStream
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

    // Verificar se o autoloader está disponível
    $autoloaderPaths = [
        __DIR__ . '/../vendor/autoload.php',
        __DIR__ . '/vendor/autoload.php',
    ];

    $autoloaderLoaded = false;
    foreach ($autoloaderPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $autoloaderLoaded = true;
            break;
        }
    }

    if (!$autoloaderLoaded) {
        throw new Exception('Não foi possível carregar o autoloader. Verifique se o Composer está instalado corretamente.');
    }

    if (!class_exists('\PhpOffice\PhpSpreadsheet\Spreadsheet')) {
        throw new Exception('A classe PhpSpreadsheet não foi encontrada. Verifique se o pacote está instalado: composer require phpoffice/phpspreadsheet');
    }

    foreach ($autoloaderPaths as $path) {
        if (file_exists($path)) {
            require_once $path;
            $autoloaderLoaded = true;
            error_log("Autoloader carregado de: " . $path);
            break;
        }
    }

    // Verifica se há dados para exportar
    if (empty($dados)) {
        throw new Exception('Nenhum dado disponível para exportação.');
    }

    try {
        // Aumentar limite de memória e tempo de execução
        ini_set('memory_limit', '256M');
        set_time_limit(300);

        // Criar uma nova instância do Spreadsheet
        $spreadsheet = new \PhpOffice\PhpSpreadsheet\Spreadsheet();
        $sheet = $spreadsheet->getActiveSheet();

        // Definir título da planilha
        $sheet->setTitle(substr('Relatório', 0, 31)); // Máximo de 31 caracteres

        // Configurar propriedades do documento
        $spreadsheet->getProperties()
            ->setCreator('Lavorato System')
            ->setLastModifiedBy('Lavorato System')
            ->setTitle($titulo_relatorio)
            ->setSubject('Relatório de Guias')
            ->setDescription('Relatório gerado pelo sistema Lavorato')
            ->setCategory('Relatórios');

        // Título do relatório na célula A1
        $sheet->setCellValue('A1', $titulo_relatorio);

        // Estilizar o título
        $sheet->getStyle('A1')->getFont()->setBold(true)->setSize(14);
        $sheet->getStyle('A1')->getAlignment()->setHorizontal(\PhpOffice\PhpSpreadsheet\Style\Alignment::HORIZONTAL_CENTER);

        // Mesclar células para o título
        $lastColumn = empty($dados) ? 'D' : \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex(count(reset($dados)) - 1);
        $sheet->mergeCells('A1:' . $lastColumn . '1');

        // Adicionar data do relatório e total de registros
        $sheet->setCellValue('A2', 'Data do Relatório: ' . date('d/m/Y H:i:s'));
        $sheet->setCellValue('A3', 'Total de Registros: ' . count($dados));
        $sheet->mergeCells('A2:' . $lastColumn . '2');
        $sheet->mergeCells('A3:' . $lastColumn . '3');

        // Adicionar altura da linha do título
        $sheet->getRowDimension(1)->setRowHeight(25);

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
            $lastCol = \PhpOffice\PhpSpreadsheet\Cell\Coordinate::stringFromColumnIndex($column - 1);
            $sheet->getStyle('A5:' . $lastCol . '5')->getFont()->setBold(true);
            $sheet->getStyle('A5:' . $lastCol . '5')->getFill()
                ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                ->getStartColor()->setRGB('00B3FF');
            $sheet->getStyle('A5:' . $lastCol . '5')->getFont()->getColor()->setRGB('FFFFFF');

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

            // Auto-dimensionar colunas
            foreach (range('A', $lastCol) as $col) {
                $sheet->getColumnDimension($col)->setAutoSize(true);
            }

            // Adicionar filtros aos cabeçalhos
            $sheet->setAutoFilter('A5:' . $lastCol . '5');

            // Formatar linha alternada (estilo zebra)
            for ($i = 6; $i < $row; $i += 2) {
                $sheet->getStyle('A' . $i . ':' . $lastCol . $i)->getFill()
                    ->setFillType(\PhpOffice\PhpSpreadsheet\Style\Fill::FILL_SOLID)
                    ->getStartColor()->setRGB('F2F2F2');
            }
        } else {
            // Se não houver dados, exibir mensagem
            $sheet->setCellValue('A5', 'Nenhum registro encontrado.');
            $sheet->mergeCells('A5:D5');
        }

        // Método alternativo: Salvar em arquivo temporário e enviar para download
        // Isso evita problemas com ZipStream

        // Criar diretório temporário se não existir
        $tempDir = __DIR__ . '/../temp';
        if (!file_exists($tempDir)) {
            if (!mkdir($tempDir, 0755, true)) {
                throw new Exception("Não foi possível criar o diretório temporário.");
            }
        }

        // Gerar nome de arquivo temporário
        $tempFile = $tempDir . '/' . $filename;

        // Limpar saída antes de enviar cabeçalhos
        while (ob_get_level()) {
            ob_end_clean();
        }

        // Criar e salvar o arquivo
        $writer = new \PhpOffice\PhpSpreadsheet\Writer\Xlsx($spreadsheet);
        $writer->setPreCalculateFormulas(false);
        $writer->save($tempFile);

        // Verificar se o arquivo foi criado
        if (!file_exists($tempFile) || filesize($tempFile) <= 0) {
            throw new Exception("Falha ao gerar o arquivo Excel.");
        }

        // Enviar o arquivo para download
        header('Content-Description: File Transfer');
        header('Content-Type: application/vnd.openxmlformats-officedocument.spreadsheetml.sheet');
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Transfer-Encoding: binary');
        header('Expires: 0');
        header('Cache-Control: must-revalidate');
        header('Pragma: public');
        header('Content-Length: ' . filesize($tempFile));

        // Ler e enviar o arquivo
        if (readfile($tempFile)) {
            // Remover arquivo temporário após envio
            @unlink($tempFile);
        } else {
            throw new Exception("Falha ao ler o arquivo temporário.");
        }

        exit();
    } catch (Exception $e) {
        // Registrar o erro no log
        error_log("Erro na exportação Excel: " . $e->getMessage() . "\n" . $e->getTraceAsString());

        // Exibir mensagem de erro para o usuário
        header('Content-Type: text/html; charset=utf-8');
        echo '<h2>Erro ao gerar o arquivo Excel</h2>';
        echo '<p>Ocorreu um erro ao tentar gerar o arquivo. Detalhes do erro:</p>';
        echo '<pre>' . htmlspecialchars($e->getMessage()) . '</pre>';
        echo '<p>Por favor, tente novamente ou entre em contato com o suporte técnico.</p>';
        echo '<p><a href="javascript:history.back()">Voltar</a></p>';
    }

    exit();
}
