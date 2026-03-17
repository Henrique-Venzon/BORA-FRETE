<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Meus Veículos';
$showSidebar = true;

// Buscar veículos do usuário
try {
    $stmt = $pdo->prepare("
        SELECT * FROM veiculos
        WHERE user_id = ? AND ativo = 1
        ORDER BY data_cadastro DESC
    ");
    $stmt->execute([$_SESSION['user_id']]);
    $veiculos = $stmt->fetchAll();
} catch (PDOException $e) {
    error_log("Erro ao buscar veículos: " . $e->getMessage());
    $veiculos = [];
}

include 'views/layout/header.php';
?>

<div class="row mb-3">
    <div class="col-md-12">
        <a href="cadastro-veiculo.php" class="btn btn-primary">
            <i class="fas fa-plus"></i> Cadastrar Novo Veículo
        </a>
    </div>
</div>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Meus Veículos Cadastrados</h3>
            </div>
            <div class="card-body">

                <?php if (isset($_GET['excluido'])): ?>
                    <div class="alert alert-success">
                        Veículo excluído com sucesso!
                    </div>
                <?php endif; ?>

                <?php if (count($veiculos) > 0): ?>
                    <div class="table-responsive">
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Tipo</th>
                                    <th>Carroceria</th>
                                    <th>Placa(s)</th>
                                    <th>Marca/Modelo</th>
                                    <th>Capacidade</th>
                                    <th>Data Cadastro</th>
                                    <th>Ações</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php foreach ($veiculos as $veiculo): ?>
                                    <tr>
                                        <td>
                                            <span class="badge badge-primary">
                                                <?php echo strtoupper($veiculo['tipo_veiculo']); ?>
                                            </span>
                                        </td>
                                        <td><?php echo ucfirst($veiculo['tipo_carroceria']); ?></td>
                                        <td>
                                            <strong><?php echo htmlspecialchars($veiculo['placa_cavalo']); ?></strong>
                                            <?php if (!empty($veiculo['placa_carreta'])): ?>
                                                <br><small><?php echo htmlspecialchars($veiculo['placa_carreta']); ?></small>
                                            <?php endif; ?>
                                            <?php if (!empty($veiculo['placa_carreta2'])): ?>
                                                <br><small><?php echo htmlspecialchars($veiculo['placa_carreta2']); ?></small>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <?php
                                            if (!empty($veiculo['marca_cavalo']) || !empty($veiculo['modelo_cavalo'])) {
                                                echo htmlspecialchars($veiculo['marca_cavalo'] . ' ' . $veiculo['modelo_cavalo']);
                                            } else {
                                                echo '<span class="text-light">-</span>';
                                            }
                                            ?>
                                        </td>
                                        <td>
                                            <?php if (!empty($veiculo['capacidade_peso'])): ?>
                                                <?php echo number_format($veiculo['capacidade_peso'], 2, ',', '.'); ?>t
                                            <?php else: ?>
                                                <span class="text-light">-</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo formatDateTime($veiculo['data_cadastro']); ?></td>
                                        <td>
                                            <a href="editar-veiculo.php?id=<?php echo $veiculo['id']; ?>" class="btn btn-sm btn-secondary" title="Editar">
                                                <i class="fas fa-edit"></i>
                                            </a>
                                        </td>
                                    </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                <?php else: ?>
                    <div class="text-center" style="padding: 60px 20px;">
                        <i class="fas fa-truck" style="font-size: 64px; color: var(--text-light); margin-bottom: 20px;"></i>
                        <h4 style="color: var(--text-color); margin-bottom: 10px;">Nenhum veículo cadastrado</h4>
                        <p style="color: var(--text-light); margin-bottom: 24px;">Comece cadastrando seu primeiro veículo para receber propostas de frete.</p>
                        <a href="cadastro-veiculo.php" class="btn btn-primary">
                            <i class="fas fa-plus"></i> Cadastrar Primeiro Veículo
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
