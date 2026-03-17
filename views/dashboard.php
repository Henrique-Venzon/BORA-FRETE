<?php
/**
 * BORAFRETE - Dashboard Principal
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Dashboard';

// Buscar veículos do usuário
$stmtVeiculos = $pdo->prepare("
    SELECT * FROM veiculos
    WHERE usuario_id = ?
    ORDER BY created_at DESC
");
$stmtVeiculos->execute([$_SESSION['usuario_id']]);
$veiculos = $stmtVeiculos->fetchAll();

// Buscar dados do usuário
$stmtUsuario = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmtUsuario->execute([$_SESSION['usuario_id']]);
$usuario = $stmtUsuario->fetch();

require_once 'layout/header.php';
?>

<div class="dashboard-container">

    <!-- BEM-VINDO CARD -->
    <div class="welcome-card glass-card">
        <div class="welcome-content">
            <div class="welcome-avatar">
                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                    <circle cx="12" cy="8" r="4" fill="#4A90E2"/>
                    <path d="M12 14C8.13 14 1 15.93 1 19.75V22H23V19.75C23 15.93 15.87 14 12 14Z" fill="#4A90E2"/>
                </svg>
            </div>
            <div class="welcome-text">
                <h2>BEM-VINDO <?php echo strtoupper($usuario['tipo_perfil']); ?></h2>
                <p>Sua jornada inteligente começa agora.<br>Verifique seus próximos passos.</p>
            </div>
        </div>
    </div>

    <div class="dashboard-grid">

        <!-- VEÍCULOS CADASTRADOS -->
        <div class="vehicles-section glass-card">
            <div class="section-header">
                <h3>VEÍCULOS CADASTRADOS</h3>
                <a href="<?php echo BASE_URL; ?>views/cadastro-veiculo.php" class="btn btn-primary btn-sm">
                    + Adicionar Veículo
                </a>
            </div>

            <div class="vehicles-grid">
                <?php if (count($veiculos) > 0): ?>
                    <?php foreach ($veiculos as $veiculo): ?>
                        <div class="vehicle-card">
                            <div class="vehicle-icon">
                                <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                                    <path d="M20 8H17V4H3C1.9 4 1 4.9 1 6V17H3C3 18.66 4.34 20 6 20C7.66 20 9 18.66 9 17H15C15 18.66 16.34 20 18 20C19.66 20 21 18.66 21 17H23V12L20 8ZM6 18.5C5.17 18.5 4.5 17.83 4.5 17C4.5 16.17 5.17 15.5 6 15.5C6.83 15.5 7.5 16.17 7.5 17C7.5 17.83 6.83 18.5 6 18.5ZM19 9.5L21.46 12H17V9.5H19ZM18 18.5C17.17 18.5 16.5 17.83 16.5 17C16.5 16.17 17.17 15.5 18 15.5C18.83 15.5 19.5 16.17 19.5 17C19.5 17.83 18.83 18.5 18 18.5Z" fill="#1E3A8A"/>
                                </svg>
                            </div>
                            <div class="vehicle-photo">
                                <?php if ($veiculo['foto']): ?>
                                    <img src="<?php echo UPLOAD_URL . htmlspecialchars($veiculo['foto']); ?>" alt="Veículo">
                                <?php else: ?>
                                    <div class="no-photo">Sem foto</div>
                                <?php endif; ?>
                            </div>
                            <div class="vehicle-info">
                                <h4><?php echo strtoupper(htmlspecialchars($veiculo['marca'])); ?> (<?php echo $veiculo['ano']; ?>)</h4>
                                <p class="vehicle-plate"><?php echo strtoupper(htmlspecialchars($veiculo['placa_1'])); ?></p>
                                <p class="vehicle-type"><?php echo strtoupper(htmlspecialchars($veiculo['tipo_veiculo'])); ?></p>
                            </div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <div class="empty-state">
                        <svg width="80" height="80" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                            <path d="M20 8H17V4H3C1.9 4 1 4.9 1 6V17H3C3 18.66 4.34 20 6 20C7.66 20 9 18.66 9 17H15C15 18.66 16.34 20 18 20C19.66 20 21 18.66 21 17H23V12L20 8Z" fill="#CCC"/>
                        </svg>
                        <p>Nenhum veículo cadastrado</p>
                        <a href="<?php echo BASE_URL; ?>views/cadastro-veiculo.php" class="btn btn-primary">Cadastrar Primeiro Veículo</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- MAPA E STATUS -->
        <div class="map-section glass-card">
            <div class="map-container">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d3657.1975979597!2d-46.6583!3d-23.5613!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x0%3A0x0!2zMjPCsDMzJzQwLjciUyA0NsKwMzknMjkuOSJX!5e0!3m2!1spt-BR!2sbr!4v1234567890"
                    width="100%"
                    height="300"
                    style="border:0; border-radius: 15px;"
                    allowfullscreen=""
                    loading="lazy"
                    referrerpolicy="no-referrer-when-downgrade">
                </iframe>
                <div class="map-label">
                    <strong>Miens des Maps</strong><br>
                    <small>Vem trivor manhô</small>
                </div>
            </div>

            <div class="vehicle-status-section">
                <div class="status-header">
                    <h3>DISPONIBILIDADE DO VEÍCULO</h3>
                </div>

                <?php if (count($veiculos) > 0): ?>
                    <?php $primeiroVeiculo = $veiculos[0]; ?>
                    <div class="status-content">
                        <div class="status-info">
                            <span class="status-label">Status:</span>
                            <span class="status-value <?php echo $primeiroVeiculo['disponivel'] ? 'status-available' : 'status-unavailable'; ?>">
                                <?php echo $primeiroVeiculo['disponivel'] ? 'Disponível' : 'Indisponível (Em Viagem)'; ?>
                            </span>
                        </div>

                        <div class="availability-toggle">
                            <label class="toggle-switch">
                                <input
                                    type="checkbox"
                                    <?php echo $primeiroVeiculo['disponivel'] ? 'checked' : ''; ?>
                                    onchange="toggleVehicleAvailability(<?php echo $primeiroVeiculo['id']; ?>, this.checked)"
                                >
                                <span class="toggle-slider"></span>
                            </label>
                            <span class="toggle-label">DISPONIBILIZAR VEÍCULO</span>
                        </div>
                    </div>
                <?php else: ?>
                    <div class="status-content">
                        <p class="text-muted">Cadastre um veículo para gerenciar disponibilidade</p>
                    </div>
                <?php endif; ?>
            </div>
        </div>

    </div>

</div>

<script>
function toggleVehicleAvailability(veiculoId, disponivel) {
    fetch('<?php echo BASE_URL; ?>processamento/atualizar_disponibilidade.php', {
        method: 'POST',
        headers: {
            'Content-Type': 'application/json',
        },
        body: JSON.stringify({
            veiculo_id: veiculoId,
            disponivel: disponivel
        })
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            // Atualiza a página
            location.reload();
        } else {
            alert('Erro ao atualizar disponibilidade');
        }
    })
    .catch(error => {
        console.error('Erro:', error);
        alert('Erro ao atualizar disponibilidade');
    });
}
</script>

<?php require_once 'layout/footer.php'; ?>
