<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$userId = currentUserId();
$tripId = intval($_GET['id'] ?? ($_GET['trip_id'] ?? 1));

// Fetch Trip Data
$trip = db()->fetch("SELECT * FROM trips WHERE id = ? AND user_id = ?", [$tripId, $userId]);
if (!$trip) {
    setFlash('error', 'Trip not found.');
    redirect('/pages/my-trips.php');
}

// Fetch Expenses
$expenses = db()->fetchAll("SELECT * FROM expenses WHERE trip_id = ? ORDER BY expense_date DESC", [$tripId]);

$budgetTotal = floatval($trip['budget_total']);
$totalSpent = 0;
$catTotals = [];
$dailyTotals = [];

foreach ($expenses as $exp) {
    $amount = floatval($exp['amount']);
    $totalSpent += $amount;
    
    // Category totals for pie chart
    $cat = $exp['category'] ?: 'other';
    $catTotals[$cat] = ($catTotals[$cat] ?? 0) + $amount;
    
    // Daily totals for line chart
    $date = $exp['expense_date'] ?? 'TBD';
    if ($date !== 'TBD') {
        $dailyTotals[$date] = ($dailyTotals[$date] ?? 0) + $amount;
    }
}

ksort($dailyTotals); // Sort by date

$remaining = $budgetTotal - $totalSpent;
$spentPct = $budgetTotal > 0 ? min(100, round(($totalSpent / $budgetTotal) * 100)) : 0;

$theme = getMoodTheme($trip['mood']);

require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';
?>

<main class="main-content page-transition">
    <!-- Include Chart.js from CDN if not globally loaded -->
    <script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

    <div class="page-header" style="margin-bottom: var(--space-8); display: flex; justify-content: space-between; align-items: flex-end;">
        <div>
            <a href="<?= APP_URL ?>/pages/itinerary-view.php?id=<?= $tripId ?>" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
                <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Itinerary
            </a>
            <h1 style="font-size: var(--text-4xl); margin-bottom: var(--space-2); font-weight:800; letter-spacing:-0.03em;">Budget — <?= e($trip['name']) ?></h1>
            <p style="color: var(--text-secondary); font-size:var(--text-lg);">Real-time spending intelligence.</p>
        </div>
        <div style="display: flex; gap: var(--space-3);">
            <a href="<?= APP_URL ?>/pages/invoice.php?trip_id=<?= $tripId ?>" class="btn btn-secondary"><i data-lucide="download" style="width:16px;height:16px;"></i> Export PDF</a>
        </div>
    </div>

    <!-- Magic Add Expense Panel -->
    <div class="glass-card-static" style="margin-bottom: var(--space-8); background: var(--gradient-hero); border-color: rgba(56,189,248,0.2);">
        <div style="display:flex;align-items:flex-start;gap:var(--space-3);">
            <i data-lucide="sparkles" style="width:24px;height:24px;color:var(--accent-cyan);margin-top:4px;"></i>
            <div style="flex:1;">
                <h4 style="font-size:var(--text-lg);margin-bottom:4px;color:var(--text-primary);">Magic Add Expense</h4>
                <p style="font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--space-3);">Type a natural sentence and Gemini AI will automatically extract the vendor, amount, and category.</p>
                <div style="display:flex;gap:var(--space-2);">
                    <input type="text" id="magicExpenseInput" class="input-field" placeholder="e.g. Spent 45 euros on a taxi to the Eiffel Tower" style="flex:1;">
                    <button type="button" class="btn btn-primary" onclick="magicAddExpense(this, <?= $tripId ?>)" style="padding:0 var(--space-4);">
                        <i data-lucide="wand-2" style="width:16px;height:16px;"></i>
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Budget Insights Panel -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-6); margin-bottom: var(--space-10);">
        <div class="stat-card">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm); font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Total Budget (<?= e($trip['currency']) ?>)</p>
            <h2 style="font-size: var(--text-4xl); font-weight:700;">$<?= number_format($budgetTotal, 2) ?></h2>
        </div>
        <div class="stat-card">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm); font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Total Spent</p>
            <h2 style="font-size: var(--text-4xl); font-weight:700; color: var(--accent-orange);">$<?= number_format($totalSpent, 2) ?></h2>
            <div style="width: 100%; height: 6px; background: rgba(255,255,255,0.05); border-radius: 999px; margin-top: var(--space-4); overflow: hidden; box-shadow:inset 0 1px 2px rgba(0,0,0,0.2);">
                <div style="width: <?= $spentPct ?>%; height: 100%; border-radius:999px; background: <?= $spentPct > 90 ? 'var(--accent-red)' : ($spentPct > 70 ? 'var(--accent-orange)' : 'var(--accent-cyan)') ?>;"></div>
            </div>
            <p style="font-size:var(--text-xs); color:var(--text-muted); margin-top:6px; font-weight:500; text-align:right;"><?= $spentPct ?>% used</p>
        </div>
        <div class="stat-card">
            <p style="color: var(--text-secondary); margin-bottom: var(--space-2); font-size: var(--text-sm); font-weight:500; text-transform:uppercase; letter-spacing:0.05em;">Remaining</p>
            <h2 style="font-size: var(--text-4xl); font-weight:700; color: <?= $remaining >= 0 ? 'var(--accent-green)' : 'var(--accent-red)' ?>;">$<?= number_format($remaining, 2) ?></h2>
        </div>
    </div>

    <!-- Charts Section -->
    <div style="display: grid; grid-template-columns: 1fr 1fr; gap: var(--space-6); margin-bottom: var(--space-10);">
        <div class="stat-card" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg); font-weight:600;">Spending by Category</h3>
            <div style="position: relative; height: 300px; width: 100%; display: flex; justify-content: center;">
                <canvas id="categoryChart"></canvas>
            </div>
        </div>
        <div class="stat-card" style="padding: var(--space-6);">
            <h3 style="margin-bottom: var(--space-4); font-size: var(--text-lg); font-weight:600;">Daily Spending Trend</h3>
            <div style="position: relative; height: 300px; width: 100%;">
                <canvas id="trendChart"></canvas>
            </div>
        </div>
    </div>

    <!-- Expense List -->
    <div class="stat-card" style="padding: var(--space-6);">
        <h3 style="margin-bottom: var(--space-6); font-size: var(--text-lg); font-weight:600;">Recent Expenses</h3>
        <div style="overflow-x:auto;">
            <table style="width: 100%; border-collapse: collapse; text-align: left;">
                <thead>
                    <tr style="border-bottom: 1px solid rgba(255,255,255,0.05); color: var(--text-muted); font-size: var(--text-sm);">
                        <th style="padding: var(--space-3) 0; font-weight:500;">Date</th>
                        <th style="padding: var(--space-3) 0; font-weight:500;">Description</th>
                        <th style="padding: var(--space-3) 0; font-weight:500;">Category</th>
                        <th style="padding: var(--space-3) 0; font-weight:500;">Status</th>
                        <th style="padding: var(--space-3) 0; text-align: right; font-weight:500;">Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (empty($expenses)): ?>
                    <tr>
                        <td colspan="5" style="text-align:center; padding:var(--space-8); color:var(--text-muted);">
                            No expenses logged yet.
                        </td>
                    </tr>
                    <?php else: ?>
                        <?php foreach($expenses as $exp): 
                            $badgeColor = match($exp['category']){
                                'transport' => 'badge-purple',
                                'accommodation' => 'badge-cyan',
                                'food' => 'badge-orange',
                                'sightseeing' => 'badge-green',
                                'shopping' => 'badge-red',
                                default => 'badge-cyan'
                            };
                        ?>
                        <tr style="border-bottom: 1px solid rgba(255,255,255,0.05);">
                            <td style="padding: var(--space-4) 0; color:var(--text-secondary); font-size:var(--text-sm);"><?= $exp['expense_date'] ? date('M j, Y', strtotime($exp['expense_date'])) : 'TBD' ?></td>
                            <td style="padding: var(--space-4) 0; font-weight: 500; font-size:var(--text-sm);"><?= e($exp['description']) ?></td>
                            <td style="padding: var(--space-4) 0;"><span class="badge <?= $badgeColor ?>" style="text-transform:capitalize;"><?= e($exp['category']) ?></span></td>
                            <td style="padding: var(--space-4) 0;">
                                <?php if($exp['is_paid']): ?>
                                    <span style="color:var(--accent-green); font-size:var(--text-xs); font-weight:500; display:flex; align-items:center; gap:4px;"><i data-lucide="check-circle" style="width:12px;height:12px;"></i> Paid</span>
                                <?php else: ?>
                                    <span style="color:var(--text-muted); font-size:var(--text-xs); font-weight:500; display:flex; align-items:center; gap:4px;"><i data-lucide="clock" style="width:12px;height:12px;"></i> Pending</span>
                                <?php endif; ?>
                            </td>
                            <td style="padding: var(--space-4) 0; text-align: right; font-weight:600; font-family:var(--font-mono);">$<?= number_format($exp['amount'], 2) ?></td>
                        </tr>
                        <?php endforeach; ?>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</main>

<script>
document.addEventListener('DOMContentLoaded', function() {
    if (typeof Chart === 'undefined') return;

    Chart.defaults.color = 'rgba(161, 161, 170, 0.8)';
    Chart.defaults.borderColor = 'rgba(255, 255, 255, 0.05)';
    Chart.defaults.font.family = "'Inter', sans-serif";

    // Prepare Category Data
    const catLabels = <?= json_encode(array_map('ucfirst', array_keys($catTotals))) ?>;
    const catData = <?= json_encode(array_values($catTotals)) ?>;
    const bgColors = {
        'Transport': '#c084fc',
        'Accommodation': '#38bdf8',
        'Food': '#fb923c',
        'Sightseeing': '#34d399',
        'Shopping': '#f87171',
        'Other': '#a1a1aa'
    };
    const mappedBgColors = catLabels.map(l => bgColors[l] || '#38bdf8');

    // Category Chart (Doughnut)
    if (document.getElementById('categoryChart')) {
        const ctxCat = document.getElementById('categoryChart').getContext('2d');
        new Chart(ctxCat, {
            type: 'doughnut',
            data: {
                labels: catLabels.length ? catLabels : ['No Data'],
                datasets: [{
                    data: catData.length ? catData : [1],
                    backgroundColor: catData.length ? mappedBgColors : ['#27272a'],
                    borderWidth: 0,
                    cutout: '75%',
                    borderRadius: 4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { position: 'right', labels: { usePointStyle: true, padding: 20 } },
                    tooltip: {
                        backgroundColor: 'rgba(9, 9, 11, 0.9)',
                        titleColor: '#fafafa',
                        bodyColor: '#a1a1aa',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: true,
                        callbacks: {
                            label: function(context) {
                                return ' $' + context.raw.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Prepare Trend Data
    const trendDates = <?= json_encode(array_map(function($d){ return date('M j', strtotime($d)); }, array_keys($dailyTotals))) ?>;
    const trendData = <?= json_encode(array_values($dailyTotals)) ?>;

    // Trend Chart (Line)
    if (document.getElementById('trendChart')) {
        const ctxTrend = document.getElementById('trendChart').getContext('2d');
        
        let gradient = ctxTrend.createLinearGradient(0, 0, 0, 300);
        gradient.addColorStop(0, 'rgba(56, 189, 248, 0.2)');
        gradient.addColorStop(1, 'rgba(56, 189, 248, 0)');

        new Chart(ctxTrend, {
            type: 'line',
            data: {
                labels: trendDates.length ? trendDates : ['Day 1'],
                datasets: [{
                    label: 'Daily Spend',
                    data: trendData.length ? trendData : [0],
                    borderColor: '#38bdf8',
                    backgroundColor: gradient,
                    borderWidth: 2,
                    tension: 0.4,
                    fill: true,
                    pointBackgroundColor: '#09090b',
                    pointBorderColor: '#38bdf8',
                    pointBorderWidth: 2,
                    pointRadius: 4,
                    pointHoverRadius: 6
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: { display: false },
                    tooltip: {
                        backgroundColor: 'rgba(9, 9, 11, 0.9)',
                        titleColor: '#a1a1aa',
                        bodyColor: '#fafafa',
                        borderColor: 'rgba(255,255,255,0.1)',
                        borderWidth: 1,
                        padding: 12,
                        displayColors: false,
                        callbacks: {
                            label: function(context) {
                                return ' $' + context.raw.toLocaleString();
                            }
                        }
                    }
                },
                scales: {
                    y: { 
                        beginAtZero: true,
                        grid: { color: 'rgba(255,255,255,0.03)' },
                        border: { display: false }
                    },
                    x: {
                        grid: { display: false },
                        border: { display: false }
                    }
                }
            }
        });
    }
    }
});

async function magicAddExpense(btn, tripId) {
    const input = document.getElementById('magicExpenseInput');
    const prompt = input.value.trim();
    if (!prompt) return;

    const originalHTML = btn.innerHTML;
    btn.innerHTML = '<i data-lucide="loader-2" style="width:16px;height:16px;animation:spin 1s linear infinite;"></i>';
    btn.style.pointerEvents = 'none';
    lucide.createIcons();

    try {
        const formData = new FormData();
        formData.append('action', 'create_magic');
        formData.append('trip_id', tripId);
        formData.append('prompt', prompt);

        const res = await fetch(`<?= APP_URL ?>/api/budget.php`, {
            method: 'POST',
            body: formData
        });
        const json = await res.json();
        
        if (json.success && json.expense) {
            alert(`Added: ${json.expense.amount} ${json.expense.currency} for ${json.expense.vendor} (${json.expense.category})`);
            window.location.reload();
        } else {
            alert('Gemini could not understand the expense. Please try again.');
        }
    } catch (err) {
        console.error(err);
        alert('API error.');
    } finally {
        btn.innerHTML = originalHTML;
        btn.style.pointerEvents = 'auto';
        lucide.createIcons();
    }
}
</script>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
