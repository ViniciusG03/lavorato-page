<?php
require '../vendor/autoload.php';

use Dompdf\Dompdf;
use Dompdf\Options;
use setasign\Fpdi\Fpdi;

// Configurações do Dompdf para permitir carregamento remoto (se necessário)
$options = new Options();
$options->set('isRemoteEnabled', true);

// Função para gerar número de identificação aleatório começando com "P"
function gerarNumeroIdentificacao()
{
    return 'P' . rand(1000, 9999); // Exemplo: "P1234"
}

// Função para converter imagem em base64
function imagemParaBase64($imagemCaminho)
{
    if (!file_exists($imagemCaminho)) {
        throw new Exception("A imagem não foi encontrada no caminho especificado.");
    }
    $imagemTipo = pathinfo($imagemCaminho, PATHINFO_EXTENSION);
    $imagemDados = file_get_contents($imagemCaminho);
    return 'data:image/' . $imagemTipo . ';base64,' . base64_encode($imagemDados);
}

// Função para gerar e salvar um PDF temporário
function gerarPdfTemporario($html, $filePath)
{
    $options = new Options();
    $options->set('isRemoteEnabled', true);
    $dompdf = new Dompdf($options);

    // Carregar o HTML no Dompdf
    $dompdf->loadHtml($html);

    // Definir o tamanho do papel e a orientação
    $dompdf->setPaper('A4', 'portrait');

    // Renderizar o PDF
    $dompdf->render();

    // Salvar o PDF gerado em um arquivo temporário
    file_put_contents($filePath, $dompdf->output());
}

// Função para mesclar os PDFs temporários em um único arquivo
function mesclarPdfs($pdfFiles, $outputPath)
{
    $pdf = new Fpdi();

    foreach ($pdfFiles as $file) {
        $pageCount = $pdf->setSourceFile($file);
        for ($i = 1; $i <= $pageCount; $i++) {
            $tplIdx = $pdf->importPage($i);
            $pdf->AddPage();
            $pdf->useTemplate($tplIdx);
        }
    }

    // Salvar o PDF mesclado
    $pdf->Output($outputPath, 'F');
}

// Definir o locale para Português Brasileiro
setlocale(LC_TIME, 'pt_BR.UTF-8', 'Portuguese_Brazil');

// Obter os dados do formulário enviados via POST
$nomePaciente = $_POST['nome'] ?? '';
$especialidade = $_POST['especialidade'] ?? '';
$mesAtual = $_POST['mes'] ?? 'Novembro de 2024'; // Valor padrão para o mês

// Conectar ao banco de dados
$host = 'mysql.lavoratoguias.kinghost.net';
$dbname = 'lavoratoguias';
$username = 'lavoratogu_add1';
$password = 'system2024';

try {
    $pdo = new PDO("mysql:host=$host;dbname=$dbname", $username, $password);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Query para buscar pacientes com os filtros do formulário
    $sql = "
    SELECT t1.paciente_nome, t1.paciente_especialidade
    FROM pacientes t1
    INNER JOIN (
        SELECT MAX(id) as ultimo_id, paciente_nome, paciente_especialidade
        FROM pacientes
        WHERE paciente_convenio = 'CBMDF'
        AND (paciente_saida IS NULL OR paciente_saida = '')
        AND paciente_status NOT IN ('Saiu', 'Cancelado')
        AND paciente_especialidade NOT LIKE '%consulta%'
        AND paciente_nome LIKE :nomePaciente
        AND paciente_especialidade LIKE :especialidade
        GROUP BY paciente_nome, paciente_especialidade
    ) t2 ON t1.id = t2.ultimo_id";

    $stmt = $pdo->prepare($sql);
    $stmt->execute([
        ':nomePaciente' => "%$nomePaciente%",
        ':especialidade' => "%$especialidade%",
    ]);

    $pacientesData = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // Verifique se encontrou algum resultado
    if (count($pacientesData) === 0) {
        throw new Exception("Nenhum paciente encontrado.");
    }

    // Converter a imagem para base64
    $imagemLogoBase64 = imagemParaBase64('cbmdf.jpg'); // Caminho para a logo

    // Array para armazenar os caminhos dos arquivos temporários de PDF
    $pdfFiles = [];

    // Loop para gerar PDFs temporários
    foreach ($pacientesData as $index => $paciente) {
        $filePath = "temp_pdf_$index.pdf"; // Cria um novo arquivo temporário para cada ficha
        $nomePaciente = $paciente['paciente_nome'];
        $especialidade = $paciente['paciente_especialidade'];
        $numeroIdentificacao = gerarNumeroIdentificacao(); // Gerar número de identificação

        // HTML de cada ficha individualmente
        $html = '
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
                    width: 70px;
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
                <img src="' . $imagemLogoBase64 . '" alt="Logo">
                <h1>FICHA DE ASSINATURA</h1>
                <div class="identificacao">ID: ' . $numeroIdentificacao . '</div>
            </div>

            <div class="info-header">
                <div class="section">
                    <label>Nome do Paciente:</label> ' . htmlspecialchars($paciente['paciente_nome']) . '
                </div>
                <div class="section">
                    <label>Especialidade:</label> ' . htmlspecialchars($paciente['paciente_especialidade']) . '
                </div>
                <div class="section">
                    <label>Mês:</label> ' . htmlspecialchars($mesAtual) . '
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

        // Gerar as linhas da tabela
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
        </html>';

        // Gerar o PDF temporário para cada ficha
        gerarPdfTemporario($html, $filePath);
        $pdfFiles[] = $filePath;
    }

    // Mesclar os PDFs temporários em um único arquivo final
    $outputPath = 'Fichas_Assinatura_Todos_Pacientes.pdf';
    mesclarPdfs($pdfFiles, $outputPath);

    // Enviar o PDF final mesclado para o navegador
    header('Content-Type: application/pdf');
    header('Content-Disposition: inline; filename="' . $outputPath . '"');
    readfile($outputPath);

    // Limpar arquivos temporários
    foreach ($pdfFiles as $file) {
        unlink($file);
    }
} catch (Exception $e) {
    echo "Erro: " . $e->getMessage();
}
