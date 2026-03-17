<?php
/**
 * BORAFRETE - Redefinir Senha
 */
require_once '../config/config.php';

// Se já estiver logado, redireciona
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$token = $_GET['token'] ?? '';
$tokenValido = false;
$tokenExpirado = false;

if (!empty($token)) {
    try {
        // Verificar se token existe e é válido
        $stmt = $pdo->prepare("
            SELECT pr.*, u.nome_razao_social, u.email
            FROM password_resets pr
            JOIN usuarios u ON pr.usuario_id = u.id
            WHERE pr.token = ? AND pr.usado = FALSE
        ");
        $stmt->execute([$token]);
        $reset = $stmt->fetch();

        if ($reset) {
            // Verificar se não expirou
            if (strtotime($reset['expiracao']) > time()) {
                $tokenValido = true;
            } else {
                $tokenExpirado = true;
            }
        }
    } catch (PDOException $e) {
        error_log("Erro ao verificar token: " . $e->getMessage());
    }
}

$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Redefinir Senha - <?php echo SITE_NAME; ?></title>
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
                <h2>Criar Nova Senha</h2>
                <p>Escolha uma senha forte para proteger sua conta.</p>
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

                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?php echo $flashMessage['tipo']; ?>">
                        <?php echo $flashMessage['mensagem']; ?>
                    </div>
                <?php endif; ?>

                <?php if (empty($token)): ?>
                    <!-- Token não fornecido -->
                    <h2 class="form-title">Link Inválido</h2>
                    <p class="form-subtitle">O link de recuperação não foi fornecido.</p>
                    <a href="<?php echo BASE_URL; ?>views/recuperar-senha.php" class="btn btn-primary btn-block">Solicitar Novo Link</a>

                <?php elseif ($tokenExpirado): ?>
                    <!-- Token expirado -->
                    <h2 class="form-title">Link Expirado</h2>
                    <p class="form-subtitle">Este link de recuperação já expirou.</p>
                    <a href="<?php echo BASE_URL; ?>views/recuperar-senha.php" class="btn btn-primary btn-block">Solicitar Novo Link</a>

                <?php elseif (!$tokenValido): ?>
                    <!-- Token inválido -->
                    <h2 class="form-title">Link Inválido</h2>
                    <p class="form-subtitle">Este link de recuperação é inválido ou já foi utilizado.</p>
                    <a href="<?php echo BASE_URL; ?>views/recuperar-senha.php" class="btn btn-primary btn-block">Solicitar Novo Link</a>

                <?php else: ?>
                    <!-- Formulário de redefinição -->
                    <h2 class="form-title">Redefinir Senha</h2>
                    <p class="form-subtitle">Olá, <?php echo htmlspecialchars(explode(' ', $reset['nome_razao_social'])[0]); ?>!</p>

                    <form action="<?php echo BASE_URL; ?>processamento/processar_redefinicao.php" method="POST" class="login-form" id="formRedefinir">

                        <input type="hidden" name="token" value="<?php echo htmlspecialchars($token); ?>">

                        <div class="form-group">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8Z" fill="#999"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="nova_senha"
                                id="nova_senha"
                                placeholder="Nova Senha"
                                required
                                minlength="6"
                            >
                        </div>

                        <div class="form-group">
                            <div class="input-icon">
                                <svg width="20" height="20" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M18 8H17V6C17 3.24 14.76 1 12 1C9.24 1 7 3.24 7 6V8H6C4.9 8 4 8.9 4 10V20C4 21.1 4.9 22 6 22H18C19.1 22 20 21.1 20 20V10C20 8.9 19.1 8 18 8Z" fill="#999"/>
                                </svg>
                            </div>
                            <input
                                type="password"
                                name="confirmar_senha"
                                id="confirmar_senha"
                                placeholder="Confirmar Nova Senha"
                                required
                                minlength="6"
                            >
                        </div>

                        <button type="submit" class="btn btn-primary btn-block">Redefinir Senha</button>

                    </form>
                <?php endif; ?>

                <div class="form-footer">
                    <a href="<?php echo BASE_URL; ?>index.php" class="link-secondary">Voltar ao Login</a>
                </div>

            </div>
        </div>

    </div>

    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
    <script>
        // Validar senhas
        document.getElementById('formRedefinir')?.addEventListener('submit', function(e) {
            const senha = document.getElementById('nova_senha').value;
            const confirmar = document.getElementById('confirmar_senha').value;

            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }

            if (senha.length < 6) {
                e.preventDefault();
                alert('A senha deve ter no mínimo 6 caracteres!');
                return false;
            }
        });
    </script>
</body>
</html>
