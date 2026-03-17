<?php
/**
 * BORAFRETE - Salvar Oferta de Frete
 */
require_once '../config/config.php';
verificarLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/cadastro-oferta.php');
    exit;
}

// Capturar dados do formulário - ROTA
$origem_cidade = sanitizar($_POST['origem_cidade'] ?? '');
$origem_uf = sanitizar($_POST['origem_uf'] ?? '');
$destino_cidade = sanitizar($_POST['destino_cidade'] ?? '');
$destino_uf = sanitizar($_POST['destino_uf'] ?? '');

// DATAS
$data_carregamento = sanitizar($_POST['data_carregamento'] ?? '');
$hora_carregamento = !empty($_POST['hora_carregamento']) ? sanitizar($_POST['hora_carregamento']) : null;
$data_entrega = sanitizar($_POST['data_entrega'] ?? '');
$hora_entrega = !empty($_POST['hora_entrega']) ? sanitizar($_POST['hora_entrega']) : null;

// VEÍCULO
$tipo_veiculo = sanitizar($_POST['tipo_veiculo'] ?? '');
$tipo_carroceria = sanitizar($_POST['tipo_carroceria'] ?? '');

// CARGA
$tipo_carga = sanitizar($_POST['tipo_carga'] ?? '');
$modelo_carga = sanitizar($_POST['modelo_carga'] ?? '');
$peso = (float)($_POST['peso'] ?? 0);
$cubagem = !empty($_POST['cubagem']) ? (float)$_POST['cubagem'] : null;
$pallets = !empty($_POST['pallets']) ? (int)$_POST['pallets'] : null;

// FINANCEIRO
$frete_combinar = isset($_POST['frete_combinar']) ? 1 : 0;
$valor_frete = !$frete_combinar && !empty($_POST['valor_frete']) ? (float)$_POST['valor_frete'] : null;
$pedagio_incluso = isset($_POST['pedagio_incluso']) ? 1 : 0;
$tipo_pagamento = sanitizar($_POST['tipo_pagamento'] ?? '');
$fator_pagamento = sanitizar($_POST['fator_pagamento'] ?? '');

// Validação básica
if (empty($origem_cidade) || empty($origem_uf) || empty($destino_cidade) || empty($destino_uf) ||
    empty($data_carregamento) || empty($data_entrega) || empty($tipo_veiculo) ||
    empty($tipo_carga) || empty($modelo_carga) || $peso <= 0) {

    setFlashMessage('error', 'Por favor, preencha todos os campos obrigatórios');
    header('Location: ' . BASE_URL . 'views/cadastro-oferta.php');
    exit;
}

// Se não é frete a combinar, o valor é obrigatório
if (!$frete_combinar && ($valor_frete === null || $valor_frete <= 0)) {
    setFlashMessage('error', 'Por favor, informe o valor do frete ou marque "Frete a Combinar"');
    header('Location: ' . BASE_URL . 'views/cadastro-oferta.php');
    exit;
}

try {
    // Inserir oferta no banco de dados
    $stmt = $pdo->prepare("
        INSERT INTO ofertas (
            transportadora_id,
            origem_cidade,
            origem_uf,
            destino_cidade,
            destino_uf,
            data_carregamento,
            hora_carregamento,
            data_entrega,
            hora_entrega,
            tipo_veiculo,
            tipo_carroceria,
            tipo_carga,
            modelo_carga,
            peso,
            cubagem,
            pallets,
            frete_combinar,
            valor_frete,
            pedagio_incluso,
            tipo_pagamento,
            fator_pagamento,
            status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'ativa')
    ");

    $stmt->execute([
        $_SESSION['usuario_id'],
        $origem_cidade,
        $origem_uf,
        $destino_cidade,
        $destino_uf,
        $data_carregamento,
        $hora_carregamento,
        $data_entrega,
        $hora_entrega,
        $tipo_veiculo,
        $tipo_carroceria,
        $tipo_carga,
        $modelo_carga,
        $peso,
        $cubagem,
        $pallets,
        $frete_combinar,
        $valor_frete,
        $pedagio_incluso,
        $tipo_pagamento,
        $fator_pagamento
    ]);

    setFlashMessage('success', 'Oferta cadastrada com sucesso!');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao cadastrar oferta: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao cadastrar oferta. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/cadastro-oferta.php');
    exit;
}
