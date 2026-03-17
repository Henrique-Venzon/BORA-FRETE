/**
 * BORAFRETE - JavaScript Principal
 * Funções gerais e lógica de placas de veículos
 */

// ========================================
// USER MENU TOGGLE
// ========================================
function toggleUserMenu() {
    const userMenu = document.getElementById('userMenu');
    if (userMenu) {
        userMenu.classList.toggle('active');
    }
}

// Fechar menu ao clicar fora
document.addEventListener('click', function(event) {
    const userMenu = document.getElementById('userMenu');
    const userProfile = document.querySelector('.user-profile');

    if (userMenu && userProfile) {
        if (!userProfile.contains(event.target) && !userMenu.contains(event.target)) {
            userMenu.classList.remove('active');
        }
    }
});

// ========================================
// MÁSCARAS DE FORMATAÇÃO
// ========================================

/**
 * Formatar CPF (000.000.000-00)
 */
function formatCPF(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d{1,2})$/, '$1-$2');
        input.value = value;
    }
}

/**
 * Formatar CNPJ (00.000.000/0000-00)
 */
function formatCNPJ(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 14) {
        value = value.replace(/(\d{2})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1.$2');
        value = value.replace(/(\d{3})(\d)/, '$1/$2');
        value = value.replace(/(\d{4})(\d{1,2})$/, '$1-$2');
        input.value = value;
    }
}

/**
 * Formatar Telefone ((00) 00000-0000)
 */
function formatPhone(input) {
    let value = input.value.replace(/\D/g, '');

    if (value.length <= 11) {
        value = value.replace(/(\d{2})(\d)/, '($1) $2');
        value = value.replace(/(\d{5})(\d)/, '$1-$2');
        input.value = value;
    }
}

/**
 * Formatar Placa de Veículo (ABC-1234 ou ABC1D23)
 */
function formatPlate(input) {
    let value = input.value.toUpperCase().replace(/[^A-Z0-9]/g, '');

    if (value.length > 3) {
        value = value.slice(0, 3) + '-' + value.slice(3, 7);
    }

    input.value = value;
}

// ========================================
// LÓGICA DE PLACAS POR TIPO DE VEÍCULO
// ========================================

/**
 * Controlar visibilidade dos campos de placa baseado no tipo de veículo
 *
 * REGRAS:
 * - Van, Fiorino, 3/4, Toco, Truck: 1 placa
 * - Carreta: 2 placas
 * - Rodotrem: 3 placas
 */
function handleVehicleTypeChange() {
    const tipoVeiculo = document.getElementById('tipo_veiculo');
    if (!tipoVeiculo) return;

    const tipoVeiculoValue = tipoVeiculo.value;
    const carroceriaGroup = document.getElementById('carroceria-group');
    const tipoCarroceria = document.getElementById('tipo_carroceria');
    const placa2Group = document.getElementById('placa2-group');
    const placa3Group = document.getElementById('placa3-group');
    const placa2Input = document.getElementById('placa_2');
    const placa3Input = document.getElementById('placa_3');

    // REGRA 1: Ocultar carroceria para Van e Fiorino
    if (tipoVeiculoValue === 'van' || tipoVeiculoValue === 'fiorino') {
        if (carroceriaGroup) {
            carroceriaGroup.style.display = 'none';
            if (tipoCarroceria) tipoCarroceria.value = '';
        }
    } else {
        if (carroceriaGroup) {
            carroceriaGroup.style.display = 'block';
        }
    }

    // REGRA 2: Controlar quantidade de placas
    // Resetar campos primeiro
    if (placa2Group) placa2Group.style.display = 'none';
    if (placa3Group) placa3Group.style.display = 'none';
    if (placa2Input) placa2Input.value = '';
    if (placa3Input) placa3Input.value = '';

    // Carreta: mostrar 2 placas
    if (tipoVeiculoValue === 'carreta') {
        if (placa2Group) placa2Group.style.display = 'block';
    }

    // Rodotrem: mostrar 3 placas
    else if (tipoVeiculoValue === 'rodotrem') {
        if (placa2Group) placa2Group.style.display = 'block';
        if (placa3Group) placa3Group.style.display = 'block';
    }
}

// ========================================
// PREVIEW DE IMAGEM
// ========================================

/**
 * Mostrar preview da imagem selecionada
 */
function previewImage(input) {
    const preview = document.getElementById('imagePreview');
    if (!preview) return;

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

// ========================================
// TOGGLE FRETE A COMBINAR
// ========================================

/**
 * Mostrar/ocultar campo de valor do frete
 */
function toggleFreteValor() {
    const freteCombinar = document.getElementById('frete_combinar');
    const valorFreteGroup = document.getElementById('valor-frete-group');
    const valorFreteInput = document.getElementById('valor_frete');

    if (!freteCombinar || !valorFreteGroup || !valorFreteInput) return;

    if (freteCombinar.checked) {
        valorFreteGroup.style.display = 'none';
        valorFreteInput.required = false;
        valorFreteInput.value = '';
    } else {
        valorFreteGroup.style.display = 'flex';
        valorFreteInput.required = true;
    }
}

// ========================================
// VALIDAÇÃO DE FORMULÁRIOS
// ========================================

/**
 * Validar CPF
 */
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]+/g, '');

    if (cpf.length !== 11 || /^(\d)\1+$/.test(cpf)) {
        return false;
    }

    let soma = 0;
    let resto;

    for (let i = 1; i <= 9; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (11 - i);
    }

    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(9, 10))) return false;

    soma = 0;
    for (let i = 1; i <= 10; i++) {
        soma += parseInt(cpf.substring(i - 1, i)) * (12 - i);
    }

    resto = (soma * 10) % 11;
    if (resto === 10 || resto === 11) resto = 0;
    if (resto !== parseInt(cpf.substring(10, 11))) return false;

    return true;
}

/**
 * Validar CNPJ
 */
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]+/g, '');

    if (cnpj.length !== 14) return false;

    if (/^(\d)\1+$/.test(cnpj)) return false;

    let tamanho = cnpj.length - 2;
    let numeros = cnpj.substring(0, tamanho);
    let digitos = cnpj.substring(tamanho);
    let soma = 0;
    let pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    let resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(0)) return false;

    tamanho = tamanho + 1;
    numeros = cnpj.substring(0, tamanho);
    soma = 0;
    pos = tamanho - 7;

    for (let i = tamanho; i >= 1; i--) {
        soma += numeros.charAt(tamanho - i) * pos--;
        if (pos < 2) pos = 9;
    }

    resultado = soma % 11 < 2 ? 0 : 11 - soma % 11;
    if (resultado != digitos.charAt(1)) return false;

    return true;
}

/**
 * Validar Email
 */
function validarEmail(email) {
    const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    return regex.test(email);
}

// ========================================
// INICIALIZAÇÃO
// ========================================

document.addEventListener('DOMContentLoaded', function() {

    // Auto-aplicar máscaras em campos específicos
    const cpfInputs = document.querySelectorAll('input[name="cpf"]');
    cpfInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatCPF(this);
        });
    });

    const cnpjInputs = document.querySelectorAll('input[name="cnpj"]');
    cnpjInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatCNPJ(this);
        });
    });

    const phoneInputs = document.querySelectorAll('input[name="telefone"]');
    phoneInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatPhone(this);
        });
    });

    // Aplicar formatação de placa em todos inputs de placa
    const plateInputs = document.querySelectorAll('input[name^="placa"]');
    plateInputs.forEach(input => {
        input.addEventListener('input', function() {
            formatPlate(this);
        });
    });

    // Listener para mudança de tipo de veículo
    const tipoVeiculoSelect = document.getElementById('tipo_veiculo');
    if (tipoVeiculoSelect) {
        tipoVeiculoSelect.addEventListener('change', handleVehicleTypeChange);
    }

    // Listener para frete a combinar
    const freteCombinarCheckbox = document.getElementById('frete_combinar');
    if (freteCombinarCheckbox) {
        freteCombinarCheckbox.addEventListener('change', toggleFreteValor);
    }

    console.log('BoraFrete - Sistema inicializado com sucesso!');
});

// ========================================
// FUNÇÕES AUXILIARES
// ========================================

/**
 * Formatar valor monetário
 */
function formatMoney(value) {
    return new Intl.NumberFormat('pt-BR', {
        style: 'currency',
        currency: 'BRL'
    }).format(value);
}

/**
 * Formatar data para padrão brasileiro
 */
function formatDate(dateString) {
    const date = new Date(dateString);
    return date.toLocaleDateString('pt-BR');
}

/**
 * Debounce para otimizar eventos
 */
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
        };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}
