<?php
/**
 * BORAFRETE - Perfil do Usuário
 */
require_once '../config/config.php';
verificarLogin();

$pageTitle = 'Meu Perfil';

// Buscar dados do usuário
$stmt = $pdo->prepare("SELECT * FROM usuarios WHERE id = ?");
$stmt->execute([$_SESSION['usuario_id']]);
$usuario = $stmt->fetch();

require_once 'layout/header.php';
?>

<div class="page-container">

    <div class="page-header">
        <h1>Meu Perfil</h1>
        <p>Gerencie suas informações pessoais e de conta</p>
    </div>

    <div class="profile-container glass-card">

        <div class="profile-header">
            <div class="profile-avatar-large">
                <?php echo strtoupper(substr($usuario['nome_razao_social'], 0, 2)); ?>
            </div>
            <div class="profile-info">
                <h2><?php echo htmlspecialchars($usuario['nome_razao_social']); ?></h2>
                <p class="profile-type"><?php echo ucfirst($usuario['tipo_perfil']); ?></p>
                <p class="profile-member-since">Membro desde <?php echo date('d/m/Y', strtotime($usuario['created_at'])); ?></p>
            </div>
        </div>

        <form action="<?php echo BASE_URL; ?>processamento/atualizar_perfil.php" method="POST" class="profile-form">

            <div class="form-section">
                <h3 class="section-title">Informações Pessoais</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nome_razao_social">Nome / Razão Social *</label>
                        <input
                            type="text"
                            name="nome_razao_social"
                            id="nome_razao_social"
                            value="<?php echo htmlspecialchars($usuario['nome_razao_social']); ?>"
                            required
                        >
                    </div>

                    <div class="form-group">
                        <label for="email">E-mail *</label>
                        <input
                            type="email"
                            name="email"
                            id="email"
                            value="<?php echo htmlspecialchars($usuario['email']); ?>"
                            required
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="documento_tipo">Tipo de Documento *</label>
                        <select name="documento_tipo" id="documento_tipo" disabled>
                            <option value="cpf" <?php echo $usuario['documento_tipo'] === 'cpf' ? 'selected' : ''; ?>>CPF</option>
                            <option value="cnpj" <?php echo $usuario['documento_tipo'] === 'cnpj' ? 'selected' : ''; ?>>CNPJ</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="documento_numero">Número do Documento *</label>
                        <input
                            type="text"
                            name="documento_numero"
                            id="documento_numero"
                            value="<?php echo formatarDocumento($usuario['documento_numero']); ?>"
                            readonly
                        >
                    </div>
                </div>

                <?php if ($usuario['documento_tipo'] === 'cnpj'): ?>
                <div class="form-row">
                    <div class="form-group">
                        <label for="ie">Inscrição Estadual</label>
                        <input
                            type="text"
                            name="ie"
                            id="ie"
                            value="<?php echo htmlspecialchars($usuario['ie'] ?? ''); ?>"
                        >
                    </div>
                </div>
                <?php endif; ?>

                <div class="form-row">
                    <div class="form-group">
                        <label for="telefone">Telefone *</label>
                        <input
                            type="text"
                            name="telefone"
                            id="telefone"
                            value="<?php echo htmlspecialchars($usuario['telefone']); ?>"
                            required
                        >
                    </div>
                </div>
            </div>

            <?php if ($usuario['tipo_perfil'] === 'motorista'): ?>
            <div class="form-section">
                <h3 class="section-title">Informações de Motorista</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label class="checkbox-label">
                            <input
                                type="checkbox"
                                name="mopp"
                                id="mopp"
                                value="1"
                                <?php echo $usuario['mopp'] ? 'checked' : ''; ?>
                            >
                            <span>Possui MOPP</span>
                        </label>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label>Categorias da CNH</label>
                        <div class="checkbox-group">
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    name="cnh_categorias[]"
                                    value="C"
                                    <?php echo strpos($usuario['cnh_categorias'], 'C') !== false ? 'checked' : ''; ?>
                                >
                                <span>Categoria C</span>
                            </label>
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    name="cnh_categorias[]"
                                    value="D"
                                    <?php echo strpos($usuario['cnh_categorias'], 'D') !== false ? 'checked' : ''; ?>
                                >
                                <span>Categoria D</span>
                            </label>
                            <label class="checkbox-label">
                                <input
                                    type="checkbox"
                                    name="cnh_categorias[]"
                                    value="E"
                                    <?php echo strpos($usuario['cnh_categorias'], 'E') !== false ? 'checked' : ''; ?>
                                >
                                <span>Categoria E</span>
                            </label>
                        </div>
                    </div>
                </div>
            </div>
            <?php endif; ?>

            <div class="form-section">
                <h3 class="section-title">Alterar Senha</h3>

                <div class="form-row">
                    <div class="form-group">
                        <label for="senha_atual">Senha Atual</label>
                        <input
                            type="password"
                            name="senha_atual"
                            id="senha_atual"
                            placeholder="Digite sua senha atual para alterar"
                        >
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="nova_senha">Nova Senha</label>
                        <input
                            type="password"
                            name="nova_senha"
                            id="nova_senha"
                            placeholder="Digite a nova senha"
                        >
                    </div>

                    <div class="form-group">
                        <label for="confirmar_senha">Confirmar Nova Senha</label>
                        <input
                            type="password"
                            name="confirmar_senha"
                            id="confirmar_senha"
                            placeholder="Confirme a nova senha"
                        >
                    </div>
                </div>
            </div>

            <div class="form-actions">
                <a href="<?php echo BASE_URL; ?>views/dashboard.php" class="btn btn-secondary">Cancelar</a>
                <button type="submit" class="btn btn-primary">Salvar Alterações</button>
            </div>

        </form>

    </div>

</div>

<?php require_once 'layout/footer.php'; ?>
