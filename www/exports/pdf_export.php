<?php

/**
 * Função para exportar dados para PDF usando DomPDF
 * 
 * @param array $dados Array associativo com os dados para exportar
 * @param string $titulo_relatorio Título do relatório
 * @param string $filename Nome do arquivo (opcional)
 */
function exportarParaPDF($dados, $titulo_relatorio, $filename = null)
{
    // Se o nome do arquivo não for especificado, gera um nome baseado no título
    if ($filename === null) {
        $filename = 'Relatorio_' . date('Y-m-d_H-i-s') . '.pdf';
    }

    // Verifica se a extensão já existe no nome do arquivo
    if (strtolower(substr($filename, -4)) !== '.pdf') {
        $filename .= '.pdf';
    }

    require_once('../vendor/autoload.php');

    // Configurar o DomPDF
    $options = new Dompdf\Options();
    $options->set('isRemoteEnabled', true);
    $options->set('isHtml5ParserEnabled', true);
    $options->set('defaultFont', 'Helvetica');
    $options->set('defaultMediaType', 'print');
    $options->set('fontHeightRatio', 0.8); // Reduz a altura da fonte para caber mais informações

    $dompdf = new Dompdf\Dompdf($options);

    // Definir tamanho de página como A4 paisagem
    $dompdf->setPaper('A4', 'landscape');

    // Contar o número de colunas para ajustar tamanho da tabela
    $numColunas = 0;
    if (count($dados) > 0) {
        $numColunas = count(array_keys($dados[0]));
    }

    // Ajustar tamanho da fonte baseado no número de colunas
    $fontSizeHeader = 10;
    $fontSizeData = 9;
    $cellPadding = 4;

    if ($numColunas > 8) {
        $fontSizeHeader = 8;
        $fontSizeData = 7;
        $cellPadding = 3;
    }

    if ($numColunas > 12) {
        $fontSizeHeader = 7;
        $fontSizeData = 6;
        $cellPadding = 2;
    }

    // Remover prefixo "paciente_" das colunas e formatar cabeçalhos
    $colunas_formatadas = [];
    if (count($dados) > 0) {
        foreach (array_keys($dados[0]) as $coluna) {
            $coluna_formatada = str_replace('paciente_', '', $coluna);
            $coluna_formatada = ucwords(str_replace('_', ' ', $coluna_formatada));
            $colunas_formatadas[] = $coluna_formatada;
        }
    }

    // Gerar conteúdo HTML para o PDF
    $html = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($titulo_relatorio) . '</title>
        <style>
            @page {
                margin: 0.5cm;
            }
            body {
                font-family: Helvetica, Arial, sans-serif;
                font-size: ' . $fontSizeData . 'pt;
                color: #333;
                line-height: 1.3;
                margin: 0;
                padding: 10px;
            }
            h1 {
                font-size: 14pt;
                text-align: center;
                margin-bottom: 10pt;
                color: #00b3ff;
            }
            .header-info {
                margin-bottom: 15pt;
                font-size: 9pt;
                color: #666;
            }
            .header-info p {
                margin: 3pt 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15pt;
                table-layout: fixed;
            }
            th {
                background-color: #00b3ff;
                color: white;
                font-weight: bold;
                text-align: left;
                padding: ' . $cellPadding . 'pt;
                font-size: ' . $fontSizeHeader . 'pt;
                overflow: hidden;
                white-space: nowrap;
                text-overflow: ellipsis;
                border: 0.5pt solid #ddd;
            }
            td {
                padding: ' . $cellPadding . 'pt;
                border: 0.5pt solid #ddd;
                font-size: ' . $fontSizeData . 'pt;
                word-wrap: break-word;
            }
            tr:nth-child(even) {
                background-color: #f2f2f2;
            }
            .footer {
                position: fixed;
                bottom: 0;
                left: 0;
                right: 0;
                text-align: center;
                font-size: 7pt;
                color: #666;
                padding: 3pt;
                border-top: 0.5pt solid #ddd;
            }
            .page-number:after {
                content: counter(page);
            }
        </style>
    </head>
    <body>
        <h1>' . htmlspecialchars($titulo_relatorio) . '</h1>
        
        <div class="header-info">
            <p><strong>Data do Relatório:</strong> ' . date('d/m/Y H:i:s') . '</p>
            <p><strong>Total de Registros:</strong> ' . count($dados) . '</p>
        </div>
        
        <table>';

    // Adicionar cabeçalho da tabela
    if (count($dados) > 0) {
        $html .= '<thead><tr>';

        // Usar cabeçalhos formatados
        foreach ($colunas_formatadas as $coluna) {
            $html .= '<th>' . htmlspecialchars($coluna) . '</th>';
        }
        $html .= '</tr></thead>';

        // Adicionar dados
        $html .= '<tbody>';
        foreach ($dados as $linha) {
            $html .= '<tr>';
            foreach ($linha as $valor) {
                $html .= '<td>' . htmlspecialchars($valor) . '</td>';
            }
            $html .= '</tr>';
        }
        $html .= '</tbody>';
    } else {
        $html .= '<tr><td colspan="100%" style="text-align: center; padding: 20pt;">Nenhum registro encontrado.</td></tr>';
    }

    $html .= '</table>
        
        <div class="footer">
            <p>Lavorato System - Relatório gerado em ' . date('d/m/Y H:i:s') . ' - Página <span class="page-number"></span></p>
        </div>
    </body>
    </html>';

    $dompdf->loadHtml($html);
    $dompdf->render();

    // Verificar se o PDF foi gerado corretamente
    if ($dompdf->getCanvas()->get_width() <= 0 || $dompdf->getCanvas()->get_height() <= 0) {
        // Falha na geração do PDF
        header("Content-Type: text/html; charset=utf-8");
        echo "<h1>Erro ao gerar o PDF</h1>";
        echo "<p>Não foi possível gerar o relatório em PDF. Por favor, tente exportar para Excel.</p>";
        exit();
    }

    $dompdf->stream($filename, [
        'Attachment' => true // true = download, false = abrir no navegador
    ]);

    exit();
}
