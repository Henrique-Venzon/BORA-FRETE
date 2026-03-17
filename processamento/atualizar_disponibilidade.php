<?php
/**
 * BORAFRETE - Atualizar Disponibilidade do Veículo (AJAX)
 */
require_once '../config/config.php';
verificarLogin();

// Verificar se é uma requisição POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Método não permitido']);
    exit;
}

// Receber dados JSON
$input = file_get_contents('php://input');
$data = json_decode($input, true);

$veiculo_id = (int)($data['veiculo_id'] ?? 0);
$disponivel = isset($data['disponivel']) && $data['disponivel'] ? 1 : 0;

// Validação
if ($veiculo_id <= 0) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'ID do veículo inválido']);
    exit;
}

try {
    // Verificar se o veículo pertence ao usuário logado
    $stmt = $pdo->prepare("SELECT id FROM veiculos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$veiculo_id, $_SESSION['usuario_id']]);

    if (!$stmt->fetch()) {
        http_response_code(403);
        echo json_encode(['success' => false, 'message' => 'Acesso negado']);
        exit;
    }

    // Atualizar disponibilidade
    $stmtUpdate = $pdo->prepare("UPDATE veiculos SET disponivel = ? WHERE id = ?");
    $stmtUpdate->execute([$disponivel, $veiculo_id]);

    echo json_encode([
        'success' => true,
        'message' => 'Disponibilidade atualizada com sucesso',
        'disponivel' => $disponivel
    ]);

} catch (PDOException $e) {
    error_log("Erro ao atualizar disponibilidade: " . $e->getMessage());
    http_response_code(500);
    echo json_encode(['success' => false, 'message' => 'Erro ao atualizar disponibilidade']);
}
