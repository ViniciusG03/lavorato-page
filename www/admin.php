<?php 

require_once 'database/database.php';

$database = new Database();
$db = $database->connect();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['inserir'])) {
    echo '<p>Botão clicado com sucesso!</p>';
}

?>

Arthur andrade frança
18- 2Mus, 2psicomot, 2teo
19- 1Psi 2Fono 1Ped 