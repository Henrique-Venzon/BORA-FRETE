<?php
/**
 * BORAFRETE - Cadastro de Oferta de Frete
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Cadastrar Oferta de Frete';

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>Cadastrar Nova Oferta de Frete</h1>
        <p>Preencha os dados da carga e rota para disponibilizar a oferta</p>
    </div>

    <form action="<?php echo BASE_URL; ?>processamento/salvar_oferta.php" method="POST" class="form-offer glass-card">

        <!-- ROTA -->
        <div class="form-section">
            <h3 class="section-title">Rota</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="origem_uf">Origem - Estado *</label>
                    <select name="origem_uf" id="origem_uf" required onchange="carregarCidades('origem')">
                        <option value="">Selecione o estado...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="origem_cidade">Origem - Cidade *</label>
                    <select name="origem_cidade" id="origem_cidade" required disabled>
                        <option value="">Primeiro selecione o estado</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="destino_uf">Destino - Estado *</label>
                    <select name="destino_uf" id="destino_uf" required onchange="carregarCidades('destino')">
                        <option value="">Selecione o estado...</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="destino_cidade">Destino - Cidade *</label>
                    <select name="destino_cidade" id="destino_cidade" required disabled>
                        <option value="">Primeiro selecione o estado</option>
                    </select>
                </div>
            </div>
        </div>

        <!-- DATAS -->
        <div class="form-section">
            <h3 class="section-title">Datas e Horários</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="data_carregamento">Data de Carregamento *</label>
                    <input type="date" name="data_carregamento" id="data_carregamento" required>
                </div>

                <div class="form-group">
                    <label for="hora_carregamento">Hora de Carregamento (Opcional)</label>
                    <input type="time" name="hora_carregamento" id="hora_carregamento">
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="data_entrega">Data de Entrega *</label>
                    <input type="date" name="data_entrega" id="data_entrega" required>
                </div>

                <div class="form-group">
                    <label for="hora_entrega">Hora de Entrega (Opcional)</label>
                    <input type="time" name="hora_entrega" id="hora_entrega">
                </div>
            </div>
        </div>

        <!-- VEÍCULO -->
        <div class="form-section">
            <h3 class="section-title">Veículo</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_veiculo">Tipo de Veículo *</label>
                    <select name="tipo_veiculo" id="tipo_veiculo" required>
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

                <div class="form-group">
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
        </div>

        <!-- CARGA -->
        <div class="form-section">
            <h3 class="section-title">Informações da Carga</h3>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_carga">Tipo de Carga *</label>
                    <select name="tipo_carga" id="tipo_carga" required>
                        <option value="">Selecione...</option>
                        <option value="seca">Seca</option>
                        <option value="refrigerada">Refrigerada</option>
                        <option value="congelada">Congelada</option>
                        <option value="perigosa">Perigosa</option>
                        <option value="quimica">Química</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="modelo_carga">Modelo da Carga *</label>
                    <select name="modelo_carga" id="modelo_carga" required>
                        <option value="">Selecione...</option>
                        <option value="caixas">Caixas</option>
                        <option value="maquinario">Maquinário</option>
                        <option value="sacarias">Sacarias</option>
                        <option value="racao">Ração</option>
                        <option value="roupa">Roupa</option>
                        <option value="eletronicos">Eletrônicos</option>
                    </select>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="peso">Peso (kg) *</label>
                    <input type="number" name="peso" id="peso" required step="0.01" min="0" placeholder="Ex: 1500">
                </div>

                <div class="form-group">
                    <label for="cubagem">Cubagem (m³)</label>
                    <input type="number" name="cubagem" id="cubagem" step="0.01" min="0" placeholder="Ex: 15.00">
                </div>

                <div class="form-group">
                    <label for="pallets">Quantidade de Pallets</label>
                    <input type="number" name="pallets" id="pallets" min="0" placeholder="Ex: 8">
                </div>
            </div>
        </div>

        <!-- FINANCEIRO -->
        <div class="form-section">
            <h3 class="section-title">Financeiro</h3>

            <div class="form-row">
                <div class="form-group full-width">
                    <label class="checkbox-label">
                        <input
                            type="checkbox"
                            name="frete_combinar"
                            id="frete_combinar"
                            value="1"
                            onchange="toggleFreteValor()"
                        >
                        <span>Frete a Combinar</span>
                    </label>
                </div>
            </div>

            <div id="valor-frete-group" class="form-row">
                <div class="form-group">
                    <label for="valor_frete">Valor do Frete (R$) *</label>
                    <input
                        type="number"
                        name="valor_frete"
                        id="valor_frete"
                        step="0.01"
                        min="0"
                        placeholder="Ex: 2500.00"
                        required
                    >
                </div>

                <div class="form-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="pedagio_incluso" id="pedagio_incluso" value="1">
                        <span>Pedágio Incluso</span>
                    </label>
                </div>
            </div>

            <div class="form-row">
                <div class="form-group">
                    <label for="tipo_pagamento">Tipo de Pagamento</label>
                    <select name="tipo_pagamento" id="tipo_pagamento">
                        <option value="">Selecione...</option>
                        <option value="Pamcard">Pamcard</option>
                        <option value="Repom">Repom</option>
                        <option value="Ticket">Ticket</option>
                        <option value="PIX">PIX</option>
                        <option value="Transferência">Transferência</option>
                        <option value="Dinheiro">Dinheiro</option>
                    </select>
                </div>

                <div class="form-group">
                    <label for="fator_pagamento">Fator de Pagamento</label>
                    <select name="fator_pagamento" id="fator_pagamento">
                        <option value="">Selecione...</option>
                        <option value="100/0">100% Adiantado</option>
                        <option value="70/30">70% Adiantado / 30% na Entrega</option>
                        <option value="50/50">50% Adiantado / 50% na Entrega</option>
                        <option value="0/100">100% na Entrega</option>
                        <option value="custom">Outro</option>
                    </select>
                </div>
            </div>
        </div>

        <div class="form-actions">
            <a href="<?php echo BASE_URL; ?>views/dashboard.php" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Cadastrar Oferta</button>
        </div>

    </form>

</div>

<script>
// Toggle campo de valor do frete
function toggleFreteValor() {
    const freteCombinar = document.getElementById('frete_combinar').checked;
    const valorFreteGroup = document.getElementById('valor-frete-group');
    const valorFreteInput = document.getElementById('valor_frete');

    if (freteCombinar) {
        valorFreteGroup.style.display = 'none';
        valorFreteInput.required = false;
        valorFreteInput.value = '';
    } else {
        valorFreteGroup.style.display = 'flex';
        valorFreteInput.required = true;
    }
}

// Carregar estados ao iniciar a página
document.addEventListener('DOMContentLoaded', function() {
    carregarEstados();
});
</script>

<?php require_once 'layout/footer.php'; ?>
