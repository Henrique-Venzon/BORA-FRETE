<?php
/**
 * BORAFRETE - Arquivo de Configuração
 * Conexão com banco de dados e constantes do sistema
 */

// Iniciar sessão
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Configurações do banco de dados
define('DB_HOST', 'localhost');
define('DB_NAME', 'borafrete');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// URL base do sistema (AJUSTE CONFORME SEU AMBIENTE)
define('BASE_URL', 'http://localhost/bora-frete/');

// Configurações gerais
define('SITE_NAME', 'BoraFrete');
define('UPLOAD_DIR', __DIR__ . '/../public/uploads/');
define('UPLOAD_URL', BASE_URL . 'public/uploads/');

// Timezone
date_default_timezone_set('America/Sao_Paulo');

// Configurações de Email (SMTP)
define('MAIL_HOST', 'smtp.hostinger.com');
define('MAIL_PORT', 587);
define('MAIL_USER', 'informativos@tac.creatertools.com');
define('MAIL_PASS', 'Creater@2026');
define('MAIL_FROM', 'informativos@tac.creatertools.com');
define('MAIL_FROM_NAME', 'BoraFrete - TAC Corporation');

/**
 * Conexão PDO com MySQL
 */
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    die("Erro na conexão com o banco de dados: " . $e->getMessage());
}

/**
 * Função para verificar se usuário está logado
 */
function verificarLogin() {
    if (!isset($_SESSION['usuario_id'])) {
        header('Location: ' . BASE_URL . 'index.php');
        exit;
    }
}

/**
 * Função para sanitizar dados
 */
function sanitizar($data) {
    return htmlspecialchars(strip_tags(trim($data)));
}

/**
 * Função para formatar CPF/CNPJ
 */
function formatarDocumento($documento) {
    $documento = preg_replace('/[^0-9]/', '', $documento);

    if (strlen($documento) == 11) {
        // CPF: 000.000.000-00
        return preg_replace('/(\d{3})(\d{3})(\d{3})(\d{2})/', '$1.$2.$3-$4', $documento);
    } elseif (strlen($documento) == 14) {
        // CNPJ: 00.000.000/0000-00
        return preg_replace('/(\d{2})(\d{3})(\d{3})(\d{4})(\d{2})/', '$1.$2.$3/$4-$5', $documento);
    }

    return $documento;
}

/**
 * Função para formatar telefone
 */
function formatarTelefone($telefone) {
    $telefone = preg_replace('/[^0-9]/', '', $telefone);

    if (strlen($telefone) == 11) {
        // (00) 00000-0000
        return preg_replace('/(\d{2})(\d{5})(\d{4})/', '($1) $2-$3', $telefone);
    } elseif (strlen($telefone) == 10) {
        // (00) 0000-0000
        return preg_replace('/(\d{2})(\d{4})(\d{4})/', '($1) $2-$3', $telefone);
    }

    return $telefone;
}

/**
 * Função para formatar moeda
 */
function formatarMoeda($valor) {
    return 'R$ ' . number_format($valor, 2, ',', '.');
}

/**
 * Função para upload de arquivos
 */
function uploadArquivo($arquivo, $pasta = 'veiculos') {
    if (!isset($arquivo) || $arquivo['error'] !== UPLOAD_ERR_OK) {
        return null;
    }

    $extensoesPermitidas = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
    $nomeOriginal = $arquivo['name'];
    $extensao = strtolower(pathinfo($nomeOriginal, PATHINFO_EXTENSION));

    if (!in_array($extensao, $extensoesPermitidas)) {
        return null;
    }

    $nomeArquivo = uniqid() . '_' . time() . '.' . $extensao;
    $pastaDestino = UPLOAD_DIR . $pasta . '/';

    if (!is_dir($pastaDestino)) {
        mkdir($pastaDestino, 0755, true);
    }

    $caminhoCompleto = $pastaDestino . $nomeArquivo;

    if (move_uploaded_file($arquivo['tmp_name'], $caminhoCompleto)) {
        return $pasta . '/' . $nomeArquivo;
    }

    return null;
}

/**
 * Função para exibir mensagens flash
 */
function setFlashMessage($tipo, $mensagem) {
    $_SESSION['flash_message'] = [
        'tipo' => $tipo, // success, error, warning, info
        'mensagem' => $mensagem
    ];
}

function getFlashMessage() {
    if (isset($_SESSION['flash_message'])) {
        $msg = $_SESSION['flash_message'];
        unset($_SESSION['flash_message']);
        return $msg;
    }
    return null;
}

// Incluir EmailHelper
require_once __DIR__ . '/../lib/EmailHelper.php';
