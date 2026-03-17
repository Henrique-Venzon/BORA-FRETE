<?php
require_once 'config/config.php';
requireLogin();

$pageTitle = 'Cadastrar Oferta de Carga';
$showSidebar = true;
$customScripts = ['ibge-api.js', 'oferta-form.js'];

include 'views/layout/header.php';
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Cadastrar Nova Oferta de Carga</h3>
            </div>
            <div class="card-body">

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success">
                        Oferta cadastrada com sucesso!
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger">
                        Erro ao cadastrar oferta. Por favor, verifique os dados e tente novamente.
                    </div>
                <?php endif; ?>

                <form action="processamento/salvar-oferta.php" method="POST" id="formOferta">

                    <h5 class="mb-3">Informações Básicas</h5>

                    <div class="form-group">
                        <label for="titulo" class="form-label">Título da Oferta *</label>
                        <input type="text" class="form-control" id="titulo" name="titulo" placeholder="Ex: Carga de eletrônicos para São Paulo" required>
                    </div>

                    <div class="form-group">
                        <label for="descricao" class="form-label">Descrição</label>
                        <textarea class="form-control" id="descricao" name="descricao" rows="3" placeholder="Detalhes adicionais sobre a carga..."></textarea>
                    </div>

                    <h5 class="mb-3 mt-4">Origem e Destino</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="uf_origem" class="form-label">UF Origem *</label>
                            <select class="form-select" id="uf_origem" name="uf_origem" required>
                                <option value="">Carregando...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cidade_origem" class="form-label">Cidade Origem *</label>
                            <select class="form-select" id="cidade_origem" name="cidade_origem" required disabled>
                                <option value="">Selecione o Estado primeiro</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="uf_destino" class="form-label">UF Destino *</label>
                            <select class="form-select" id="uf_destino" name="uf_destino" required>
                                <option value="">Carregando...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cidade_destino" class="form-label">Cidade Destino *</label>
                            <select class="form-select" id="cidade_destino" name="cidade_destino" required disabled>
                                <option value="">Selecione o Estado primeiro</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Detalhes da Carga</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_carga" class="form-label">Tipo de Carga *</label>
                            <input type="text" class="form-control" id="tipo_carga" name="tipo_carga" placeholder="Ex: Eletrônicos, Alimentos, etc." required>
                        </div>

                        <div class="form-group">
                            <label for="tipo_carroceria_necessaria" class="form-label">Tipo de Carroceria *</label>
                            <select class="form-select" id="tipo_carroceria_necessaria" name="tipo_carroceria_necessaria" required>
                                <option value="">Selecione...</option>
                                <option value="bau">Baú</option>
                                <option value="sider">Sider</option>
                                <option value="graneleiro">Graneleiro</option>
                                <option value="cacamba">Caçamba</option>
                                <option value="refrigerado">Refrigerado</option>
                                <option value="porta-container">Porta Container</option>
                                <option value="qualquer">Qualquer</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="peso_total" class="form-label">Peso Total (Toneladas)</label>
                            <input type="number" step="0.01" class="form-control" id="peso_total" name="peso_total" placeholder="30.00">
                        </div>

                        <div class="form-group">
                            <label for="valor_mercadoria" class="form-label">Valor da Mercadoria (R$)</label>
                            <input type="number" step="0.01" class="form-control" id="valor_mercadoria" name="valor_mercadoria" placeholder="50000.00">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Dimensões e Pallets</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="quantidade_pallets" class="form-label">Quantidade de Pallets</label>
                            <input type="number" class="form-control" id="quantidade_pallets" name="quantidade_pallets" placeholder="0">
                        </div>

                        <div class="form-group">
                            <label for="cubagem" class="form-label">Cubagem (m³)</label>
                            <input type="number" step="0.01" class="form-control" id="cubagem" name="cubagem" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="comprimento" class="form-label">Comprimento (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="comprimento" name="comprimento" placeholder="0.00">
                        </div>

                        <div class="form-group">
                            <label for="largura" class="form-label">Largura (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="largura" name="largura" placeholder="0.00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="altura" class="form-label">Altura (metros)</label>
                            <input type="number" step="0.01" class="form-control" id="altura" name="altura" placeholder="0.00">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Valores e Datas</h5>

                    <div class="form-check mb-3">
                        <input class="form-check-input" type="checkbox" id="frete_a_combinar" name="frete_a_combinar" value="1">
                        <label class="form-check-label" for="frete_a_combinar">
                            Frete a Combinar
                        </label>
                    </div>

                    <div class="form-row" id="valor-frete-container">
                        <div class="form-group">
                            <label for="valor_frete" class="form-label">Valor do Frete (R$) *</label>
                            <input type="number" step="0.01" class="form-control" id="valor_frete" name="valor_frete" placeholder="5000.00">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="data_coleta" class="form-label">Data de Coleta</label>
                            <input type="date" class="form-control" id="data_coleta" name="data_coleta">
                        </div>

                        <div class="form-group">
                            <label for="data_entrega" class="form-label">Data de Entrega</label>
                            <input type="date" class="form-control" id="data_entrega" name="data_entrega">
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Informações de Contato</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contato_nome" class="form-label">Nome do Contato</label>
                            <input type="text" class="form-control" id="contato_nome" name="contato_nome" placeholder="Nome completo">
                        </div>

                        <div class="form-group">
                            <label for="contato_telefone" class="form-label">Telefone do Contato</label>
                            <input type="tel" class="form-control" id="contato_telefone" name="contato_telefone" placeholder="(11) 99999-9999">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="contato_email" class="form-label">Email do Contato</label>
                        <input type="email" class="form-control" id="contato_email" name="contato_email" placeholder="contato@email.com">
                    </div>

                    <div class="form-group">
                        <label for="observacoes" class="form-label">Observações</label>
                        <textarea class="form-control" id="observacoes" name="observacoes" rows="4" placeholder="Informações adicionais..."></textarea>
                    </div>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Salvar Oferta
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
