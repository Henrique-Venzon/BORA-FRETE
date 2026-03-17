<?php
/**
 * BORAFRETE - Página de Login
 */
require_once 'config/config.php';

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

// Verifica se há mensagem flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body class="login-page">

    <div class="login-container">

        <!-- LADO ESQUERDO - Informações -->
        <div class="login-left">
            <div class="login-brand">
                <div class="brand-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>
                    </svg>
                </div>
                <h1 class="brand-name">borafrete</h1>
            </div>

            <div class="login-icons">
                <div class="icon-item">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 18.5C18 19.328 17.328 20 16.5 20H5.5C4.672 20 4 19.328 4 18.5V9.5C4 8.672 4.672 8 5.5 8H7V6C7 3.791 8.791 2 11 2H13C15.209 2 17 3.791 17 6V8H18.5C19.328 8 20 8.672 20 9.5V18.5C20 19.328 19.328 20 18.5 20H16.5ZM9 6C9 4.895 9.895 4 11 4H13C14.105 4 15 4.895 15 6V8H9V6Z" fill="white" opacity="0.9"/>
                    </svg>
                </div>
                <div class="icon-item">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M21 16V8C21 6.895 20.105 6 19 6H5C3.895 6 3 6.895 3 8V16C3 17.105 3.895 18 5 18H19C20.105 18 21 17.105 21 16ZM5 8H19V16H5V8ZM10 10L17 14L10 18V10Z" fill="white" opacity="0.9"/>
                    </svg>
                </div>
                <div class="icon-item">
                    <svg width="48" height="48" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 8H17V4H3C1.9 4 1 4.9 1 6V17H3C3 18.66 4.34 20 6 20C7.66 20 9 18.66 9 17H15C15 18.66 16.34 20 18 20C19.66 20 21 18.66 21 17H23V12L20 8ZM6 18.5C5.17 18.5 4.5 17.83 4.5 17C4.5 16.17 5.17 15.5 6 15.5C6.83 15.5 7.5 16.17 7.5 17C7.5 17.83 6.83 18.5 6 18.5ZM19 9.5L21.46 12H17V9.5H19ZM18 18.5C17.17 18.5 16.5 17.83 16.5 17C16.5 16.17 17.17 15.5 18 15.5C18.83 15.5 19.5 16.17 19.5 17C19.5 17.83 18.83 18.5 18 18.5Z" fill="white" opacity="0.9"/>
                    </svg>
                </div>
            </div>

            <div class="login-description">
                <h2>Plataforma líder em logística de fretes e encomendas</h2>
                <p>Otimize seus envios hoje.</p>
            </div>

            <div class="login-features">
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 18.5C18 19.328 17.328 20 16.5 20H5.5C4.672 20 4 19.328 4 18.5V9.5C4 8.672 4.672 8 5.5 8H16.5C17.328 8 18 8.672 18 9.5V18.5Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2L2 7V12C2 16.55 5.84 20.74 12 22C18.16 20.74 22 16.55 22 12V7L12 2Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 18H4V6H20V18Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C6.48 2 2 6.48 2 12C2 17.52 6.48 22 12 22C17.52 22 22 17.52 22 12C22 6.48 17.52 2 12 2ZM12 20C7.59 20 4 16.41 4 12C4 7.59 7.59 4 12 4C16.41 4 20 7.59 20 12C20 16.41 16.41 20 12 20Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- LADO DIREITO - Formulário -->
        <div class="login-right">
            <div class="login-form-container">

                <div class="login-logo">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#4A90E2"/>
                    </svg>
                    <span class="logo-text">borafrete</span>
                </div>

                <h2 class="form-title">Acesse Sua Conta</h2>
                <p class="form-subtitle">Conecte-se e comece a enviar</p>

                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?php echo $flashMessage['tipo']; ?>">
                        <?php echo $flashMessage['mensagem']; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>processamento/auth.php" method="POST" class="login-form">

                    <div class="form-group">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" fill="#999"/>
                            </svg>
                        </div>
                        <input
                            type="email"
                            name="email"
                            placeholder="E-mail"
                            required
                            autocomplete="email"
                        >
                    </div>

                    <div class="form-group">
                        <div class="input-icon">
                            <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM12 17C10.9 17 10 16.1 10 15C10 13.9 10.9 13 12 13C13.1 13 14 13.9 14 15C14 16.1 13.1 17 12 17ZM15.1 8H8.9V6C8.9 4.29 10.29 2.9 12 2.9C13.71 2.9 15.1 4.29 15.1 6V8Z" fill="#999"/>
                            </svg>
                        </div>
                        <input
                            type="password"
                            name="senha"
                            placeholder="Senha"
                            required
                            autocomplete="current-password"
                        >
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Entrar</button>

                </form>

                <div class="form-footer">
                    <a href="<?php echo BASE_URL; ?>views/recuperar-senha.php" class="link-secondary">Esqueceu a senha?</a>
                    <a href="<?php echo BASE_URL; ?>views/cadastro.php" class="link-primary">Criar uma conta</a>
                </div>

                <div class="social-login">
                    <div class="divider">
                        <span>ou</span>
                    </div>
                    <button class="btn btn-social">
                        <svg width="24" height="24" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M24 12.073C24 5.405 18.627 0 12 0S0 5.405 0 12.073C0 18.1 4.388 23.094 10.125 24V15.563H7.078V12.073H10.125V9.413C10.125 6.387 11.917 4.716 14.658 4.716C15.97 4.716 17.344 4.952 17.344 4.952V7.92H15.83C14.34 7.92 13.875 8.853 13.875 9.808V12.073H17.203L16.67 15.563H13.875V24C19.612 23.094 24 18.1 24 12.073Z" fill="#1877F2"/>
                        </svg>
                        Entrar com Facebook
                    </button>
                </div>

            </div>
        </div>

    </div>

    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
</body>
</html>
