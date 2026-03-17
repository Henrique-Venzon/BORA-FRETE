<?php
/**
 * BORAFRETE - Logout
 */
require_once '../config/config.php';

// Destruir sessão
session_destroy();

// Redirecionar para login
header('Location: ' . BASE_URL . 'index.php');
exit;
