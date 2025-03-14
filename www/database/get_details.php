<?php
// Este é um arquivo ponte para manter compatibilidade com o código existente
// Simplesmente redireciona para o arquivo get_detalhes_guia.php

// Verifica se o ID está presente
if (!isset($_GET['id']) || empty($_GET['id'])) {
    echo "<div class='alert alert-danger'>ID de guia não fornecido</div>";
    exit;
}

$id = intval($_GET['id']);

// Inclui o arquivo real com a implementação
include_once 'get_detalhes_guia.php';
?>