<?php
session_start();
$is_admin = isset($_SESSION['is_admin']) ? $_SESSION['is_admin'] : false;
if (isset($_SESSION['login'])) {
    header("Location: ../index.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="pt-br">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Lavorato System</title>
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
        
        .login-container {
            max-width: 900px;
            margin: auto;
            box-shadow: 0 10px 30px rgba(0, 0, 0, 0.1);
            border-radius: 12px;
            overflow: hidden;
        }
        
        .login-sidebar {
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
        
        .login-sidebar h2 {
            font-weight: 700;
            font-size: 2rem;
            margin-bottom: 20px;
        }
        
        .login-sidebar p {
            font-size: 1.1rem;
            margin-bottom: 30px;
            opacity: 0.9;
        }
        
        .login-form {
            padding: 40px;
            background-color: white;
        }
        
        .login-form h3 {
            font-weight: 600;
            margin-bottom: 30px;
            color: var(--text-color);
        }
        
        .login-form .form-control {
            height: 50px;
            font-size: 1rem;
            border-radius: 8px;
            padding-left: 45px;
        }
        
        .login-form .input-group-text {
            background-color: transparent;
            border-right: none;
            padding-right: 0;
        }
        
        .login-form .form-control {
            border-left: none;
        }
        
        .login-form .input-group:focus-within .input-group-text {
            border-color: var(--main-color-btn);
        }
        
        .login-form .btn-login {
            height: 50px;
            font-size: 1rem;
            font-weight: 600;
        }
        
        .login-form .forgot-password {
            color: var(--main-color-btn);
            text-decoration: none;
            font-size: 0.9rem;
            transition: var(--transition);
        }
        
        .login-form .forgot-password:hover {
            color: #0099cc;
            text-decoration: underline;
        }
        
        .company-logo {
            max-width: 180px;
            margin-bottom: 30px;
        }
        
        .login-features {
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
            .login-container {
                margin: 20px;
            }
            
            .login-sidebar {
                padding: 30px;
            }
            
            .login-form {
                padding: 30px;
            }
        }
    </style>
</head>
<body>
    <div class="container mt-5 mb-5">
        <div class="login-container row">
            <!-- Left sidebar with branding -->
            <div class="col-lg-6 login-sidebar">
                <div>
                    <img src="../assets/Logo-Lavorato-alfa.png" alt="Lavorato Logo" class="company-logo">
                    <h2>Bem-vindo ao Sistema de Gestão Lavorato</h2>
                    <p>Gerencie guias médicas, acompanhe processos e gere relatórios com facilidade.</p>
                    
                    <div class="login-features">
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-check"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Gestão Simplificada</h5>
                                <p class="mb-0">Cadastre e acompanhe guias médicas em tempo real</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-chart-line"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Relatórios Detalhados</h5>
                                <p class="mb-0">Dados e estatísticas para tomada de decisões</p>
                            </div>
                        </div>
                        
                        <div class="feature-item">
                            <div class="feature-icon">
                                <i class="fas fa-users"></i>
                            </div>
                            <div>
                                <h5 class="mb-1">Multi-usuário</h5>
                                <p class="mb-0">Controle de acesso por usuário e permissões</p>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Right side with login form -->
            <div class="col-lg-6 login-form">
                <div>
                    <h3>Acesso ao Sistema</h3>
                    <?php
                    if(isset($_GET['error']) && $_GET['error'] == 1) {
                        echo '<div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="fas fa-exclamation-circle me-2"></i> Nome de usuário ou senha incorretos.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>';
                    }
                    ?>
                    <form action="login_usuario.php" method="POST" class="needs-validation" novalidate>
                        <div class="mb-4">
                            <label for="login" class="form-label">Usuário</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-user"></i>
                                </span>
                                <input type="text" name="login" id="login" class="form-control" placeholder="Seu nome de usuário" required>
                            </div>
                            <div class="invalid-feedback">
                                Por favor, informe seu nome de usuário.
                            </div>
                        </div>

                        <div class="mb-3">
                            <label for="senha" class="form-label">Senha</label>
                            <div class="input-group">
                                <span class="input-group-text">
                                    <i class="fas fa-lock"></i>
                                </span>
                                <input type="password" name="senha" id="senha" class="form-control" placeholder="Sua senha" required>
                                <button type="button" class="btn btn-outline-secondary" id="togglePassword">
                                    <i class="fas fa-eye" id="toggleIcon"></i>
                                </button>
                            </div>
                            <div class="invalid-feedback">
                                Por favor, informe sua senha.
                            </div>
                        </div>

                        <div class="d-flex justify-content-between align-items-center mb-4">
                            <div class="form-check">
                                <input class="form-check-input" type="checkbox" id="rememberMe">
                                <label class="form-check-label" for="rememberMe">
                                    Lembrar-me
                                </label>
                            </div>
                            <a href="esqueci_senha.php" class="forgot-password">Esqueceu a senha?</a>
                        </div>

                        <button type="submit" id="entrar" name="entrar" class="btn btn-primary btn-login w-100">
                            <i class="fas fa-sign-in-alt me-2"></i> Entrar
                        </button>
                    </form>
                    
                    <div class="text-center mt-4">
                        <p class="text-muted">Não tem uma conta? Entre em contato com o administrador</p>
                    </div>

                    <div class="alert alert-info mt-4" role="alert">
                        <div class="d-flex">
                            <div class="me-3">
                                <i class="fas fa-info-circle fa-2x"></i>
                            </div>
                            <div>
                                <h5 class="alert-heading">Precisa de ajuda?</h5>
                                <p class="mb-0">Se você estiver tendo problemas para acessar sua conta, entre em contato com o suporte técnico.</p>
                            </div>
                        </div>
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