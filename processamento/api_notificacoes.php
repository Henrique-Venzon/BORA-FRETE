<?php
/**
 * BORAFRETE - API de Notificações
 */
require_once '../config/config.php';
verificarLogin();

header('Content-Type: application/json');

$acao = $_GET['acao'] ?? 'listar';

try {
    switch ($acao) {
        case 'listar':
            // Buscar notificações do usuário
            $stmt = $pdo->prepare("
                SELECT * FROM notificacoes
                WHERE usuario_id = ?
                ORDER BY created_at DESC
                LIMIT 50
            ");
            $stmt->execute([$_SESSION['usuario_id']]);
            $notificacoes = $stmt->fetchAll();

            // Contar não lidas
            $stmtCount = $pdo->prepare("
                SELECT COUNT(*) as total
                FROM notificacoes
                WHERE usuario_id = ? AND lida = FALSE
            ");
            $stmtCount->execute([$_SESSION['usuario_id']]);
            $count = $stmtCount->fetch();

            echo json_encode([
                'sucesso' => true,
                'notificacoes' => $notificacoes,
                'nao_lidas' => $count['total']
            ]);
            break;

        case 'marcar_lida':
            $notificacao_id = (int)($_POST['id'] ?? 0);
            if ($notificacao_id > 0) {
                $stmt = $pdo->prepare("
                    UPDATE notificacoes
                    SET lida = TRUE
                    WHERE id = ? AND usuario_id = ?
                ");
                $stmt->execute([$notificacao_id, $_SESSION['usuario_id']]);
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'erro' => 'ID inválido']);
            }
            break;

        case 'marcar_todas_lidas':
            $stmt = $pdo->prepare("
                UPDATE notificacoes
                SET lida = TRUE
                WHERE usuario_id = ? AND lida = FALSE
            ");
            $stmt->execute([$_SESSION['usuario_id']]);
            echo json_encode(['sucesso' => true]);
            break;

        case 'deletar':
            $notificacao_id = (int)($_POST['id'] ?? 0);
            if ($notificacao_id > 0) {
                $stmt = $pdo->prepare("
                    DELETE FROM notificacoes
                    WHERE id = ? AND usuario_id = ?
                ");
                $stmt->execute([$notificacao_id, $_SESSION['usuario_id']]);
                echo json_encode(['sucesso' => true]);
            } else {
                echo json_encode(['sucesso' => false, 'erro' => 'ID inválido']);
            }
            break;

        default:
            echo json_encode(['sucesso' => false, 'erro' => 'Ação inválida']);
    }

} catch (PDOException $e) {
    error_log("Erro na API de notificações: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao processar requisição']);
}
