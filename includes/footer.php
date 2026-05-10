    </div> <!-- End main-content -->
    <?php if (isLoggedIn()): ?>
        <?php include_once __DIR__ . '/ai-chat-widget.php'; ?>
    <?php endif; ?>
</div> <!-- End app-layout -->

<script>
    // Initialize Lucide icons
    lucide.createIcons();
</script>
</body>
</html>
