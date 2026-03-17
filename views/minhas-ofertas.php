<?php
/**
 * BORAFRETE - Minhas Ofertas (Gestão Completa)
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Minhas Ofertas';

// Buscar ofertas do usuário
$stmt = $pdo->prepare("
    SELECT
        o.*,
        u.nome_razao_social as motorista_nome,
        u.telefone as motorista_telefone
    FROM ofertas o
    LEFT JOIN usuarios u ON o.motorista_id = u.id
    WHERE o.transportadora_id = ?
    ORDER BY
        CASE o.status
            WHEN 'em_andamento' THEN 1
            WHEN 'ativa' THEN 2
            WHEN 'concluida' THEN 3
            WHEN 'cancelada' THEN 4
        END,
        o.created_at DESC
");
$stmt->execute([$_SESSION['usuario_id']]);
$ofertas = $stmt->fetchAll();

// Agrupar por status
$ofertas_por_status = [
    'em_andamento' => [],
    'ativa' => [],
    'concluida' => [],
    'cancelada' => []
];

foreach ($ofertas as $oferta) {
    $ofertas_por_status[$oferta['status']][] = $oferta;
}

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <div>
            <h1>Minhas Ofertas</h1>
            <p>Gerencie todas as suas ofertas de frete</p>
        </div>
        <a href="<?php echo BASE_URL; ?>views/cadastro-oferta.php" class="btn btn-primary">
            + Nova Oferta
        </a>
    </div>

    <!-- Abas de Status -->
    <div class="tabs-container glass-card">
        <div class="tabs">
            <button class="tab-btn active" onclick="switchTab('em_andamento')">
                Em Andamento
                <span class="badge-tab"><?php echo count($ofertas_por_status['em_andamento']); ?></span>
            </button>
            <button class="tab-btn" onclick="switchTab('ativa')">
                Ativas
                <span class="badge-tab"><?php echo count($ofertas_por_status['ativa']); ?></span>
            </button>
            <button class="tab-btn" onclick="switchTab('concluida')">
                Concluídas
                <span class="badge-tab"><?php echo count($ofertas_por_status['concluida']); ?></span>
            </button>
            <button class="tab-btn" onclick="switchTab('cancelada')">
                Canceladas
                <span class="badge-tab"><?php echo count($ofertas_por_status['cancelada']); ?></span>
            </button>
        </div>
    </div>

    <!-- Conteúdo das Abas -->
    <?php foreach ($ofertas_por_status as $status => $lista): ?>
        <div class="tab-content" id="tab-<?php echo $status; ?>" <?php echo $status !== 'em_andamento' ? 'style="display: none;"' : ''; ?>>

            <?php if (empty($lista)): ?>
                <div class="glass-card text-center" style="padding: 60px;">
                    <div style="opacity: 0.3; font-size: 60px; margin-bottom: 20px;">📦</div>
                    <h3 style="color: #6B7280;">Nenhuma oferta <?php echo ucfirst(str_replace('_', ' ', $status)); ?></h3>
                </div>
            <?php else: ?>
                <div class="ofertas-list">
                    <?php foreach ($lista as $oferta): ?>
                        <div class="oferta-item glass-card">
                            <!-- Header -->
                            <div class="oferta-item-header">
                                <div class="oferta-rota">
                                    <div class="rota-info">
                                        <span class="uf-badge"><?php echo $oferta['origem_uf']; ?></span>
                                        <span class="cidade"><?php echo $oferta['origem_cidade']; ?></span>
                                    </div>
                                    <div class="rota-arrow">→</div>
                                    <div class="rota-info">
                                        <span class="uf-badge"><?php echo $oferta['destino_uf']; ?></span>
                                        <span class="cidade"><?php echo $oferta['destino_cidade']; ?></span>
                                    </div>
                                </div>
                                <div class="status-badge status-<?php echo $oferta['status']; ?>">
                                    <?php
                                    $status_labels = [
                                        'ativa' => 'Ativa',
                                        'em_andamento' => 'Em Andamento',
                                        'concluida' => 'Concluída',
                                        'cancelada' => 'Cancelada'
                                    ];
                                    echo $status_labels[$oferta['status']];
                                    ?>
                                </div>
                            </div>

                            <!-- Informações -->
                            <div class="oferta-item-body">
                                <div class="info-grid">
                                    <div class="info-col">
                                        <strong>Carregamento:</strong>
                                        <?php echo date('d/m/Y', strtotime($oferta['data_carregamento'])); ?>
                                        <?php if ($oferta['hora_carregamento']): ?>
                                            às <?php echo date('H:i', strtotime($oferta['hora_carregamento'])); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info-col">
                                        <strong>Entrega:</strong>
                                        <?php echo date('d/m/Y', strtotime($oferta['data_entrega'])); ?>
                                        <?php if ($oferta['hora_entrega']): ?>
                                            às <?php echo date('H:i', strtotime($oferta['hora_entrega'])); ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info-col">
                                        <strong>Veículo:</strong>
                                        <?php echo ucfirst($oferta['tipo_veiculo']); ?>
                                        <?php if ($oferta['tipo_carroceria']): ?>
                                            - <?php echo $oferta['tipo_carroceria']; ?>
                                        <?php endif; ?>
                                    </div>
                                    <div class="info-col">
                                        <strong>Peso:</strong>
                                        <?php echo number_format($oferta['peso'], 2, ',', '.'); ?> kg
                                    </div>
                                    <div class="info-col">
                                        <strong>Valor:</strong>
                                        <?php if ($oferta['frete_combinar']): ?>
                                            <span class="valor-combinar">A combinar</span>
                                        <?php else: ?>
                                            <span class="valor-destaque"><?php echo formatarMoeda($oferta['valor_frete']); ?></span>
                                        <?php endif; ?>
                                    </div>
                                </div>

                                <?php if ($oferta['motorista_id']): ?>
                                    <div class="motorista-info">
                                        <strong>Motorista:</strong>
                                        <?php echo htmlspecialchars($oferta['motorista_nome']); ?>
                                        | <?php echo htmlspecialchars($oferta['motorista_telefone']); ?>
                                    </div>
                                <?php endif; ?>
                            </div>

                            <!-- Ações -->
                            <div class="oferta-item-footer">
                                <div class="oferta-meta">
                                    <small>Criada em <?php echo date('d/m/Y H:i', strtotime($oferta['created_at'])); ?></small>
                                </div>
                                <div class="oferta-actions">
                                    <?php if ($oferta['status'] === 'ativa'): ?>
                                        <button class="btn btn-success btn-sm" onclick="atribuirMotorista(<?php echo $oferta['id']; ?>)">
                                            Atribuir Motorista
                                        </button>
                                        <button class="btn btn-secondary btn-sm" onclick="editarOferta(<?php echo $oferta['id']; ?>)">
                                            Editar
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="cancelarOferta(<?php echo $oferta['id']; ?>)">
                                            Cancelar
                                        </button>
                                    <?php elseif ($oferta['status'] === 'em_andamento'): ?>
                                        <button class="btn btn-success btn-sm" onclick="concluirOferta(<?php echo $oferta['id']; ?>)">
                                            Concluir Viagem
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="cancelarOferta(<?php echo $oferta['id']; ?>)">
                                            Cancelar
                                        </button>
                                    <?php elseif ($oferta['status'] === 'concluida' || $oferta['status'] === 'cancelada'): ?>
                                        <button class="btn btn-secondary btn-sm" onclick="verDetalhes(<?php echo $oferta['id']; ?>)">
                                            Ver Detalhes
                                        </button>
                                        <button class="btn btn-danger btn-sm" onclick="excluirOferta(<?php echo $oferta['id']; ?>)">
                                            Excluir
                                        </button>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php endif; ?>

        </div>
    <?php endforeach; ?>

</div>

<!-- Modal Atribuir Motorista -->
<div id="modalMotorista" class="modal">
    <div class="modal-content">
        <span class="modal-close" onclick="fecharModal()">&times;</span>
        <h3>Atribuir Motorista</h3>
        <form id="formAtribuir" method="POST" action="<?php echo BASE_URL; ?>processamento/atribuir_motorista.php">
            <input type="hidden" name="oferta_id" id="oferta_id_modal">

            <div class="form-group">
                <label>Selecione o motorista:</label>
                <select name="motorista_id" required id="selectMotorista">
                    <option value="">Carregando...</option>
                </select>
            </div>

            <div class="modal-actions">
                <button type="button" class="btn btn-secondary" onclick="fecharModal()">Cancelar</button>
                <button type="submit" class="btn btn-primary">Confirmar</button>
            </div>
        </form>
    </div>
</div>

<style>
.tabs-container {
    margin-bottom: 24px;
    padding: 0;
    overflow: hidden;
}

.tabs {
    display: flex;
    border-bottom: 2px solid var(--cinza-medio);
}

.tab-btn {
    flex: 1;
    padding: 16px 24px;
    background: none;
    border: none;
    font-size: 15px;
    font-weight: 600;
    color: var(--cinza-escuro);
    cursor: pointer;
    transition: all 0.3s;
    position: relative;
    display: flex;
    align-items: center;
    justify-content: center;
    gap: 8px;
}

.tab-btn:hover {
    background: var(--cinza-claro);
    color: var(--azul-claro);
}

.tab-btn.active {
    color: var(--azul-claro);
    border-bottom: 3px solid var(--azul-claro);
}

.badge-tab {
    background: var(--cinza-medio);
    color: var(--preto);
    padding: 2px 8px;
    border-radius: 12px;
    font-size: 12px;
    font-weight: 700;
}

.tab-btn.active .badge-tab {
    background: var(--azul-claro);
    color: white;
}

.ofertas-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.oferta-item {
    padding: 24px;
}

.oferta-item-header {
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

.rota-info {
    display: flex;
    flex-direction: column;
    gap: 4px;
}

.status-badge {
    padding: 8px 16px;
    border-radius: 20px;
    font-size: 13px;
    font-weight: 600;
    text-transform: uppercase;
}

.status-ativa {
    background: rgba(16, 185, 129, 0.1);
    color: var(--verde-sucesso);
}

.status-em_andamento {
    background: rgba(74, 144, 226, 0.1);
    color: var(--azul-claro);
}

.status-concluida {
    background: rgba(107, 114, 128, 0.1);
    color: var(--cinza-escuro);
}

.status-cancelada {
    background: rgba(239, 68, 68, 0.1);
    color: var(--vermelho-erro);
}

.info-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(200px, 1fr));
    gap: 12px;
    margin-bottom: 16px;
}

.info-col {
    font-size: 14px;
    color: var(--cinza-escuro);
}

.motorista-info {
    padding: 12px;
    background: var(--cinza-claro);
    border-radius: 8px;
    margin-top: 16px;
}

.oferta-item-footer {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-top: 16px;
    padding-top: 16px;
    border-top: 2px solid var(--cinza-medio);
}

.oferta-meta small {
    color: var(--cinza-escuro);
    font-size: 12px;
}

.oferta-actions {
    display: flex;
    gap: 8px;
    flex-wrap: wrap;
}

.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background: rgba(0, 0, 0, 0.6);
    align-items: center;
    justify-content: center;
}

.modal.show {
    display: flex;
}

.modal-content {
    background: white;
    padding: 32px;
    border-radius: 20px;
    max-width: 500px;
    width: 90%;
    position: relative;
}

.modal-close {
    position: absolute;
    right: 20px;
    top: 20px;
    font-size: 28px;
    cursor: pointer;
    color: var(--cinza-escuro);
}

.modal-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 24px;
}

@media (max-width: 768px) {
    .tabs {
        overflow-x: auto;
    }

    .tab-btn {
        white-space: nowrap;
    }

    .oferta-item-header {
        flex-direction: column;
        align-items: flex-start;
        gap: 12px;
    }

    .oferta-actions {
        width: 100%;
    }

    .oferta-actions button {
        flex: 1;
    }
}
</style>

<script>
function switchTab(status) {
    // Remover active de todos
    document.querySelectorAll('.tab-btn').forEach(btn => btn.classList.remove('active'));
    document.querySelectorAll('.tab-content').forEach(content => content.style.display = 'none');

    // Ativar selecionado
    event.target.classList.add('active');
    document.getElementById('tab-' + status).style.display = 'block';
}

function atribuirMotorista(ofertaId) {
    document.getElementById('oferta_id_modal').value = ofertaId;
    document.getElementById('modalMotorista').classList.add('show');

    // Buscar motoristas disponíveis
    fetch(BASE_URL + 'processamento/listar_motoristas.php')
        .then(r => r.json())
        .then(data => {
            const select = document.getElementById('selectMotorista');
            select.innerHTML = '<option value="">Selecione...</option>';
            data.motoristas.forEach(m => {
                select.innerHTML += `<option value="${m.id}">${m.nome_razao_social} - ${m.telefone}</option>`;
            });
        });
}

function fecharModal() {
    document.getElementById('modalMotorista').classList.remove('show');
}

function cancelarOferta(id) {
    if (!confirm('Tem certeza que deseja cancelar esta oferta?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URL + 'processamento/gerenciar_oferta.php';
    form.innerHTML = `
        <input type="hidden" name="acao" value="cancelar">
        <input type="hidden" name="oferta_id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function concluirOferta(id) {
    if (!confirm('Confirma que a viagem foi concluída?')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URL + 'processamento/gerenciar_oferta.php';
    form.innerHTML = `
        <input type="hidden" name="acao" value="concluir">
        <input type="hidden" name="oferta_id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function excluirOferta(id) {
    if (!confirm('Tem certeza que deseja excluir esta oferta? Esta ação não pode ser desfeita.')) return;

    const form = document.createElement('form');
    form.method = 'POST';
    form.action = BASE_URL + 'processamento/gerenciar_oferta.php';
    form.innerHTML = `
        <input type="hidden" name="acao" value="excluir">
        <input type="hidden" name="oferta_id" value="${id}">
    `;
    document.body.appendChild(form);
    form.submit();
}

function editarOferta(id) {
    window.location.href = BASE_URL + 'views/editar-oferta.php?id=' + id;
}

function verDetalhes(id) {
    window.location.href = BASE_URL + 'views/detalhes-oferta.php?id=' + id;
}
</script>

<?php require_once 'layout/footer.php'; ?>
