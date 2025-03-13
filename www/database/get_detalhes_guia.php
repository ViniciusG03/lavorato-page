<?php
// Arquivo: www/database/get_detalhes_guia.php
// Inclui o arquivo de funções para especialidades
require_once 'functions_especialidades.php';

// Verificação de segurança
session_start();
if (!isset($_SESSION['login'])) {
    header('HTTP/1.0 403 Forbidden');
    echo "Acesso negado";
    exit;
}

if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID de guia não fornecido</div>";
    exit;
}

$id = intval($_GET['id']);

// Conexão com o banco de dados
$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    echo "<div class='alert alert-danger'>Erro de conexão: " . $conn->connect_error . "</div>";
    exit;
}

// Consulta principal para obter dados da guia
$sql = "SELECT * FROM pacientes WHERE id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo "<div class='alert alert-warning'>Guia não encontrada</div>";
    exit;
}

$guia = $result->fetch_assoc();
$stmt->close();

// Obter todas as especialidades
$especialidades = obter_especialidades_paciente($id, $conn);
?>

<div class="row">
    <div class="col-md-6">
        <h5 class="mb-3">Informações do Paciente</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">Nome:</th>
                <td><?php echo htmlspecialchars($guia['paciente_nome']); ?></td>
            </tr>
            <tr>
                <th>Convênio:</th>
                <td><?php echo htmlspecialchars($guia['paciente_convenio']); ?></td>
            </tr>
            <tr>
                <th>Número da Guia:</th>
                <td><?php echo htmlspecialchars($guia['paciente_guia']); ?></td>
            </tr>
            <tr>
                <th>Mês:</th>
                <td><?php echo htmlspecialchars($guia['paciente_mes']); ?></td>
            </tr>
            <tr>
                <th>Especialidades:</th>
                <td><?php echo formatar_especialidades($especialidades); ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5 class="mb-3">Status e Datas</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">Status:</th>
                <td>
                    <span class="badge bg-<?php
                    $status = $guia['paciente_status'];
                    if ($status == 'Emitido') echo 'success';
                    elseif ($status == 'Subiu') echo 'primary';
                    elseif ($status == 'Cancelado') echo 'danger';
                    elseif ($status == 'Saiu') echo 'warning';
                    elseif ($status == 'Retornou') echo 'purple';
                    elseif ($status == 'Não Usou') echo 'secondary';
                    elseif ($status == 'Assinado') echo 'info';
                    elseif ($status == 'Faturado') echo 'dark';
                    elseif ($status == 'Enviado a BM') echo 'pink';
                    elseif ($status == 'Devolvido BM') echo 'orange';
                    else echo 'secondary';
                    ?>">
                        <?php echo htmlspecialchars($status); ?>
                    </span>
                </td>
            </tr>
            <tr>
                <th>Data de Entrada:</th>
                <td><?php echo !empty($guia['paciente_entrada']) ? htmlspecialchars($guia['paciente_entrada']) : '<em>Não informada</em>'; ?></td>
            </tr>
            <tr>
                <th>Data de Saída:</th>
                <td><?php echo !empty($guia['paciente_saida']) ? htmlspecialchars($guia['paciente_saida']) : '<em>Não informada</em>'; ?></td>
            </tr>
            <tr>
                <th>Validade:</th>
                <td><?php echo !empty($guia['paciente_validade']) ? htmlspecialchars($guia['paciente_validade']) : '<em>Não informada</em>'; ?></td>
            </tr>
            <tr>
                <th>Data de Atualização:</th>
                <td><?php echo !empty($guia['data_hora_insercao']) ? date('d/m/Y H:i:s', strtotime($guia['data_hora_insercao'])) : '<em>Não informada</em>'; ?></td>
            </tr>
        </table>
    </div>
</div>

<div class="row mt-3">
    <div class="col-md-6">
        <h5 class="mb-3">Dados de Faturamento</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">Número de Sessões:</th>
                <td><?php echo !empty($guia['paciente_section']) ? htmlspecialchars($guia['paciente_section']) : '<em>Não informado</em>'; ?></td>
            </tr>
            <tr>
                <th>Lote:</th>
                <td><?php echo !empty($guia['paciente_lote']) ? htmlspecialchars($guia['paciente_lote']) : '<em>Não informado</em>'; ?></td>
            </tr>
            <tr>
                <th>Valor:</th>
                <td><?php echo !empty($guia['paciente_valor']) ? 'R$ ' . htmlspecialchars($guia['paciente_valor']) : '<em>Não informado</em>'; ?></td>
            </tr>
            <tr>
                <th>Quantidade Faturada:</th>
                <td><?php echo !empty($guia['paciente_faturado']) ? htmlspecialchars($guia['paciente_faturado']) : '<em>Não informado</em>'; ?></td>
            </tr>
            <tr>
                <th>Data Remessa:</th>
                <td><?php echo !empty($guia['paciente_data_remessa']) ? htmlspecialchars($guia['paciente_data_remessa']) : '<em>Não informada</em>'; ?></td>
            </tr>
        </table>
    </div>
    <div class="col-md-6">
        <h5 class="mb-3">Responsáveis</h5>
        <table class="table table-sm">
            <tr>
                <th width="40%">Usuário Responsável:</th>
                <td><?php 
                    $usuarios = [
                        'admin' => 'Vinicius Oliveira',
                        'talita' => 'Talita Ruiz',
                        'gustavoramos' => 'Gustavo Ramos',
                        'kaynnanduraes' => 'Kaynnan Durães',
                        'eviny' => 'Eviny Santos',
                        'tulio' => 'Tulio Uler',
                        'will' => 'Williams Licar'
                    ];
                    echo !empty($guia['usuario_responsavel']) ? 
                        (isset($usuarios[$guia['usuario_responsavel']]) ? 
                            $usuarios[$guia['usuario_responsavel']] : 
                            $guia['usuario_responsavel']) : 
                        '<em>Não informado</em>'; 
                ?></td>
            </tr>
        </table>
        
        <div class="mt-3">
            <h5 class="mb-3">Ações</h5>
            <a href="historico_guia.php?guia=<?php echo urlencode($guia['paciente_guia']); ?>" class="btn btn-info btn-sm" target="_blank">
                <i class="fas fa-history me-1"></i> Ver Histórico de Alterações
            </a>
        </div>
    </div>
</div>

<?php $conn->close(); ?>