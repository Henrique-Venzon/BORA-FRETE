<?php
/**
 * BORAFRETE - Processar Envio de Recuperação de Senha
 */
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/recuperar-senha.php');
    exit;
}

$email = sanitizar($_POST['email'] ?? '');

// Validação básica
if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'Por favor, informe um e-mail válido');
    header('Location: ' . BASE_URL . 'views/recuperar-senha.php');
    exit;
}

try {
    // Buscar usuário pelo email
    $stmt = $pdo->prepare("SELECT id, nome_razao_social, email FROM usuarios WHERE email = ?");
    $stmt->execute([$email]);
    $usuario = $stmt->fetch();

    // Sempre mostrar mensagem de sucesso (segurança)
    // Não revelar se o email existe ou não
    setFlashMessage('success', 'Se este e-mail estiver cadastrado, você receberá um link de recuperação em instantes.');

    if ($usuario) {
        // Gerar token único
        $token = bin2hex(random_bytes(32));
        $expiracao = date('Y-m-d H:i:s', strtotime('+1 hour'));

        // Salvar token no banco
        // Primeiro, criar a tabela se não existir
        $pdo->exec("
            CREATE TABLE IF NOT EXISTS password_resets (
                id INT AUTO_INCREMENT PRIMARY KEY,
                usuario_id INT NOT NULL,
                token VARCHAR(64) NOT NULL,
                expiracao DATETIME NOT NULL,
                usado BOOLEAN DEFAULT FALSE,
                created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
                INDEX idx_token (token),
                INDEX idx_expiracao (expiracao),
                FOREIGN KEY (usuario_id) REFERENCES usuarios(id) ON DELETE CASCADE
            ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci
        ");

        // Invalidar tokens anteriores do usuário
        $stmtInvalidar = $pdo->prepare("UPDATE password_resets SET usado = TRUE WHERE usuario_id = ? AND usado = FALSE");
        $stmtInvalidar->execute([$usuario['id']]);

        // Inserir novo token
        $stmtToken = $pdo->prepare("
            INSERT INTO password_resets (usuario_id, token, expiracao)
            VALUES (?, ?, ?)
        ");
        $stmtToken->execute([$usuario['id'], $token, $expiracao]);

        // Enviar email
        try {
            $emailHelper = new EmailHelper();
            $enviado = $emailHelper->enviarRecuperacaoSenha(
                $usuario['email'],
                $usuario['nome_razao_social'],
                $token
            );

            if (!$enviado) {
                error_log("Falha ao enviar email de recuperação para: " . $usuario['email']);
            }

        } catch (Exception $e) {
            error_log("Erro ao enviar email de recuperação: " . $e->getMessage());
        }
    }

    header('Location: ' . BASE_URL . 'views/recuperar-senha.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao processar recuperação: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao processar solicitação. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/recuperar-senha.php');
    exit;
}
