<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$user = currentUser();
$hour = date('H');
if ($hour < 12) $greeting = "Good morning";
elseif ($hour < 18) $greeting = "Good afternoon";
else $greeting = "Good evening";
?>

<main class="main-content">
    <div class="dashboard-header" style="margin-bottom: var(--space-8);">
        <h1 class="text-gradient" style="font-size: var(--text-4xl); margin-bottom: var(--space-2);"><?= $greeting ?>, <?= e($user['name'] ?? 'Traveler') ?></h1>
        <p style="color: var(--text-secondary);">Where are we escaping to next?</p>
    </div>

    <!-- Stats -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(200px, 1fr)); gap: var(--space-4); margin-bottom: var(--space-8);">
        <div class="stat-card glass-card" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(0, 212, 255, 0.1); color: var(--accent-cyan); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="map-pin"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">0</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Trips Planned</p>
            </div>
        </div>
        <div class="stat-card glass-card" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(168, 85, 247, 0.1); color: var(--accent-purple); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="globe"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">0</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Countries</p>
            </div>
        </div>
        <div class="stat-card glass-card" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 107, 53, 0.1); color: var(--accent-orange); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="calendar"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">0</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Days Traveled</p>
            </div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-8);">
        <!-- Left Column -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-4);">
                <h3 style="font-size: var(--text-xl);">Recent Trips</h3>
                <a href="<?= APP_URL ?>/pages/my-trips.php" style="color: var(--accent-cyan); text-decoration: none; font-size: var(--text-sm);">View all</a>
            </div>
            
            <div class="trips-grid" style="display: grid; grid-template-columns: repeat(auto-fit, minmax(250px, 1fr)); gap: var(--space-4);">
                <!-- Empty State -->
                <div class="glass-card-static text-center" style="padding: var(--space-8); grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center;">
                    <i data-lucide="compass" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: var(--space-4);"></i>
                    <h4 style="margin-bottom: var(--space-2);">No trips yet</h4>
                    <p style="color: var(--text-secondary); margin-bottom: var(--space-4);">Start your next adventure today.</p>
                    <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-primary">Create Trip</a>
                </div>
            </div>
        </div>

        <!-- Right Column -->
        <div>
            <!-- AI Panel -->
            <div class="glass-card-static" style="padding: var(--space-6); background: linear-gradient(145deg, rgba(168, 85, 247, 0.05) 0%, rgba(0, 212, 255, 0.05) 100%);">
                <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4);">
                    <i data-lucide="sparkles" style="color: var(--accent-purple);"></i>
                    <h3 style="font-size: var(--text-lg); margin: 0;">AI Insights</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <div style="background: var(--bg-elevated); padding: var(--space-3); border-radius: var(--radius-md); font-size: var(--text-sm);">
                        Based on your interest in <span style="color: var(--accent-cyan);">Adventure</span>, we recommend exploring Patagonia next month.
                    </div>
                    <div style="background: var(--bg-elevated); padding: var(--space-3); border-radius: var(--radius-md); font-size: var(--text-sm);">
                        Flights to Kyoto are 20% cheaper than usual right now.
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div style="margin-top: var(--space-8);">
                <h3 style="font-size: var(--text-lg); margin-bottom: var(--space-4);">Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: var(--space-2);">
                    <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-secondary" style="justify-content: flex-start;">
                        <i data-lucide="plus"></i> New Trip
                    </a>
                    <a href="<?= APP_URL ?>/pages/city-search.php" class="btn btn-secondary" style="justify-content: flex-start;">
                        <i data-lucide="search"></i> Explore Cities
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
