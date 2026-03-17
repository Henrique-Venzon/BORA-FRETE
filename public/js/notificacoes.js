/**
 * BORAFRETE - Sistema de Notificações
 */

class SistemaNotificacoes {
    constructor() {
        this.notificacoes = [];
        this.naoLidas = 0;
        this.dropdown = null;
        this.init();
    }

    init() {
        this.criarDropdown();
        this.carregarNotificacoes();
        this.setupEventListeners();

        // Atualizar a cada 30 segundos
        setInterval(() => this.carregarNotificacoes(), 30000);
    }

    criarDropdown() {
        const dropdown = document.createElement('div');
        dropdown.id = 'dropdown-notificacoes';
        dropdown.className = 'dropdown-notificacoes';
        dropdown.innerHTML = `
            <div class="dropdown-header">
                <h3>Notificações</h3>
                <button class="btn-marcar-todas" onclick="notificacoes.marcarTodasLidas()">
                    Marcar todas como lidas
                </button>
            </div>
            <div class="dropdown-content" id="lista-notificacoes">
                <div class="loading">Carregando...</div>
            </div>
        `;
        document.body.appendChild(dropdown);
        this.dropdown = dropdown;
    }

    setupEventListeners() {
        const btnSino = document.querySelector('.notification-bell');
        if (btnSino) {
            btnSino.addEventListener('click', (e) => {
                e.stopPropagation();
                this.toggleDropdown();
            });
        }

        // Fechar ao clicar fora
        document.addEventListener('click', (e) => {
            if (!this.dropdown.contains(e.target) && !e.target.closest('.notification-bell')) {
                this.fecharDropdown();
            }
        });
    }

    async carregarNotificacoes() {
        try {
            const response = await fetch(BASE_URL + 'processamento/api_notificacoes.php?acao=listar');
            const data = await response.json();

            if (data.sucesso) {
                this.notificacoes = data.notificacoes;
                this.naoLidas = data.nao_lidas;
                this.atualizarBadge();
                this.renderizarNotificacoes();
            }
        } catch (error) {
            console.error('Erro ao carregar notificações:', error);
        }
    }

    atualizarBadge() {
        const badge = document.querySelector('.notification-badge');
        if (badge) {
            if (this.naoLidas > 0) {
                badge.textContent = this.naoLidas > 99 ? '99+' : this.naoLidas;
                badge.style.display = 'flex';
            } else {
                badge.style.display = 'none';
            }
        }
    }

    renderizarNotificacoes() {
        const lista = document.getElementById('lista-notificacoes');
        if (!lista) return;

        if (this.notificacoes.length === 0) {
            lista.innerHTML = `
                <div class="notificacao-vazia">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 22C13.1 22 14 21.1 14 20H10C10 21.1 10.9 22 12 22ZM18 16V11C18 7.93 16.37 5.36 13.5 4.68V4C13.5 3.17 12.83 2.5 12 2.5C11.17 2.5 10.5 3.17 10.5 4V4.68C7.64 5.36 6 7.92 6 11V16L4 18V19H20V18L18 16Z" fill="#ccc"/>
                    </svg>
                    <p>Nenhuma notificação</p>
                </div>
            `;
            return;
        }

        lista.innerHTML = this.notificacoes.map(notif => `
            <div class="item-notificacao ${notif.lida == 0 ? 'nao-lida' : ''}" data-id="${notif.id}">
                <div class="notif-icone ${notif.tipo}">
                    ${this.getIcone(notif.tipo)}
                </div>
                <div class="notif-conteudo">
                    <div class="notif-titulo">${notif.titulo}</div>
                    <div class="notif-mensagem">${notif.mensagem}</div>
                    <div class="notif-tempo">${this.formatarTempo(notif.created_at)}</div>
                </div>
                <div class="notif-acoes">
                    ${notif.lida == 0 ? `
                        <button onclick="notificacoes.marcarLida(${notif.id})" title="Marcar como lida">
                            ✓
                        </button>
                    ` : ''}
                    <button onclick="notificacoes.deletar(${notif.id})" title="Excluir">
                        ✕
                    </button>
                </div>
            </div>
        `).join('');
    }

    getIcone(tipo) {
        const icones = {
            'oferta': '📦',
            'veiculo': '🚛',
            'mensagem': '💬',
            'alerta': '⚠️',
            'sucesso': '✅',
            'info': 'ℹ️'
        };
        return icones[tipo] || 'ℹ️';
    }

    formatarTempo(dataStr) {
        const data = new Date(dataStr);
        const agora = new Date();
        const diff = Math.floor((agora - data) / 1000); // diferença em segundos

        if (diff < 60) return 'agora mesmo';
        if (diff < 3600) return Math.floor(diff / 60) + ' min atrás';
        if (diff < 86400) return Math.floor(diff / 3600) + ' h atrás';
        if (diff < 604800) return Math.floor(diff / 86400) + ' dias atrás';

        return data.toLocaleDateString('pt-BR');
    }

    toggleDropdown() {
        if (this.dropdown.classList.contains('aberto')) {
            this.fecharDropdown();
        } else {
            this.abrirDropdown();
        }
    }

    abrirDropdown() {
        this.dropdown.classList.add('aberto');
    }

    fecharDropdown() {
        this.dropdown.classList.remove('aberto');
    }

    async marcarLida(id) {
        try {
            const formData = new FormData();
            formData.append('id', id);

            await fetch(BASE_URL + 'processamento/api_notificacoes.php?acao=marcar_lida', {
                method: 'POST',
                body: formData
            });

            await this.carregarNotificacoes();
        } catch (error) {
            console.error('Erro ao marcar como lida:', error);
        }
    }

    async marcarTodasLidas() {
        try {
            await fetch(BASE_URL + 'processamento/api_notificacoes.php?acao=marcar_todas_lidas', {
                method: 'POST'
            });

            await this.carregarNotificacoes();
        } catch (error) {
            console.error('Erro ao marcar todas como lidas:', error);
        }
    }

    async deletar(id) {
        try {
            const formData = new FormData();
            formData.append('id', id);

            await fetch(BASE_URL + 'processamento/api_notificacoes.php?acao=deletar', {
                method: 'POST',
                body: formData
            });

            await this.carregarNotificacoes();
        } catch (error) {
            console.error('Erro ao deletar notificação:', error);
        }
    }

    // Método para criar nova notificação (usada internamente)
    static async criar(titulo, mensagem, tipo = 'info') {
        try {
            const formData = new FormData();
            formData.append('titulo', titulo);
            formData.append('mensagem', mensagem);
            formData.append('tipo', tipo);

            await fetch(BASE_URL + 'processamento/criar_notificacao.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Erro ao criar notificação:', error);
        }
    }
}

// Inicializar sistema de notificações
let notificacoes;
document.addEventListener('DOMContentLoaded', () => {
    notificacoes = new SistemaNotificacoes();
});
