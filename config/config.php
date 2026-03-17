<?php
/**
 * ================================================================
 * SISTEMA DE LOGÍSTICA - CONFIGURAÇÕES GLOBAIS
 * ================================================================
 */

// Iniciar sessão se ainda não foi iniciada
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// ================================================================
// CONSTANTES DO SISTEMA
// ================================================================
define('BASE_URL', '');
define('SITE_NAME', 'FreteBras');
define('SITE_DESCRIPTION', 'Sistema de Gestão Logística');
define('SITE_VERSION', '1.0.0');

// ================================================================
// CONFIGURAÇÕES DE BANCO DE DADOS
// ================================================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'fretelog');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// ================================================================
// CONFIGURAÇÕES DE TIMEZONE
// ================================================================
date_default_timezone_set('America/Sao_Paulo');

// ================================================================
// CONFIGURAÇÕES DE ERRO (Desenvolvimento)
// ================================================================
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ================================================================
// CONEXÃO COM BANCO DE DADOS (PDO)
// ================================================================
try {
    $dsn = "mysql:host=" . DB_HOST . ";dbname=" . DB_NAME . ";charset=" . DB_CHARSET;
    $options = [
        PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
        PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
        PDO::ATTR_EMULATE_PREPARES   => false,
    ];

    $pdo = new PDO($dsn, DB_USER, DB_PASS, $options);

} catch (PDOException $e) {
    // Em produção, não exiba detalhes do erro
    die("Erro ao conectar com o banco de dados. Por favor, tente novamente mais tarde.");
    // Para debug: die("Erro: " . $e->getMessage());
}

// ================================================================
// FUNÇÕES AUXILIARES
// ================================================================

/**
 * Verifica se o usuário está logado
 */
function isLoggedIn() {
    return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
}

/**
 * Redireciona para uma página
 */
function redirect($url) {
    header("Location: " . BASE_URL . "/" . $url);
    exit();
}

/**
 * Protege páginas que requerem autenticação
 */
function requireLogin() {
    if (!isLoggedIn()) {
        redirect('index.php?erro=acesso_negado');
    }
}

/**
 * Sanitiza entrada de dados
 */
function sanitizeInput($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data, ENT_QUOTES, 'UTF-8');
    return $data;
}

/**
 * Formata CPF
 */
function formatCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);
    if (strlen($cpf) != 11) return $cpf;
    return substr($cpf, 0, 3) . '.' . substr($cpf, 3, 3) . '.' . substr($cpf, 6, 3) . '-' . substr($cpf, 9, 2);
}

/**
 * Formata CNPJ
 */
function formatCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);
    if (strlen($cnpj) != 14) return $cnpj;
    return substr($cnpj, 0, 2) . '.' . substr($cnpj, 2, 3) . '.' . substr($cnpj, 5, 3) . '/' . substr($cnpj, 8, 4) . '-' . substr($cnpj, 12, 2);
}

/**
 * Formata telefone
 */
function formatPhone($phone) {
    $phone = preg_replace('/[^0-9]/', '', $phone);
    $len = strlen($phone);

    if ($len == 11) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 5) . '-' . substr($phone, 7, 4);
    } elseif ($len == 10) {
        return '(' . substr($phone, 0, 2) . ') ' . substr($phone, 2, 4) . '-' . substr($phone, 6, 4);
    }

    return $phone;
}

/**
 * Formata moeda
 */
function formatMoney($value) {
    return 'R$ ' . number_format($value, 2, ',', '.');
}

/**
 * Formata data brasileira
 */
function formatDate($date) {
    if (empty($date)) return '';
    $timestamp = strtotime($date);
    return date('d/m/Y', $timestamp);
}

/**
 * Formata data e hora brasileira
 */
function formatDateTime($datetime) {
    if (empty($datetime)) return '';
    $timestamp = strtotime($datetime);
    return date('d/m/Y H:i', $timestamp);
}

/**
 * Gera token de sessão
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Validação de CPF
 */
function validaCPF($cpf) {
    $cpf = preg_replace('/[^0-9]/', '', $cpf);

    if (strlen($cpf) != 11) {
        return false;
    }

    if (preg_match('/(\d)\1{10}/', $cpf)) {
        return false;
    }

    for ($t = 9; $t < 11; $t++) {
        for ($d = 0, $c = 0; $c < $t; $c++) {
            $d += $cpf[$c] * (($t + 1) - $c);
        }
        $d = ((10 * $d) % 11) % 10;
        if ($cpf[$c] != $d) {
            return false;
        }
    }

    return true;
}

/**
 * Validação de CNPJ
 */
function validaCNPJ($cnpj) {
    $cnpj = preg_replace('/[^0-9]/', '', $cnpj);

    if (strlen($cnpj) != 14) {
        return false;
    }

    if (preg_match('/(\d)\1{13}/', $cnpj)) {
        return false;
    }

    for ($i = 0, $j = 5, $soma = 0; $i < 12; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    if ($cnpj[12] != ($resto < 2 ? 0 : 11 - $resto)) {
        return false;
    }

    for ($i = 0, $j = 6, $soma = 0; $i < 13; $i++) {
        $soma += $cnpj[$i] * $j;
        $j = ($j == 2) ? 9 : $j - 1;
    }

    $resto = $soma % 11;

    return $cnpj[13] == ($resto < 2 ? 0 : 11 - $resto);
}

/**
 * Validação de email
 */
function validaEmail($email) {
    return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
}
