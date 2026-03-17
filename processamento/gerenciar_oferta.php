<?php
/**
 * BORAFRETE - Gerenciar Ofertas (Cancelar, Concluir, Excluir)
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;
}

$acao = sanitizar($_POST['acao'] ?? '');
$oferta_id = (int)($_POST['oferta_id'] ?? 0);

if ($oferta_id <= 0) {
    setFlashMessage('error', 'Oferta inválida');
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;
}

try {
    // Verificar se a oferta pertence ao usuário
    $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id = ? AND transportadora_id = ?");
    $stmt->execute([$oferta_id, $_SESSION['usuario_id']]);
    $oferta = $stmt->fetch();

    if (!$oferta) {
        setFlashMessage('error', 'Oferta não encontrada');
        header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
        exit;
    }

    switch ($acao) {
        case 'cancelar':
            $stmt = $pdo->prepare("UPDATE ofertas SET status = 'cancelada' WHERE id = ?");
            $stmt->execute([$oferta_id]);

            // Criar notificação para o motorista se existir
            if ($oferta['motorista_id']) {
                require_once 'criar_notificacao.php';
                criarNotificacao(
                    $oferta['motorista_id'],
                    'Viagem Cancelada',
                    "A viagem {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']} foi cancelada.",
                    'alerta'
                );
            }

            setFlashMessage('success', 'Oferta cancelada com sucesso');
            break;

        case 'concluir':
            if ($oferta['status'] !== 'em_andamento') {
                setFlashMessage('error', 'Apenas ofertas em andamento podem ser concluídas');
                header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
                exit;
            }

            $stmt = $pdo->prepare("UPDATE ofertas SET status = 'concluida' WHERE id = ?");
            $stmt->execute([$oferta_id]);

            // Criar notificação para o motorista
            if ($oferta['motorista_id']) {
                require_once 'criar_notificacao.php';
                criarNotificacao(
                    $oferta['motorista_id'],
                    'Viagem Concluída!',
                    "Parabéns! A viagem {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']} foi concluída com sucesso.",
                    'sucesso'
                );
            }

            setFlashMessage('success', 'Viagem concluída com sucesso!');
            break;

        case 'excluir':
            if ($oferta['status'] === 'em_andamento') {
                setFlashMessage('error', 'Não é possível excluir ofertas em andamento');
                header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
                exit;
            }

            $stmt = $pdo->prepare("DELETE FROM ofertas WHERE id = ?");
            $stmt->execute([$oferta_id]);

            setFlashMessage('success', 'Oferta excluída com sucesso');
            break;

        default:
            setFlashMessage('error', 'Ação inválida');
    }

    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao gerenciar oferta: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao processar ação. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;
}
