<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Minhas Ofertas';
$showSidebar = true;

// Buscar ofertas do usuário
try {
    $stmt = $pdo->prepare("
        SELECT * FROM ofertas
        WHERE user_id = ?
        ORDER BY data_cadastro DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $ofertas = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar ofertas: " . $e->getMessage());
    $ofertas = [];
}

include 'views/layout/header.php';
?>

<div class="row mb-3">
    <div class="col-md-12">
        <a href="cadastro-oferta.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Cadastrar Nova Oferta
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Minhas Ofertas de Carga</h3>
            </div>
            <div class="card-body">

                <?php if (count($ofertas) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Título</th>
                                    <th>Rota</th>
                                    <th>Tipo Carga</th>
                                    <th>Carroceria</th>
                                    <th>Peso</th>
                                    <th>Valor Frete</th>
                                    <th>Coleta</th>
                                    <th>Status</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($ofertas as $oferta): ?>
                                    <tr>
                                        <td>
                                            <strong><?php echo htmlspecialchars($oferta['titulo']); ?></strong>
                                            <?php if (!empty($oferta['descricao'])): ?>
                                                <br><small class="text-light"><?php echo substr(htmlspecialchars($oferta['descricao']), 0, 50); ?>...</small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($oferta['cidade_origem'] . '/' . $oferta['uf_origem']); ?></strong>
                                            <br>
                                            <i class="fas fa-arrow-down" style="font-size: 10px; color: var(--text-light);"></i>
                                            <br>
                                            <strong><?php echo htmlspecialchars($oferta['cidade_destino'] . '/' . $oferta['uf_destino']); ?></strong>
                                        </td>
                                        <td><?php echo htmlspecialchars($oferta['tipo_carga']); ?></td>
                                        <td>
                                            <span class="badge badge-secondary">
                                                <?php echo ucfirst($oferta['tipo_carroceria_necessaria']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if (!empty($oferta['peso_total'])): ?>
                                                <?php echo number_format($oferta['peso_total'], 2, ',', '.'); ?>t
                                            <?php else: ?>
                                                <span class="text-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if ($oferta['frete_a_combinar'] == 1): ?>
                                                <span class="badge badge-warning">A Combinar</span>
                                            <?php else: ?>
                                                <?php echo formatMoney($oferta['valor_frete']); ?>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($oferta['data_coleta'])): ?>
                                                <?php echo formatDate($oferta['data_coleta']); ?>
                                            <?php else: ?>
                                                <span class="text-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            $statusClass = '';
                                            switch ($oferta['status']) {
                                                case 'ativa':
                                                    $statusClass = 'badge-success';
                                                    $statusText = 'Ativa';
                                                    break;
                                                case 'em_negociacao':
                                                    $statusClass = 'badge-warning';
                                                    $statusText = 'Em Negociação';
                                                    break;
                                                case 'finalizada':
                                                    $statusClass = 'badge-primary';
                                                    $statusText = 'Finalizada';
                                                    break;
                                                case 'cancelada':
                                                    $statusClass = 'badge-danger';
                                                    $statusText = 'Cancelada';
                                                    break;
                                                default:
                                                    $statusClass = 'badge-secondary';
                                                    $statusText = ucfirst($oferta['status']);
                                            }
                                            echo '<span class="badge ' . $statusClass . '">' . $statusText . '</span>';
                                            ?>
                                        </td>
                                        <td>
                                            <a href="ver-oferta.php?id=<?php echo $oferta['id']; ?>" class="btn btn-sm btn-secondary" title="Visualizar">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 60px 20px;">
                        <i class="fas fa-box" style="font-size: 64px; color: var(--text-light); margin-bottom: 20px;"></i>
                        <h4 style="color: var(--text-color); margin-bottom: 10px;">Nenhuma oferta cadastrada</h4>
                        <p style="color: var(--text-light); margin-bottom: 24px;">Cadastre sua primeira oferta de carga e comece a receber propostas.</p>
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
