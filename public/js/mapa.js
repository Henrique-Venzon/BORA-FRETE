/**
 * BORAFRETE - Sistema de Mapa com Geolocalização e Ofertas
 */

class MapaBoraFrete {
    constructor(elementId) {
        this.elementId = elementId;
        this.map = null;
        this.userMarker = null;
        this.ofertasMarkers = [];
        this.userLocation = null;
        this.init();
    }

    async init() {
        await this.initMap();
        await this.getUserLocation();
        await this.carregarOfertas();

        // Atualizar localização a cada 30 segundos
        setInterval(() => this.getUserLocation(), 30000);

        // Atualizar ofertas a cada 60 segundos
        setInterval(() => this.carregarOfertas(), 60000);
    }

    async initMap() {
        const element = document.getElementById(this.elementId);
        if (!element) return;

        // Centro padrão (Brasil)
        const center = { lat: -15.7942, lng: -47.8822 };

        // Criar mapa
        this.map = new google.maps.Map(element, {
            zoom: 5,
            center: center,
            mapTypeControl: true,
            streetViewControl: false,
            fullscreenControl: true,
            styles: [
                {
                    featureType: 'poi',
                    elementType: 'labels',
                    stylers: [{ visibility: 'off' }]
                }
            ]
        });
    }

    async getUserLocation() {
        if (!navigator.geolocation) {
            console.log('Geolocalização não suportada');
            return;
        }

        return new Promise((resolve) => {
            navigator.geolocation.getCurrentPosition(
                (position) => {
                    this.userLocation = {
                        lat: position.coords.latitude,
                        lng: position.coords.longitude
                    };

                    this.atualizarMarcadorUsuario();
                    this.salvarLocalizacao();
                    resolve(this.userLocation);
                },
                (error) => {
                    console.log('Erro ao obter localização:', error.message);
                    resolve(null);
                },
                {
                    enableHighAccuracy: true,
                    timeout: 10000,
                    maximumAge: 0
                }
            );
        });
    }

    atualizarMarcadorUsuario() {
        if (!this.userLocation || !this.map) return;

        // Remover marcador anterior
        if (this.userMarker) {
            this.userMarker.setMap(null);
        }

        // Criar ícone personalizado (azul)
        const icon = {
            path: google.maps.SymbolPath.CIRCLE,
            scale: 12,
            fillColor: '#4A90E2',
            fillOpacity: 1,
            strokeColor: '#ffffff',
            strokeWeight: 3
        };

        // Adicionar novo marcador
        this.userMarker = new google.maps.Marker({
            position: this.userLocation,
            map: this.map,
            icon: icon,
            title: 'Você está aqui',
            animation: google.maps.Animation.DROP
        });

        // Adicionar info window
        const infoWindow = new google.maps.InfoWindow({
            content: `
                <div style="padding: 10px;">
                    <strong>Sua Localização</strong><br>
                    <small>Atualizado em tempo real</small>
                </div>
            `
        });

        this.userMarker.addListener('click', () => {
            infoWindow.open(this.map, this.userMarker);
        });

        // Centralizar mapa na localização do usuário
        this.map.setCenter(this.userLocation);
        this.map.setZoom(12);
    }

    async salvarLocalizacao() {
        if (!this.userLocation) return;

        try {
            const formData = new FormData();
            formData.append('lat', this.userLocation.lat);
            formData.append('lng', this.userLocation.lng);

            await fetch(BASE_URL + 'processamento/salvar_localizacao.php', {
                method: 'POST',
                body: formData
            });
        } catch (error) {
            console.error('Erro ao salvar localização:', error);
        }
    }

    async carregarOfertas() {
        if (!this.map) return;

        try {
            const response = await fetch(BASE_URL + 'processamento/listar_ofertas_mapa.php');
            const data = await response.json();

            if (data.sucesso) {
                this.renderizarOfertas(data.ofertas);
            }
        } catch (error) {
            console.error('Erro ao carregar ofertas:', error);
        }
    }

    renderizarOfertas(ofertas) {
        // Limpar marcadores anteriores
        this.ofertasMarkers.forEach(marker => marker.setMap(null));
        this.ofertasMarkers = [];

        ofertas.forEach(oferta => {
            if (!oferta.lat || !oferta.lng) return;

            // Ícone verde para ofertas
            const icon = {
                path: google.maps.SymbolPath.CIRCLE,
                scale: 10,
                fillColor: '#10B981',
                fillOpacity: 0.9,
                strokeColor: '#ffffff',
                strokeWeight: 2
            };

            const marker = new google.maps.Marker({
                position: { lat: parseFloat(oferta.lat), lng: parseFloat(oferta.lng) },
                map: this.map,
                icon: icon,
                title: `${oferta.origem_cidade}/${oferta.origem_uf} → ${oferta.destino_cidade}/${oferta.destino_uf}`
            });

            const infoContent = `
                <div style="padding: 12px; min-width: 250px;">
                    <h4 style="margin: 0 0 8px 0; color: #1E3A8A;">
                        ${oferta.origem_cidade}/${oferta.origem_uf} → ${oferta.destino_cidade}/${oferta.destino_uf}
                    </h4>
                    <p style="margin: 4px 0; font-size: 13px;">
                        <strong>Carregamento:</strong> ${formatarData(oferta.data_carregamento)}
                    </p>
                    <p style="margin: 4px 0; font-size: 13px;">
                        <strong>Veículo:</strong> ${ucfirst(oferta.tipo_veiculo)}
                    </p>
                    <p style="margin: 4px 0; font-size: 13px;">
                        <strong>Peso:</strong> ${formatarPeso(oferta.peso)}
                    </p>
                    <p style="margin: 8px 0 4px 0; font-size: 14px; font-weight: 600; color: #10B981;">
                        ${oferta.frete_combinar ? 'Valor a combinar' : formatarMoeda(oferta.valor_frete)}
                    </p>
                    <a href="${BASE_URL}views/ofertas.php" style="display: inline-block; margin-top: 8px; padding: 6px 12px; background: #4A90E2; color: white; text-decoration: none; border-radius: 6px; font-size: 12px;">
                        Ver Detalhes
                    </a>
                </div>
            `;

            const infoWindow = new google.maps.InfoWindow({
                content: infoContent
            });

            marker.addListener('click', () => {
                // Fechar outras info windows
                this.ofertasMarkers.forEach(m => {
                    if (m.infoWindow) m.infoWindow.close();
                });

                infoWindow.open(this.map, marker);
            });

            marker.infoWindow = infoWindow;
            this.ofertasMarkers.push(marker);
        });
    }

    // Método para adicionar filtro rápido
    adicionarFiltroRapido(filtros) {
        // Implementar filtros de ofertas no mapa
        console.log('Filtros aplicados:', filtros);
    }
}

// Funções auxiliares
function formatarData(dataStr) {
    const data = new Date(dataStr);
    return data.toLocaleDateString('pt-BR');
}

function formatarPeso(peso) {
    return new Intl.NumberFormat('pt-BR').format(peso) + ' kg';
}

function formatarMoeda(valor) {
    return 'R$ ' + new Intl.NumberFormat('pt-BR', {
        minimumFractionDigits: 2,
        maximumFractionDigits: 2
    }).format(valor);
}

function ucfirst(str) {
    return str.charAt(0).toUpperCase() + str.slice(1);
}

// Inicializar mapa quando a página carregar
let mapaBoraFrete;
window.initMap = function() {
    mapaBoraFrete = new MapaBoraFrete('mapa-interativo');
};
