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

    $dompdf = new Dompdf\Dompdf($options);
    $dompdf->setPaper('A4', 'landscape');  // Use 'portrait' para vertical

    // Gerar conteúdo HTML para o PDF
    $html = '
    <!DOCTYPE html>
    <html lang="pt-BR">
    <head>
        <meta charset="UTF-8">
        <title>' . htmlspecialchars($titulo_relatorio) . '</title>
        <style>
            @page {
                margin: 1cm;
            }
            body {
                font-family: Arial, sans-serif;
                font-size: 10pt;
                color: #333;
                line-height: 1.4;
            }
            h1 {
                font-size: 16pt;
                text-align: center;
                margin-bottom: 15pt;
                color: #00b3ff;
            }
            .header-info {
                margin-bottom: 20pt;
                font-size: 9pt;
                color: #666;
            }
            .header-info p {
                margin: 5pt 0;
            }
            table {
                width: 100%;
                border-collapse: collapse;
                margin-bottom: 15pt;
            }
            th {
                background-color: #00b3ff;
                color: white;
                font-weight: bold;
                text-align: left;
                padding: 5pt;
                font-size: 9pt;
            }
            td {
                padding: 5pt;
                border-bottom: 1px solid #ddd;
                font-size: 8pt;
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
                font-size: 8pt;
                color: #666;
                padding: 5pt;
                border-top: 1px solid #ddd;
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
        foreach (array_keys($dados[0]) as $coluna) {
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

    $dompdf->stream($filename, [
        'Attachment' => true // true = download, false = abrir no navegador
    ]);

    exit();
}
