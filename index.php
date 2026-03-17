<?php
require_once 'config/config.php';

// Se já está logado, redireciona para o dashboard
if (isLoggedIn()) {
    redirect('dashboard.php');
}

$pageTitle = 'Login';
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>

    <!-- Bootstrap 5 CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<div class="login-container">
    <div class="login-box">
        <div class="login-logo">
            <h1><?php echo SITE_NAME; ?></h1>
            <p>Sistema de Gestão Logística</p>
        </div>

        <?php if (isset($_GET['erro'])): ?>
            <div class="alert alert-danger">
                <?php
                switch ($_GET['erro']) {
                    case 'credenciais_invalidas':
                        echo 'Email ou senha inválidos.';
                        break;
                    case 'acesso_negado':
                        echo 'Você precisa fazer login para acessar esta página.';
                        break;
                    case 'usuario_inativo':
                        echo 'Sua conta está inativa. Entre em contato com o suporte.';
                        break;
                    default:
                        echo 'Erro ao fazer login. Tente novamente.';
                }
                ?>
            </div>
        <?php endif; ?>

        <?php if (isset($_GET['sucesso'])): ?>
            <div class="alert alert-success">
                <?php
                switch ($_GET['sucesso']) {
                    case 'cadastro':
                        echo 'Cadastro realizado com sucesso! Faça login para continuar.';
                        break;
                    case 'logout':
                        echo 'Logout realizado com sucesso.';
                        break;
                    default:
                        echo 'Operação realizada com sucesso.';
                }
                ?>
            </div>
        <?php endif; ?>

        <form action="processamento/login.php" method="POST">
            <div class="form-group">
                <label for="email" class="form-label">Email</label>
                <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required>
            </div>

            <div class="form-group">
                <label for="senha" class="form-label">Senha</label>
                <input type="password" class="form-control" id="senha" name="senha" placeholder="Digite sua senha" required>
            </div>

            <button type="submit" class="btn btn-primary">
                <i class="fas fa-sign-in-alt"></i> Entrar
            </button>
        </form>

        <div class="login-footer">
            <p>Não tem uma conta? <a href="perfil.php">Cadastre-se aqui</a></p>
        </div>
    </div>
</div>

<!-- Bootstrap 5 JS -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

</body>
</html>
