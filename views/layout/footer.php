    </div><!-- end main-wrapper -->

    <footer class="site-footer">
        <div class="footer-content">
            <p>Suporte 24/7 | Ajuda</p>
            <p>&copy; <?php echo date('Y'); ?> <?php echo SITE_NAME; ?>. Todos os direitos reservados.</p>
        </div>
    </footer>

    <script>
        const BASE_URL = '<?php echo BASE_URL; ?>';
    </script>
    <script src="<?php echo BASE_URL; ?>public/js/main.js"></script>
    <script src="<?php echo BASE_URL; ?>public/js/ibge.js"></script>
    <script src="<?php echo BASE_URL; ?>public/js/validacao.js"></script>
    <script src="<?php echo BASE_URL; ?>public/js/notificacoes.js"></script>
    <?php if (isset($extraScripts)): ?>
        <?php echo $extraScripts; ?>
    <?php endif; ?>
</body>
</html>
