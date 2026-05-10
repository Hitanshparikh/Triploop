<?php
require_once __DIR__ . '/../../includes/functions.php';
requireAdmin();
require_once __DIR__ . '/../../includes/header.php';
require_once __DIR__ . '/../../includes/sidebar.php';
?>

<main class="main-content">
    <div class="dashboard-header" style="margin-bottom: var(--space-8);">
        <h1 class="text-gradient-purple" style="font-size: var(--text-4xl); margin-bottom: var(--space-2);">Admin Dashboard</h1>
        <p style="color: var(--text-secondary);">System overview and analytics.</p>
    </div>

    <!-- Metrics Cards -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(4, 1fr); gap: var(--space-4); margin-bottom: var(--space-8);">
        <div class="stat-card glass-card-static" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(0, 212, 255, 0.1); color: var(--accent-cyan); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="users"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">1,240</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Total Users</p>
            </div>
        </div>
        <div class="stat-card glass-card-static" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(168, 85, 247, 0.1); color: var(--accent-purple); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="map"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">8,530</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Total Trips</p>
            </div>
        </div>
        <div class="stat-card glass-card-static" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(16, 185, 129, 0.1); color: var(--accent-green); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="activity"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">342</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Active Trips</p>
            </div>
        </div>
        <div class="stat-card glass-card-static" style="padding: var(--space-4); display: flex; align-items: center; gap: var(--space-4);">
            <div style="width: 48px; height: 48px; border-radius: 50%; background: rgba(255, 107, 53, 0.1); color: var(--accent-orange); display: flex; align-items: center; justify-content: center;">
                <i data-lucide="dollar-sign"></i>
            </div>
            <div>
                <p style="font-size: var(--text-2xl); font-weight: bold; margin: 0;">$12.5k</p>
                <p style="color: var(--text-muted); font-size: var(--text-sm); margin: 0;">Revenue</p>
            </div>
        </div>
    </div>

    <!-- Charts -->
    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-6); margin-bottom: var(--space-8);">
        <div class="glass-card-static" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">User Signups (30 Days)</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="signupsChart"></canvas>
            </div>
        </div>
        <div class="glass-card-static" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">Trips by Mood</h3>
            <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                <canvas id="moodsChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Top Cities Table -->
    <div class="glass-card-static" style="padding: var(--space-6);">
        <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">Top Destinations</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-muted); font-size: var(--text-sm);">
                    <th style="padding: var(--space-3) 0;">City</th>
                    <th style="padding: var(--space-3) 0;">Country</th>
                    <th style="padding: var(--space-3) 0;">Trip Count</th>
                    <th style="padding: var(--space-3) 0;">Trend</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Tokyo</td>
                    <td style="padding: var(--space-3) 0;">Japan</td>
                    <td style="padding: var(--space-3) 0;">1,240</td>
                    <td style="padding: var(--space-3) 0; color: var(--accent-green);"><i data-lucide="trending-up" style="width: 16px; height: 16px;"></i> +12%</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Paris</td>
                    <td style="padding: var(--space-3) 0;">France</td>
                    <td style="padding: var(--space-3) 0;">980</td>
                    <td style="padding: var(--space-3) 0; color: var(--accent-green);"><i data-lucide="trending-up" style="width: 16px; height: 16px;"></i> +5%</td>
                </tr>
                <tr>
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Bali</td>
                    <td style="padding: var(--space-3) 0;">Indonesia</td>
                    <td style="padding: var(--space-3) 0;">850</td>
                    <td style="padding: var(--space-3) 0; color: var(--accent-red);"><i data-lucide="trending-down" style="width: 16px; height: 16px;"></i> -2%</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = 'rgba(148, 163, 184, 0.8)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

    // Signups Chart (Line)
    const ctxSignups = document.getElementById('signupsChart').getContext('2d');
    new Chart(ctxSignups, {
        type: 'line',
        data: {
            labels: ['Week 1', 'Week 2', 'Week 3', 'Week 4'],
            datasets: [{
                label: 'Signups',
                data: [150, 230, 180, 320],
                borderColor: '#A855F7',
                backgroundColor: 'rgba(168, 85, 247, 0.1)',
                tension: 0.4,
                fill: true
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            scales: {
                y: { beginAtZero: true }
            }
        }
    });

    // Moods Chart (Pie)
    const ctxMoods = document.getElementById('moodsChart').getContext('2d');
    new Chart(ctxMoods, {
        type: 'pie',
        data: {
            labels: ['Adventure', 'Romantic', 'Luxury', 'Healing'],
            datasets: [{
                data: [40, 25, 20, 15],
                backgroundColor: [
                    '#FF6B35',
                    '#EC4899',
                    '#F59E0B',
                    '#10B981'
                ],
                borderWidth: 0
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'bottom' }
            }
        }
    });
});
</script>

<?php require_once __DIR__ . '/../../includes/footer.php'; ?>
