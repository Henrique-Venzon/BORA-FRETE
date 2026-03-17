<?php
/**
 * BORAFRETE - Manifestar Interesse em Carga
 */
require_once '../config/config.php';
verificarLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido']);
    exit;
}

$oferta_id = (int)($_POST['oferta_id'] ?? 0);

if ($oferta_id <= 0) {
    echo json_encode(['sucesso' => false, 'erro' => 'Oferta inválida']);
    exit;
}

try {
    // Buscar oferta
    $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id = ? AND status = 'ativa'");
    $stmt->execute([$oferta_id]);
    $oferta = $stmt->fetch();

    if (!$oferta) {
        echo json_encode(['sucesso' => false, 'erro' => 'Oferta não encontrada']);
        exit;
    }

    // Buscar dados do motorista
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
    $stmt->execute([$_SESSION['usuario_id']]);
    $motorista = $stmt->fetch();

    // Criar notificação para a transportadora
    require_once 'criar_notificacao.php';
    criarNotificacao(
        $oferta['transportadora_id'],
        'Motorista Interessado!',
        "{$motorista['nome_razao_social']} manifestou interesse na carga {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']}. Telefone: {$motorista['telefone']}",
        'oferta'
    );

    // Criar mensagem automática
    $stmtMsg = $pdo->prepare("
        INSERT INTO mensagens (remetente_id, destinatario_id, oferta_id, mensagem)
        VALUES (?, ?, ?, ?)
    ");

    $mensagem = "Olá! Tenho interesse na carga {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']} com carregamento em " . date('d/m/Y', strtotime($oferta['data_carregamento'])) . ". Aguardo contato!";

    $stmtMsg->execute([
        $_SESSION['usuario_id'],
        $oferta['transportadora_id'],
        $oferta_id,
        $mensagem
    ]);

    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    error_log("Erro ao manifestar interesse: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao processar interesse']);
}
