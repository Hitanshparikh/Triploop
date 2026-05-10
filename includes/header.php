<?php
require_once __DIR__ . '/functions.php';
requireAuth(); // Ensure user is logged in
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= e(APP_NAME) ?> — Dashboard</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/components.css">
    <style>
        .app-layout {
            display: flex;
            min-height: 100vh;
            background: var(--bg-body);
        }
        .main-content {
            flex: 1;
            margin-left: 280px; /* Sidebar width */
            padding: var(--space-8);
            transition: margin var(--transition-base);
        }
        @media (max-width: 1024px) {
            .main-content {
                margin-left: 0;
            }
        }
    </style>
    <!-- Lucide Icons -->
    <script src="https://unpkg.com/lucide@latest"></script>
    <!-- Chart.js -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
</head>
<body>
    <div class="app-layout">
