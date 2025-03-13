<?php
// session_start();
// if (!isset($_SESSION['is_admin']) || !$_SESSION['is_admin']) {
//     header('Location: ../index.php');
//     exit();
// }
?>

<!DOCTYPE html>
<html lang="pt-BR">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro de Usuário - Lavorato System</title>
    <link rel="shortcut icon" href="../assets/Logo-Lavorato-alfa.png" type="image/x-icon">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@300;400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../bootstrap/css/bootstrap.min.css">
    <link rel="stylesheet" href="../stylesheet/base.css">
    <style>
        body {
            min-height: 100vh;
            display: flex;
            flex-direction: column;
            background-image: linear-gradient(to right, #b9d7d9, #ffffff);
        }
        
        .cadastro-container {
            max-width: 900px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .cadastro-sidebar {
            background-color: var(--main-color-btn);
            color: white;
            padding: 40px;
            display: flex;
            flex-direction: column;
            justify-content: center;
            background-image: linear-gradient(135deg, rgba(0, 179, 255, 0.9), rgba(0, 130, 200, 0.9)), 
                              url('../assets/pattern.svg');
            background-size: cover;
            background-position: center;
        }
        
        .cadastro-sidebar h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .cadastro-sidebar p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .cadastro-form {
            padding: 40px;
            background-color: white;
        }
        
        .cadastro-form h3 {
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-color);
        }
        
        .cadastro-form .form-control {
            height: 50px;
            font-size: 1rem;
            border-radius: 8px;
            padding-left: 45px;
        }
        
        .cadastro-form .input-group-text {
            background-color: transparent;
            border-right: none;
            padding-right: 0;
        }
        
        .cadastro-form .form-control {
            border-left: none;
        }
        
        .cadastro-form .input-group:focus-within .input-group-text {
            border-color: var(--main-color-btn);
        }
        
        .cadastro-form .btn-cadastrar {
            height: 50px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .company-logo {
            max-width: 180px;
            margin-bottom: 30px;
        }
        
        .security-features {
            margin-top: 30px;
        }
        
        .feature-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 15px;
        }
        
        .feature-icon {
            margin-right: 15px;
            width: 24px;
            height: 24px;
            background-color: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .footer {
            margin-top: auto;
            background-color: var(--main-color-btn);
            color: white;
            text-align: center;
            padding: 15px 0;
            font-size: 0.9rem;
        }
        
        /* Responsive adjustments */
        @media (max-width: 768px) {
            .cadastro-container {
                margin: 20px;
            }
            
            .cadastro-sidebar {
                padding: 30px;
            }
            
            .cadastro-form {
                padding: 30px;
            }
        }
    </style>
</head>

<body>
    <div class="container mt-5 mb-5">
        <div class="cadastro-container row">
            <!-- Left sidebar with branding -->
            <div class="col-lg-6 cadastro-sidebar">
                <div>
                    <img src="../assets/Logo-Lavorato-alfa.png" alt="Lavorato Logo" class="company-logo">
                    <h2>Cadastro de Usuários</h2>
                    <p>Crie novas credenciais para o Sistema de Gestão Lavorato.</p>
                    
                    <div class="security-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-shield-alt"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Segurança Avançada</h5>
                                <p class="mb-0">Criptografia de dados para proteção das informações</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-user-lock"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Controle de Acesso</h5>
                                <p class="mb-0">Níveis de permissão por usuário conforme necessidade</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-history"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Rastreabilidade</h5>
                                <p class="mb-0">Registro e histórico de atividades realizadas no sistema</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side with cadastro form -->
            <div class="col-lg-6 cadastro-form">
                <div>
                    <h3>Criar Nova Conta</h3>
                    <form method="POST" action="cadastro_usuario.php" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="login" class="form-label">Nome de Usuário</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" class="form-control" name="login" id="login" placeholder="Digite o nome de usuário" autocomplete="off" required>
                            </div>
                            <div class="invalid-feedback">
                                Por favor, informe um nome de usuário válido.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" name="senha" id="senha" placeholder="Digite a senha" autocomplete="off" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Por favor, informe uma senha válida.
                            </div>
                            <div class="form-text mt-2">
                                A senha deve conter pelo menos 8 caracteres, incluindo letras e números.
                            </div>
                        </div>
                        
                        <div class="mb-4">
                            <label for="confirma_senha" class="form-label">Confirme a Senha</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" class="form-control" id="confirma_senha" placeholder="Confirme a senha" autocomplete="off" required>
                            </div>
                            <div class="invalid-feedback">
                                As senhas não coincidem.
                            </div>
                        </div>
                        
                        <button type="submit" class="btn btn-primary btn-cadastrar w-100" id="cadastrar" name="cadastrar">
                            <i class="fas fa-user-plus me-2"></i> Cadastrar Usuário
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <a href="../index.php" class="text-decoration-none">
                            <i class="fas fa-arrow-left me-1"></i> Voltar para página inicial
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <footer class="footer">
        <div class="container">
            <p class="mb-0">© 2025 Lavorato Tech. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script src="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/js/all.min.js"></script>
    <script src="../bootstrap/js/bootstrap.bundle.min.js"></script>
    <script>
        // Script para mostrar/ocultar senha
        const togglePassword = document.getElementById('togglePassword');
        const toggleIcon = document.getElementById('toggleIcon');
        const passwordInput = document.getElementById('senha');
        
        togglePassword.addEventListener('click', function() {
            const type = passwordInput.getAttribute('type') === 'password' ? 'text' : 'password';
            passwordInput.setAttribute('type', type);
            
            // Toggle icon between eye and eye-slash
            toggleIcon.classList.toggle('fa-eye');
            toggleIcon.classList.toggle('fa-eye-slash');
        });
        
        // Script para validação do formulário
        (function () {
            'use strict'
            
            // Fetch all forms we want to apply custom Bootstrap validation styles to
            var forms = document.querySelectorAll('.needs-validation')
            const senha = document.getElementById('senha');
            const confirmaSenha = document.getElementById('confirma_senha');
            
            // Validar que as senhas coincidem
            confirmaSenha.addEventListener('input', function() {
                if (senha.value !== confirmaSenha.value) {
                    confirmaSenha.setCustomValidity('As senhas não coincidem');
                } else {
                    confirmaSenha.setCustomValidity('');
                }
            });
            
            // Loop over them and prevent submission
            Array.prototype.slice.call(forms)
                .forEach(function (form) {
                    form.addEventListener('submit', function (event) {
                        if (!form.checkValidity()) {
                            event.preventDefault()
                            event.stopPropagation()
                        }
                        
                        form.classList.add('was-validated')
                    }, false)
                })
        })()
    </script>
</body>

</html>