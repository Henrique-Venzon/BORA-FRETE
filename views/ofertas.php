<?php
/**
 * BORAFRETE - Visualizar Ofertas
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Ofertas Disponíveis';

// Filtros
$filtro_origem = sanitizar($_GET['origem_uf'] ?? '');
$filtro_destino = sanitizar($_GET['destino_uf'] ?? '');
$filtro_tipo_veiculo = sanitizar($_GET['tipo_veiculo'] ?? '');
$filtro_tipo_carga = sanitizar($_GET['tipo_carga'] ?? '');

// Buscar ofertas com filtros
$sql = "
    SELECT
        o.*,
        u.nome_razao_social as transportadora_nome,
        u.telefone as transportadora_telefone,
        u.email as transportadora_email
    FROM ofertas o
    JOIN usuarios u ON o.transportadora_id = u.id
    WHERE o.status = 'ativa'
";

$params = [];

if (!empty($filtro_origem)) {
    $sql .= " AND o.origem_uf = ?";
    $params[] = $filtro_origem;
}

if (!empty($filtro_destino)) {
    $sql .= " AND o.destino_uf = ?";
    $params[] = $filtro_destino;
}

if (!empty($filtro_tipo_veiculo)) {
    $sql .= " AND o.tipo_veiculo = ?";
    $params[] = $filtro_tipo_veiculo;
}

if (!empty($filtro_tipo_carga)) {
    $sql .= " AND o.tipo_carga = ?";
    $params[] = $filtro_tipo_carga;
}

$sql .= " ORDER BY o.created_at DESC LIMIT 50";

try {
    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $ofertas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar ofertas: " . $e->getMessage());
    $ofertas = [];
}

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <div>
            <h1>Ofertas de Frete</h1>
            <p>Encontre fretes disponíveis para seu veículo</p>
        </div>
        <?php if ($_SESSION['usuario_tipo'] === 'transportadora' || $_SESSION['usuario_tipo'] === 'agenciador'): ?>
            <a href="<?php echo BASE_URL; ?>views/cadastro-oferta.php" class="btn btn-primary">
                + Nova Oferta
            </a>
        <?php endif; ?>
    </div>

    <!-- Filtros -->
    <div class="glass-card" style="margin-bottom: 24px;">
        <form method="GET" action="" class="filter-form">
            <div class="form-row">
                <div class="form-group">
                    <label for="origem_uf">UF Origem</label>
                    <select name="origem_uf" id="origem_uf">
                        <option value="">Todas</option>
                        <?php
                        $estados = ['AC', 'AL', 'AP', 'AM', 'BA', 'CE', 'DF', 'ES', 'GO', 'MA', 'MT', 'MS', 'MG', 'PA', 'PB', 'PR', 'PE', 'PI', 'RJ', 'RN', 'RS', 'RO', 'RR', 'SC', 'SP', 'SE', 'TO'];
                        foreach ($estados as $estado) {
                            $selected = ($filtro_origem === $estado) ? 'selected' : '';
                            echo "<option value='$estado' $selected>$estado</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="destino_uf">UF Destino</label>
                    <select name="destino_uf" id="destino_uf">
                        <option value="">Todas</option>
                        <?php
                        foreach ($estados as $estado) {
                            $selected = ($filtro_destino === $estado) ? 'selected' : '';
                            echo "<option value='$estado' $selected>$estado</option>";
                        }
                        ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_veiculo">Tipo de Veículo</label>
                    <select name="tipo_veiculo" id="tipo_veiculo">
                        <option value="">Todos</option>
                        <option value="van" <?php echo $filtro_tipo_veiculo === 'van' ? 'selected' : ''; ?>>Van</option>
                        <option value="fiorino" <?php echo $filtro_tipo_veiculo === 'fiorino' ? 'selected' : ''; ?>>Fiorino</option>
                        <option value="3/4" <?php echo $filtro_tipo_veiculo === '3/4' ? 'selected' : ''; ?>>3/4</option>
                        <option value="toco" <?php echo $filtro_tipo_veiculo === 'toco' ? 'selected' : ''; ?>>Toco</option>
                        <option value="truck" <?php echo $filtro_tipo_veiculo === 'truck' ? 'selected' : ''; ?>>Truck</option>
                        <option value="carreta" <?php echo $filtro_tipo_veiculo === 'carreta' ? 'selected' : ''; ?>>Carreta</option>
                        <option value="rodotrem" <?php echo $filtro_tipo_veiculo === 'rodotrem' ? 'selected' : ''; ?>>Rodotrem</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_carga">Tipo de Carga</label>
                    <select name="tipo_carga" id="tipo_carga">
                        <option value="">Todas</option>
                        <option value="seca" <?php echo $filtro_tipo_carga === 'seca' ? 'selected' : ''; ?>>Seca</option>
                        <option value="refrigerada" <?php echo $filtro_tipo_carga === 'refrigerada' ? 'selected' : ''; ?>>Refrigerada</option>
                        <option value="congelada" <?php echo $filtro_tipo_carga === 'congelada' ? 'selected' : ''; ?>>Congelada</option>
                        <option value="perigosa" <?php echo $filtro_tipo_carga === 'perigosa' ? 'selected' : ''; ?>>Perigosa</option>
                        <option value="quimica" <?php echo $filtro_tipo_carga === 'quimica' ? 'selected' : ''; ?>>Química</option>
                    </select>
                </div>

                <div class="form-group" style="display: flex; align-items: flex-end;">
                    <button type="submit" class="btn btn-primary">Filtrar</button>
                </div>
            </div>
        </form>
    </div>

    <!-- Lista de Ofertas -->
    <div class="ofertas-grid">
        <?php if (empty($ofertas)): ?>
            <div class="glass-card text-center" style="padding: 60px;">
                <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg" style="opacity: 0.3; margin: 0 auto 20px;">
                    <path d="M20 6H16V4C16 2.89 15.11 2 14 2H10C8.89 2 8 2.89 8 4V6H4C2.89 6 2.01 6.89 2.01 8L2 19C2 20.11 2.89 21 4 21H20C21.11 21 22 20.11 22 19V8C22 6.89 21.11 6 20 6ZM10 4H14V6H10V4Z" fill="#999"/>
                </svg>
                <h3 style="color: #6B7280;">Nenhuma oferta encontrada</h3>
                <p style="color: #9CA3AF;">Ajuste os filtros ou volte mais tarde.</p>
            </div>
        <?php else: ?>
            <?php foreach ($ofertas as $oferta): ?>
                <div class="oferta-card glass-card">
                    <div class="oferta-header">
                        <div class="oferta-rota">
                            <div class="rota-origem">
                                <span class="uf-badge"><?php echo htmlspecialchars($oferta['origem_uf']); ?></span>
                                <span class="cidade"><?php echo htmlspecialchars($oferta['origem_cidade']); ?></span>
                            </div>
                            <div class="rota-arrow">→</div>
                            <div class="rota-destino">
                                <span class="uf-badge"><?php echo htmlspecialchars($oferta['destino_uf']); ?></span>
                                <span class="cidade"><?php echo htmlspecialchars($oferta['destino_cidade']); ?></span>
                            </div>
                        </div>
                        <div class="oferta-valor">
                            <?php if ($oferta['frete_combinar']): ?>
                                <span class="valor-combinar">A combinar</span>
                            <?php else: ?>
                                <span class="valor"><?php echo formatarMoeda($oferta['valor_frete']); ?></span>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="oferta-info">
                        <div class="info-row">
                            <strong>Data Carregamento:</strong>
                            <?php echo date('d/m/Y', strtotime($oferta['data_carregamento'])); ?>
                            <?php if ($oferta['hora_carregamento']): ?>
                                às <?php echo date('H:i', strtotime($oferta['hora_carregamento'])); ?>
                            <?php endif; ?>
                        </div>
                        <div class="info-row">
                            <strong>Data Entrega:</strong>
                            <?php echo date('d/m/Y', strtotime($oferta['data_entrega'])); ?>
                            <?php if ($oferta['hora_entrega']): ?>
                                às <?php echo date('H:i', strtotime($oferta['hora_entrega'])); ?>
                            <?php endif; ?>
                        </div>
                        <div class="info-row">
                            <strong>Veículo:</strong> <?php echo ucfirst($oferta['tipo_veiculo']); ?>
                            <?php if ($oferta['tipo_carroceria']): ?>
                                - <?php echo htmlspecialchars($oferta['tipo_carroceria']); ?>
                            <?php endif; ?>
                        </div>
                        <div class="info-row">
                            <strong>Carga:</strong> <?php echo ucfirst($oferta['tipo_carga']); ?> -
                            <?php echo ucfirst($oferta['modelo_carga']); ?>
                        </div>
                        <div class="info-row">
                            <strong>Peso:</strong> <?php echo number_format($oferta['peso'], 2, ',', '.'); ?> kg
                            <?php if ($oferta['cubagem']): ?>
                                | <strong>Cubagem:</strong> <?php echo number_format($oferta['cubagem'], 2, ',', '.'); ?> m³
                            <?php endif; ?>
                            <?php if ($oferta['pallets']): ?>
                                | <strong>Pallets:</strong> <?php echo $oferta['pallets']; ?>
                            <?php endif; ?>
                        </div>
                    </div>

                    <div class="oferta-footer">
                        <div class="transportadora-info">
                            <strong><?php echo htmlspecialchars($oferta['transportadora_nome']); ?></strong>
                        </div>
                        <div class="oferta-actions">
                            <a href="tel:<?php echo preg_replace('/[^0-9]/', '', $oferta['transportadora_telefone']); ?>" class="btn btn-secondary btn-sm">
                                Ligar
                            </a>
                            <a href="mailto:<?php echo $oferta['transportadora_email']; ?>" class="btn btn-primary btn-sm">
                                Contatar
                            </a>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        <?php endif; ?>
    </div>

</div>

<style>
.ofertas-grid {
    display: grid;
    gap: 20px;
}

.oferta-card {
    padding: 24px;
    transition: transform 0.2s, box-shadow 0.2s;
}

.oferta-card:hover {
    transform: translateY(-4px);
    box-shadow: 0 12px 48px rgba(0, 0, 0, 0.15);
}

.oferta-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 20px;
    padding-bottom: 16px;
    border-bottom: 2px solid var(--cinza-medio);
}

.oferta-rota {
    display: flex;
    align-items: center;
    gap: 16px;
}

.rota-origem, .rota-destino {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.uf-badge {
    background: linear-gradient(135deg, var(--azul-claro) 0%, var(--azul-principal) 100%);
    color: white;
    padding: 4px 12px;
    border-radius: 8px;
    font-weight: 600;
    font-size: 14px;
    display: inline-block;
    width: fit-content;
}

.cidade {
    font-size: 16px;
    font-weight: 500;
    color: var(--preto);
}

.rota-arrow {
    font-size: 24px;
    color: var(--azul-claro);
    font-weight: bold;
}

.oferta-valor .valor {
    font-size: 28px;
    font-weight: 700;
    color: var(--verde-sucesso);
}

.oferta-valor .valor-combinar {
    font-size: 18px;
    font-weight: 600;
    color: var(--azul-claro);
}

.oferta-info {
    display: flex;
    flex-direction: column;
    gap: 8px;
    margin-bottom: 20px;
}

.info-row {
    font-size: 14px;
    color: var(--cinza-escuro);
}

.info-row strong {
    color: var(--preto);
}

.oferta-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: 16px;
    border-top: 2px solid var(--cinza-medio);
}

.transportadora-info strong {
    color: var(--azul-principal);
}

.oferta-actions {
    display: flex;
    gap: 12px;
}

.btn-sm {
    padding: 8px 16px;
    font-size: 14px;
}

.filter-form .form-row {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
    gap: 16px;
    align-items: end;
}

@media (max-width: 768px) {
    .oferta-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 16px;
    }

    .oferta-footer {
        flex-direction: column;
        gap: 12px;
        align-items: flex-start;
    }

    .oferta-actions {
        width: 100%;
    }

    .oferta-actions .btn {
        flex: 1;
    }
}
</style>

<?php require_once 'layout/footer.php'; ?>
