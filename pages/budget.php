<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content">
    <div class="dashboard-header" style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <h1 class="text-gradient" style="font-size: var(--text-4xl); margin-bottom: var(--space-2);">Budget Dashboard</h1>
            <p style="color: var(--text-secondary);">Real-time spending intelligence.</p>
        </div>
        <div style="display: flex; gap: var(--space-3);">
            <button class="btn btn-secondary"><i data-lucide="download"></i> Export PDF</button>
            <button class="btn btn-primary"><i data-lucide="plus"></i> Add Expense</button>
        </div>
    </div>

    <!-- Budget Insights Panel -->
    <div class="stats-grid" style="display: grid; grid-template-columns: repeat(3, 1fr); gap: var(--space-4); margin-bottom: var(--space-8);">
        <div class="stat-card glass-card-static" style="padding: var(--space-6);">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm);">Total Budget</p>
            <h2 style="font-size: var(--text-3xl);">$5,000.00</h2>
        </div>
        <div class="stat-card glass-card-static" style="padding: var(--space-6);">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm);">Total Spent</p>
            <h2 style="font-size: var(--text-3xl); color: var(--accent-orange);">$2,450.00</h2>
            <div style="width: 100%; height: 6px; background: var(--bg-elevated); border-radius: 3px; margin-top: var(--space-4); overflow: hidden;">
                <div style="width: 49%; height: 100%; background: var(--gradient-warm);"></div>
            </div>
        </div>
        <div class="stat-card glass-card-static" style="padding: var(--space-6);">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm);">Remaining</p>
            <h2 style="font-size: var(--text-3xl); color: var(--accent-green);">$2,550.00</h2>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6); margin-bottom: var(--space-8);">
        <div class="glass-card-static" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">Spending by Category</h3>
            <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="glass-card-static" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">Daily Spending Trend</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Expense List -->
    <div class="glass-card-static" style="padding: var(--space-6);">
        <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg);">Recent Expenses</h3>
        <table style="width: 100%; border-collapse: collapse; text-align: left;">
            <thead>
                <tr style="border-bottom: 1px solid var(--border-light); color: var(--text-muted); font-size: var(--text-sm);">
                    <th style="padding: var(--space-3) 0;">Date</th>
                    <th style="padding: var(--space-3) 0;">Description</th>
                    <th style="padding: var(--space-3) 0;">Category</th>
                    <th style="padding: var(--space-3) 0; text-align: right;">Amount</th>
                </tr>
            </thead>
            <tbody>
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: var(--space-3) 0;">Oct 12, 2026</td>
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Flight to Tokyo</td>
                    <td style="padding: var(--space-3) 0;"><span class="badge badge-purple">Transport</span></td>
                    <td style="padding: var(--space-3) 0; text-align: right;">$1,200.00</td>
                </tr>
                <tr style="border-bottom: 1px solid var(--border-light);">
                    <td style="padding: var(--space-3) 0;">Oct 12, 2026</td>
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Shinjuku Hotel Deposit</td>
                    <td style="padding: var(--space-3) 0;"><span class="badge badge-cyan">Accommodation</span></td>
                    <td style="padding: var(--space-3) 0; text-align: right;">$800.00</td>
                </tr>
                <tr>
                    <td style="padding: var(--space-3) 0;">Oct 15, 2026</td>
                    <td style="padding: var(--space-3) 0; font-weight: var(--font-medium);">Sushi Omakase</td>
                    <td style="padding: var(--space-3) 0;"><span class="badge badge-orange">Food</span></td>
                    <td style="padding: var(--space-3) 0; text-align: right;">$150.00</td>
                </tr>
            </tbody>
        </table>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    Chart.defaults.color = 'rgba(148, 163, 184, 0.8)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';

    // Category Chart (Pie)
    const ctxCat = document.getElementById('categoryChart').getContext('2d');
    new Chart(ctxCat, {
        type: 'doughnut',
        data: {
            labels: ['Transport', 'Accommodation', 'Food', 'Activities'],
            datasets: [{
                data: [1200, 800, 150, 300],
                backgroundColor: [
                    '#A855F7',
                    '#00D4FF',
                    '#FF6B35',
                    '#10B981'
                ],
                borderWidth: 0,
                cutout: '70%'
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: { position: 'right' }
            }
        }
    });

    // Trend Chart (Line)
    const ctxTrend = document.getElementById('trendChart').getContext('2d');
    new Chart(ctxTrend, {
        type: 'line',
        data: {
            labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
            datasets: [{
                label: 'Daily Spend',
                data: [50, 120, 80, 200, 150],
                borderColor: '#00D4FF',
                backgroundColor: 'rgba(0, 212, 255, 0.1)',
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
});
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
