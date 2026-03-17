<?php
/**
 * BORAFRETE - Salvar Localização em Tempo Real
 */
require_once '../config/config.php';
verificarLogin();

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['sucesso' => false, 'erro' => 'Método inválido']);
    exit;
}

$lat = floatval($_POST['lat'] ?? 0);
$lng = floatval($_POST['lng'] ?? 0);

if ($lat === 0.0 || $lng === 0.0) {
    echo json_encode(['sucesso' => false, 'erro' => 'Coordenadas inválidas']);
    exit;
}

try {
    // Atualizar localização do usuário
    $stmt = $pdo->prepare("
        UPDATE usuarios SET
            latitude = ?,
            longitude = ?,
            ultima_localizacao = NOW()
        WHERE id = ?
    ");

    $stmt->execute([$lat, $lng, $_SESSION['usuario_id']]);

    echo json_encode(['sucesso' => true]);

} catch (PDOException $e) {
    error_log("Erro ao salvar localização: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao salvar localização']);
}
