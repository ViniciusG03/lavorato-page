<?php
session_start();

if (!isset($_SESSION['login'])) {
  header("Location: login/login.php");
  exit();
}

function hasPermission($roles)
{
  return (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) || in_array($_SESSION['login'], $roles);
}
?>

<!DOCTYPE html>
<html lang="pt-br">

<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title>Lavorato - Sistema de Gestão</title>
  <link rel="shortcut icon" href="assets/Logo-Lavorato-alfa.png" type="image/x-icon" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:ital,wght@0,100;0,200;0,300;0,400;0,500;0,600;0,700;0,800;0,900;1,100;1,200;1,300;1,400;1,500;1,600;1,700;1,800;1,900&display=swap"
    rel="stylesheet" />
  <link rel="stylesheet" href="bootstrap/css/bootstrap.min.css">
  <link rel="stylesheet" href="stylesheet/base.css" />
  <script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
  <script src="scripts/notificacoes.js" defer></script>
  <script src="scripts/autocomplete.js" defer></script>
  <script src="index.js" defer></script>
</head>

<body>
  <!-- Navbar com design melhorado -->
  <nav class="navbar navbar-expand-lg navbar-dark">
    <div class="container-fluid">
      <a class="navbar-brand d-flex align-items-center" href="#">
        <img src="assets/Logo-Lavorato-alfa.png" alt="Lavorato Logo" width="40" class="me-2">
        Lavorato's System
      </a>
      <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav"
        aria-controls="navbarNav" aria-expanded="false" aria-label="Toggle navigation">
        <span class="navbar-toggler-icon"></span>
      </button>
      <div class="collapse navbar-collapse" id="navbarNav">
        <ul class="navbar-nav ms-auto">
          <li class="nav-item dropdown">
            <a class="nav-link dropdown-toggle" href="#" id="navbarDropdownMenuLink" role="button"
              data-bs-toggle="dropdown" aria-expanded="false">
              <i class="fas fa-cog me-1"></i> Opções
            </a>
            <ul class="dropdown-menu dropdown-menu-end" aria-labelledby="navbarDropdownMenuLink">
              <li><a class="dropdown-item" href="controle-page/controle.php">
                <i class="fas fa-sliders-h me-2"></i>Controle</a>
              </li>
              <?php
              if (isset($_SESSION['is_admin']) && $_SESSION['is_admin']) {
                echo '<li><a class="dropdown-item" href="login/cadastro.php">
                  <i class="fas fa-user-plus me-2"></i>Cadastro</a></li>';
              }
              ?>
              <li><a class="dropdown-item" href="login/logout.php">
                <i class="fas fa-sign-out-alt me-2"></i>Logout</a>
              </li>
              <li><hr class="dropdown-divider"></li>
              <li><a class="dropdown-item" href="views/atas.php">
                <i class="fas fa-file-alt me-2"></i>ATAS</a>
              </li>
              <?php
              if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])) {
                echo '<li><a class="dropdown-item" href="views/relatorios.php">
                  <i class="fas fa-chart-bar me-2"></i>Relatórios</a></li>';
              }
              ?>
              <?php
              if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
                echo '<li><a class="dropdown-item d-flex justify-content-between align-items-center" href="views/relatorios_compartilhados.php">
                  <span><i class="fas fa-share-alt me-2"></i>Relatórios Compartilhados</span>
                  <span id="notificacao-badge" class="badge bg-danger rounded-pill" style="display: none;">0</span>
                </a></li>';
              }
              ?>
              <?php
              if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
                echo '<li><a class="dropdown-item" href="views/pesquisar_guias.php">
                  <i class="fas fa-search me-2"></i>Pesquisar Guia em Relatórios</a></li>';
              }
              ?>
              <?php
              if ($_SESSION['login'] == 'admin' || in_array($_SESSION['login'], ['gustavoramos', 'talita'])) {
                echo '<li><a class="dropdown-item" href="views/admin_relatorios.php">
                  <i class="fas fa-user-shield me-2"></i>Admin Relatórios</a></li>';
              }
              ?>
              <?php
              if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])) {
                echo '<li><a class="dropdown-item" href="views/visualizar_logs.php">
                  <i class="fas fa-history me-2"></i>Histórico de Alterações</a></li>';
              }
              ?>
            </ul>
          </li>
          <li class="nav-item">
            <a class="nav-link" href="#" data-bs-toggle="modal" data-bs-target="#userProfileModal">
              <i class="fas fa-user-circle me-1"></i> <?php echo $_SESSION['login']; ?>
            </a>
          </li>
        </ul>
      </div>
    </div>
  </nav>

  <!-- Conteúdo principal com design aprimorado -->
  <div class="container py-5">
    <div class="text-center mb-4">
      <div class="image-logo">
        <img src="assets/Logo-Lavorato-alfa.png" alt="Lavorato Logo" class="img-fluid" style="max-height: 200px;">
      </div>
      <h1 class="display-5 mt-3 mb-4 text-primary fw-bold">Sistema de Gestão de Guias</h1>
    </div>

    <!-- Dashboard Cards - Visão rápida -->
    <?php if (hasPermission(['gustavoramos', 'raphael', 'admin'])): ?>
    <div class="row mb-5">
      <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
          <div class="card-body">
            <i class="fas fa-file-medical fa-3x text-primary mb-3"></i>
            <h5 class="card-title">Guias Ativas</h5>
            <p class="card-text fs-4 fw-bold">124</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
          <div class="card-body">
            <i class="fas fa-tasks fa-3x text-warning mb-3"></i>
            <h5 class="card-title">Pendentes</h5>
            <p class="card-text fs-4 fw-bold">18</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
          <div class="card-body">
            <i class="fas fa-check-circle fa-3x text-success mb-3"></i>
            <h5 class="card-title">Completadas</h5>
            <p class="card-text fs-4 fw-bold">43</p>
          </div>
        </div>
      </div>
      <div class="col-md-3 mb-3">
        <div class="card text-center h-100">
          <div class="card-body">
            <i class="fas fa-users fa-3x text-info mb-3"></i>
            <h5 class="card-title">Pacientes</h5>
            <p class="card-text fs-4 fw-bold">87</p>
          </div>
        </div>
      </div>
    </div>
    <?php endif; ?>

    <!-- Botões principais com ícones e design melhorado -->
    <div class="row row-cols-1 row-cols-md-3 g-4 justify-content-center">
      <?php if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])): ?>
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-file-medical fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Cadastrar Guia</h5>
            <p class="card-text text-muted">Adicionar uma nova guia ao sistema</p>
            <button id="btn-cadastrar" class="btn btn-primary mt-auto w-100">Cadastrar</button>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])): ?>
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-edit fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Atualizar Guia</h5>
            <p class="card-text text-muted">Modificar informações de uma guia existente</p>
            <button id="btn-atualizar" class="btn btn-primary mt-auto w-100">Atualizar</button>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])): ?>
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-layer-group fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Atualização em Massa</h5>
            <p class="card-text text-muted">Atualizar múltiplas guias de uma vez</p>
            <button id="btn-atualizarEmMassa" class="btn btn-primary mt-auto w-100">Atualizar em Massa</button>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-list fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Listar Guias</h5>
            <p class="card-text text-muted">Visualizar todas as guias cadastradas no sistema</p>
            <button id="btn-listar" class="btn btn-primary mt-auto w-100">Listar</button>
          </div>
        </div>
      </div>
      
      <?php if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin', 'talita'])): ?>
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-chart-line fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Relatórios</h5>
            <p class="card-text text-muted">Gerar relatórios com base nos dados do sistema</p>
            <button id="btn-relatorio" class="btn btn-primary mt-auto w-100">Relatório</button>
          </div>
        </div>
      </div>
      <?php endif; ?>
      
      <?php if (hasPermission(['gustavoramos', 'raphael', 'kaynnanduraes', 'will', 'eviny', 'tulio', 'admin'])): ?>
      <div class="col">
        <div class="card h-100 text-center border-0 shadow-sm">
          <div class="card-body d-flex flex-column">
            <div class="mb-3">
              <i class="fas fa-trash-alt fa-3x text-primary"></i>
            </div>
            <h5 class="card-title">Remover Guia</h5>
            <p class="card-text text-muted">Excluir uma guia existente do sistema</p>
            <button id="btn-remover" class="btn btn-primary mt-auto w-100">Remover</button>
          </div>
        </div>
      </div>
      <?php endif; ?>
    </div>
  </div>

  <!-- Modais com design aprimorado -->
  <!-- Modal de cadastro -->
  <div class="modal fade" id="modalCadastro" tabindex="-1" aria-labelledby="modalCadastroLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalCadastroLabel">Cadastro de Guia</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="formCadastro" action="database/cadastrar.php" method="post" class="needs-validation" novalidate>
          <div class="row">
            <div class="col-12 mb-3">
              <label for="nome" class="form-label">Nome do Paciente <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-user"></i></span>
                <input type="text" id="nome" name="nome" class="form-control" autocomplete="off" required />
              </div>
              <div id="nome-suggestions" class="dropdown-menu"></div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="convenio" class="form-label">Convênio <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                <input type="text" id="convenio" name="convenio" class="form-control" autocomplete="off" required />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="numero_guia" class="form-label">Número da Guia <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" required />
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="status_guia" class="form-label">Status <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                <select id="status_guia" name="status_guia" class="form-select" required>
                  <option value="" selected disabled>Selecione o status</option>
                  <option value="Emitido">Emitido</option>
                  <option value="Subiu">Subiu</option>
                  <option value="Cancelado">Cancelado</option>
                  <option value="Saiu">Saiu</option>
                  <option value="Retornou">Retornou</option>
                  <option value="Não Usou">Não Usou</option>
                  <option value="Assinado">Assinado</option>
                  <option value="Faturado">Faturado</option>
                  <option value="Enviado a BM">Enviado a BM</option>
                  <option value="Devolvido BM">Devolvido BM</option>
                </select>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="numero_section" class="form-label">Número de sessões <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                <input type="number" name="numero_section" id="numero_section" class="form-control" min="1" max="100" autocomplete="off" required>
              </div>
            </div>
            <div class="col-md-12 mb-3">
            <label for="especialidades" class="form-label">Especialidades <span class="text-danger">*</span></label>
             <div class="input-group">
            <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
            <select id="especialidades" name="especialidades[]" class="form-select selectpicker" multiple data-live-search="true" required>
              <option>AVALIACAO NEUROPSICOLOGICA</option>
              <option>SESSAO DE ARTETERAPIA</option>
              <option>SESSAO DE EQUOTERAPIA</option>
              <option>SESSAO DE FISIOTERAPIA</option>
              <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
              <option>SESSAO DE MUSICOTERAPIA</option>
              <option>SESSAO DE NUTRIÇÃO</option>
              <option>SESSAO DE PSICOLOGIA DE CASAL</option>
              <option>SESSAO DE PSICOMOTRICIDADE</option>
              <option>SESSAO DE PSICOPEDAGOGIA</option>
              <option>SESSAO DE PSICOTERAPIA</option>
              <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
              <option>SESSAO DE TERAPIA OCUPACIONAL</option>
              <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
              <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
              <option>SESSAO DE TERAPIA ABA</option>
              <option>TRATAMENTO SERIADO</option>
            </select>
            </div>
             <small class="form-text text-muted">Segure Ctrl (ou Command no Mac) para selecionar múltiplas especialidades.</small>
            </div>
            <div class="col-md-6 mb-3">
              <label for="mes" class="form-label">Mês <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                <select id="mes" name="mes" class="form-select" required>
                  <option value="" selected disabled>Selecione o mês</option>
                  <option>Janeiro</option>
                  <option>Fevereiro</option>
                  <option>Março</option>
                  <option>Abril</option>
                  <option>Maio</option>
                  <option>Junho</option>
                  <option>Julho</option>
                  <option>Agosto</option>
                  <option>Setembro</option>
                  <option>Outubro</option>
                  <option>Novembro</option>
                  <option>Dezembro</option>
                </select>
              </div>
            </div>
            <div class="col-12 mb-3">
              <label for="validade" class="form-label">Validade</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                <input type="date" id="validade" name="validade" class="form-control" autocomplete="off">
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="entrada" class="form-label">Entrada <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                <input type="date" id="entrada" name="entrada" class="form-control" autocomplete="off" required>
              </div>
            </div>
            <div class="col-md-6 mb-3">
              <label for="saida" class="form-label">Saída</label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-calendar-minus"></i></span>
                <input type="date" id="saida" name="saida" class="form-control" autocomplete="off">
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <!-- Modal de atualização -->
  <div class="modal fade" id="modalAtualizacao" tabindex="-1" aria-labelledby="modalAtualizacaoLabel"
  aria-hidden="true">
  <div class="modal-dialog modal-xl modal-dialog-scrollable">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAtualizacaoLabel">Atualização de Guias</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <form id="updateForm" action="database/atualizar.php" method="post" enctype="multipart/form-data" class="needs-validation" novalidate>
          <div class="row g-3">
            <div class="col-md-6">
              <div class="card mb-3">
                <div class="card-header bg-light text-dark">
                  <h6 class="mb-0">Identificação da Guia</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="numero_guia" class="form-label">ID da Guia <span class="text-danger">*</span></label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                      <input type="text" id="numero_guia" name="numero_guia" class="form-control" autocomplete="off" required />
                    </div>
                    <div class="form-check mt-2">
                      <input type="checkbox" id="checkbox_guia" name="checkbox_guia" class="form-check-input" />
                      <label for="checkbox_guia" class="form-check-label">Usar numeração da guia</label>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="correcao_guia" class="form-label">Corrigir número da guia</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-edit"></i></span>
                      <input type="text" id="correcao_guia" name="correcao_guia" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card mb-3">
                <div class="card-header bg-light text-dark">
                  <h6 class="mb-0">Status e Valores</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="status_guia" class="form-label">Status</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                      <select id="status_guia" name="status_guia" class="form-select">
                        <option value="" selected disabled>Selecione o status</option>
                        <option>Emitido</option>
                        <option>Subiu</option>
                        <option>Cancelado</option>
                        <option>Saiu</option>
                        <option>Retornou</option>
                        <option>Não Usou</option>
                        <option>Assinado</option>
                        <option>Faturado</option>
                        <option>Enviado a BM</option>
                        <option>Devolvido BM</option>
                      </select>
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="valor_guia" class="form-label">Valor da Guia</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-dollar-sign"></i></span>
                      <input type="text" id="valor_guia" name="valor_guia" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card mb-3">
                <div class="card-header bg-light text-dark">
                  <h6 class="mb-0">Detalhes de Faturamento</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="qtd_faturada" class="form-label">Quantidade faturada</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-sort-numeric-up"></i></span>
                      <input type="number" id="qtd_faturada" name="qtd_faturada" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="data_remessa" class="form-label">Data da Remessa</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                      <input type="date" id="data_remessa" name="data_remessa" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                  <div class="mb-3">
                    <label for="validade" class="form-label">Validade</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-calendar-times"></i></span>
                      <input type="date" id="validade" name="validade" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-6">
              <div class="card mb-3">
                <div class="card-header bg-light text-dark">
                  <h6 class="mb-0">Informações Adicionais</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="section" class="form-label">Número de Sessões</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-calendar-check"></i></span>
                      <input type="number" id="section" name="section" class="form-control" autocomplete="off" />
                    </div>
                  </div>
                  <div class="mb-3">
                  <label for="especialidades" class="form-label">Especialidades</label>
                  <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
                  <select id="especialidades" name="especialidades[]" class="form-select" multiple>
                    <option>AVALIACAO NEUROPSICOLOGICA</option>
                    <option>SESSAO DE ARTETERAPIA</option>
                    <option>SESSAO DE EQUOTERAPIA</option>
                    <option>SESSAO DE FISIOTERAPIA</option>
                    <option>SESSAO DE FONOAUDIOLOGIA</option>
                    <option>SESSAO DE FONOAUDIOLOGIA EM GRUPO</option>
                    <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                    <option>SESSAO DE MUSICOTERAPIA</option>
                    <option>SESSAO DE NUTRIÇÃO</option>
                    <option>SESSAO DE PSICOLOGIA DE CASAL</option>
                    <option>SESSAO DE PSICOMOTRICIDADE</option>
                    <option>SESSAO DE PSICOPEDAGOGIA</option>
                    <option>SESSAO DE PSICOTERAPIA</option>
                    <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                    <option>SESSAO DE TERAPIA OCUPACIONAL</option>
                    <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                    <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                    <option>SESSAO DE TERAPIA ABA</option>
                    <option>TRATAMENTO SERIADO</option>
                  </select>
                </div>
                <small class="form-text text-muted">Segure Ctrl (ou Command no Mac) para selecionar múltiplas especialidades.</small>
              </div>
                </div>
              </div>
            </div>
            
            <div class="col-md-12">
              <div class="card mb-3">
                <div class="card-header bg-light text-dark">
                  <h6 class="mb-0">Importação e Período</h6>
                </div>
                <div class="card-body">
                  <div class="mb-3">
                    <label for="excelFile" class="form-label">Importar arquivo Excel</label>
                    <div class="input-group">
                      <span class="input-group-text"><i class="fas fa-file-excel"></i></span>
                      <input type="file" id="excelFile" name="excelFile" accept=".xlsx, .xls" class="form-control" />
                    </div>
                    <small class="text-muted">Formatos aceitos: .xlsx, .xls</small>
                  </div>
                  <div class="row">
                    <div class="col-md-6 mb-3">
                      <label for="mes" class="form-label">Mês de atualização</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                        <select id="mes" name="mes" class="form-select">
                          <option value="" selected disabled>Selecione o mês</option>
                          <option>Janeiro</option>
                          <option>Fevereiro</option>
                          <option>Março</option>
                          <option>Abril</option>
                          <option>Maio</option>
                          <option>Junho</option>
                          <option>Julho</option>
                          <option>Agosto</option>
                          <option>Setembro</option>
                          <option>Outubro</option>
                          <option>Novembro</option>
                          <option>Dezembro</option>
                        </select>
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="numero_lote" class="form-label">Número do lote</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-layer-group"></i></span>
                        <input type="text" id="numero_lote" name="numero_lote" class="form-control" autocomplete="off" />
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="entrada" class="form-label">Entrada</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-plus"></i></span>
                        <input type="date" id="entrada" name="entrada" class="form-control" autocomplete="off" />
                      </div>
                    </div>
                    <div class="col-md-6 mb-3">
                      <label for="saida" class="form-label">Saída</label>
                      <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-calendar-minus"></i></span>
                        <input type="date" id="saida" name="saida" class="form-control" autocomplete="off" />
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          </div>
          <div class="d-flex justify-content-end mt-3">
            <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
            <button type="submit" class="btn btn-primary">Salvar</button>
          </div>
        </form>
      </div>
    </div>
  </div>
</div>

  <!-- Modal relatorio -->
  <div class="modal fade" id="modalRelatorio" tabindex="-1" aria-labelledby="modalRelatorioLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRelatorioLabel">Emissão de Relatório</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <form action="database/relatorio.php" method="post" class="needs-validation" novalidate>
            <div class="row g-3">
              <div class="col-md-6">
                <label for="data" class="form-label">Data <span class="text-danger">*</span></label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-calendar"></i></span>
                  <input type="date" id="data" name="data" class="form-control" required>
                </div>
              </div>
              <div class="col-md-6">
                <label for="status" class="form-label">Status</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
                  <select id="status" name="status" class="form-select">
                    <option value="" selected>Todos os status</option>
                    <option>Emitido</option>
                    <option>Subiu</option>
                    <option>Cancelado</option>
                    <option>Saiu</option>
                    <option>Retornou</option>
                    <option>Não Usou</option>
                    <option>Assinado</option>
                    <option>Faturado</option>
                    <option>Enviado a BM</option>
                    <option>Devolvido BM</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label for="hora" class="form-label">Hora</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-clock"></i></span>
                  <input type="time" id="hora" name="hora" class="form-control" placeholder="HH:MM">
                </div>
              </div>
              <div class="col-md-6">
                <label for="especialidade" class="form-label">Especialidade</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-stethoscope"></i></span>
                  <select id="especialidade" name="especialidade" class="form-select">
                    <option value="todas" selected>Todas as Especialidades</option>
                    <option>AVALIACAO NEUROPSICOLOGICA</option>
                    <option>SESSAO DE ARTETERAPIA</option>
                    <option>SESSAO DE EQUOTERAPIA</option>
                    <option>SESSAO DE FISIOTERAPIA</option>
                    <option>SESSAO DE FONOAUDIOLOGIA</option>
                    <option>SESSAO DE FONOAUDIOLOGIA EM GRUPO</option>
                    <option>SESSAO DE FONOAUDIOLOGIA FORMAL DE CABINE</option>
                    <option>SESSAO DE MUSICOTERAPIA</option>
                    <option>SESSAO DE NUTRIÇÃO</option>
                    <option>SESSAO DE PSICOLOGIA DE CASAL</option>
                    <option>SESSAO DE PSICOMOTRICIDADE</option>
                    <option>SESSAO DE PSICOPEDAGOGIA</option>
                    <option>SESSAO DE PSICOTERAPIA</option>
                    <option>SESSAO DE TERAPIA COMPORTAMENTAL APLICADA</option>
                    <option>SESSAO DE TERAPIA OCUPACIONAL</option>
                    <option>SESSAO DE TERAPIA OCUPACIONAL EM GRUPO</option>
                    <option>TERAPIA INTENSIVA NO MODELO PEDIASUIT</option>
                    <option>TRATAMENTO SERIADO</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label for="convenio" class="form-label">Convênio</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-id-card"></i></span>
                  <select id="convenio" name="convenio" class="form-select">
                    <option value="todos" selected>TODOS OS CONVÊNIOS</option>
                    <option value="CASSI">CASSI</option>
                    <option value="CAIXA">CAIXA</option>
                    <option value="TRF">TRF</option>
                    <option value="SERPRO">SERPRO</option>
                    <option value="TST">TST</option>
                    <option value="POSTAL SAUDE">POSTAL SAUDE</option>
                    <option value="TJDFT">TJDFT</option>
                    <option value="PARTICULAR">PARTICULAR</option>
                    <option value="FUSEX">FUSEX</option>
                    <option value="FUSEX(PNE)">FUSEX(PNE)</option>
                    <option value="CBMDF">CBMDF</option>
                    <option value="SIS SENADO">SIS SENADO</option>
                    <option value="PLAN ASSISTE">PLAN ASSISTE</option>
                    <option value="FASCAL">FASCAL</option>
                    <option value="BACEN">BACEN</option>
                    <option value="STM">STM</option>
                    <option value="E-VIDA">E-VIDA</option>
                    <option value="BRB">BRB</option>
                    <option value="UNIVIDA">UNIVIDA</option>
                    <option value="PORTO">PORTO</option>
                    <option value="CASEMBRAPA">CASEMBRAPA</option>
                    <option value="TRT">TRT</option>
                    <option value="GEAP">GEAP</option>
                    <option value="OMINT">OMINT</option>
                    <option value="STF">STF</option>
                    <option value="BRADESCO">BRADESCO</option>
                    <option value="CAMARA DOS DEPUTADOS">CAMARA DOS DEPUTADOS</option>
                  </select>
                </div>
              </div>
              <div class="col-md-6">
                <label for="mes" class="form-label">Mês</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                  <select id="mes" name="mes" class="form-select">
                    <option value="todos" selected>TODOS OS MESES</option>
                    <option value="Janeiro">Janeiro</option>
                    <option value="Fevereiro">Fevereiro</option>
                    <option value="Março">Março</option>
                    <option value="Abril">Abril</option>
                    <option value="Maio">Maio</option>
                    <option value="Junho">Junho</option>
                    <option value="Julho">Julho</option>
                    <option value="Agosto">Agosto</option>
                    <option value="Setembro">Setembro</option>
                    <option value="Outubro">Outubro</option>
                    <option value="Novembro">Novembro</option>
                    <option value="Dezembro">Dezembro</option>
                  </select>
                </div>
              </div>
              <div class="col-md-12">
                <label for="excluir_convenio" class="form-label">Convênios para Excluir</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-filter"></i></span>
                  <select id="excluir_convenio" name="excluir_convenio[]" class="form-select" multiple size="5">
                    <option value="CASSI">CASSI</option>
                    <option value="CAIXA">CAIXA</option>
                    <option value="TRF">TRF</option>
                    <option value="SERPRO">SERPRO</option>
                    <option value="TST">TST</option>
                    <option value="POSTAL SAUDE">POSTAL SAUDE</option>
                    <option value="TJDFT">TJDFT</option>
                    <option value="PARTICULAR">PARTICULAR</option>
                    <option value="FUSEX">FUSEX</option>
                    <option value="CBMDF">CBMDF</option>
                    <option value="SIS SENADO">SIS SENADO</option>
                    <option value="PLAN ASSISTE">PLAN ASSISTE</option>
                    <option value="FASCAL">FASCAL</option>
                    <option value="BACEN">BACEN</option>
                    <option value="STM">STM</option>
                    <option value="E-VIDA">E-VIDA</option>
                    <option value="BRB">BRB</option>
                    <option value="UNIVIDA">UNIVIDA</option>
                    <option value="PORTO">PORTO</option>
                    <option value="CASEMBRAPA">CASEMBRAPA</option>
                    <option value="TRT">TRT</option>
                    <option value="GEAP">GEAP</option>
                    <option value="OMINT">OMINT</option>
                    <option value="STF">STF</option>
                    <option value="BRADESCO">BRADESCO</option>
                    <option value="CAMARA DOS DEPUTADOS">CAMARA DOS DEPUTADOS</option>
                  </select>
                </div>
                <small class="text-muted">Segure Ctrl para selecionar múltiplos convênios</small>
              </div>
              <div class="col-md-12">
                <label for="observacao" class="form-label">Observação</label>
                <div class="input-group">
                  <span class="input-group-text"><i class="fas fa-comment"></i></span>
                  <textarea name="observacao" id="observacao" placeholder="Digite sua observação..." class="form-control" rows="3"></textarea>
                </div>
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-primary">Gerar Relatório</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <div class="modal fade" id="modalAtualizar" tabindex="-1" aria-labelledby="modalAtualizarLabel" aria-hidden="true">
  <div class="modal-dialog modal-lg">
    <div class="modal-content">
      <div class="modal-header">
        <h5 class="modal-title" id="modalAtualizarLabel">Atualizar Guias em Massa</h5>
        <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
      </div>
      <div class="modal-body">
        <!-- Campo de busca -->
        <div class="mb-3">
          <label for="buscaGuia" class="form-label">Buscar guia</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-search"></i></span>
            <input type="text" id="buscaGuia" class="form-control" placeholder="Digite o número da guia...">
          </div>
        </div>

        <!-- Lista de guias filtradas com design melhorado -->
        <div class="card mb-4">
          <div class="card-header bg-light text-dark d-flex justify-content-between align-items-center">
            <h6 class="mb-0">Guias Encontradas</h6>
            <span class="badge bg-primary" id="totalGuias">0</span>
          </div>
          <div class="card-body p-0">
            <div class="table-responsive">
              <table class="table table-hover mb-0">
                <thead>
                  <tr>
                    <th class="text-center" width="50">
                      <input type="checkbox" id="selectAll" class="form-check-input">
                    </th>
                    <th>Número da Guia</th>
                    <th>Paciente</th>
                    <th>Status Atual</th>
                  </tr>
                </thead>
                <tbody id="listaGuias">
                  <!-- Guias serão carregadas aqui via AJAX -->
                  <tr>
                    <td colspan="4" class="text-center py-3">Digite um número para buscar guias</td>
                  </tr>
                </tbody>
              </table>
            </div>
          </div>
        </div>

        <!-- Seleção do novo status -->
        <div class="mb-3">
          <label for="novoStatus" class="form-label">Novo Status</label>
          <div class="input-group">
            <span class="input-group-text"><i class="fas fa-check-circle"></i></span>
            <select id="novoStatus" class="form-select">
              <option value="" selected disabled>Selecione o novo status</option>
              <option value="Emitido">Emitido</option>
              <option value="Subiu">Subiu</option>
              <option value="Cancelado">Cancelado</option>
              <option value="Saiu">Saiu</option>
              <option value="Retornou">Retornou</option>
              <option value="Não Usou">Não Usou</option>
              <option value="Assinado">Assinado</option>
              <option value="Enviado a BM">Enviado a BM</option>
              <option value="Faturado">Faturado</option>
              <option value="Devolvido BM">Devolvido BM</option>
            </select>
          </div>
        </div>
      </div>
      <div class="modal-footer">
        <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
        <button type="button" class="btn btn-primary" id="confirmarAtualizacao">Atualizar Guias</button>
      </div>
    </div>
  </div>
</div>

  <!-- Modal remoção -->
  <div class="modal fade" id="modalRemover" tabindex="-1" aria-labelledby="modalRemoverLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="modalRemoverLabel">Remoção de Guias</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="alert alert-warning">
            <i class="fas fa-exclamation-triangle me-2"></i> Atenção: Esta ação não pode ser desfeita.
          </div>
          <form action="database/remover.php" method="post" class="needs-validation" novalidate>
            <div class="mb-3">
              <label for="id_guia" class="form-label">ID da Guia <span class="text-danger">*</span></label>
              <div class="input-group">
                <span class="input-group-text"><i class="fas fa-hashtag"></i></span>
                <input type="text" id="id_guia" name="id_guia" class="form-control" autocomplete="off" required />
              </div>
              <div class="invalid-feedback">
                Por favor, informe o ID da guia que deseja remover.
              </div>
            </div>
            <div class="d-flex justify-content-end mt-3">
              <button type="button" class="btn btn-secondary me-2" data-bs-dismiss="modal">Cancelar</button>
              <button type="submit" class="btn btn-danger">Remover</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </div>

  <!-- Modal de perfil do usuário -->
  <div class="modal fade" id="userProfileModal" tabindex="-1" aria-labelledby="userProfileModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered">
      <div class="modal-content">
        <div class="modal-header">
          <h5 class="modal-title" id="userProfileModalLabel">Perfil do Usuário</h5>
          <button type="button" class="btn-close btn-close-white" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
          <div class="text-center mb-4">
            <i class="fas fa-user-circle fa-5x text-primary"></i>
            <h4 class="mt-2"><?php echo $_SESSION['login']; ?></h4>
            <p class="text-muted">
              <?php echo isset($_SESSION['is_admin']) && $_SESSION['is_admin'] ? 'Administrador' : 'Usuário'; ?>
            </p>
          </div>
          
          <div class="list-group">
            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <div>
                <i class="fas fa-key me-2"></i> Alterar Senha
              </div>
              <i class="fas fa-chevron-right"></i>
            </a>
            <a href="#" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center">
              <div>
                <i class="fas fa-cog me-2"></i> Preferências
              </div>
              <i class="fas fa-chevron-right"></i>
            </a>
            <a href="login/logout.php" class="list-group-item list-group-item-action d-flex justify-content-between align-items-center text-danger">
              <div>
                <i class="fas fa-sign-out-alt me-2"></i> Sair
              </div>
              <i class="fas fa-chevron-right"></i>
            </a>
          </div>
        </div>
      </div>
    </div>
  </div>

  <!-- Scripts -->
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.1.3/dist/js/bootstrap.bundle.min.js"></script>
  <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
  <script>
    // Script para verificar formulários
    (function() {
      'use strict';
      
      // Fetch all forms we want to apply custom validation
      var forms = document.querySelectorAll('.needs-validation');
      
      // Loop over them and prevent submission
      Array.prototype.slice.call(forms).forEach(function(form) {
        form.addEventListener('submit', function(event) {
          if (!form.checkValidity()) {
            event.preventDefault();
            event.stopPropagation();
          }
          
          form.classList.add('was-validated');
        }, false);
      });
    })();

    // Script para o seletor "Selecionar todos"
    document.getElementById('selectAll')?.addEventListener('change', function() {
      const isChecked = this.checked;
      document.querySelectorAll('#listaGuias .checkbox-guia').forEach(checkbox => {
        checkbox.checked = isChecked;
        if (isChecked) {
          guiasSelecionadas.add(checkbox.value);
        } else {
          guiasSelecionadas.delete(checkbox.value);
        }
      });
    });

    // Script para verificação de tempo ocioso
    var idleTime = 0;
    var idleInterval = setInterval(timerIncrement, 60000); // 1 minuto

    function timerIncrement() {
      idleTime++;
      if (idleTime >= 15) { // 15 minutos
        window.location.href = "login/logout.php";
      }
    }

    document.addEventListener("mousemove", function() {
      idleTime = 0;
    });

    document.addEventListener("keypress", function() {
      idleTime = 0;
    });

    // Script para botão de listar
    const btnListar = document.getElementById("btn-listar");
    btnListar.addEventListener("click", () => {
      window.location.href = "database/listar.php";
    });

    $(document).ready(function() {
  $('.selectpicker').selectpicker({
    noneSelectedText: 'Selecione especialidades',
    selectAllText: 'Selecionar Todos',
    deselectAllText: 'Desmarcar Todos',
    countSelectedText: '{0} especialidades selecionadas'
  });
});

function carregarEspecialidades(pacienteId) {
  // Limpar seleções anteriores
  const selectEspecialidades = document.getElementById('especialidades');
  if (selectEspecialidades) {
    // Desmarcar todos os options
    Array.from(selectEspecialidades.options).forEach(option => {
      option.selected = false;
    });
    
    // Buscar especialidades do paciente via AJAX
    fetch(`database/get_especialidades.php?paciente_id=${pacienteId}`)
      .then(response => response.json())
      .then(data => {
        if (data.success && data.especialidades) {
          // Marcar as especialidades existentes
          Array.from(selectEspecialidades.options).forEach(option => {
            if (data.especialidades.includes(option.value)) {
              option.selected = true;
            }
          });
          
          // Atualizar o Bootstrap Select se estiver em uso
          if (typeof $(selectEspecialidades).selectpicker === 'function') {
            $(selectEspecialidades).selectpicker('refresh');
          }
        }
      })
      .catch(error => console.error('Erro ao carregar especialidades:', error));
  }
}

// Modificar o evento de clique dos botões de edição para carregar especialidades
document.querySelectorAll('.edit-button').forEach(button => {
  button.addEventListener('click', function() {
    const id = this.getAttribute('data-id');
    // Adicionar chamada para carregar especialidades depois que o modal for aberto
    setTimeout(() => carregarEspecialidades(id), 500);
  });
});
  </script>
</body>
