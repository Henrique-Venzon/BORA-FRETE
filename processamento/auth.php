<?php
/**
 * BORAFRETE - Autenticação de Usuário
 */
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$email = sanitizar($_POST['email'] ?? '');
$senha = $_POST['senha'] ?? '';

// Validação básica
if (empty($email) || empty($senha)) {
    setFlashMessage('error', 'Por favor, preencha todos os campos');
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

try {
    // Buscar usuário pelo email
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Debug: Log tentativa de login
    if (!$usuario) {
        error_log("Login falhou - Email não encontrado: $email");
        setFlashMessage('error', 'E-mail ou senha incorretos');
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }

    // Verificar senha
    if (!password_verify($senha, $usuario['senha'])) {
        error_log("Login falhou - Senha incorreta para email: $email");
        setFlashMessage('error', 'E-mail ou senha incorretos');
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }

    // Login bem-sucedido
    error_log("Login bem-sucedido - Usuario ID: " . $usuario['id']);

    $_SESSION['usuario_id'] = $usuario['id'];
    $_SESSION['usuario_nome'] = $usuario['nome_razao_social'];
    $_SESSION['usuario_tipo'] = $usuario['tipo_perfil'];
    $_SESSION['usuario_email'] = $usuario['email'];

    // Redirecionar para o dashboard
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro no login (PDO): " . $e->getMessage());
    setFlashMessage('error', 'Erro ao processar login. Tente novamente.');
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}
