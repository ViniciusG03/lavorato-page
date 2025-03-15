<?php
// Salve este arquivo como ExportErrorHandler.php na pasta exports

/**
 * Classe para lidar com erros durante exportações
 */
class ExportErrorHandler
{
    /**
     * Registra um erro de exportação e apresenta uma mensagem amigável ao usuário
     *
     * @param Exception $exception Exceção capturada
     * @param string $type Tipo de exportação (excel, pdf, etc)
     * @param boolean $returnToForm Se verdadeiro, inclui um link para voltar ao formulário
     * @return void
     */
    public static function handleError($exception, $type = 'excel', $returnToForm = true)
    {
        // Registrar erro no log do sistema
        $logMessage = sprintf(
            "[%s] Erro na exportação %s: %s\nStack trace: %s",
            date('Y-m-d H:i:s'),
            strtoupper($type),
            $exception->getMessage(),
            $exception->getTraceAsString()
        );

        error_log($logMessage, 0);

        // Limpar qualquer saída que possa ter sido enviada
        if (ob_get_level()) {
            ob_end_clean();
        }

        // Definir cabeçalho para HTML
        header('Content-Type: text/html; charset=utf-8');

        // Exibir mensagem de erro amigável
        echo '<!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Erro na Exportação - Lavorato System</title>
            <style>
                body {
                    font-family: "Poppins", Arial, sans-serif;
                    background-color: #f8f9fa;
                    color: #333;
                    line-height: 1.6;
                    padding: 20px;
                    max-width: 800px;
                    margin: 0 auto;
                }
                .error-container {
                    background-color: #fff;
                    border-radius: 8px;
                    padding: 25px;
                    box-shadow: 0 2px 10px rgba(0,0,0,0.1);
                    margin-top: 30px;
                }
                h1 {
                    color: #dc3545;
                    margin-top: 0;
                }
                .error-details {
                    background-color: #f8f9fa;
                    border-left: 4px solid #dc3545;
                    padding: 15px;
                    margin: 20px 0;
                    border-radius: 0 4px 4px 0;
                }
                .actions {
                    margin-top: 25px;
                }
                .btn {
                    display: inline-block;
                    font-weight: 400;
                    text-align: center;
                    white-space: nowrap;
                    vertical-align: middle;
                    user-select: none;
                    border: 1px solid transparent;
                    padding: 0.375rem 0.75rem;
                    font-size: 1rem;
                    line-height: 1.5;
                    border-radius: 0.25rem;
                    text-decoration: none;
                    transition: all 0.15s ease-in-out;
                    cursor: pointer;
                }
                .btn-primary {
                    color: #fff;
                    background-color: #00b3ff;
                    border-color: #00b3ff;
                }
                .btn-primary:hover {
                    background-color: #0099e6;
                    border-color: #0099e6;
                }
                .btn-secondary {
                    color: #fff;
                    background-color: #6c757d;
                    border-color: #6c757d;
                }
                .btn-secondary:hover {
                    background-color: #5a6268;
                    border-color: #545b62;
                }
                .alert {
                    padding: 15px;
                    margin-bottom: 20px;
                    border: 1px solid transparent;
                    border-radius: 4px;
                }
                .alert-info {
                    color: #0c5460;
                    background-color: #d1ecf1;
                    border-color: #bee5eb;
                }
            </style>
        </head>
        <body>
            <div class="error-container">
                <h1>Erro ao Gerar ' . ucfirst($type) . '</h1>
                <p>Ocorreu um erro ao tentar gerar o arquivo ' . strtoupper($type) . '. Nossa equipe técnica foi notificada automaticamente.</p>
                
                <div class="error-details">
                    <h3>Detalhes do erro:</h3>
                    <p>' . htmlspecialchars($exception->getMessage()) . '</p>
                </div>
                
                <div class="alert alert-info">
                    <strong>Sugestões para resolver o problema:</strong>
                    <ul>
                        <li>Verifique se os filtros selecionados não retornam um volume muito grande de dados.</li>
                        <li>Tente exportar um conjunto menor de dados, aplicando mais filtros.</li>
                        <li>Tente novamente em alguns minutos, pois pode ser um problema temporário.</li>
                    </ul>
                </div>
                
                <div class="actions">
                    <a href="javascript:history.back()" class="btn btn-primary">Voltar</a>';

        if ($returnToForm) {
            echo '<a href="../views/relatorios_customizados.php" class="btn btn-secondary" style="margin-left:10px;">Voltar para Relatórios</a>';
        }

        echo '
                </div>
            </div>
        </body>
        </html>';

        exit;
    }
}
