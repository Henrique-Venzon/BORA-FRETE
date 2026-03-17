<?php
/**
 * BORAFRETE - Editar Veículo
 */
require_once '../config/config.php';
verificarLogin();

$veiculo_id = (int)($_GET['id'] ?? 0);

if ($veiculo_id <= 0) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

// Buscar veículo
$stmt = $pdo->prepare("SELECT * FROM veiculos WHERE id = ? AND usuario_id = ?");
$stmt->execute([$veiculo_id, $_SESSION['usuario_id']]);
$veiculo = $stmt->fetch();

if (!$veiculo) {
    setFlashMessage('error', 'Veículo não encontrado');
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

$pageTitle = 'Editar Veículo';
require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>Editar Veículo</h1>
        <p>Atualize as informações do seu veículo</p>
    </div>

    <form action="<?php echo BASE_URL; ?>processamento/editar_veiculo.php" method="POST" enctype="multipart/form-data" class="form-vehicle glass-card">

        <input type="hidden" name="veiculo_id" value="<?php echo $veiculo['id']; ?>">

        <div class="form-section">
            <h3 class="section-title">Informações do Veículo</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_veiculo">Tipo de Veículo *</label>
                    <select name="tipo_veiculo" id="tipo_veiculo" required>
                        <option value="">Selecione...</option>
                        <option value="van" <?php echo $veiculo['tipo_veiculo'] === 'van' ? 'selected' : ''; ?>>Van</option>
                        <option value="fiorino" <?php echo $veiculo['tipo_veiculo'] === 'fiorino' ? 'selected' : ''; ?>>Fiorino</option>
                        <option value="3/4" <?php echo $veiculo['tipo_veiculo'] === '3/4' ? 'selected' : ''; ?>>3/4</option>
                        <option value="toco" <?php echo $veiculo['tipo_veiculo'] === 'toco' ? 'selected' : ''; ?>>Toco</option>
                        <option value="truck" <?php echo $veiculo['tipo_veiculo'] === 'truck' ? 'selected' : ''; ?>>Truck</option>
                        <option value="carreta" <?php echo $veiculo['tipo_veiculo'] === 'carreta' ? 'selected' : ''; ?>>Carreta</option>
                        <option value="rodotrem" <?php echo $veiculo['tipo_veiculo'] === 'rodotrem' ? 'selected' : ''; ?>>Rodotrem</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="tipo_carroceria">Tipo de Carroceria</label>
                    <select name="tipo_carroceria" id="tipo_carroceria">
                        <option value="">Selecione...</option>
                        <option value="Aberta" <?php echo $veiculo['tipo_carroceria'] === 'Aberta' ? 'selected' : ''; ?>>Aberta</option>
                        <option value="Fechada/Baú" <?php echo $veiculo['tipo_carroceria'] === 'Fechada/Baú' ? 'selected' : ''; ?>>Fechada/Baú</option>
                        <option value="Sider" <?php echo $veiculo['tipo_carroceria'] === 'Sider' ? 'selected' : ''; ?>>Sider</option>
                        <option value="Refrigerada" <?php echo $veiculo['tipo_carroceria'] === 'Refrigerada' ? 'selected' : ''; ?>>Refrigerada</option>
                        <option value="Graneleira" <?php echo $veiculo['tipo_carroceria'] === 'Graneleira' ? 'selected' : ''; ?>>Graneleira</option>
                        <option value="Tanque" <?php echo $veiculo['tipo_carroceria'] === 'Tanque' ? 'selected' : ''; ?>>Tanque</option>
                        <option value="Caçamba" <?php echo $veiculo['tipo_carroceria'] === 'Caçamba' ? 'selected' : ''; ?>>Caçamba</option>
                        <option value="Cegonha" <?php echo $veiculo['tipo_carroceria'] === 'Cegonha' ? 'selected' : ''; ?>>Cegonha</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="marca">Marca/Modelo *</label>
                    <input type="text" name="marca" id="marca" required value="<?php echo htmlspecialchars($veiculo['marca']); ?>">
                </div>

                <div class="form-group">
                    <label for="ano">Ano *</label>
                    <input type="number" name="ano" id="ano" required min="1990" max="2030" value="<?php echo $veiculo['ano']; ?>">
                </div>
            </div>

        </div>

        <div class="form-section">
            <h3 class="section-title">Placas</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="placa_1">Placa Principal *</label>
                    <input type="text" name="placa_1" id="placa_1" required maxlength="8" value="<?php echo htmlspecialchars($veiculo['placa_1']); ?>">
                </div>

                <div class="form-group">
                    <label for="placa_2">Placa 2 (Reboque)</label>
                    <input type="text" name="placa_2" id="placa_2" maxlength="8" value="<?php echo htmlspecialchars($veiculo['placa_2'] ?? ''); ?>">
                </div>

                <div class="form-group">
                    <label for="placa_3">Placa 3 (Reboque 2)</label>
                    <input type="text" name="placa_3" id="placa_3" maxlength="8" value="<?php echo htmlspecialchars($veiculo['placa_3'] ?? ''); ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Capacidades</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="capacidade_peso">Capacidade de Peso (kg) *</label>
                    <input type="number" name="capacidade_peso" id="capacidade_peso" required step="0.01" value="<?php echo $veiculo['capacidade_peso']; ?>">
                </div>

                <div class="form-group">
                    <label for="capacidade_m3">Capacidade Cúbica (m³)</label>
                    <input type="number" name="capacidade_m3" id="capacidade_m3" step="0.01" value="<?php echo $veiculo['capacidade_m3'] ?? ''; ?>">
                </div>

                <div class="form-group">
                    <label for="qtd_pallets">Quantidade de Pallets</label>
                    <input type="number" name="qtd_pallets" id="qtd_pallets" value="<?php echo $veiculo['qtd_pallets'] ?? ''; ?>">
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Foto do Veículo</h3>

            <?php if ($veiculo['foto']): ?>
                <div class="current-photo">
                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($veiculo['foto']); ?>" alt="Foto Atual" style="max-width: 300px; border-radius: 12px;">
                    <p><small>Foto atual - envie uma nova para substituir</small></p>
                </div>
            <?php endif; ?>

            <div class="form-group">
                <label for="foto">Nova Foto (opcional)</label>
                <input type="file" name="foto" id="foto" accept="image/*">
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo BASE_URL; ?>views/dashboard.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Salvar Alterações</button>
        </div>

    </form>

</div>

<style>
.current-photo {
    margin-bottom: 20px;
    padding: 16px;
    background: var(--cinza-claro);
    border-radius: 12px;
}

.current-photo img {
    display: block;
    margin-bottom: 12px;
}

.form-actions {
    display: flex;
    gap: 12px;
    justify-content: flex-end;
    margin-top: 32px;
    padding-top: 24px;
    border-top: 2px solid var(--cinza-medio);
}
</style>

<?php require_once 'layout/footer.php'; ?>
