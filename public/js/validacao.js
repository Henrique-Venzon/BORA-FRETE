/**
 * BORAFRETE - Validação de Documentos
 */

// Validar CPF
function validarCPF(cpf) {
    cpf = cpf.replace(/[^\d]/g, '');

    if (cpf.length !== 11 || /^(\d)\1{10}$/.test(cpf)) {
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

// Validar CNPJ
function validarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]/g, '');

    if (cnpj.length !== 14 || /^(\d)\1{13}$/.test(cnpj)) {
        return false;
    }

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

// Consultar CNPJ na ReceitaWS (API Pública)
async function consultarCNPJ(cnpj) {
    cnpj = cnpj.replace(/[^\d]/g, '');

    if (!validarCNPJ(cnpj)) {
        return { sucesso: false, erro: 'CNPJ inválido' };
    }

    try {
        const response = await fetch(`https://brasilapi.com.br/api/cnpj/v1/${cnpj}`);

        if (!response.ok) {
            return { sucesso: false, erro: 'CNPJ não encontrado' };
        }

        const dados = await response.json();

        return {
            sucesso: true,
            razao_social: dados.razao_social || dados.nome_fantasia,
            nome_fantasia: dados.nome_fantasia,
            situacao: dados.descricao_situacao_cadastral,
            data_abertura: dados.data_inicio_atividade,
            uf: dados.uf,
            cidade: dados.municipio,
            dados_completos: dados
        };

    } catch (error) {
        console.error('Erro ao consultar CNPJ:', error);
        return { sucesso: false, erro: 'Erro ao consultar CNPJ. Tente novamente.' };
    }
}

// Validar IE (Inscrição Estadual) - Validação básica
function validarIE(ie, uf) {
    ie = ie.replace(/[^\d]/g, '');

    if (ie.length < 8 || ie.length > 14) {
        return false;
    }

    // Validação básica - apenas formato
    // Implementação completa por estado seria muito extensa
    return true;
}

// Validar formulário de cadastro
function validarFormularioCadastro() {
    const tipoDoc = document.getElementById('documento_tipo').value;
    const numeroDoc = document.getElementById('documento_numero').value;

    if (tipoDoc === 'cpf') {
        if (!validarCPF(numeroDoc)) {
            alert('CPF inválido! Verifique o número digitado.');
            return false;
        }
    } else if (tipoDoc === 'cnpj') {
        if (!validarCNPJ(numeroDoc)) {
            alert('CNPJ inválido! Verifique o número digitado.');
            return false;
        }
    }

    return true;
}

// Consultar CNPJ e preencher dados automaticamente
async function consultarEPreencherCNPJ() {
    const cnpjInput = document.getElementById('documento_numero');
    const cnpj = cnpjInput.value.replace(/[^\d]/g, '');

    if (cnpj.length !== 14) {
        return;
    }

    const btn = document.getElementById('btn-consultar-cnpj');
    if (btn) {
        btn.disabled = true;
        btn.textContent = 'Consultando...';
    }

    const resultado = await consultarCNPJ(cnpj);

    if (resultado.sucesso) {
        // Preencher razão social se estiver vazio
        const razaoSocialInput = document.getElementById('nome_razao_social');
        if (razaoSocialInput && !razaoSocialInput.value) {
            razaoSocialInput.value = resultado.razao_social;
        }

        // Mostrar mensagem de sucesso
        mostrarMensagem('success', `CNPJ válido: ${resultado.razao_social}`);
    } else {
        mostrarMensagem('error', resultado.erro);
    }

    if (btn) {
        btn.disabled = false;
        btn.textContent = 'Consultar CNPJ';
    }
}

// Mostrar mensagem temporária
function mostrarMensagem(tipo, mensagem) {
    const existente = document.querySelector('.alert-flutuante');
    if (existente) {
        existente.remove();
    }

    const div = document.createElement('div');
    div.className = `alert alert-${tipo} alert-flutuante`;
    div.textContent = mensagem;
    div.style.cssText = `
        position: fixed;
        top: 20px;
        right: 20px;
        z-index: 9999;
        animation: slideIn 0.3s ease;
        max-width: 400px;
    `;

    document.body.appendChild(div);

    setTimeout(() => {
        div.style.animation = 'slideOut 0.3s ease';
        setTimeout(() => div.remove(), 300);
    }, 4000);
}
