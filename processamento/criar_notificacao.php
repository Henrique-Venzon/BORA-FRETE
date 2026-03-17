<?php
/**
 * BORAFRETE - Criar Notificação
 */
require_once '../config/config.php';

/**
 * Criar notificação para usuário
 */
function criarNotificacao($usuario_id, $titulo, $mensagem, $tipo = 'info') {
    global $pdo;

    try {
        $stmt = $pdo->prepare("
            INSERT INTO notificacoes (usuario_id, tipo, titulo, mensagem)
            VALUES (?, ?, ?, ?)
        ");

        $stmt->execute([$usuario_id, $tipo, $titulo, $mensagem]);
        return true;

    } catch (PDOException $e) {
        error_log("Erro ao criar notificação: " . $e->getMessage());
        return false;
    }
}

// Se chamado via POST (API)
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    verificarLogin();

    $titulo = sanitizar($_POST['titulo'] ?? '');
    $mensagem = sanitizar($_POST['mensagem'] ?? '');
    $tipo = sanitizar($_POST['tipo'] ?? 'info');

    if (empty($titulo) || empty($mensagem)) {
        echo json_encode(['sucesso' => false, 'erro' => 'Dados incompletos']);
        exit;
    }

    $sucesso = criarNotificacao($_SESSION['usuario_id'], $titulo, $mensagem, $tipo);

    header('Content-Type: application/json');
    echo json_encode(['sucesso' => $sucesso]);
}
