<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Cadastrar Veículo';
$showSidebar = true;
$customScripts = ['veiculo-placas.js'];

include 'views/layout/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cadastrar Novo Veículo</h3>
            </div>
            <div class="card-body">

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success">
                        Veículo cadastrado com sucesso!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger">
                        Erro ao cadastrar veículo. Por favor, verifique os dados e tente novamente.
                    </div>
                <?php endif; ?>

                <form action="processamento/salvar-veiculo.php" method="POST" id="formVeiculo">

                    <h5 class="mb-3">Informações Básicas</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_veiculo" class="form-label">Tipo de Veículo *</label>
                            <select class="form-select" id="tipo_veiculo" name="tipo_veiculo" required>
                                <option value="">Selecione...</option>
                                <option value="van">Van</option>
                                <option value="truck">Truck</option>
                                <option value="3/4">3/4 (Três Quartos)</option>
                                <option value="toco">Toco</option>
                                <option value="carreta">Carreta (Cavalo + Carreta)</option>
                                <option value="bitrem">Bitrem</option>
                                <option value="rodotrem">Rodotrem</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo_carroceria" class="form-label">Tipo de Carroceria *</label>
                            <select class="form-select" id="tipo_carroceria" name="tipo_carroceria" required>
                                <option value="">Selecione...</option>
                                <option value="bau">Baú</option>
                                <option value="sider">Sider</option>
                                <option value="graneleiro">Graneleiro</option>
                                <option value="cacamba">Caçamba</option>
                                <option value="refrigerado">Refrigerado</option>
                                <option value="porta-container">Porta Container</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Placas do Veículo</h5>

                    <!-- Campo de placa do cavalo (sempre visível) -->
                    <div class="form-row" id="placa-cavalo-container">
                        <div class="form-group">
                            <label for="placa_cavalo" class="form-label">Placa do Veículo/Cavalo *</label>
                            <input type="text" class="form-control" id="placa_cavalo" name="placa_cavalo" placeholder="ABC-1234" maxlength="8" required>
                        </div>

                        <div class="form-group">
                            <label for="renavam_cavalo" class="form-label">Renavam</label>
                            <input type="text" class="form-control" id="renavam_cavalo" name="renavam_cavalo" placeholder="00000000000">
                        </div>
                    </div>

                    <!-- Campo de placa da carreta (oculto por padrão) -->
                    <div class="form-row hidden" id="placa-carreta-container">
                        <div class="form-group">
                            <label for="placa_carreta" class="form-label">Placa da Carreta *</label>
                            <input type="text" class="form-control" id="placa_carreta" name="placa_carreta" placeholder="ABC-1234" maxlength="8">
                        </div>

                        <div class="form-group">
                            <label for="renavam_carreta" class="form-label">Renavam da Carreta</label>
                            <input type="text" class="form-control" id="renavam_carreta" name="renavam_carreta" placeholder="00000000000">
                        </div>
                    </div>

                    <!-- Campo de placa da segunda carreta (oculto por padrão) -->
                    <div class="form-row hidden" id="placa-carreta2-container">
                        <div class="form-group">
                            <label for="placa_carreta2" class="form-label">Placa da Segunda Carreta *</label>
                            <input type="text" class="form-control" id="placa_carreta2" name="placa_carreta2" placeholder="ABC-1234" maxlength="8">
                        </div>

                        <div class="form-group">
                            <label for="renavam_carreta2" class="form-label">Renavam da Segunda Carreta</label>
                            <input type="text" class="form-control" id="renavam_carreta2" name="renavam_carreta2" placeholder="00000000000">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Informações do Veículo</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="marca_cavalo" class="form-label">Marca</label>
                            <input type="text" class="form-control" id="marca_cavalo" name="marca_cavalo" placeholder="Ex: Scania">
                        </div>

                        <div class="form-group">
                            <label for="modelo_cavalo" class="form-label">Modelo</label>
                            <input type="text" class="form-control" id="modelo_cavalo" name="modelo_cavalo" placeholder="Ex: R-450">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="ano_fabricacao_cavalo" class="form-label">Ano de Fabricação</label>
                            <input type="number" class="form-control" id="ano_fabricacao_cavalo" name="ano_fabricacao_cavalo" placeholder="2020" min="1900" max="2100">
                        </div>

                        <div class="form-group">
                            <label for="ano_modelo_cavalo" class="form-label">Ano do Modelo</label>
                            <input type="number" class="form-control" id="ano_modelo_cavalo" name="ano_modelo_cavalo" placeholder="2021" min="1900" max="2100">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Capacidades e Dimensões</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="capacidade_peso" class="form-label">Capacidade de Peso (Toneladas)</label>
                            <input type="number" step="0.01" class="form-control" id="capacidade_peso" name="capacidade_peso" placeholder="30.00">
                        </div>

                        <div class="form-group">
                            <label for="capacidade_volume" class="form-label">Capacidade de Volume (m³)</label>
                            <input type="number" step="0.01" class="form-control" id="capacidade_volume" name="capacidade_volume" placeholder="85.00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="comprimento" class="form-label">Comprimento (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="comprimento" name="comprimento" placeholder="13.50">
                        </div>

                        <div class="form-group">
                            <label for="largura" class="form-label">Largura (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="largura" name="largura" placeholder="2.60">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="altura" class="form-label">Altura (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="altura" name="altura" placeholder="2.90">
                        </div>

                        <div class="form-group">
                            <label for="antt" class="form-label">Registro ANTT</label>
                            <input type="text" class="form-control" id="antt" name="antt" placeholder="00000000">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="4" placeholder="Informações adicionais sobre o veículo..."></textarea>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Veículo
                        </button>
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i> Cancelar
                        </a>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php
include 'views/layout/footer.php';
?>
