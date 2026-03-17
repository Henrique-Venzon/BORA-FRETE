<?php
/**
 * BORAFRETE - Listar Motoristas Disponíveis
 */
require_once '../config/config.php';
verificarLogin();

header('Content-Type: application/json');

try {
    $stmt = $pdo->prepare("
        SELECT id, nome_razao_social, telefone, email
        FROM usuarios
        WHERE tipo_perfil = 'motorista'
        ORDER BY nome_razao_social ASC
    ");
    $stmt->execute();
    $motoristas = $stmt->fetchAll();

    echo json_encode(['sucesso' => true, 'motoristas' => $motoristas]);

} catch (PDOException $e) {
    error_log("Erro ao listar motoristas: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao buscar motoristas']);
}
