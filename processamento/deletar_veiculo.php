<?php
/**
 * BORAFRETE - Deletar Veículo
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$veiculo_id = (int)($_POST['veiculo_id'] ?? 0);

if ($veiculo_id <= 0) {
    setFlashMessage('error', 'Veículo inválido');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

try {
    // Buscar foto para deletar
    $stmtFoto = $pdo->prepare("SELECT foto FROM veiculos WHERE id = ? AND usuario_id = ?");
    $stmtFoto->execute([$veiculo_id, $_SESSION['usuario_id']]);
    $veiculo = $stmtFoto->fetch();

    if (!$veiculo) {
        setFlashMessage('error', 'Veículo não encontrado');
        header('Location: ' . BASE_URL . 'views/dashboard.php');
        exit;
    }

    // Deletar foto se existir
    if ($veiculo['foto'] && file_exists(UPLOAD_DIR . $veiculo['foto'])) {
        unlink(UPLOAD_DIR . $veiculo['foto']);
    }

    // Deletar veículo do banco
    $stmt = $pdo->prepare("DELETE FROM veiculos WHERE id = ? AND usuario_id = ?");
    $stmt->execute([$veiculo_id, $_SESSION['usuario_id']]);

    setFlashMessage('success', 'Veículo removido com sucesso!');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao deletar veículo: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao remover veículo. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}
