<?php if (isset($showSidebar) && $showSidebar === true): ?>
            </div>
            <!-- Fim do Conteúdo da Página -->
        </main>
        <!-- Fim do Conteúdo Principal -->
    </div>
    <!-- Fim do Main Wrapper -->
<?php endif; ?>

<!-- Bootstrap 5 JS Bundle -->
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>

<!-- Scripts Customizados -->
<?php if (isset($customScripts) && is_array($customScripts)): ?>
    <?php foreach ($customScripts as $script): ?>
        <script src="public/js/<?php echo $script; ?>"></script>
    <?php endforeach; ?>
<?php endif; ?>

</body>
</html>
