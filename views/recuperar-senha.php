<?php
/**
 * BORAFRETE - Recuperação de Senha
 */
require_once '../config/config.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Recuperar Senha - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body class="login-page">

    <div class="login-container">

        <!-- LADO ESQUERDO -->
        <div class="login-left">
            <div class="login-brand">
                <div class="brand-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>
                    </svg>
                </div>
                <h1 class="brand-name">borafrete</h1>
            </div>

            <div class="login-description">
                <h2>Esqueceu sua senha?</h2>
                <p>Não se preocupe! Enviaremos um link de recuperação para seu email.</p>
            </div>

            <div class="login-features">
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M20 4H4C2.9 4 2 4.9 2 6V18C2 19.1 2.9 20 4 20H20C21.1 20 22 19.1 22 18V6C22 4.9 21.1 4 20 4ZM20 8L12 13L4 8V6L12 11L20 6V8Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8ZM12 17C10.9 17 10 16.1 10 15C10 13.9 10.9 13 12 13C13.1 13 14 13.9 14 15C14 16.1 13.1 17 12 17Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- LADO DIREITO -->
        <div class="login-right">
            <div class="login-form-container">

                <div class="login-logo">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#4A90E2"/>
                    </svg>
                    <span class="logo-text">borafrete</span>
                </div>

                <h2 class="form-title">Recuperar Senha</h2>
                <p class="form-subtitle">Digite seu email cadastrado</p>

                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?php echo $flashMessage['tipo']; ?>">
                        <?php echo $flashMessage['mensagem']; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>processamento/enviar_recuperacao.php" method="POST" class="login-form">

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

                    <button type="submit" class="btn btn-primary btn-block">Enviar Link de Recuperação</button>

                </form>

                <div class="form-footer">
                    <a href="<?php echo BASE_URL; ?>index.php" class="link-secondary">Voltar ao Login</a>
                    <a href="<?php echo BASE_URL; ?>views/cadastro.php" class="link-primary">Criar uma conta</a>
                </div>

            </div>
        </div>

    </div>

    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
</body>
</html>
