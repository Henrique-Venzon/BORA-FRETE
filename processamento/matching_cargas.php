<?php
/**
 * BORAFRETE - Sistema de Matching Inteligente de Cargas
 * Encontra automaticamente cargas compatíveis para motoristas
 */
require_once '../config/config.php';

/**
 * Encontrar cargas compatíveis para um motorista
 */
function encontrarCargasCompativeis($motorista_id) {
    global $pdo;

    // Buscar dados do motorista
    $stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ? AND tipo_perfil = 'motorista'");
    $stmt->execute([$motorista_id]);
    $motorista = $stmt->fetch();

    if (!$motorista) {
        return [];
    }

    // Buscar veículos do motorista
    $stmt = $pdo->prepare("SELECT * FROM veiculos WHERE usuario_id = ? AND disponivel = TRUE");
    $stmt->execute([$motorista_id]);
    $veiculos = $stmt->fetchAll();

    if (empty($veiculos)) {
        return [];
    }

    $matches = [];

    foreach ($veiculos as $veiculo) {
        // Buscar ofertas compatíveis
        $sql = "
            SELECT o.*, u.nome_razao_social as transportadora_nome
            FROM ofertas o
            JOIN usuarios u ON o.transportadora_id = u.id
            WHERE o.status = 'ativa'
            AND o.tipo_veiculo = ?
        ";

        $params = [$veiculo['tipo_veiculo']];

        // Filtrar por tipo de carroceria se especificado
        if (!empty($veiculo['tipo_carroceria']) && !empty($o['tipo_carroceria'])) {
            $sql .= " AND o.tipo_carroceria = ?";
            $params[] = $veiculo['tipo_carroceria'];
        }

        // Filtrar por capacidade de peso
        $sql .= " AND o.peso <= ?";
        $params[] = $veiculo['capacidade_peso'];

        $sql .= " ORDER BY o.created_at DESC LIMIT 50";

        $stmt = $pdo->prepare($sql);
        $stmt->execute($params);
        $ofertas = $stmt->fetchAll();

        foreach ($ofertas as $oferta) {
            $score = calcularScore($motorista, $veiculo, $oferta);

            if ($score >= 50) { // Mínimo 50% de compatibilidade
                $matches[] = [
                    'oferta' => $oferta,
                    'veiculo' => $veiculo,
                    'score' => $score,
                    'motivos' => explicarScore($score)
                ];
            }
        }
    }

    // Ordenar por score (maior primeiro)
    usort($matches, function($a, $b) {
        return $b['score'] - $a['score'];
    });

    return $matches;
}

/**
 * Calcular pontuação de compatibilidade (0-100)
 */
function calcularScore($motorista, $veiculo, $oferta) {
    $score = 0;

    // 1. Tipo de veículo correto (+30 pontos)
    if ($veiculo['tipo_veiculo'] === $oferta['tipo_veiculo']) {
        $score += 30;
    }

    // 2. Tipo de carroceria compatível (+20 pontos)
    if (!empty($veiculo['tipo_carroceria']) && !empty($oferta['tipo_carroceria'])) {
        if ($veiculo['tipo_carroceria'] === $oferta['tipo_carroceria']) {
            $score += 20;
        }
    } else {
        $score += 10; // Bonus se não há exigência específica
    }

    // 3. Capacidade de peso adequada (+15 pontos)
    if ($oferta['peso'] <= $veiculo['capacidade_peso']) {
        $percentual = ($oferta['peso'] / $veiculo['capacidade_peso']) * 100;
        if ($percentual >= 70 && $percentual <= 100) {
            $score += 15; // Carga otimiza capacidade
        } elseif ($percentual >= 50) {
            $score += 10;
        } else {
            $score += 5;
        }
    }

    // 4. Proximidade geográfica (+20 pontos)
    if (!empty($motorista['latitude']) && !empty($motorista['longitude']) &&
        !empty($oferta['origem_lat']) && !empty($oferta['origem_lng'])) {

        $distancia = calcularDistancia(
            $motorista['latitude'],
            $motorista['longitude'],
            $oferta['origem_lat'],
            $oferta['origem_lng']
        );

        if ($distancia <= 50) {
            $score += 20;
        } elseif ($distancia <= 100) {
            $score += 15;
        } elseif ($distancia <= 200) {
            $score += 10;
        } elseif ($distancia <= 500) {
            $score += 5;
        }
    }

    // 5. Data de carregamento próxima (+15 pontos)
    $hoje = new DateTime();
    $dataCarregamento = new DateTime($oferta['data_carregamento']);
    $diff = $hoje->diff($dataCarregamento)->days;

    if ($diff <= 2) {
        $score += 15;
    } elseif ($diff <= 5) {
        $score += 10;
    } elseif ($diff <= 10) {
        $score += 5;
    }

    return min(100, $score); // Máximo 100
}

/**
 * Calcular distância entre duas coordenadas (fórmula de Haversine)
 */
function calcularDistancia($lat1, $lon1, $lat2, $lon2) {
    $earthRadius = 6371; // km

    $dLat = deg2rad($lat2 - $lat1);
    $dLon = deg2rad($lon2 - $lon1);

    $a = sin($dLat/2) * sin($dLat/2) +
         cos(deg2rad($lat1)) * cos(deg2rad($lat2)) *
         sin($dLon/2) * sin($dLon/2);

    $c = 2 * atan2(sqrt($a), sqrt(1-$a));
    $distance = $earthRadius * $c;

    return round($distance, 2);
}

/**
 * Explicar pontuação
 */
function explicarScore($score) {
    if ($score >= 90) return 'Perfeito para você!';
    if ($score >= 75) return 'Altamente compatível';
    if ($score >= 60) return 'Boa opção';
    if ($score >= 50) return 'Compatível';
    return 'Baixa compatibilidade';
}

/**
 * Processar matching para nova oferta
 */
function processarMatchingNovaOferta($oferta_id) {
    global $pdo;

    // Buscar oferta
    $stmt = $pdo->prepare("SELECT * FROM ofertas WHERE id = ?");
    $stmt->execute([$oferta_id]);
    $oferta = $stmt->fetch();

    if (!$oferta || $oferta['status'] !== 'ativa') {
        return [];
    }

    // Buscar motoristas com veículos compatíveis
    $sql = "
        SELECT DISTINCT u.*, v.*
        FROM usuarios u
        JOIN veiculos v ON u.id = v.usuario_id
        WHERE u.tipo_perfil = 'motorista'
        AND v.disponivel = TRUE
        AND v.tipo_veiculo = ?
        AND v.capacidade_peso >= ?
    ";

    $params = [$oferta['tipo_veiculo'], $oferta['peso']];

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $motoristas = $stmt->fetchAll();

    $matchings = [];

    foreach ($motoristas as $motorista) {
        $score = calcularScore($motorista, $motorista, $oferta);

        if ($score >= 60) { // Apenas notificar matches bons
            // Salvar matching no banco
            $stmtMatch = $pdo->prepare("
                INSERT INTO matchings (oferta_id, motorista_id, score, distancia_km)
                VALUES (?, ?, ?, ?)
                ON DUPLICATE KEY UPDATE score = ?, distancia_km = ?
            ");

            $distancia = 0;
            if (!empty($motorista['latitude']) && !empty($motorista['longitude']) &&
                !empty($oferta['origem_lat']) && !empty($oferta['origem_lng'])) {
                $distancia = calcularDistancia(
                    $motorista['latitude'],
                    $motorista['longitude'],
                    $oferta['origem_lat'],
                    $oferta['origem_lng']
                );
            }

            $stmtMatch->execute([$oferta_id, $motorista['id'], $score, $distancia, $score, $distancia]);

            // Criar notificação para o motorista
            require_once 'criar_notificacao.php';
            criarNotificacao(
                $motorista['id'],
                'Nova Carga Compatível! ' . $score . '%',
                "Encontramos uma carga perfeita para você: {$oferta['origem_cidade']}/{$oferta['origem_uf']} → {$oferta['destino_cidade']}/{$oferta['destino_uf']}",
                'oferta'
            );

            $matchings[] = [
                'motorista' => $motorista,
                'score' => $score,
                'distancia' => $distancia
            ];
        }
    }

    return $matchings;
}

// Se chamado diretamente (API)
if (isset($_GET['motorista_id'])) {
    verificarLogin();
    header('Content-Type: application/json');

    $matches = encontrarCargasCompativeis($_SESSION['usuario_id']);
    echo json_encode(['sucesso' => true, 'matches' => $matches]);
}
