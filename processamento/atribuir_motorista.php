<?php
/**
 * BORAFRETE - Atribuir Motorista à Oferta
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;
}

$oferta_id = (int)($_POST['oferta_id'] ?? 0);
$motorista_id = (int)($_POST['motorista_id'] ?? 0);

if ($oferta_id <= 0 || $motorista_id <= 0) {
    setFlashMessage('error', 'Dados inválidos');
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

    if ($oferta['status'] !== 'ativa') {
        setFlashMessage('error', 'Esta oferta não está mais disponível');
        header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
        exit;
    }

    // Verificar se motorista existe
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_perfil = 'motorista'");
    $stmt->execute([$motorista_id]);
    $motorista = $stmt->fetch();

    if (!$motorista) {
        setFlashMessage('error', 'Motorista não encontrado');
        header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
        exit;
    }

    // Atribuir motorista e alterar status
    $stmt = $pdo->prepare("
        UPDATE ofertas SET
            motorista_id = ?,
            status = 'em_andamento'
        WHERE id = ?
    ");
    $stmt->execute([$motorista_id, $oferta_id]);

    // Criar notificação para o motorista
    require_once 'criar_notificacao.php';
    criarNotificacao(
        $motorista_id,
        'Nova Viagem Atribuída!',
        "Você foi designado para uma viagem: {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']}. Carregamento em " . date('d/m/Y', strtotime($oferta['data_carregamento'])),
        'oferta'
    );

    setFlashMessage('success', 'Motorista atribuído com sucesso! A oferta está agora em andamento.');
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao atribuir motorista: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao atribuir motorista. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/minhas-ofertas.php');
    exit;
}
