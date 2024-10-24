<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;

// Configurações do Dompdf para permitir carregamento remoto (se necessário)
$options = new Options();
$options->set('isRemoteEnabled', true);

// Crie uma instância do Dompdf
$dompdf = new Dompdf($options);

// Definir o locale para Português Brasileiro
setlocale(LC_TIME, 'pt_BR.UTF-8', 'Portuguese_Brazil');

// Obter o mês atual no formato "Mês de Ano" (ex: "Outubro de 2024")
$mesAtual = strftime('%B de %Y'); // Exemplo: "Outubro de 2024"

// Função para gerar número de identificação aleatório começando com "P"
function gerarNumeroIdentificacao() {
    return 'P' . rand(1000, 9999); // Exemplo: "P1234"
}

// Função para converter imagem em base64
function imagemParaBase64($imagemCaminho) {
    $imagemTipo = pathinfo($imagemCaminho, PATHINFO_EXTENSION);
    $imagemDados = file_get_contents($imagemCaminho);
    return 'data:image/' . $imagemTipo . ';base64,' . base64_encode($imagemDados);
}

// Conectar ao banco de dados (exemplo de conexão MySQL)
$host = 'mysql.lavoratoguias.kinghost.net';
$dbname = 'lavoratoguias';
$username = 'lavoratogu_add1';
$password = 'system2024';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para buscar apenas o último registro de cada especialidade com base no maior ID
    $sql = "
    SELECT t1.paciente_nome, t1.paciente_especialidade
    FROM pacientes t1
    INNER JOIN (
        SELECT MAX(id) as ultimo_id, paciente_especialidade
        FROM pacientes
        WHERE paciente_nome = :nome
        GROUP BY paciente_especialidade
    ) t2 ON t1.id = t2.ultimo_id";
    
    $stmt = $pdo->prepare($sql);
    $stmt->execute(['nome' => 'LEVI NOGUEIRA MATOS']); // Nome do paciente, pode ser dinâmico

    // Fetch todos os dados do último registro de cada especialidade
    $pacienteData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifique se encontrou algum resultado
    if (count($pacienteData) === 0) {
        throw new Exception("Nenhuma especialidade encontrada para o paciente.");
    }

    // Converter a imagem para base64
    $imagemLogoBase64 = imagemParaBase64('logo.jpeg'); // Caminho para a logo

    // Variável para acumular todo o conteúdo HTML
    $html = '';

    foreach ($pacienteData as $paciente) {
        $nomePaciente = $paciente['paciente_nome'];
        $especialidade = $paciente['paciente_especialidade'];
        $numeroIdentificacao = gerarNumeroIdentificacao(); // Gerar número de identificação

        // Acumular o HTML de cada ficha com uma quebra de página
        $html .= '
        <div style="page-break-after: always;">
        <!DOCTYPE html>
        <html lang="pt-BR">
        <head>
            <meta charset="UTF-8">
            <meta name="viewport" content="width=device-width, initial-scale=1.0">
            <title>Ficha de Assinatura</title>
            <style>
                body {
                    font-family: Arial, sans-serif;
                    font-size: 12px;
                    margin: 5px;
                }
                .header {
                    display: flex;
                    justify-content: space-between;
                    align-items: center;
                    margin-bottom: 10px;
                }
                .header img {
                    width: 100px;
                    height: auto;
                }
                .header h1 {
                    font-size: 18px;
                    margin: 0;
                    text-align: center;
                    flex-grow: 1;
                }
                .header .identificacao {
                    font-size: 14px;
                    font-weight: bold;
                }
                .section {
                    margin-bottom: 5px;
                }
                .section label {
                    display: inline-block;
                    width: 200px;
                    font-weight: bold;
                }
                .table {
                    width: 100%;
                    border-collapse: collapse;
                }
                .table th, .table td {
                    border: 1px solid #000;
                    padding: 5px;
                    text-align: center;
                }
                .info-header {
                    text-align: left;
                    margin-bottom: 5px;
                }
            </style>
        </head>
        <body>
            <div class="header">
                <img src="' . $imagemLogoBase64 . '" alt="Logo"> <!-- Imagem em base64 -->
                <h1>FICHA DE ASSINATURA</h1>
                <div class="identificacao">ID: ' . $numeroIdentificacao . '</div>
            </div>

            <div class="info-header">
                <div class="section">
                    <label>Nome do Paciente:</label> ' . $nomePaciente . '
                </div>
                <div class="section">
                    <label>Especialidade:</label> ' . $especialidade . '
                </div>
                <div class="section">
                    <label>Mês:</label> ' . $mesAtual . '
                </div>
            </div>

            <table class="table">
                <thead>
                    <tr>
                        <th>Nº</th>
                        <th>Data de Atendimento</th>
                        <th>Assinatura do Responsável</th>
                    </tr>
                </thead>
                <tbody>';
        
        // Gerar as linhas da tabela (você pode adicionar dados específicos aqui)
        for ($i = 1; $i <= 30; $i++) {
            $html .= '<tr>
                <td>' . $i . '</td>
                <td></td>
                <td></td>
            </tr>';
        }

        $html .= '
                </tbody>
            </table>
        </body>
        </html>
        </div>'; // Fim da ficha e marca de quebra de página
    }

    // Carregar todo o conteúdo HTML no Dompdf
    $dompdf->loadHtml($html);

    // Definir o tamanho do papel e orientação
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar o PDF
    $dompdf->render();

    // Enviar o PDF para o navegador como um único arquivo com várias páginas
    $dompdf->stream("Ficha_Assinatura_Paciente.pdf", ["Attachment" => false]);

} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
