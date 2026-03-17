<?php
require_once 'config/config.php';

// Se está logado, buscar dados do usuário
$editMode = isLoggedIn();
$userData = null;

if ($editMode) {
    try {
        $stmt = $pdo->prepare("SELECT * FROM users WHERE id = ?");
        $stmt->execute([$_SESSION['user_id']]);
        $userData = $stmt->fetch();
    } catch (PDOException $e) {
        error_log("Erro ao buscar dados do usuário: " . $e->getMessage());
    }
}

$pageTitle = $editMode ? 'Meu Perfil' : 'Cadastro';
$showSidebar = $editMode;
$customScripts = ['ibge-api.js', 'perfil-form.js'];

if ($editMode) {
    include 'views/layout/header.php';
} else {
    // Header simplificado para página de cadastro
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $pageTitle . ' - ' . SITE_NAME; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>
<div class="page-content" style="padding: 40px 20px;">
<?php
}
?>

<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title"><?php echo $editMode ? 'Editar Perfil' : 'Cadastro de Novo Usuário'; ?></h3>
            </div>
            <div class="card-body">

                <?php if (isset($_GET['sucesso'])): ?>
                    <div class="alert alert-success">
                        <?php echo $editMode ? 'Perfil atualizado com sucesso!' : 'Cadastro realizado com sucesso! Você será redirecionado para o login...'; ?>
                        <?php if (!$editMode): ?>
                            <script>
                                setTimeout(function() {
                                    window.location.href = 'index.php?sucesso=cadastro';
                                }, 3000);
                            </script>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>

                <?php if (isset($_GET['erro'])): ?>
                    <div class="alert alert-danger">
                        <?php
                        switch ($_GET['erro']) {
                            case 'email_existente':
                                echo 'Este email já está cadastrado.';
                                break;
                            case 'documento_existente':
                                echo 'Este CPF/CNPJ já está cadastrado.';
                                break;
                            case 'senhas_diferentes':
                                echo 'As senhas não conferem.';
                                break;
                            case 'documento_invalido':
                                echo 'CPF/CNPJ inválido.';
                                break;
                            default:
                                echo 'Erro ao processar. Tente novamente.';
                        }
                        ?>
                    </div>
                <?php endif; ?>

                <form action="processamento/salvar-perfil.php" method="POST" id="formPerfil">

                    <?php if ($editMode): ?>
                        <input type="hidden" name="edit_mode" value="1">
                    <?php endif; ?>

                    <h5 class="mb-3">Tipo de Perfil</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="tipo_perfil" class="form-label">Tipo de Perfil *</label>
                            <select class="form-select" id="tipo_perfil" name="tipo_perfil" required <?php echo $editMode ? 'disabled' : ''; ?>>
                                <option value="">Selecione...</option>
                                <option value="transportadora" <?php echo ($userData && $userData['tipo_perfil'] == 'transportadora') ? 'selected' : ''; ?>>Transportadora</option>
                                <option value="agenciador" <?php echo ($userData && $userData['tipo_perfil'] == 'agenciador') ? 'selected' : ''; ?>>Agenciador</option>
                                <option value="motorista" <?php echo ($userData && $userData['tipo_perfil'] == 'motorista') ? 'selected' : ''; ?>>Motorista</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="tipo_documento" class="form-label">Tipo de Documento *</label>
                            <select class="form-select" id="tipo_documento" name="tipo_documento" required <?php echo $editMode ? 'disabled' : ''; ?>>
                                <option value="">Selecione...</option>
                                <option value="cpf" <?php echo ($userData && $userData['tipo_documento'] == 'cpf') ? 'selected' : ''; ?>>CPF</option>
                                <option value="cnpj" <?php echo ($userData && $userData['tipo_documento'] == 'cnpj') ? 'selected' : ''; ?>>CNPJ</option>
                            </select>
                        </div>
                    </div>

                    <h5 class="mb-3 mt-4">Informações Pessoais/Empresariais</h5>

                    <div class="form-group">
                        <label for="documento" class="form-label" id="label-documento">CPF/CNPJ *</label>
                        <input type="text" class="form-control" id="documento" name="documento" placeholder="" required <?php echo $editMode ? 'readonly' : ''; ?> value="<?php echo $userData ? htmlspecialchars($userData['documento']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label for="nome_completo" class="form-label">Nome Completo *</label>
                        <input type="text" class="form-control" id="nome_completo" name="nome_completo" placeholder="Nome completo" required value="<?php echo $userData ? htmlspecialchars($userData['nome_completo']) : ''; ?>">
                    </div>

                    <div class="form-group" id="razao-social-container" style="display: none;">
                        <label for="razao_social" class="form-label">Razão Social</label>
                        <input type="text" class="form-control" id="razao_social" name="razao_social" placeholder="Razão Social da Empresa" value="<?php echo $userData ? htmlspecialchars($userData['razao_social']) : ''; ?>">
                    </div>

                    <h5 class="mb-3 mt-4">Contato</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="email" class="form-label">Email *</label>
                            <input type="email" class="form-control" id="email" name="email" placeholder="seu@email.com" required value="<?php echo $userData ? htmlspecialchars($userData['email']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="telefone" class="form-label">Telefone *</label>
                            <input type="tel" class="form-control" id="telefone" name="telefone" placeholder="(11) 99999-9999" required value="<?php echo $userData ? htmlspecialchars($userData['telefone']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="celular" class="form-label">Celular</label>
                        <input type="tel" class="form-control" id="celular" name="celular" placeholder="(11) 98888-8888" value="<?php echo $userData ? htmlspecialchars($userData['celular']) : ''; ?>">
                    </div>

                    <h5 class="mb-3 mt-4">Endereço</h5>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="cep" class="form-label">CEP</label>
                            <input type="text" class="form-control" id="cep" name="cep" placeholder="00000-000" maxlength="9" value="<?php echo $userData ? htmlspecialchars($userData['cep']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="endereco" class="form-label">Endereço</label>
                            <input type="text" class="form-control" id="endereco" name="endereco" placeholder="Rua, Avenida..." value="<?php echo $userData ? htmlspecialchars($userData['endereco']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="numero" class="form-label">Número</label>
                            <input type="text" class="form-control" id="numero" name="numero" placeholder="123" value="<?php echo $userData ? htmlspecialchars($userData['numero']) : ''; ?>">
                        </div>

                        <div class="form-group">
                            <label for="complemento" class="form-label">Complemento</label>
                            <input type="text" class="form-control" id="complemento" name="complemento" placeholder="Apto, Sala..." value="<?php echo $userData ? htmlspecialchars($userData['complemento']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="bairro" class="form-label">Bairro</label>
                            <input type="text" class="form-control" id="bairro" name="bairro" placeholder="Centro" value="<?php echo $userData ? htmlspecialchars($userData['bairro']) : ''; ?>">
                        </div>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="uf" class="form-label">UF</label>
                            <select class="form-select" id="uf" name="uf">
                                <option value="">Carregando...</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="cidade" class="form-label">Cidade</label>
                            <select class="form-select" id="cidade" name="cidade" disabled>
                                <option value="">Selecione o Estado primeiro</option>
                            </select>
                        </div>
                    </div>

                    <?php if (!$editMode): ?>
                        <h5 class="mb-3 mt-4">Senha</h5>

                        <div class="form-row">
                            <div class="form-group">
                                <label for="senha" class="form-label">Senha *</label>
                                <input type="password" class="form-control" id="senha" name="senha" placeholder="Mínimo 6 caracteres" required minlength="6">
                            </div>

                            <div class="form-group">
                                <label for="confirmar_senha" class="form-label">Confirmar Senha *</label>
                                <input type="password" class="form-control" id="confirmar_senha" name="confirmar_senha" placeholder="Repita a senha" required minlength="6">
                            </div>
                        </div>
                    <?php endif; ?>

                    <div class="card-footer">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> <?php echo $editMode ? 'Atualizar Perfil' : 'Cadastrar'; ?>
                        </button>
                        <?php if ($editMode): ?>
                            <a href="dashboard.php" class="btn btn-secondary">
                                <i class="fas fa-times"></i> Cancelar
                            </a>
                        <?php else: ?>
                            <a href="index.php" class="btn btn-secondary">
                                <i class="fas fa-arrow-left"></i> Voltar para Login
                            </a>
                        <?php endif; ?>
                    </div>

                </form>

            </div>
        </div>
    </div>
</div>

<?php
if ($editMode) {
    include 'views/layout/footer.php';
} else {
    echo '</div>';
    echo '<script src="public/js/ibge-api.js"></script>';
    echo '<script src="public/js/perfil-form.js"></script>';
    echo '</body></html>';
}
?>
