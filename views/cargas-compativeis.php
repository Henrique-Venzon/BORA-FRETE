<?php
/**
 * BORAFRETE - Cargas Compatíveis (Matching Inteligente)
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Cargas Compatíveis';

// Buscar cargas compatíveis
require_once '../processamento/matching_cargas.php';

// Verificar se é motorista
$stmtTipo = $pdo->prepare("SELECT tipo_perfil FROM usuarios WHERE id = ?");
$stmtTipo->execute([$_SESSION['usuario_id']]);
$userTipo = $stmtTipo->fetchColumn();

if ($userTipo !== 'motorista') {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$matches = encontrarCargasCompativeis($_SESSION['usuario_id']) ?? [];

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <div>
            <h1>🚀 Cargas Compatíveis Para Você</h1>
            <p>Encontramos automaticamente as melhores cargas para seus veículos</p>
        </div>
        <button class="btn btn-secondary" onclick="atualizarMatches()">
            Atualizar Matches
        </button>
    </div>

    <?php if (empty($matches)): ?>
        <div class="glass-card text-center" style="padding: 60px;">
            <div style="font-size: 80px; margin-bottom: 20px;">📦</div>
            <h3 style="color: #6B7280; margin-bottom: 12px;">Nenhuma carga compatível no momento</h3>
            <p style="color: #9CA3AF;">Volte mais tarde ou cadastre mais veículos para aumentar as opções.</p>
            <a href="<?php echo BASE_URL; ?>views/cadastro-veiculo.php" class="btn btn-primary" style="margin-top: 20px;">
                Cadastrar Veículo
            </a>
        </div>
    <?php else: ?>

        <div class="matches-info glass-card" style="margin-bottom: 24px; padding: 20px;">
            <h3>✨ Encontramos <?php echo count($matches); ?> carga(s) compatível(is)</h3>
            <p style="color: var(--cinza-escuro); margin-top: 8px;">
                Ordenadas por compatibilidade. Cargas acima de 75% são altamente recomendadas!
            </p>
        </div>

        <div class="matches-grid">
            <?php foreach ($matches as $match): ?>
                <?php
                $oferta = $match['oferta'];
                $veiculo = $match['veiculo'];
                $score = $match['score'];
                ?>

                <div class="match-card glass-card">
                    <!-- Score Badge -->
                    <div class="match-score-badge score-<?php echo $score >= 75 ? 'high' : ($score >= 60 ? 'medium' : 'low'); ?>">
                        <div class="score-number"><?php echo $score; ?>%</div>
                        <div class="score-label"><?php echo $match['motivos']; ?></div>
                    </div>

                    <!-- Informações da Rota -->
                    <div class="match-route">
                        <div class="route-point">
                            <div class="route-icon">📍</div>
                            <div class="route-info">
                                <strong><?php echo htmlspecialchars($oferta['origem_cidade']); ?></strong>
                                <span class="uf-tag"><?php echo $oferta['origem_uf']; ?></span>
                            </div>
                        </div>
                        <div class="route-arrow">→</div>
                        <div class="route-point">
                            <div class="route-icon">🎯</div>
                            <div class="route-info">
                                <strong><?php echo htmlspecialchars($oferta['destino_cidade']); ?></strong>
                                <span class="uf-tag"><?php echo $oferta['destino_uf']; ?></span>
                            </div>
                        </div>
                    </div>

                    <!-- Detalhes -->
                    <div class="match-details">
                        <div class="detail-row">
                            <span class="detail-icon">📅</span>
                            <strong>Carregamento:</strong>
                            <?php echo date('d/m/Y', strtotime($oferta['data_carregamento'])); ?>
                            <?php if ($oferta['hora_carregamento']): ?>
                                às <?php echo date('H:i', strtotime($oferta['hora_carregamento'])); ?>
                            <?php endif; ?>
                        </div>

                        <div class="detail-row">
                            <span class="detail-icon">🚛</span>
                            <strong>Veículo:</strong>
                            <?php echo ucfirst($oferta['tipo_veiculo']); ?>
                            <?php if ($oferta['tipo_carroceria']): ?>
                                - <?php echo $oferta['tipo_carroceria']; ?>
                            <?php endif; ?>
                        </div>

                        <div class="detail-row">
                            <span class="detail-icon">⚖️</span>
                            <strong>Peso:</strong>
                            <?php echo number_format($oferta['peso'], 2, ',', '.'); ?> kg
                            <small style="color: var(--verde-sucesso);">
                                (Seu veículo: <?php echo number_format($veiculo['capacidade_peso'], 0, ',', '.'); ?> kg)
                            </small>
                        </div>

                        <div class="detail-row">
                            <span class="detail-icon">📦</span>
                            <strong>Carga:</strong>
                            <?php echo ucfirst($oferta['tipo_carga']); ?> -
                            <?php echo ucfirst($oferta['modelo_carga']); ?>
                        </div>

                        <div class="detail-row valor-row">
                            <span class="detail-icon">💰</span>
                            <strong>Valor:</strong>
                            <?php if ($oferta['frete_combinar']): ?>
                                <span class="valor-combinar">A combinar</span>
                            <?php else: ?>
                                <span class="valor-destaque"><?php echo formatarMoeda($oferta['valor_frete']); ?></span>
                            <?php endif; ?>
                        </div>

                        <div class="detail-row">
                            <span class="detail-icon">🏢</span>
                            <strong>Transportadora:</strong>
                            <?php echo htmlspecialchars($oferta['transportadora_nome']); ?>
                        </div>
                    </div>

                    <!-- Ações -->
                    <div class="match-actions">
                        <button class="btn btn-success" onclick="interesseCarga(<?php echo $oferta['id']; ?>)">
                            ✓ Tenho Interesse
                        </button>
                        <button class="btn btn-primary" onclick="verDetalhes(<?php echo $oferta['id']; ?>)">
                            Ver Detalhes
                        </button>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

    <?php endif; ?>

</div>

<style>
.matches-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(450px, 1fr));
    gap: 24px;
}

.match-card {
    padding: 0;
    overflow: hidden;
    position: relative;
}

.match-score-badge {
    padding: 20px;
    text-align: center;
    color: white;
}

.score-high {
    background: linear-gradient(135deg, #10B981, #059669);
}

.score-medium {
    background: linear-gradient(135deg, #4A90E2, #2563EB);
}

.score-low {
    background: linear-gradient(135deg, #F59E0B, #D97706);
}

.score-number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 4px;
}

.score-label {
    font-size: 14px;
    opacity: 0.9;
    text-transform: uppercase;
    letter-spacing: 1px;
}

.match-route {
    display: flex;
    align-items: center;
    justify-content: space-around;
    padding: 24px;
    background: var(--cinza-claro);
}

.route-point {
    display: flex;
    align-items: center;
    gap: 12px;
}

.route-icon {
    font-size: 32px;
}

.route-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.uf-tag {
    background: var(--azul-claro);
    color: white;
    padding: 2px 8px;
    border-radius: 8px;
    font-size: 12px;
    font-weight: 600;
    width: fit-content;
}

.route-arrow {
    font-size: 28px;
    color: var(--azul-claro);
    font-weight: bold;
}

.match-details {
    padding: 24px;
    display: flex;
    flex-direction: column;
    gap: 12px;
}

.detail-row {
    display: flex;
    align-items: center;
    gap: 8px;
    font-size: 14px;
    color: var(--cinza-escuro);
}

.detail-icon {
    font-size: 18px;
}

.valor-row {
    padding: 12px;
    background: rgba(16, 185, 129, 0.05);
    border-radius: 8px;
    margin-top: 8px;
}

.valor-destaque {
    font-size: 20px;
    font-weight: 700;
    color: var(--verde-sucesso);
}

.match-actions {
    display: flex;
    gap: 12px;
    padding: 20px 24px;
    border-top: 2px solid var(--cinza-medio);
}

.match-actions .btn {
    flex: 1;
}

@media (max-width: 768px) {
    .matches-grid {
        grid-template-columns: 1fr;
    }

    .match-route {
        flex-direction: column;
        gap: 16px;
    }

    .route-arrow {
        transform: rotate(90deg);
    }
}
</style>

<script>
function atualizarMatches() {
    window.location.reload();
}

function interesseCarga(ofertaId) {
    if (!confirm('Confirma que tem interesse nesta carga? A transportadora será notificada.')) {
        return;
    }

    fetch(BASE_URL + 'processamento/manifestar_interesse.php', {
        method: 'POST',
        headers: { 'Content-Type': 'application/x-www-form-urlencoded' },
        body: 'oferta_id=' + ofertaId
    })
    .then(r => r.json())
    .then(data => {
        if (data.sucesso) {
            alert('Interesse registrado! A transportadora receberá sua mensagem.');
            window.location.reload();
        } else {
            alert('Erro: ' + data.erro);
        }
    });
}

function verDetalhes(ofertaId) {
    window.location.href = BASE_URL + 'views/ofertas.php#oferta-' + ofertaId;
}
</script>

<?php require_once 'layout/footer.php'; ?>
