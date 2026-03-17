<?php
/**
 * BORAFRETE - Salvar Novo Usuário (Cadastro)
 */
require_once '../config/config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

// Capturar dados do formulário
$tipo_perfil = sanitizar($_POST['tipo_perfil'] ?? '');
$nome_razao_social = sanitizar($_POST['nome_razao_social'] ?? '');
$documento_tipo = sanitizar($_POST['documento_tipo'] ?? '');
$documento_numero = preg_replace('/[^0-9]/', '', $_POST['documento_numero'] ?? '');
$ie = sanitizar($_POST['ie'] ?? '');
$email = sanitizar($_POST['email'] ?? '');
$telefone = sanitizar($_POST['telefone'] ?? '');
$senha = $_POST['senha'] ?? '';
$confirmar_senha = $_POST['confirmar_senha'] ?? '';

// Campos de motorista
$mopp = isset($_POST['mopp']) ? 1 : 0;
$cnh_categorias = isset($_POST['cnh_categorias']) ? implode(',', $_POST['cnh_categorias']) : null;

// Validação básica
if (empty($tipo_perfil) || empty($nome_razao_social) || empty($documento_tipo) ||
    empty($documento_numero) || empty($email) || empty($telefone) || empty($senha)) {
    setFlashMessage('error', 'Por favor, preencha todos os campos obrigatórios');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

// Validar senhas
if ($senha !== $confirmar_senha) {
    setFlashMessage('error', 'As senhas não coincidem');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

if (strlen($senha) < 6) {
    setFlashMessage('error', 'A senha deve ter no mínimo 6 caracteres');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

// Validar email
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
    setFlashMessage('error', 'E-mail inválido');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

// Validar CPF/CNPJ
if ($documento_tipo === 'cpf' && strlen($documento_numero) !== 11) {
    setFlashMessage('error', 'CPF inválido');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

if ($documento_tipo === 'cnpj' && strlen($documento_numero) !== 14) {
    setFlashMessage('error', 'CNPJ inválido');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}

try {
    // Verificar se email já existe
    $stmtEmail = $pdo->prepare("SELECT id FROM usuarios WHERE email = ?");
    $stmtEmail->execute([$email]);
    if ($stmtEmail->fetch()) {
        setFlashMessage('error', 'Este e-mail já está cadastrado');
        header('Location: ' . BASE_URL . 'views/cadastro.php');
        exit;
    }

    // Verificar se documento já existe
    $stmtDoc = $pdo->prepare("SELECT id FROM usuarios WHERE documento_numero = ?");
    $stmtDoc->execute([$documento_numero]);
    if ($stmtDoc->fetch()) {
        setFlashMessage('error', 'Este documento já está cadastrado');
        header('Location: ' . BASE_URL . 'views/cadastro.php');
        exit;
    }

    // Criptografar senha
    $senhaHash = password_hash($senha, PASSWORD_DEFAULT);

    // Inserir usuário
    $stmt = $pdo->prepare("
        INSERT INTO usuarios (
            tipo_perfil,
            nome_razao_social,
            documento_tipo,
            documento_numero,
            ie,
            email,
            senha,
            telefone,
            mopp,
            cnh_categorias
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)
    ");

    $stmt->execute([
        $tipo_perfil,
        $nome_razao_social,
        $documento_tipo,
        $documento_numero,
        !empty($ie) ? $ie : null,
        $email,
        $senhaHash,
        $telefone,
        $mopp,
        $cnh_categorias
    ]);

    // Login automático após cadastro
    $usuario_id = $pdo->lastInsertId();
    $_SESSION['usuario_id'] = $usuario_id;
    $_SESSION['usuario_nome'] = $nome_razao_social;
    $_SESSION['usuario_tipo'] = $tipo_perfil;
    $_SESSION['usuario_email'] = $email;

    setFlashMessage('success', 'Cadastro realizado com sucesso! Bem-vindo ao BoraFrete!');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;

} catch (PDOException $e) {
    error_log("Erro ao cadastrar usuário: " . $e->getMessage());
    setFlashMessage('error', 'Erro ao realizar cadastro. Tente novamente.');
    header('Location: ' . BASE_URL . 'views/cadastro.php');
    exit;
}
