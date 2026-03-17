<!DOCTYPE html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo isset($pageTitle) ? $pageTitle . ' - ' . SITE_NAME : SITE_NAME; ?></title>
    <meta name="description" content="<?php echo SITE_DESCRIPTION; ?>">

    <!-- Bootstrap 5 CSS (Grid e Componentes Básicos) -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">

    <!-- Font Awesome Icons -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <!-- Google Fonts - Inter -->
    <link href="https://fonts.googleapis.com/css2?family=Inter:wght@300;400;500;600;700;800&display=swap" rel="stylesheet">

    <!-- Custom CSS -->
    <link rel="stylesheet" href="public/css/style.css">
</head>
<body>

<?php if (isset($showSidebar) && $showSidebar === true): ?>
    <!-- Layout Principal com Sidebar -->
    <div class="main-wrapper">
        <!-- Sidebar -->
        <aside class="sidebar" id="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <h2><?php echo SITE_NAME; ?></h2>
                    <p>Sistema de Logística</p>
                </div>
            </div>

            <nav class="sidebar-menu">
                <a href="dashboard.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'dashboard.php') ? 'active' : ''; ?>">
                    <i class="fas fa-home"></i>
                    <span>Dashboard</span>
                </a>

                <div class="menu-separator"></div>

                <div class="menu-title">Cadastros</div>

                <a href="perfil.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'perfil.php') ? 'active' : ''; ?>">
                    <i class="fas fa-user"></i>
                    <span>Meu Perfil</span>
                </a>

                <a href="cadastro-veiculo.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cadastro-veiculo.php') ? 'active' : ''; ?>">
                    <i class="fas fa-truck"></i>
                    <span>Cadastrar Veículo</span>
                </a>

                <a href="cadastro-oferta.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'cadastro-oferta.php') ? 'active' : ''; ?>">
                    <i class="fas fa-box"></i>
                    <span>Cadastrar Oferta</span>
                </a>

                <div class="menu-separator"></div>

                <div class="menu-title">Gestão</div>

                <a href="meus-veiculos.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'meus-veiculos.php') ? 'active' : ''; ?>">
                    <i class="fas fa-list"></i>
                    <span>Meus Veículos</span>
                </a>

                <a href="minhas-ofertas.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'minhas-ofertas.php') ? 'active' : ''; ?>">
                    <i class="fas fa-clipboard-list"></i>
                    <span>Minhas Ofertas</span>
                </a>

                <a href="buscar-fretes.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'buscar-fretes.php') ? 'active' : ''; ?>">
                    <i class="fas fa-search"></i>
                    <span>Buscar Fretes</span>
                </a>

                <a href="propostas.php" class="menu-item <?php echo (basename($_SERVER['PHP_SELF']) == 'propostas.php') ? 'active' : ''; ?>">
                    <i class="fas fa-handshake"></i>
                    <span>Propostas</span>
                </a>

                <div class="menu-separator"></div>

                <a href="processamento/logout.php" class="menu-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Sair</span>
                </a>
            </nav>
        </aside>

        <!-- Conteúdo Principal -->
        <main class="main-content">
            <!-- Topbar -->
            <div class="topbar">
                <div class="topbar-left">
                    <h1><?php echo isset($pageTitle) ? $pageTitle : 'Dashboard'; ?></h1>
                </div>
                <div class="topbar-right">
                    <div class="user-menu">
                        <div class="user-avatar">
                            <?php
                            if (isset($_SESSION['nome_completo'])) {
                                $nomes = explode(' ', $_SESSION['nome_completo']);
                                echo strtoupper(substr($nomes[0], 0, 1));
                            } else {
                                echo 'U';
                            }
                            ?>
                        </div>
                        <div class="user-info">
                            <span class="user-name"><?php echo isset($_SESSION['nome_completo']) ? $_SESSION['nome_completo'] : 'Usuário'; ?></span>
                            <span class="user-role">
                                <?php
                                if (isset($_SESSION['tipo_perfil'])) {
                                    echo ucfirst($_SESSION['tipo_perfil']);
                                }
                                ?>
                            </span>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Conteúdo da Página -->
            <div class="page-content">
<?php endif; ?>
