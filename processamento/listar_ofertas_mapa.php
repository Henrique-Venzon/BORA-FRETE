<?php
/**
 * BORAFRETE - Listar Ofertas para Mapa
 */
require_once '../config/config.php';

header('Content-Type: application/json');

try {
    // Buscar ofertas ativas com coordenadas (geocodificadas)
    $stmt = $pdo->prepare("
        SELECT
            o.*,
            COALESCE(o.origem_lat, 0) as lat,
            COALESCE(o.origem_lng, 0) as lng
        FROM ofertas o
        WHERE o.status = 'ativa'
        AND o.origem_lat IS NOT NULL
        AND o.origem_lng IS NOT NULL
        ORDER BY o.created_at DESC
        LIMIT 100
    ");

    $stmt->execute();
    $ofertas = $stmt->fetchAll();

    // Se não houver coordenadas, tentar geocodificar
    foreach ($ofertas as &$oferta) {
        if (empty($oferta['lat']) || empty($oferta['lng'])) {
            $coords = geocodificarCidade($oferta['origem_cidade'], $oferta['origem_uf']);
            if ($coords) {
                $oferta['lat'] = $coords['lat'];
                $oferta['lng'] = $coords['lng'];

                // Salvar no banco
                $stmtUpdate = $pdo->prepare("
                    UPDATE ofertas SET origem_lat = ?, origem_lng = ? WHERE id = ?
                ");
                $stmtUpdate->execute([$coords['lat'], $coords['lng'], $oferta['id']]);
            }
        }
    }

    echo json_encode(['sucesso' => true, 'ofertas' => $ofertas]);

} catch (PDOException $e) {
    error_log("Erro ao listar ofertas: " . $e->getMessage());
    echo json_encode(['sucesso' => false, 'erro' => 'Erro ao buscar ofertas']);
}

/**
 * Geocodificar cidade usando coordenadas aproximadas
 */
function geocodificarCidade($cidade, $uf) {
    // Coordenadas aproximadas das capitais e principais cidades
    $coordenadas = [
        // Sul
        'Curitiba' => ['lat' => -25.4284, 'lng' => -49.2733],
        'Porto Alegre' => ['lat' => -30.0346, 'lng' => -51.2177],
        'Florianópolis' => ['lat' => -27.5954, 'lng' => -48.5480],

        // Sudeste
        'São Paulo' => ['lat' => -23.5505, 'lng' => -46.6333],
        'Rio de Janeiro' => ['lat' => -22.9068, 'lng' => -43.1729],
        'Belo Horizonte' => ['lat' => -19.9167, 'lng' => -43.9345],
        'Vitória' => ['lat' => -20.3155, 'lng' => -40.3128],

        // Centro-Oeste
        'Brasília' => ['lat' => -15.7942, 'lng' => -47.8822],
        'Goiânia' => ['lat' => -16.6869, 'lng' => -49.2648],
        'Campo Grande' => ['lat' => -20.4697, 'lng' => -54.6201],
        'Cuiabá' => ['lat' => -15.6014, 'lng' => -56.0979],

        // Nordeste
        'Salvador' => ['lat' => -12.9714, 'lng' => -38.5014],
        'Recife' => ['lat' => -8.0476, 'lng' => -34.8770],
        'Fortaleza' => ['lat' => -3.7172, 'lng' => -38.5433],
        'Natal' => ['lat' => -5.7945, 'lng' => -35.2110],
        'João Pessoa' => ['lat' => -7.1195, 'lng' => -34.8450],
        'Maceió' => ['lat' => -9.6658, 'lng' => -35.7353],
        'Aracaju' => ['lat' => -10.9095, 'lng' => -37.0748],
        'São Luís' => ['lat' => -2.5387, 'lng' => -44.2825],
        'Teresina' => ['lat' => -5.0892, 'lng' => -42.8019],

        // Norte
        'Manaus' => ['lat' => -3.1190, 'lng' => -60.0217],
        'Belém' => ['lat' => -1.4558, 'lng' => -48.5044],
        'Porto Velho' => ['lat' => -8.7619, 'lng' => -63.9039],
        'Rio Branco' => ['lat' => -9.9750, 'lng' => -67.8243],
        'Boa Vista' => ['lat' => 2.8197, 'lng' => -60.6733],
        'Macapá' => ['lat' => 0.0349, 'lng' => -51.0664],
        'Palmas' => ['lat' => -10.2128, 'lng' => -48.3603]
    ];

    return $coordenadas[$cidade] ?? null;
}
