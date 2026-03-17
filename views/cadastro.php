<?php
/**
 * BORAFRETE - Página de Cadastro de Usuário
 */
require_once '../config/config.php';

// Se já estiver logado, redireciona para o dashboard
if (isset($_SESSION['usuario_id'])) {
    header('Location: ' . BASE_URL . 'views/dashboard.php');
    exit;
}

// Verifica se há mensagem flash
$flashMessage = getFlashMessage();
?>
<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Cadastro - <?php echo SITE_NAME; ?></title>
    <link rel="stylesheet" href="<?php echo BASE_URL; ?>public/css/style.css">
</head>
<body class="login-page">

    <div class="login-container cadastro-container">

        <!-- LADO ESQUERDO - Informações -->
        <div class="login-left">
            <div class="login-brand">
                <div class="brand-icon">
                    <svg width="60" height="60" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="white"/>
                    </svg>
                </div>
                <h1 class="brand-name">borafrete</h1>
            </div>

            <div class="login-description">
                <h2>Junte-se à maior plataforma de logística do Brasil</h2>
                <p>Conecte-se com milhares de transportadoras e motoristas.</p>
            </div>

            <div class="login-features">
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M9 11.75C6.66 11.75 2 12.92 2 15.25V17H16V15.25C16 12.92 11.34 11.75 9 11.75ZM4.34 15C5.18 14.42 7.21 13.75 9 13.75C10.79 13.75 12.82 14.42 13.66 15H4.34ZM9 10C10.93 10 12.5 8.43 12.5 6.5C12.5 4.57 10.93 3 9 3C7.07 3 5.5 4.57 5.5 6.5C5.5 8.43 7.07 10 9 10ZM9 5C9.83 5 10.5 5.67 10.5 6.5C10.5 7.33 9.83 8 9 8C8.17 8 7.5 7.33 7.5 6.5C7.5 5.67 8.17 5 9 5Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 1L3 5V11C3 16.55 6.84 21.74 12 23C17.16 21.74 21 16.55 21 11V5L12 1ZM12 11.99H19C18.47 16.11 15.72 19.78 12 20.93V12H5V6.3L12 3.19V11.99Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
                <div class="feature-icon">
                    <svg width="32" height="32" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M19 3H5C3.9 3 3 3.9 3 5V19C3 20.1 3.9 21 5 21H19C20.1 21 21 20.1 21 19V5C21 3.9 20.1 3 19 3ZM19 19H5V5H19V19Z" fill="white" opacity="0.6"/>
                    </svg>
                </div>
            </div>
        </div>

        <!-- LADO DIREITO - Formulário de Cadastro -->
        <div class="login-right" style="overflow-y: auto; max-height: 90vh;">
            <div class="login-form-container" style="max-width: 550px; padding: 30px 10px;">

                <div class="login-logo">
                    <svg width="50" height="50" viewBox="0 0 24 24" fill="none" xmlns="http://www.w3.org/2000/svg">
                        <path d="M12 2C8.13 2 5 5.13 5 9c0 5.25 7 13 7 13s7-7.75 7-13c0-3.87-3.13-7-7-7zm0 9.5c-1.38 0-2.5-1.12-2.5-2.5s1.12-2.5 2.5-2.5 2.5 1.12 2.5 2.5-1.12 2.5-2.5 2.5z" fill="#4A90E2"/>
                    </svg>
                    <span class="logo-text">borafrete</span>
                </div>

                <h2 class="form-title">Criar Conta</h2>
                <p class="form-subtitle">Preencha seus dados para começar</p>

                <?php if ($flashMessage): ?>
                    <div class="alert alert-<?php echo $flashMessage['tipo']; ?>">
                        <?php echo $flashMessage['mensagem']; ?>
                    </div>
                <?php endif; ?>

                <form action="<?php echo BASE_URL; ?>processamento/salvar_usuario.php" method="POST" class="login-form" id="formCadastro" style="gap: 16px;">

                    <div class="form-group">
                        <label for="tipo_perfil">Tipo de Perfil *</label>
                        <select name="tipo_perfil" id="tipo_perfil" required onchange="handlePerfilChange()">
                            <option value="">Selecione...</option>
                            <option value="motorista">Motorista</option>
                            <option value="transportadora">Transportadora</option>
                            <option value="agenciador">Agenciador</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="nome_razao_social">Nome / Razão Social *</label>
                        <input type="text" name="nome_razao_social" id="nome_razao_social" required placeholder="Digite seu nome completo">
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="documento_tipo">Tipo de Documento *</label>
                            <select name="documento_tipo" id="documento_tipo" required onchange="handleDocumentoChange()">
                                <option value="">Selecione...</option>
                                <option value="cpf">CPF</option>
                                <option value="cnpj">CNPJ</option>
                            </select>
                        </div>

                        <div class="form-group">
                            <label for="documento_numero" id="labelDocumento">Número do Documento *</label>
                            <input type="text" name="documento_numero" id="documento_numero" required placeholder="000.000.000-00">
                        </div>
                    </div>

                    <div class="form-group" id="ie-group" style="display: none;">
                        <label for="ie">Inscrição Estadual</label>
                        <input type="text" name="ie" id="ie" placeholder="000.000.000.000">
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="email">E-mail *</label>
                            <input type="email" name="email" id="email" required placeholder="seu@email.com">
                        </div>

                        <div class="form-group">
                            <label for="telefone">Telefone *</label>
                            <input type="text" name="telefone" id="telefone" required placeholder="(00) 00000-0000">
                        </div>
                    </div>

                    <div class="form-grid-2">
                        <div class="form-group">
                            <label for="senha">Senha *</label>
                            <input type="password" name="senha" id="senha" required placeholder="Mínimo 6 caracteres" minlength="6">
                        </div>

                        <div class="form-group">
                            <label for="confirmar_senha">Confirmar Senha *</label>
                            <input type="password" name="confirmar_senha" id="confirmar_senha" required placeholder="Digite a senha novamente" minlength="6">
                        </div>
                    </div>

                    <!-- Campos específicos para motorista -->
                    <div id="campos-motorista" style="display: none;">
                        <div class="form-group">
                            <label class="checkbox-label">
                                <input type="checkbox" name="mopp" id="mopp" value="1">
                                <span>Possui MOPP</span>
                            </label>
                        </div>

                        <div class="form-group">
                            <label>Categorias da CNH</label>
                            <div class="checkbox-group">
                                <label class="checkbox-label">
                                    <input type="checkbox" name="cnh_categorias[]" value="C">
                                    <span>Categoria C</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="cnh_categorias[]" value="D">
                                    <span>Categoria D</span>
                                </label>
                                <label class="checkbox-label">
                                    <input type="checkbox" name="cnh_categorias[]" value="E">
                                    <span>Categoria E</span>
                                </label>
                            </div>
                        </div>
                    </div>

                    <button type="submit" class="btn btn-primary btn-block">Criar Conta</button>

                </form>

                <div class="form-footer">
                    <span>Já tem uma conta?</span>
                    <a href="<?php echo BASE_URL; ?>index.php" class="link-primary">Fazer Login</a>
                </div>

            </div>
        </div>

    </div>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>public/js/validacao.js"></script>
    <script>
        function handlePerfilChange() {
            const perfil = document.getElementById('tipo_perfil').value;
            const camposMotorista = document.getElementById('campos-motorista');

            if (perfil === 'motorista') {
                camposMotorista.style.display = 'block';
            } else {
                camposMotorista.style.display = 'none';
            }
        }

        function handleDocumentoChange() {
            const tipoDoc = document.getElementById('documento_tipo').value;
            const docInput = document.getElementById('documento_numero');
            const ieGroup = document.getElementById('ie-group');
            const labelDoc = document.getElementById('labelDocumento');

            if (tipoDoc === 'cpf') {
                labelDoc.textContent = 'CPF *';
                docInput.placeholder = '000.000.000-00';
                docInput.maxLength = 14;
                ieGroup.style.display = 'none';
            } else if (tipoDoc === 'cnpj') {
                labelDoc.textContent = 'CNPJ *';
                docInput.placeholder = '00.000.000/0000-00';
                docInput.maxLength = 18;
                ieGroup.style.display = 'block';
            }

            docInput.value = '';
        }

        // Aplicar máscaras
        document.getElementById('documento_numero').addEventListener('input', function() {
            const tipo = document.getElementById('documento_tipo').value;
            if (tipo === 'cpf') {
                formatCPF(this);
            } else if (tipo === 'cnpj') {
                formatCNPJ(this);
            }
        });

        document.getElementById('telefone').addEventListener('input', function() {
            formatPhone(this);
        });

        // Validar senhas e documento
        document.getElementById('formCadastro').addEventListener('submit', function(e) {
            const senha = document.getElementById('senha').value;
            const confirmar = document.getElementById('confirmar_senha').value;

            if (senha !== confirmar) {
                e.preventDefault();
                alert('As senhas não coincidem!');
                return false;
            }

            // Validar documento (CPF/CNPJ)
            if (!validarFormularioCadastro()) {
                e.preventDefault();
                return false;
            }
        });
    </script>
</body>
</html>
