<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Trip Simulation — JourneyOS AI</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
    <script src="https://unpkg.com/lucide@latest"></script>
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
    <style>
        .app-layout { display: flex; min-height: 100vh; }
        .main-content { flex: 1; margin-left: 260px; padding: var(--space-8); background: var(--bg-primary); }
        .sim-grid { display: grid; grid-template-columns: repeat(auto-fit, minmax(400px, 1fr)); gap: var(--space-6); margin-top: var(--space-8); }
        .chart-card { background: var(--bg-glass); border-radius: var(--radius-2xl); padding: var(--space-6); border: 1px solid rgba(255,255,255,0.05); }
        .score-circle { width: 120px; height: 120px; border-radius: 50%; border: 8px solid rgba(0,212,255,0.1); display: flex; align-items: center; justify-content: center; font-size: 2rem; font-weight: 800; margin-bottom: var(--space-4); position: relative; }
        .score-circle::after { content: ''; position: absolute; top: -8px; left: -8px; right: -8px; bottom: -8px; border-radius: 50%; border: 8px solid var(--accent-cyan); border-top-color: transparent; transform: rotate(45deg); }
    </style>
</head>
<body>
    <div class="app-layout">
        <?php include __DIR__ . '/../includes/sidebar.php'; ?>
        
        <main class="main-content">
            <div class="header">
                <span class="badge badge-purple animate-fade-in"><i data-lucide="bar-chart-2"></i> Predictive Intelligence</span>
                <h1 class="mt-2 animate-fade-in-up" style="font-size: var(--text-4xl); font-weight: 800;">Trip <span class="text-gradient-aurora">Simulation</span></h1>
                <p class="mt-2 animate-fade-in-up delay-100" style="color: var(--text-secondary);">AI-driven predictive analysis for your upcoming journeys.</p>
            </div>

            <div class="sim-grid">
                <div class="chart-card reveal-scale">
                    <h3>Fatigue Prediction</h3>
                    <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Estimated energy levels across your 5-day trip.</p>
                    <canvas id="fatigueChart"></canvas>
                </div>

                <div class="chart-card reveal-scale delay-100">
                    <h3>Budget Variance Probability</h3>
                    <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Likelihood of staying within your $1,500 budget.</p>
                    <canvas id="budgetChart"></canvas>
                </div>

                <div class="chart-card reveal-scale delay-200">
                    <div style="display: flex; justify-content: space-between; align-items: flex-start;">
                        <div>
                            <h3>Health Score</h3>
                            <p style="font-size: var(--text-sm); color: var(--text-muted); margin-bottom: var(--space-4);">Overall trip sustainability index.</p>
                        </div>
                        <div class="score-circle text-gradient">84%</div>
                    </div>
                    <div class="suggestions" style="margin-top: var(--space-4);">
                        <div style="background: rgba(16,185,129,0.1); padding: var(--space-3); border-radius: var(--radius-lg); border-left: 4px solid var(--accent-green); margin-bottom: var(--space-3);">
                            <p style="font-size: var(--text-sm); font-weight: 600;">Optimal Flow Detected</p>
                            <p style="font-size: var(--text-xs); color: var(--text-secondary);">Your current pace allows for maximum cultural immersion without burnout.</p>
                        </div>
                        <div style="background: rgba(245,158,11,0.1); padding: var(--space-3); border-radius: var(--radius-lg); border-left: 4px solid var(--accent-orange);">
                            <p style="font-size: var(--text-sm); font-weight: 600;">Weather Risk: High (Day 3)</p>
                            <p style="font-size: var(--text-xs); color: var(--text-secondary);">70% chance of rain in Taipei. Consider indoor activities for Ximending.</p>
                        </div>
                    </div>
                </div>

                <div class="chart-card reveal-scale delay-300">
                    <h3>Activity Distribution</h3>
                    <canvas id="distChart"></canvas>
                </div>
            </div>
        </main>
    </div>

    <script>
        lucide.createIcons();

        // Fatigue Chart
        new Chart(document.getElementById('fatigueChart'), {
            type: 'line',
            data: {
                labels: ['Day 1', 'Day 2', 'Day 3', 'Day 4', 'Day 5'],
                datasets: [{
                    label: 'Energy Level',
                    data: [100, 85, 60, 75, 50],
                    borderColor: '#00D4FF',
                    backgroundColor: 'rgba(0, 212, 255, 0.1)',
                    fill: true,
                    tension: 0.4
                }]
            },
            options: { plugins: { legend: { display: false } }, scales: { y: { beginAtZero: true, max: 100 } } }
        });

        // Budget Chart
        new Chart(document.getElementById('budgetChart'), {
            type: 'bar',
            data: {
                labels: ['Food', 'Transport', 'Stay', 'Activities', 'Shopping'],
                datasets: [{
                    label: 'Planned',
                    data: [300, 200, 600, 300, 100],
                    backgroundColor: '#A855F7'
                }, {
                    label: 'Predicted',
                    data: [350, 220, 600, 350, 150],
                    backgroundColor: 'rgba(168, 85, 247, 0.3)'
                }]
            }
        });

        // Distribution Chart
        new Chart(document.getElementById('distChart'), {
            type: 'doughnut',
            data: {
                labels: ['Adventure', 'Relaxation', 'Culture', 'Food'],
                datasets: [{
                    data: [40, 20, 25, 15],
                    backgroundColor: ['#FF6B35', '#10B981', '#6366F1', '#EC4899'],
                    borderWidth: 0
                }]
            },
            options: { plugins: { legend: { position: 'bottom' } } }
        });
    </script>
</body>
</html>
