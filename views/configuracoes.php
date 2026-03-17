<?php
/**
 * BORAFRETE - Configurações
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Configurações';

// Processar salvamento de configurações
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $acao = $_POST['acao'] ?? '';

    if ($acao === 'atualizar_senha') {
        $senha_atual = $_POST['senha_atual'] ?? '';
        $senha_nova = $_POST['senha_nova'] ?? '';
        $senha_confirmar = $_POST['senha_confirmar'] ?? '';

        // Verificar senha atual
        $stmt = $pdo->prepare("SELECT senha FROM usuarios WHERE id = ?");
        $stmt->execute([$_SESSION['usuario_id']]);
        $usuario = $stmt->fetch();

        if (password_verify($senha_atual, $usuario['senha'])) {
            if ($senha_nova === $senha_confirmar && strlen($senha_nova) >= 6) {
                $senha_hash = password_hash($senha_nova, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE usuarios SET senha = ? WHERE id = ?");
                $stmt->execute([$senha_hash, $_SESSION['usuario_id']]);
                setFlashMessage('success', 'Senha atualizada com sucesso!');
            } else {
                setFlashMessage('error', 'As senhas não coincidem ou são muito curtas.');
            }
        } else {
            setFlashMessage('error', 'Senha atual incorreta.');
        }
        header('Location: ' . BASE_URL . 'views/configuracoes.php');
        exit;
    }

    if ($acao === 'atualizar_notificacoes') {
        $notif_ofertas = isset($_POST['notif_ofertas']) ? 1 : 0;
        $notif_mensagens = isset($_POST['notif_mensagens']) ? 1 : 0;
        $notif_sistema = isset($_POST['notif_sistema']) ? 1 : 0;

        $stmt = $pdo->prepare("
            UPDATE usuarios SET
                notif_ofertas = ?,
                notif_mensagens = ?,
                notif_sistema = ?
            WHERE id = ?
        ");
        $stmt->execute([$notif_ofertas, $notif_mensagens, $notif_sistema, $_SESSION['usuario_id']]);
        setFlashMessage('success', 'Preferências de notificação atualizadas!');
        header('Location: ' . BASE_URL . 'views/configuracoes.php');
        exit;
    }
}

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>Configurações</h1>
        <p>Gerencie suas preferências e segurança</p>
    </div>

    <?php if (isset($_SESSION['flash_message'])): ?>
        <div class="alert alert-<?php echo $_SESSION['flash_type']; ?>">
            <?php
            echo $_SESSION['flash_message'];
            unset($_SESSION['flash_message']);
            unset($_SESSION['flash_type']);
            ?>
        </div>
    <?php endif; ?>

    <div class="settings-grid">

        <!-- Informações da Conta -->
        <div class="glass-card">
            <h3 class="section-title">Informações da Conta</h3>
            <div class="info-list">
                <div class="info-item">
                    <label>Nome/Razão Social</label>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['nome_razao_social']); ?></div>
                </div>
                <div class="info-item">
                    <label>E-mail</label>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['email']); ?></div>
                </div>
                <div class="info-item">
                    <label>Telefone</label>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['telefone']); ?></div>
                </div>
                <div class="info-item">
                    <label>Tipo de Perfil</label>
                    <div class="info-value"><?php echo ucfirst($usuario['tipo_perfil']); ?></div>
                </div>
                <div class="info-item">
                    <label>Documento</label>
                    <div class="info-value"><?php echo htmlspecialchars($usuario['documento_numero']); ?></div>
                </div>
            </div>
            <a href="<?php echo BASE_URL; ?>views/perfil.php" class="btn btn-secondary" style="margin-top: 20px;">
                Editar Perfil
            </a>
        </div>

        <!-- Alterar Senha -->
        <div class="glass-card">
            <h3 class="section-title">Segurança</h3>
            <form method="POST" class="settings-form">
                <input type="hidden" name="acao" value="atualizar_senha">

                <div class="form-group">
                    <label for="senha_atual">Senha Atual *</label>
                    <input type="password" name="senha_atual" id="senha_atual" required>
                </div>

                <div class="form-group">
                    <label for="senha_nova">Nova Senha *</label>
                    <input type="password" name="senha_nova" id="senha_nova" required minlength="6">
                </div>

                <div class="form-group">
                    <label for="senha_confirmar">Confirmar Nova Senha *</label>
                    <input type="password" name="senha_confirmar" id="senha_confirmar" required minlength="6">
                </div>

                <button type="submit" class="btn btn-primary">Atualizar Senha</button>
            </form>
        </div>

        <!-- Notificações -->
        <div class="glass-card">
            <h3 class="section-title">Preferências de Notificação</h3>
            <form method="POST" class="settings-form">
                <input type="hidden" name="acao" value="atualizar_notificacoes">

                <div class="checkbox-group">
                    <label class="checkbox-label">
                        <input type="checkbox" name="notif_ofertas" <?php echo ($usuario['notif_ofertas'] ?? 1) ? 'checked' : ''; ?>>
                        <span>Notificações de novas ofertas</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="notif_mensagens" <?php echo ($usuario['notif_mensagens'] ?? 1) ? 'checked' : ''; ?>>
                        <span>Notificações de mensagens</span>
                    </label>

                    <label class="checkbox-label">
                        <input type="checkbox" name="notif_sistema" <?php echo ($usuario['notif_sistema'] ?? 1) ? 'checked' : ''; ?>>
                        <span>Notificações do sistema</span>
                    </label>
                </div>

                <button type="submit" class="btn btn-primary">Salvar Preferências</button>
            </form>
        </div>

        <!-- Estatísticas -->
        <div class="glass-card">
            <h3 class="section-title">Estatísticas</h3>
            <?php
            // Buscar estatísticas
            if ($usuario['tipo_perfil'] === 'motorista') {
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM veiculos WHERE usuario_id = ?");
                $stmt->execute([$_SESSION['usuario_id']]);
                $veiculos = $stmt->fetchColumn();

                echo "<div class='stats-grid'>";
                echo "<div class='stat-item'>";
                echo "<div class='stat-number'>{$veiculos}</div>";
                echo "<div class='stat-label'>Veículos Cadastrados</div>";
                echo "</div>";
                echo "</div>";
            } else {
                $stmt = $pdo->prepare("SELECT COUNT(*) as total FROM ofertas WHERE transportadora_id = ? AND status = 'ativa'");
                $stmt->execute([$_SESSION['usuario_id']]);
                $ofertas = $stmt->fetchColumn();

                echo "<div class='stats-grid'>";
                echo "<div class='stat-item'>";
                echo "<div class='stat-number'>{$ofertas}</div>";
                echo "<div class='stat-label'>Ofertas Ativas</div>";
                echo "</div>";
                echo "</div>";
            }
            ?>
        </div>

    </div>

</div>

<style>
.settings-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
    gap: 24px;
}

.settings-form {
    display: flex;
    flex-direction: column;
    gap: 20px;
}

.info-list {
    display: flex;
    flex-direction: column;
    gap: 16px;
}

.info-item label {
    font-size: 12px;
    color: var(--cinza-escuro);
    text-transform: uppercase;
    font-weight: 600;
    display: block;
    margin-bottom: 4px;
}

.info-value {
    font-size: 16px;
    color: var(--preto);
    font-weight: 500;
}

.stats-grid {
    display: grid;
    grid-template-columns: repeat(auto-fit, minmax(120px, 1fr));
    gap: 16px;
}

.stat-item {
    text-align: center;
    padding: 20px;
    background: linear-gradient(135deg, var(--azul-claro), var(--azul-principal));
    border-radius: 12px;
    color: white;
}

.stat-number {
    font-size: 36px;
    font-weight: 700;
    margin-bottom: 8px;
}

.stat-label {
    font-size: 14px;
    opacity: 0.9;
}

@media (max-width: 768px) {
    .settings-grid {
        grid-template-columns: 1fr;
    }
}
</style>

<?php require_once 'layout/footer.php'; ?>
