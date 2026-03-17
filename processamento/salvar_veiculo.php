<?php
/**
 * BORAFRETE - Salvar Veículo
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/cadastro-veiculo.php');
    exit;
}

// Capturar dados do formulário
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
if (empty($tipo_veiculo) || empty($marca) || $ano <= 0 || empty($placa_1) || $capacidade_peso <= 0) {
    setFlashMessage('error', 'Por favor, preencha todos os campos obrigatórios');
    header('Location: ' . BASE_URL . 'views/cadastro-veiculo.php');
    exit;
}

// Processar upload de foto
$foto = null;
if (isset($_FILES['foto']) && $_FILES['foto']['error'] === UPLOAD_ERR_OK) {
    $foto = uploadArquivo($_FILES['foto'], 'veiculos');
}

try {
    // Inserir veículo no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO veiculos (
            usuario_id,
            tipo_veiculo,
            tipo_carroceria,
            marca,
            ano,
            placa_1,
            placa_2,
            placa_3,
            capacidade_peso,
            capacidade_m3,
            qtd_pallets,
            foto,
            disponivel
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 1)
    ");

    $stmt->execute([
        $_SESSION['usuario_id'],
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
        $foto
    ]);

    setFlashMessage('success', 'Veículo cadastrado com sucesso!');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao cadastrar veículo: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao cadastrar veículo. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/cadastro-veiculo.php');
    exit;
}
