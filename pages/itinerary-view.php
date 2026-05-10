<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$userId = currentUserId();
$tripId = intval($_GET['id'] ?? ($_GET['trip_id'] ?? 1));

// Fetch Trip
$trip = db()->fetch("SELECT * FROM trips WHERE id = ? AND user_id = ?", [$tripId, $userId]);
if (!$trip) {
    setFlash('error', 'Trip not found.');
    redirect('/pages/my-trips.php');
}

// Fetch Itinerary Sections
$sections = db()->fetchAll("SELECT * FROM itinerary_sections WHERE trip_id = ? ORDER BY start_date ASC, order_index ASC", [$tripId]);

// Group by Date
$days = [];
foreach ($sections as $sec) {
    $date = $sec['start_date'] ?? 'TBD';
    if (!isset($days[$date])) {
        $days[$date] = [
            'date' => $date,
            'stop' => $trip['destination'], // simplifed stop name
            'items' => []
        ];
    }
    // Map section type to categories
    $cat = match($sec['section_type']) {
        'hotel' => 'accommodation',
        'travel' => 'transport',
        'food' => 'food',
        'activity' => 'activity',
        default => 'other'
    };
    
    $days[$date]['items'][] = [
        'time' => 'TBD',
        'type' => 'activity',
        'title' => $sec['title'],
        'notes' => $sec['notes'] ?? '',
        'cost' => floatval($sec['budget']),
        'cat' => $cat,
        'status' => $sec['status'],
        'duration' => '1h'
    ];
}

// Fetch Expenses
$expenses = db()->fetchAll("SELECT * FROM expenses WHERE trip_id = ?", [$tripId]);
foreach ($expenses as $exp) {
    $date = $exp['expense_date'] ?? 'TBD';
    if (!isset($days[$date])) {
        $days[$date] = ['date' => $date, 'stop' => $trip['destination'], 'items' => []];
    }
    $days[$date]['items'][] = [
        'time' => 'N/A',
        'type' => 'expense',
        'title' => $exp['description'],
        'notes' => 'Expense logged',
        'cost' => floatval($exp['amount']),
        'cat' => $exp['category'],
        'status' => $exp['is_paid'] ? 'completed' : 'planned',
        'duration' => '-'
    ];
}

// Sort days by date
ksort($days);
$daysList = array_values($days); // index 0,1,2...

$catColors=['transport'=>'#A855F7','accommodation'=>'#3B82F6','sightseeing'=>'#00D4FF','food'=>'#F59E0B','culture'=>'#EC4899','shopping'=>'#10B981','activity'=>'#FF6B35','other'=>'#94A3B8'];
$catIcons=['transport'=>'plane','accommodation'=>'building-2','sightseeing'=>'eye','food'=>'utensils','culture'=>'landmark','shopping'=>'shopping-bag','activity'=>'zap','other'=>'circle'];
$statusColors=['completed'=>'#10B981','planned'=>'#94A3B8','skipped'=>'#EF4444','booked'=>'#3B82F6'];

// Calculate Totals
$totalCost = 0;
$totalSpent = 0;
foreach ($sections as $s) $totalCost += $s['budget'];
foreach ($expenses as $e) $totalSpent += $e['amount'];

$budget = floatval($trip['budget_total']);
$spentPct = $budget > 0 ? min(100, round(($totalSpent / $budget) * 100)) : 0;
$theme = getMoodTheme($trip['mood']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Itinerary — <?= e($trip['name']) ?></title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}
.itinerary-layout{display:grid;grid-template-columns:1fr 320px;gap:var(--space-8);align-items:start;}
/* Toolbar */
.page-toolbar{display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);flex-wrap:wrap;}
.search-wrap{position:relative;flex:1;min-width:180px;}
.search-wrap input{width:100%;padding:var(--space-2) var(--space-4) var(--space-2) 36px;background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;box-shadow:var(--shadow-sm);}
.search-wrap input:focus{border-color:var(--accent-cyan);}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.tbar-sel{padding:var(--space-2) var(--space-3);background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* Day header */
.day-block{margin-bottom:var(--space-10);}
.day-header{display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-6);}
.day-badge{min-width:64px;height:64px;border-radius:var(--radius-xl);background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.2);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;}
.day-badge .day-num{font-size:var(--text-2xl);font-weight:var(--font-bold);color:var(--accent-cyan);line-height:1;}
.day-badge .day-lbl{font-size:10px;font-weight:var(--font-bold);color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.05em;margin-top:2px;}
/* Timeline */
.timeline{position:relative;padding-left:var(--space-8);}
.timeline::before{content:'';position:absolute;left:20px;top:0;bottom:0;width:1px;background:linear-gradient(to bottom,var(--accent-cyan),rgba(56,189,248,0.05));}
.timeline-item{position:relative;margin-bottom:var(--space-5);}
.timeline-dot{position:absolute;left:-26px;top:18px;width:12px;height:12px;border-radius:50%;border:2px solid var(--bg-primary);flex-shrink:0;z-index:2;box-shadow:0 0 0 1px rgba(255,255,255,0.1);}
/* Item card */
.item-card{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-5);transition:all var(--transition-base);box-shadow:var(--shadow-sm);}
.item-card::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.item-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.item-card:hover::before{border-color:rgba(56,189,248,0.3);}
.item-card.completed{opacity:0.6;}
.item-header{display:flex;align-items:flex-start;gap:var(--space-4);}
.item-icon{width:40px;height:40px;border-radius:var(--radius-lg);display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(255,255,255,0.05);}
.item-time{font-size:var(--text-xs);font-weight:var(--font-bold);color:var(--text-muted);white-space:nowrap;margin-top:2px;font-family:var(--font-mono);}
.item-meta{display:flex;align-items:center;gap:var(--space-4);margin-top:var(--space-2);flex-wrap:wrap;}
.item-meta span{display:flex;align-items:center;gap:4px;font-size:var(--text-xs);color:var(--text-secondary);font-weight:500;}
.item-meta i{width:12px;height:12px;}
.item-notes{font-size:var(--text-sm);color:var(--text-secondary);margin-top:var(--space-3);line-height:var(--leading-relaxed);padding-left:calc(40px + var(--space-4));}
.status-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.item-expense-tag{font-size:10px;padding:2px 8px;border-radius:9999px;background:rgba(251,146,60,0.1);color:var(--accent-orange);border:1px solid rgba(251,146,60,0.2);}
/* Stop label */
.stop-label{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-1) var(--space-3);background:rgba(56,189,248,0.08);border:1px solid rgba(56,189,248,0.15);border-radius:var(--radius-full);font-size:var(--text-xs);color:var(--accent-cyan);}
/* Sticky summary */
.summary-card{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-6);position:sticky;top:var(--space-8);margin-bottom:var(--space-5);box-shadow:var(--shadow-md);}
.summary-card::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.sum-stat{display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid rgba(255,255,255,0.05);font-size:var(--text-sm);}
.sum-stat:last-child{border-bottom:none;}
.prog-bar{width:100%;height:6px;background:rgba(255,255,255,0.05);border-radius:999px;overflow:hidden;margin-top:var(--space-3);box-shadow:inset 0 1px 2px rgba(0,0,0,0.2);}
.prog-fill{height:100%;border-radius:999px;}
/* Day nav */
.day-nav{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-5);box-shadow:var(--shadow-sm);}
.day-nav::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.day-nav-item{display:flex;align-items:center;gap:var(--space-3);padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);cursor:pointer;font-size:var(--text-sm);color:var(--text-secondary);transition:all var(--transition-fast);text-decoration:none;margin-bottom:var(--space-1);}
.day-nav-item:hover,.day-nav-item.active{background:rgba(255,255,255,0.05);color:var(--text-primary);}
.day-nav-item.active .dn-badge{background:var(--accent-cyan);color:var(--bg-primary);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Header -->
  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> My Trips
      </a>
      <h1 style="font-size:var(--text-4xl);margin-bottom:8px;font-weight:800;letter-spacing:-0.03em;"><?= e($trip['name']) ?></h1>
      <div style="display:flex;align-items:center;gap:var(--space-4);flex-wrap:wrap;">
        <span style="font-size:var(--text-sm);color:var(--text-secondary);display:flex;align-items:center;gap:4px;">
          <i data-lucide="map-pin" style="width:14px;height:14px;"></i> <?= e($trip['destination']) ?>
        </span>
        <span style="font-size:var(--text-sm);color:var(--text-secondary);display:flex;align-items:center;gap:4px;">
          <i data-lucide="calendar" style="width:14px;height:14px;"></i> <?= $trip['start_date'] ? date('M d', strtotime($trip['start_date'])) : 'TBD' ?> — <?= $trip['end_date'] ? date('M d, Y', strtotime($trip['end_date'])) : 'TBD' ?>
        </span>
        <span class="badge" style="background:<?= $theme['color'] ?>22;color:<?= $theme['color'] ?>;border:1px solid <?= $theme['color'] ?>44;">
            <i data-lucide="<?= e(str_replace(['<i data-lucide="', '"></i>'], '', $theme['icon'])) ?>" style="width:12px;height:12px;margin-right:4px;"></i>
            <?= e($theme['label']) ?>
        </span>
      </div>
    </div>
    <div style="display:flex;gap:var(--space-3);">
      <a href="<?= APP_URL ?>/pages/itinerary-builder.php?id=<?= $tripId ?>" class="btn btn-secondary">
        <i data-lucide="edit-3" style="width:16px;height:16px;"></i> Builder
      </a>
      <a href="<?= APP_URL ?>/pages/budget.php?id=<?= $tripId ?>" class="btn btn-secondary">
        <i data-lucide="wallet" style="width:16px;height:16px;"></i> Budget
      </a>
      <button class="btn btn-primary" onclick="window.print()">
        <i data-lucide="share-2" style="width:16px;height:16px;"></i> Share
      </button>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="page-toolbar">
    <div class="search-wrap">
      <span class="search-icon"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
      <input type="text" placeholder="Search activities, locations..." oninput="searchItems(this.value)">
    </div>
    <select class="tbar-sel" onchange="filterItems(this.value)">
      <option value="">All Types</option>
      <option value="activity">Activities</option>
      <option value="expense">Expenses</option>
    </select>
  </div>

  <div class="itinerary-layout">
    <!-- Timeline -->
    <div id="timelineContainer">
      <?php if (empty($daysList)): ?>
        <div class="empty-state glass-card-static" style="text-align:center;padding:var(--space-12);">
            <i data-lucide="calendar-plus" style="width:48px;height:48px;color:var(--text-muted);margin-bottom:var(--space-4);"></i>
            <h3>No Itinerary Yet</h3>
            <p style="color:var(--text-secondary);margin-bottom:var(--space-6);">Your journey is a blank canvas. Start adding places and activities.</p>
            <a href="<?= APP_URL ?>/pages/itinerary-builder.php?id=<?= $tripId ?>" class="btn btn-primary">Open Builder</a>
        </div>
      <?php else: ?>
          <?php foreach($daysList as $idx => $day):
            $dayNum = $idx + 1;
            $dayCost = array_sum(array_column($day['items'], 'cost'));
            $dayDate = $day['date'] !== 'TBD' ? date('l, M jS', strtotime($day['date'])) : 'Date TBD';
          ?>
          <div class="day-block animate-fade-in" id="day-<?= $dayNum ?>">
            <div class="day-header">
              <div class="day-badge">
                <span class="day-num"><?= $dayNum ?></span>
                <span class="day-lbl">Day</span>
              </div>
              <div>
                <h4 style="margin:0 0 4px;font-size:var(--text-xl);font-weight:600;"><?= $dayDate ?></h4>
                <div style="display:flex;align-items:center;gap:var(--space-3);">
                  <span class="stop-label"><i data-lucide="map-pin" style="width:12px;height:12px;"></i> <?= e($day['stop']) ?></span>
                  <span style="font-size:var(--text-xs);color:var(--text-muted);font-weight:500;"><?= count($day['items']) ?> items · $<?= number_format($dayCost) ?></span>
                </div>
              </div>
            </div>

            <div class="timeline">
              <?php foreach($day['items'] as $item):
                $cc = $catColors[$item['cat']] ?? '#94A3B8';
                $ci = $catIcons[$item['cat']] ?? 'circle';
                $sc = $statusColors[$item['status']] ?? '#94A3B8';
                $isExpense = $item['type'] === 'expense';
              ?>
              <div class="timeline-item" data-title="<?= strtolower($item['title']) ?>" data-type="<?= $item['type'] ?>" data-status="<?= $item['status'] ?>" data-cat="<?= $item['cat'] ?>">
                <div class="timeline-dot" style="background:<?= $sc ?>;"></div>
                <div class="item-card <?= $item['status']==='completed'?'completed':'' ?>">
                  <div class="item-header">
                    <div style="flex-shrink:0;text-align:right;width:45px;">
                      <div class="item-time"><?= e($item['time']) ?></div>
                    </div>
                    <div class="item-icon" style="background:<?= $cc ?>18;border-color:<?= $cc ?>40;">
                      <i data-lucide="<?= $ci ?>" style="width:18px;height:18px;color:<?= $cc ?>;"></i>
                    </div>
                    <div style="flex:1;">
                      <div style="display:flex;align-items:center;gap:var(--space-2);">
                        <h6 style="margin:0;font-size:var(--text-base);font-weight:600;"><?= e($item['title']) ?></h6>
                        <?php if($isExpense): ?><span class="item-expense-tag">Expense</span><?php endif; ?>
                      </div>
                      <div class="item-meta">
                        <span><i data-lucide="clock"></i><?= e($item['duration']) ?></span>
                        <?php if($item['cost'] > 0): ?>
                        <span style="color:<?= $isExpense?'var(--accent-orange)':'var(--accent-cyan)' ?>;font-weight:600;">
                          <i data-lucide="dollar-sign"></i><?= number_format($item['cost'], 2) ?>
                        </span>
                        <?php else: ?>
                        <span style="color:var(--accent-green);font-weight:600;"><i data-lucide="check"></i>Free</span>
                        <?php endif; ?>
                        <span style="display:flex;align-items:center;gap:4px;">
                          <span class="status-dot" style="background:<?= $sc ?>;"></span>
                          <?= ucfirst($item['status']) ?>
                        </span>
                      </div>
                    </div>
                  </div>
                  <?php if(!empty($item['notes'])): ?>
                  <p class="item-notes"><?= e($item['notes']) ?></p>
                  <?php endif; ?>
                </div>
              </div>
              <?php endforeach; ?>
            </div>
          </div>
          <?php endforeach; ?>
      <?php endif; ?>
    </div>

    <!-- Right Panel -->
    <?php if (!empty($daysList)): ?>
    <div>
      <!-- Trip Summary -->
      <div class="summary-card">
        <h5 style="margin-bottom:var(--space-5);font-size:var(--text-lg);font-weight:600;">Trip Summary</h5>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Duration</span>
          <span style="font-weight:600;color:var(--text-primary);"><?= count($daysList) ?> days</span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Total Items</span>
          <span style="font-weight:600;color:var(--text-primary);"><?= array_sum(array_map(fn($d)=>count($d['items']),$daysList)) ?></span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Budget (<?= $trip['currency'] ?>)</span>
          <span style="font-weight:600;color:var(--accent-cyan);">$<?= number_format($budget, 2) ?></span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Spent Logged</span>
          <span style="font-weight:600;color:var(--accent-orange);">$<?= number_format($totalSpent, 2) ?></span>
        </div>
        <div class="prog-bar" style="margin-top:var(--space-5);">
          <div class="prog-fill" style="width:<?= $spentPct ?>%;background:<?= $spentPct>90?'var(--accent-red)':($spentPct>70?'var(--accent-orange)':'var(--accent-cyan)') ?>;"></div>
        </div>
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin-top:6px;font-weight:500;text-align:right;"><?= $spentPct ?>% of budget used</p>
      </div>

      <!-- Day Navigator -->
      <div class="day-nav">
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-4);text-transform:uppercase;letter-spacing:0.06em;font-weight:600;">Jump to Day</p>
        <?php foreach($daysList as $idx => $day): $dayNum = $idx + 1; ?>
        <a href="#day-<?= $dayNum ?>" class="day-nav-item" onclick="highlightDay(<?= $dayNum ?>)">
          <div class="dn-badge" style="width:32px;height:32px;border-radius:var(--radius-md);background:rgba(255,255,255,0.05);display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:bold;color:var(--text-primary);flex-shrink:0;transition:all var(--transition-fast);"><?= $dayNum ?></div>
          <div>
            <p style="font-size:var(--text-sm);font-weight:500;margin:0;"><?= $day['date'] !== 'TBD' ? date('M j', strtotime($day['date'])) : 'TBD' ?></p>
            <p style="font-size:10px;color:var(--text-muted);margin:0;font-weight:500;"><?= count($day['items']) ?> items</p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

</main>
</div>

<script>
function searchItems(q) {
  q = q.toLowerCase();
  document.querySelectorAll('.timeline-item').forEach(el => {
    el.style.display = (el.dataset.title||'').includes(q) ? '' : 'none';
  });
}
function filterItems(val) {
  document.querySelectorAll('.timeline-item').forEach(el => {
    if(!val) { el.style.display=''; return; }
    const match = el.dataset.type===val || el.dataset.status===val || el.dataset.cat===val;
    el.style.display = match ? '' : 'none';
  });
}
function highlightDay(n) {
  document.querySelectorAll('.day-nav-item').forEach(a => a.classList.remove('active'));
  event.currentTarget.classList.add('active');
}
lucide.createIcons();
</script>
</body>
</html>
