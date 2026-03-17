<?php
/**
 * BORAFRETE - Atualizar Perfil do Usuário
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/perfil.php');
    exit;
}

$nome_razao_social = sanitizar($_POST['nome_razao_social'] ?? '');
$email = sanitizar($_POST['email'] ?? '');
$telefone = sanitizar($_POST['telefone'] ?? '');

if (empty($nome_razao_social) || empty($email) || empty($telefone)) {
    setFlashMessage('error', 'Preencha todos os campos obrigatórios');
    header('Location: ' . BASE_URL . 'views/perfil.php');
    exit;
}

try {
    // Verificar se email já existe (exceto do próprio usuário)
    $stmt = $pdo->prepare("SELECT id FROM usuarios WHERE email = ? AND id != ?");
    $stmt->execute([$email, $_SESSION['usuario_id']]);

    if ($stmt->fetch()) {
        setFlashMessage('error', 'Este e-mail já está em uso por outro usuário');
        header('Location: ' . BASE_URL . 'views/perfil.php');
        exit;
    }

    // Atualizar dados
    $stmt = $pdo->prepare("
        UPDATE usuarios SET
            nome_razao_social = ?,
            email = ?,
            telefone = ?
        WHERE id = ?
    ");

    $stmt->execute([
        $nome_razao_social,
        $email,
        $telefone,
        $_SESSION['usuario_id']
    ]);

    setFlashMessage('success', 'Perfil atualizado com sucesso!');
    header('Location: ' . BASE_URL . 'views/perfil.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao atualizar perfil: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao atualizar perfil. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/perfil.php');
    exit;
}
