<?php
if (isset($_GET['option'])) {
    $option = $_GET['option'];
    switch ($option) {
        case 'fusex_individual':
            echo '
            <form id="individualForm" method="POST" action="process_individual.php">
                <div class="mb-3 position-relative">
                    <label for="nome" class="form-label">Paciente:</label>
                    <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do paciente..." autocomplete="off">
                    <div id="nome-suggestions" class="dropdown-menu"></div>
                </div>                  
                <div class="mb-3">
                    <label for="especialidade" class="form-label">Especialidade:</label>
                    <input type="text" class="form-control" id="especialidade" name="especialidade">
                </div>
                <div class="mb-3">
                    <label for="mes" class="form-label">Mês:</label>
                    <input type="text" class="form-control" id="mes" name="mes">
                </div>
                <button type="submit" class="btn btn-primary">Enviar</button>
            </form>';
            break;
        case 'cbmdf_individual':
            // Retorna o formulário HTML diretamente
            echo '
                <form id="individualForm" method="POST" action="process_individual_cbmdf.php">
                    <div class="mb-3 position-relative">
                        <label for="nome" class="form-label">Paciente:</label>
                        <input type="text" class="form-control" id="nome" name="nome" placeholder="Digite o nome do paciente..." autocomplete="off">
                        <div id="nome-suggestions" class="dropdown-menu"></div>
                    </div>                  
                    <div class="mb-3">
                        <label for="especialidade" class="form-label">Especialidade:</label>
                        <input type="text" class="form-control" id="especialidade" name="especialidade">
                    </div>
                    <div class="mb-3">
                        <label for="mes" class="form-label">Mês:</label>
                        <input type="text" class="form-control" id="mes" name="mes">
                    </div>
                    <button type="submit" class="btn btn-primary">Enviar</button>
                </form>';
            break;

        case 'fusex':
            // Redireciona para emitir_fichas.php
            echo json_encode(['redirect' => '../views/emitir_fichas.php']);
            break; 

        case 'cbmdf':
            // Redireciona para emitir_fichas_cbmdf.php
            echo json_encode(['redirect' => '../views/emitir_fichas_cbmdf.php']);
            break;
            
        case 'particular':
            // Redireciona para emitir_fichas_particulares.php
            echo json_encode(['redirect' => '../views/emitir_fichas_particulares.php']);
            break;
    }
    exit;
}
