<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Dashboard';
$showSidebar = true;

// Buscar estatísticas do usuário
try {
    // Total de veículos
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM veiculos WHERE user_id = ? AND ativo = 1");
    $stmt->execute([$_SESSION['user_id']]);
    $totalVeiculos = $stmt->fetch()['total'];

    // Total de ofertas ativas
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ofertas WHERE user_id = ? AND status = 'ativa'");
    $stmt->execute([$_SESSION['user_id']]);
    $totalOfertas = $stmt->fetch()['total'];

    // Total de propostas pendentes
    $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM propostas WHERE user_id = ? AND status = 'pendente'");
    $stmt->execute([$_SESSION['user_id']]);
    $totalPropostas = $stmt->fetch()['total'];

    // Buscar últimas ofertas
    $stmt = $pdo->prepare("
        SELECT * FROM ofertas
        WHERE user_id = ?
        ORDER BY data_cadastro DESC
        LIMIT 5
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $ultimasOfertas = $stmt->fetchAll();

} catch (PDOException $e) {
    error_log("Erro ao buscar estatísticas: " . $e->getMessage());
    $totalVeiculos = 0;
    $totalOfertas = 0;
    $totalPropostas = 0;
    $ultimasOfertas = [];
}

include 'views/layout/header.php';
?>

<!-- Cards de Estatísticas -->
<div class="stats-grid">
    <div class="stat-card">
        <div class="stat-icon primary">
            <i class="fas fa-truck"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $totalVeiculos; ?></h3>
            <p>Veículos Cadastrados</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon success">
            <i class="fas fa-box"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $totalOfertas; ?></h3>
            <p>Ofertas Ativas</p>
        </div>
    </div>

    <div class="stat-card">
        <div class="stat-icon warning">
            <i class="fas fa-handshake"></i>
        </div>
        <div class="stat-content">
            <h3><?php echo $totalPropostas; ?></h3>
            <p>Propostas Pendentes</p>
        </div>
    </div>
</div>

<!-- Ações Rápidas -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Ações Rápidas</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-3 mb-3">
                        <a href="cadastro-veiculo.php" class="btn btn-primary w-100">
                            <i class="fas fa-truck"></i> Cadastrar Veículo
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="cadastro-oferta.php" class="btn btn-primary w-100">
                            <i class="fas fa-box"></i> Cadastrar Oferta
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="buscar-fretes.php" class="btn btn-secondary w-100">
                            <i class="fas fa-search"></i> Buscar Fretes
                        </a>
                    </div>
                    <div class="col-md-3 mb-3">
                        <a href="propostas.php" class="btn btn-secondary w-100">
                            <i class="fas fa-handshake"></i> Ver Propostas
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Últimas Ofertas -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Minhas Últimas Ofertas</h3>
            </div>
            <div class="card-body">
                <?php if (count($ultimasOfertas) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Origem</th>
                                    <th>Destino</th>
                                    <th>Data Coleta</th>
                                    <th>Valor</th>
                                    <th>Status</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ultimasOfertas as $oferta): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($oferta['titulo']); ?></td>
                                        <td><?php echo htmlspecialchars($oferta['cidade_origem'] . '/' . $oferta['uf_origem']); ?></td>
                                        <td><?php echo htmlspecialchars($oferta['cidade_destino'] . '/' . $oferta['uf_destino']); ?></td>
                                        <td><?php echo formatDate($oferta['data_coleta']); ?></td>
                                        <td>
                                            <?php
                                            if ($oferta['frete_a_combinar'] == 1) {
                                                echo '<span class="badge badge-warning">A Combinar</span>';
                                            } else {
                                                echo formatMoney($oferta['valor_frete']);
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($oferta['status']) {
                                                case 'ativa':
                                                    $statusClass = 'badge-success';
                                                    break;
                                                case 'em_negociacao':
                                                    $statusClass = 'badge-warning';
                                                    break;
                                                case 'finalizada':
                                                    $statusClass = 'badge-primary';
                                                    break;
                                                case 'cancelada':
                                                    $statusClass = 'badge-danger';
                                                    break;
                                            }
                                            echo '<span class="badge ' . $statusClass . '">' . ucfirst(str_replace('_', ' ', $oferta['status'])) . '</span>';
                                            ?>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center text-light" style="padding: 40px;">
                        <i class="fas fa-inbox" style="font-size: 48px; color: var(--text-light); margin-bottom: 16px;"></i>
                        <p style="color: var(--text-light);">Você ainda não possui ofertas cadastradas.</p>
                        <a href="cadastro-oferta.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Cadastrar Primeira Oferta
                        </a>
                    </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>
