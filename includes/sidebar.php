<aside class="sidebar glass-card-static" style="width: 280px; position: fixed; height: 100vh; overflow-y: auto; padding: var(--space-6); display: flex; flex-direction: column; z-index: 100; border-radius: 0; border-right: var(--border-subtle); background: var(--bg-elevated);">
    <div class="sidebar-header" style="margin-bottom: var(--space-8);">
        <a href="<?= APP_URL ?>/pages/dashboard.php" class="logo">
            <span class="logo-icon text-gradient">✦</span>
            <span class="logo-text">JourneyOS</span>
            <span class="logo-badge">AI</span>
        </a>
    </div>

    <nav class="sidebar-nav" style="display: flex; flex-direction: column; gap: var(--space-2); flex: 1;">
        <?php
        $navItems = [
            ['url' => '/pages/dashboard.php', 'icon' => 'layout-dashboard', 'label' => 'Dashboard'],
            ['url' => '/pages/my-trips.php', 'icon' => 'map', 'label' => 'My Trips'],
            ['url' => '/pages/create-trip.php', 'icon' => 'plus-circle', 'label' => 'Create Trip'],
            ['url' => '/pages/budget.php', 'icon' => 'wallet', 'label' => 'Budget'],
            ['url' => '/pages/journal.php', 'icon' => 'book-open', 'label' => 'Journal'],
            ['url' => '/pages/packing.php', 'icon' => 'check-square', 'label' => 'Packing List'],
            ['url' => '/pages/community.php', 'icon' => 'users', 'label' => 'Community Feed'],
            ['url' => '/pages/city-search.php', 'icon' => 'search', 'label' => 'Discover'],
            ['url' => '/pages/simulation.php', 'icon' => 'bar-chart-2', 'label' => 'Trip Simulation'],
            ['url' => '/pages/bookings.php', 'icon' => 'plane', 'label' => 'Bookings'],
        ];
        
        $currentPath = $_SERVER['SCRIPT_NAME'];
        foreach ($navItems as $item):
            $isActive = strpos($currentPath, $item['url']) !== false;
        ?>
        <a href="<?= APP_URL . $item['url'] ?>" class="sidebar-link <?= $isActive ? 'active' : '' ?>" style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); color: <?= $isActive ? 'var(--accent-cyan)' : 'var(--text-secondary)' ?>; background: <?= $isActive ? 'rgba(0, 212, 255, 0.1)' : 'transparent' ?>; text-decoration: none; font-weight: var(--font-medium); transition: all var(--transition-base);">
            <i data-lucide="<?= $item['icon'] ?>" style="width: 20px; height: 20px;"></i>
            <?= $item['label'] ?>
        </a>
        <?php endforeach; ?>
        
        <?php if(isAdmin()): ?>
        <div class="sidebar-divider" style="height: 1px; background: var(--border-light); margin: var(--space-4) 0;"></div>
        <a href="<?= APP_URL ?>/pages/admin/dashboard.php" class="sidebar-link <?= strpos($currentPath, '/admin/dashboard.php') !== false ? 'active' : '' ?>" style="display: flex; align-items: center; gap: var(--space-3); padding: var(--space-3) var(--space-4); border-radius: var(--radius-lg); color: var(--accent-purple); text-decoration: none; font-weight: var(--font-medium);">
            <i data-lucide="shield" style="width: 20px; height: 20px;"></i>
            Admin Panel
        </a>
        <?php endif; ?>
    </nav>

    <div class="sidebar-footer" style="margin-top: auto; padding-top: var(--space-6); border-top: 1px solid var(--border-light);">
        <div class="user-profile" style="display: flex; align-items: center; gap: var(--space-3);">
            <div class="avatar" style="width: 40px; height: 40px; border-radius: 50%; background: var(--gradient-cyan); display: flex; align-items: center; justify-content: center; font-weight: bold; color: #fff;">
                <?= substr($user['name'] ?? 'U', 0, 1) ?>
            </div>
            <div class="user-info" style="flex: 1; overflow: hidden;">
                <p style="margin: 0; font-size: var(--text-sm); font-weight: var(--font-medium); white-space: nowrap; text-overflow: ellipsis;"><?= e($user['name'] ?? 'User') ?></p>
                <a href="<?= APP_URL ?>/api/auth.php?action=logout" style="font-size: var(--text-xs); color: var(--text-muted); text-decoration: none;">Sign out</a>
            </div>
        </div>
    </div>
</aside>
