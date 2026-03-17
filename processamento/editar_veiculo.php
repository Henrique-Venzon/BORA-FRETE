<?php
/**
 * BORAFRETE - Editar Veículo
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$veiculo_id = (int)($_POST['veiculo_id'] ?? 0);
$tipo_veiculo = sanitizar($_POST['tipo_veiculo'] ?? '');
$tipo_carroceria = sanitizar($_POST['tipo_carroceria'] ?? '');
$marca = sanitizar($_POST['marca'] ?? '');
$ano = (int)($_POST['ano'] ?? 0);
$placa_1 = strtoupper(sanitizar($_POST['placa_1'] ?? ''));
$placa_2 = strtoupper(sanitizar($_POST['placa_2'] ?? ''));
$placa_3 = strtoupper(sanitizar($_POST['placa_3'] ?? ''));
$capacidade_peso = (float)($_POST['capacidade_peso'] ?? 0);
$capacidade_m3 = !empty($_POST['capacidade_m3']) ? (float)$_POST['capacidade_m3'] : null;
$qtd_pallets = !empty($_POST['qtd_pallets']) ? (int)$_POST['qtd_pallets'] : null;

// Validação básica
if ($veiculo_id <= 0 || empty($tipo_veiculo) || empty($marca) || $ano <= 0 || empty($placa_1) || $capacidade_peso <= 0) {
    setFlashMessage('error', 'Dados inválidos');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

try {
    // Verificar se o veículo pertence ao usuário
    $stmtCheck = $pdo->prepare("SELECT foto FROM veiculos WHERE id = ? AND usuario_id = ?");
    $stmtCheck->execute([$veiculo_id, $_SESSION['usuario_id']]);
    $veiculo = $stmtCheck->fetch();

    if (!$veiculo) {
        setFlashMessage('error', 'Veículo não encontrado');
        header('Location: ' . BASE_URL . 'views/dashboard.php');
        exit;
    }

    // Processar nova foto se foi enviada
    $foto = $veiculo['foto'];
    if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
        $novaFoto = uploadArquivo($_FILES['foto'], 'veiculos');
        if ($novaFoto) {
            // Remover foto antiga se existir
            if ($foto && file_exists(UPLOAD_DIR . $foto)) {
                unlink(UPLOAD_DIR . $foto);
            }
            $foto = $novaFoto;
        }
    }

    // Atualizar veículo
    $stmt = $pdo->prepare("
        UPDATE veiculos SET
            tipo_veiculo = ?,
            tipo_carroceria = ?,
            marca = ?,
            ano = ?,
            placa_1 = ?,
            placa_2 = ?,
            placa_3 = ?,
            capacidade_peso = ?,
            capacidade_m3 = ?,
            qtd_pallets = ?,
            foto = ?
        WHERE id = ? AND usuario_id = ?
    ");

    $stmt->execute([
        $tipo_veiculo,
        $tipo_carroceria,
        $marca,
        $ano,
        $placa_1,
        !empty($placa_2) ? $placa_2 : null,
        !empty($placa_3) ? $placa_3 : null,
        $capacidade_peso,
        $capacidade_m3,
        $qtd_pallets,
        $foto,
        $veiculo_id,
        $_SESSION['usuario_id']
    ]);

    setFlashMessage('success', 'Veículo atualizado com sucesso!');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao editar veículo: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao atualizar veículo. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}
