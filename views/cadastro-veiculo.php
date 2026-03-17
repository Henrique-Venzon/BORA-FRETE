<?php
/**
 * BORAFRETE - Cadastro de Veículo
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Cadastrar Veículo';

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>Cadastrar Novo Veículo</h1>
        <p>Preencha os dados do seu veículo para disponibilizá-lo na plataforma</p>
    </div>

    <form action="<?php echo BASE_URL; ?>processamento/salvar_veiculo.php" method="POST" enctype="multipart/form-data" class="form-vehicle glass-card">

        <div class="form-section">
            <h3 class="section-title">Informações do Veículo</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_veiculo">Tipo de Veículo *</label>
                    <select name="tipo_veiculo" id="tipo_veiculo" required onchange="handleVehicleTypeChange()">
                        <option value="">Selecione...</option>
                        <option value="van">Van</option>
                        <option value="fiorino">Fiorino</option>
                        <option value="3/4">3/4</option>
                        <option value="toco">Toco</option>
                        <option value="truck">Truck</option>
                        <option value="carreta">Carreta</option>
                        <option value="rodotrem">Rodotrem</option>
                    </select>
                </div>

                <div class="form-group" id="carroceria-group">
                    <label for="tipo_carroceria">Tipo de Carroceria</label>
                    <select name="tipo_carroceria" id="tipo_carroceria">
                        <option value="">Selecione...</option>
                        <option value="Aberta">Aberta</option>
                        <option value="Fechada/Baú">Fechada/Baú</option>
                        <option value="Sider">Sider</option>
                        <option value="Refrigerada">Refrigerada</option>
                        <option value="Graneleira">Graneleira</option>
                        <option value="Tanque">Tanque</option>
                        <option value="Caçamba">Caçamba</option>
                        <option value="Cegonha">Cegonha</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="marca">Marca/Modelo *</label>
                    <input type="text" name="marca" id="marca" required placeholder="Ex: Mercedes Sprinter">
                </div>

                <div class="form-group">
                    <label for="ano">Ano *</label>
                    <input type="number" name="ano" id="ano" required min="1990" max="2030" placeholder="Ex: 2021">
                </div>
            </div>

        </div>

        <div class="form-section">
            <h3 class="section-title">Placas</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="placa_1">Placa Principal *</label>
                    <input
                        type="text"
                        name="placa_1"
                        id="placa_1"
                        required
                        placeholder="ABC-1234"
                        maxlength="8"
                        oninput="formatPlate(this)"
                    >
                </div>

                <div class="form-group" id="placa2-group" style="display: none;">
                    <label for="placa_2">Placa 2 (Carreta)</label>
                    <input
                        type="text"
                        name="placa_2"
                        id="placa_2"
                        placeholder="ABC-1234"
                        maxlength="8"
                        oninput="formatPlate(this)"
                    >
                </div>

                <div class="form-group" id="placa3-group" style="display: none;">
                    <label for="placa_3">Placa 3 (Rodotrem)</label>
                    <input
                        type="text"
                        name="placa_3"
                        id="placa_3"
                        placeholder="ABC-1234"
                        maxlength="8"
                        oninput="formatPlate(this)"
                    >
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Capacidades</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="capacidade_peso">Peso (kg) *</label>
                    <input
                        type="number"
                        name="capacidade_peso"
                        id="capacidade_peso"
                        required
                        step="0.01"
                        min="0"
                        placeholder="Ex: 1500"
                    >
                </div>

                <div class="form-group">
                    <label for="capacidade_m3">Volume (m³)</label>
                    <input
                        type="number"
                        name="capacidade_m3"
                        id="capacidade_m3"
                        step="0.01"
                        min="0"
                        placeholder="Ex: 15.00"
                    >
                </div>

                <div class="form-group">
                    <label for="qtd_pallets">Quantidade de Pallets</label>
                    <input
                        type="number"
                        name="qtd_pallets"
                        id="qtd_pallets"
                        min="0"
                        placeholder="Ex: 8"
                    >
                </div>
            </div>
        </div>

        <div class="form-section">
            <h3 class="section-title">Foto do Veículo</h3>

            <div class="form-group">
                <label for="foto">Upload de Foto</label>
                <div class="file-upload-wrapper">
                    <input
                        type="file"
                        name="foto"
                        id="foto"
                        accept="image/*"
                        onchange="previewImage(this)"
                    >
                    <div class="file-upload-label">
                        <svg width="40" height="40" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M19 7V4H5V7H3V20H21V7H19ZM5 18V9H19V18H5ZM12 10.5L16 14.5H13V17H11V14.5H8L12 10.5Z" fill="#999"/>
                        </svg>
                        <p>Clique para selecionar uma foto</p>
                    </div>
                    <div id="imagePreview" class="image-preview"></div>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo BASE_URL; ?>views/dashboard.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Cadastrar Veículo</button>
        </div>

    </form>

</div>

<script>
// Função para controlar visibilidade das placas
function handleVehicleTypeChange() {
    const tipoVeiculo = document.getElementById('tipo_veiculo').value;
    const carroceriaGroup = document.getElementById('carroceria-group');
    const placa2Group = document.getElementById('placa2-group');
    const placa3Group = document.getElementById('placa3-group');
    const placa2Input = document.getElementById('placa_2');
    const placa3Input = document.getElementById('placa_3');

    // Ocultar campos de carroceria para Van e Fiorino
    if (tipoVeiculo === 'van' || tipoVeiculo === 'fiorino') {
        carroceriaGroup.style.display = 'none';
        document.getElementById('tipo_carroceria').value = '';
    } else {
        carroceriaGroup.style.display = 'block';
    }

    // Controlar placas
    // 1 placa: Van, Fiorino, 3/4, Toco, Truck
    // 2 placas: Carreta
    // 3 placas: Rodotrem

    if (tipoVeiculo === 'carreta') {
        placa2Group.style.display = 'block';
        placa3Group.style.display = 'none';
        placa3Input.value = '';
    } else if (tipoVeiculo === 'rodotrem') {
        placa2Group.style.display = 'block';
        placa3Group.style.display = 'block';
    } else {
        placa2Group.style.display = 'none';
        placa3Group.style.display = 'none';
        placa2Input.value = '';
        placa3Input.value = '';
    }
}

// Formatar placa (ABC-1234)
function formatPlate(input) {
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

    if (value.length > 3) {
        value = value.slice(0, 3) + '-' + value.slice(3, 7);
    }

    input.value = value;
}

// Preview da imagem
function previewImage(input) {
    const preview = document.getElementById('imagePreview');

    if (input.files && input.files[0]) {
        const reader = new FileReader();

        reader.onload = function(e) {
            preview.innerHTML = '<img src="' + e.target.result + '" alt="Preview">';
            preview.style.display = 'block';
        };

        reader.readAsDataURL(input.files[0]);
    } else {
        preview.innerHTML = '';
        preview.style.display = 'none';
    }
}
</script>

<?php require_once 'layout/footer.php'; ?>
