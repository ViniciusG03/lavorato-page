<?php
session_start();

if (!isset($_SESSION['login'])) {
    header("Location: ../login/login.php");
    exit();
}

// Verifica se é uma requisição AJAX
$isAjax = isset($_SERVER['HTTP_X_REQUESTED_WITH']) && $_SERVER['HTTP_X_REQUESTED_WITH'] === 'XMLHttpRequest';

$servername = "mysql.lavoratoguias.kinghost.net";
$username = "lavoratoguias";
$password = "A3g7K2m9T5p8L4v6";
$database = "lavoratoguias";

$conn = new mysqli($servername, $username, $password, $database);

if ($conn->connect_error) {
    if ($isAjax) {
        header('Content-Type: application/json');
        echo json_encode(["success" => false, "message" => "Erro na conexão: " . $conn->connect_error]);
        exit;
    } else {
        die("Erro na conexão: " . $conn->connect_error);
    }
}

// Incluir o arquivo de funções para especialidades
require_once __DIR__ . '/functions_especialidades.php';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $nome = $_POST["nome"];
    $convenio = $_POST["convenio"];
    $numero_guia = $_POST["numero_guia"];
    $status_guia = $_POST["status_guia"];
    
    // Capturar as especialidades como array
    $especialidades = isset($_POST["especialidades"]) && is_array($_POST["especialidades"]) 
        ? $_POST["especialidades"] 
        : [];
    
    // Garantir que temos ao menos uma especialidade para compatibilidade
    $especialidade = !empty($especialidades) ? $especialidades[0] : "";
    
    $mes = $_POST["mes"];
    $entrada = $_POST["entrada"];
    $saida = $_POST["saida"];
    $section = $_POST["numero_section"];
    $validade = $_POST["validade"];
    $usuario_responsavel = $_SESSION['login'];

    // Validações de campos obrigatórios
    if (empty($nome) || empty($convenio) || empty($numero_guia) || empty($status_guia) || empty($especialidades) || empty($mes) || empty($entrada) || empty($section)) {
        if ($isAjax) {
            header('Content-Type: application/json');
            echo json_encode(["success" => false, "message" => "Todos os campos devem ser preenchidos, exceto 'Saída'!"]);
            exit;
        } else {
            echo "<h1>Todos os campos devem ser preenchidos, exceto 'Saída'!</h1>";
        }
    } else {
        // Verificar se a guia já existe
        $verifica_sql = "SELECT COUNT(*) as count FROM pacientes WHERE paciente_guia = ? AND paciente_mes = ?";
        $stmt_verificacao = $conn->prepare($verifica_sql);
        $stmt_verificacao->bind_param("ss", $numero_guia, $mes);
        $stmt_verificacao->execute();
        $resultado_verificacao = $stmt_verificacao->get_result();
        $row = $resultado_verificacao->fetch_assoc();
        $numero_de_registros = $row['count'];
        $stmt_verificacao->close();

        if ($numero_de_registros > 0) {
            if ($isAjax) {
                header('Content-Type: application/json');
                echo json_encode(["success" => false, "message" => "Erro: A guia já está cadastrada!"]);
                exit;
            } else {
                echo "<h2>Erro: A guia já está cadastrada!</h2>";
            }
        } else {
            // Iniciar transação para garantir integridade
            $conn->begin_transaction();
            
            try {
                // Cadastrar na tabela principal (compatibilidade com o sistema existente)
                $sql = "INSERT INTO pacientes (
                    paciente_nome, 
                    paciente_convenio, 
                    paciente_guia, 
                    paciente_status, 
                    paciente_especialidade, 
                    paciente_mes, 
                    paciente_entrada, 
                    paciente_saida, 
                    paciente_section, 
                    paciente_validade,
                    usuario_responsavel
                ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
                
                $stmt = $conn->prepare($sql);
                $stmt->bind_param(
                    "sssssssssss", 
                    $nome, 
                    $convenio, 
                    $numero_guia, 
                    $status_guia, 
                    $especialidade, 
                    $mes, 
                    $entrada, 
                    $saida, 
                    $section, 
                    $validade,
                    $usuario_responsavel
                );
                
                if (!$stmt->execute()) {
                    throw new Exception("Erro ao cadastrar a guia: " . $stmt->error);
                }
                
                // Obter ID do registro inserido
                $paciente_id = $conn->insert_id;
                $stmt->close();
                
                // Salvar todas as especialidades
                if (!salvar_especialidades_paciente($paciente_id, $especialidades, $conn)) {
                    throw new Exception("Erro ao salvar especialidades");
                }
                
                // Confirmar transação
                $conn->commit();
                
                // Dados para apresentar no resumo
                $resumo = [
                    "nome" => $nome,
                    "convenio" => $convenio,
                    "numero_guia" => $numero_guia,
                    "section" => $section,
                    "status_guia" => $status_guia,
                    "especialidades" => $especialidades,
                    "validade" => $validade,
                    "mes" => $mes,
                    "entrada" => $entrada,
                    "saida" => $saida
                ];
                
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(["success" => true, "message" => "Guia cadastrada com sucesso!", "data" => $resumo]);
                    exit;
                } else {
                    echo "<h1> Guia cadastrada com sucesso!</h1>";
                    echo "<h2>Nome do paciente:</h2> <p>$nome</p>";
                    echo "<h2>Convênio do paciente:</h2> <p>$convenio</p>";
                    echo "<h2>Número da Guia:</h2> <p>$numero_guia</p>";
                    echo "<h2>Número de Seções:</h2> <p>$section</p>";
                    echo "<h2>Status da Guia:</h2> <p>$status_guia</p>";
                    echo "<h2>Especialidades:</h2> <p>" . implode(", ", $especialidades) . "</p>";
                    echo "<h2>Validade:</h2> <p>$validade</p>";
                    echo "<h2>Mês:</h2> <p>$mes</p>";
                    echo "<h2>Entrada:</h2> <p>$entrada</p>";
                    echo "<h2>Saida:</h2> <p>$saida</p>";
                }
            } catch (Exception $e) {
                // Reverter transação em caso de erro
                $conn->rollback();
                
                if ($isAjax) {
                    header('Content-Type: application/json');
                    echo json_encode(["success" => false, "message" => $e->getMessage()]);
                    exit;
                } else {
                    echo "<p>Erro: " . $e->getMessage() . "</p>";
                }
            }
        }
    }

    $conn->close();
}
?>