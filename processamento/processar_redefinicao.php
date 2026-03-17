<?php
/**
 * BORAFRETE - Processar Redefinição de Senha
 */
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'index.php');
    exit;
}

$token = sanitizar($_POST['token'] ?? '');
$nova_senha = $_POST['nova_senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Validação básica
if (empty($token) || empty($nova_senha) || empty($confirmar_senha)) {
    setFlashMessage('error', 'Por favor, preencha todos os campos');
    header('Location: ' . BASE_URL . 'views/redefinir-senha.php?token=' . urlencode($token));
    exit;
}

if ($nova_senha !== $confirmar_senha) {
    setFlashMessage('error', 'As senhas não coincidem');
    header('Location: ' . BASE_URL . 'views/redefinir-senha.php?token=' . urlencode($token));
    exit;
}

if (strlen($nova_senha) < 6) {
    setFlashMessage('error', 'A senha deve ter no mínimo 6 caracteres');
    header('Location: ' . BASE_URL . 'views/redefinir-senha.php?token=' . urlencode($token));
    exit;
}

try {
    // Buscar token válido
    $stmt = $pdo->prepare("
        SELECT pr.*, u.id as usuario_id, u.email
        FROM password_resets pr
        JOIN usuarios u ON pr.usuario_id = u.id
        WHERE pr.token = ? AND pr.usado = FALSE AND pr.expiracao > NOW()
    ");
    $stmt->execute([$token]);
    $reset = $stmt->fetch();

    if (!$reset) {
        setFlashMessage('error', 'Token inválido ou expirado');
        header('Location: ' . BASE_URL . 'views/recuperar-senha.php');
        exit;
    }

    // Atualizar senha do usuário
    $senhaHash = password_hash($nova_senha, PASSWORD_DEFAULT);
    $stmtUpdate = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
    $stmtUpdate->execute([$senhaHash, $reset['usuario_id']]);

    // Marcar token como usado
    $stmtUsado = $pdo->prepare("UPDATE password_resets SET usado = TRUE WHERE id = ?");
    $stmtUsado->execute([$reset['id']]);

    setFlashMessage('success', 'Senha redefinida com sucesso! Faça login com sua nova senha.');
    header('Location: ' . BASE_URL . 'index.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao redefinir senha: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao redefinir senha. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/redefinir-senha.php?token=' . urlencode($token));
    exit;
}
