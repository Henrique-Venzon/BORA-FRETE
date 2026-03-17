<?php
/**
 * BORAFRETE - Enviar Mensagem
 */
require_once '../config/config.php';
verificarLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido']);
    exit;
}

$destinatario_id = (int)($_POST['destinatario_id'] ?? 0);
$mensagem = trim($_POST['mensagem'] ?? '');

if ($destinatario_id <= 0 || empty($mensagem)) {
    echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
    exit;
}

try {
    // Inserir mensagem
    $stmt = $pdo->prepare("
        INSERT INTO mensagens (remetente_id, destinatario_id, mensagem)
        VALUES (?, ?, ?)
    ");

    $stmt->execute([$_SESSION['usuario_id'], $destinatario_id, $mensagem]);

    // Criar notificação para destinatário
    require_once 'criar_notificacao.php';

    $stmtUser = $pdo->prepare("SELECT nome_razao_social FROM usuarios WHERE id = ?");
    $stmtUser->execute([$_SESSION['usuario_id']]);
    $remetente = $stmtUser->fetchColumn();

    criarNotificacao(
        $destinatario_id,
        'Nova Mensagem',
        "{$remetente} enviou: " . substr($mensagem, 0, 50) . (strlen($mensagem) > 50 ? '...' : ''),
        'mensagem'
    );

    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    error_log("Erro ao enviar mensagem: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao enviar mensagem']);
}
