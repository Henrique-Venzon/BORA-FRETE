/**
 * BORAFRETE - Integração com API IBGE
 * Carregamento de Estados e Cidades do Brasil
 */

// ========================================
// CONFIGURAÇÕES DA API
// ========================================
const IBGE_API = {
    estados: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados',
    municipios: 'https://servicodados.ibge.gov.br/api/v1/localidades/estados/{UF}/municipios'
};

// ========================================
// CACHE DE DADOS
// ========================================
let estadosCache = null;
let municipiosCache = {};

// ========================================
// CARREGAR ESTADOS
// ========================================

/**
 * Carregar lista de estados do IBGE
 */
async function carregarEstados() {
    try {
        // Se já temos os estados em cache, não precisa buscar de novo
        if (estadosCache) {
            popularSelectEstados();
            return;
        }

        const response = await fetch(IBGE_API.estados);

        if (!response.ok) {
            throw new Error('Erro ao carregar estados');
        }

        const estados = await response.json();

        // Ordenar estados por nome
        estadosCache = estados.sort((a, b) => a.nome.localeCompare(b.nome));

        // Popular os selects de estado na página
        popularSelectEstados();

    } catch (error) {
        console.error('Erro ao carregar estados:', error);
        alert('Erro ao carregar estados. Tente novamente.');
    }
}

/**
 * Popular campos select de estados
 */
function popularSelectEstados() {
    if (!estadosCache) return;

    // Origem
    const origemUF = document.getElementById('origem_uf');
    if (origemUF) {
        origemUF.innerHTML = '<option value="">Selecione o estado...</option>';
        estadosCache.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado.sigla;
            option.textContent = `${estado.sigla} - ${estado.nome}`;
            origemUF.appendChild(option);
        });
    }

    // Destino
    const destinoUF = document.getElementById('destino_uf');
    if (destinoUF) {
        destinoUF.innerHTML = '<option value="">Selecione o estado...</option>';
        estadosCache.forEach(estado => {
            const option = document.createElement('option');
            option.value = estado.sigla;
            option.textContent = `${estado.sigla} - ${estado.nome}`;
            destinoUF.appendChild(option);
        });
    }
}

// ========================================
// CARREGAR CIDADES
// ========================================

/**
 * Carregar cidades de um estado
 * @param {string} tipo - 'origem' ou 'destino'
 */
async function carregarCidades(tipo) {
    const ufSelect = document.getElementById(`${tipo}_uf`);
    const cidadeSelect = document.getElementById(`${tipo}_cidade`);

    if (!ufSelect || !cidadeSelect) return;

    const uf = ufSelect.value;

    // Resetar select de cidade
    cidadeSelect.innerHTML = '<option value="">Carregando...</option>';
    cidadeSelect.disabled = true;

    if (!uf) {
        cidadeSelect.innerHTML = '<option value="">Primeiro selecione o estado</option>';
        return;
    }

    try {
        // Verificar cache
        let municipios;

        if (municipiosCache[uf]) {
            municipios = municipiosCache[uf];
        } else {
            // Buscar da API
            const url = IBGE_API.municipios.replace('{UF}', uf);
            const response = await fetch(url);

            if (!response.ok) {
                throw new Error('Erro ao carregar municípios');
            }

            municipios = await response.json();

            // Ordenar por nome
            municipios.sort((a, b) => a.nome.localeCompare(b.nome));

            // Salvar no cache
            municipiosCache[uf] = municipios;
        }

        // Popular select
        cidadeSelect.innerHTML = '<option value="">Selecione a cidade...</option>';

        municipios.forEach(municipio => {
            const option = document.createElement('option');
            option.value = municipio.nome;
            option.textContent = municipio.nome;
            cidadeSelect.appendChild(option);
        });

        cidadeSelect.disabled = false;

    } catch (error) {
        console.error('Erro ao carregar cidades:', error);
        cidadeSelect.innerHTML = '<option value="">Erro ao carregar cidades</option>';
        alert('Erro ao carregar cidades. Tente novamente.');
    }
}

// ========================================
// BUSCAR CIDADE POR NOME
// ========================================

/**
 * Buscar cidade específica (para autocomplete)
 * @param {string} termo - Termo de busca
 * @param {string} uf - Sigla do estado
 * @returns {Array} Lista de cidades que correspondem ao termo
 */
async function buscarCidade(termo, uf) {
    if (!uf || !termo || termo.length < 3) return [];

    try {
        // Garantir que temos os municípios do estado
        if (!municipiosCache[uf]) {
            const url = IBGE_API.municipios.replace('{UF}', uf);
            const response = await fetch(url);
            const municipios = await response.json();
            municipiosCache[uf] = municipios;
        }

        // Filtrar cidades que correspondem ao termo
        const termoLower = termo.toLowerCase();
        return municipiosCache[uf].filter(m =>
            m.nome.toLowerCase().includes(termoLower)
        );

    } catch (error) {
        console.error('Erro ao buscar cidade:', error);
        return [];
    }
}

// ========================================
// VALIDAÇÃO
// ========================================

/**
 * Validar se a cidade existe no estado selecionado
 * @param {string} cidade - Nome da cidade
 * @param {string} uf - Sigla do estado
 * @returns {boolean}
 */
function validarCidade(cidade, uf) {
    if (!municipiosCache[uf]) return false;

    return municipiosCache[uf].some(m =>
        m.nome.toLowerCase() === cidade.toLowerCase()
    );
}

/**
 * Validar UF
 * @param {string} uf - Sigla do estado
 * @returns {boolean}
 */
function validarUF(uf) {
    if (!estadosCache) return false;

    return estadosCache.some(e => e.sigla === uf);
}

// ========================================
// UTILITÁRIOS
// ========================================

/**
 * Obter nome completo do estado pela sigla
 * @param {string} sigla - Sigla do estado (ex: SP)
 * @returns {string} Nome completo do estado
 */
function getNomeEstado(sigla) {
    if (!estadosCache) return sigla;

    const estado = estadosCache.find(e => e.sigla === sigla);
    return estado ? estado.nome : sigla;
}

/**
 * Obter informações completas de um município
 * @param {string} nomeCidade - Nome da cidade
 * @param {string} uf - Sigla do estado
 * @returns {Object|null} Dados do município
 */
function getInfoMunicipio(nomeCidade, uf) {
    if (!municipiosCache[uf]) return null;

    return municipiosCache[uf].find(m =>
        m.nome.toLowerCase() === nomeCidade.toLowerCase()
    );
}

// ========================================
// EXPORTAR FUNÇÕES PARA USO GLOBAL
// ========================================

// Tornar funções disponíveis globalmente
window.carregarEstados = carregarEstados;
window.carregarCidades = carregarCidades;
window.buscarCidade = buscarCidade;
window.validarCidade = validarCidade;
window.validarUF = validarUF;
window.getNomeEstado = getNomeEstado;
window.getInfoMunicipio = getInfoMunicipio;

// ========================================
// INICIALIZAÇÃO AUTOMÁTICA
// ========================================

/**
 * Inicializar automaticamente quando a página carregar
 */
document.addEventListener('DOMContentLoaded', function() {

    // Verificar se existem campos de UF na página
    const origemUF = document.getElementById('origem_uf');
    const destinoUF = document.getElementById('destino_uf');

    if (origemUF || destinoUF) {
        // Carregar estados automaticamente
        carregarEstados();

        // Adicionar listeners para mudança de estado
        if (origemUF) {
            origemUF.addEventListener('change', function() {
                carregarCidades('origem');
            });
        }

        if (destinoUF) {
            destinoUF.addEventListener('change', function() {
                carregarCidades('destino');
            });
        }

        console.log('IBGE API - Integração inicializada com sucesso!');
    }
});

// ========================================
// PRÉ-CARREGAR DADOS (OPCIONAL)
// ========================================

/**
 * Pré-carregar dados de estados mais comuns
 * Útil para melhorar performance
 */
async function precarregarDados() {
    // Estados mais populosos do Brasil
    const estadosComuns = ['SP', 'RJ', 'MG', 'BA', 'PR', 'RS', 'PE', 'CE', 'PA', 'SC'];

    for (const uf of estadosComuns) {
        if (!municipiosCache[uf]) {
            try {
                const url = IBGE_API.municipios.replace('{UF}', uf);
                const response = await fetch(url);
                const municipios = await response.json();
                municipios.sort((a, b) => a.nome.localeCompare(b.nome));
                municipiosCache[uf] = municipios;
            } catch (error) {
                console.error(`Erro ao pré-carregar dados de ${uf}:`, error);
            }
        }
    }

    console.log('IBGE API - Dados pré-carregados com sucesso!');
}

// Iniciar pré-carregamento após 2 segundos (não bloqueia a página)
setTimeout(precarregarDados, 2000);
