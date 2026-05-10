<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user   = currentUser();
$tripId = $_GET['trip_id'] ?? 1;
$view   = $_GET['view'] ?? 'timeline'; // timeline | list

// Demo itinerary data
$trip = ['name'=>'Paris & Rome Adventure','destination'=>'France · Italy','dates'=>'Jun 12 – Jun 18, 2025','duration'=>7,'mood'=>'romantic','budget'=>5000,'spent'=>3200];
$days = [
  1 => ['date'=>'2025-06-12','stop'=>'Paris','items'=>[
    ['time'=>'09:00','type'=>'activity','title'=>'Charles de Gaulle Airport Arrival','notes'=>'Terminal 2F, collect luggage, take RER B to city center.','cost'=>25,'cat'=>'transport','status'=>'completed','duration'=>'2h'],
    ['time'=>'13:00','type'=>'activity','title'=>'Hotel Check-In — Le Marais','notes'=>'Check-in after 2pm. Room 204. Luggage storage if arriving early.','cost'=>180,'cat'=>'accommodation','status'=>'completed','duration'=>'1h'],
    ['time'=>'15:00','type'=>'activity','title'=>'Notre-Dame Cathedral Walk','notes'=>'Exterior viewing only (reconstruction). Walk along the Seine.','cost'=>0,'cat'=>'sightseeing','status'=>'completed','duration'=>'2h'],
    ['time'=>'19:30','type'=>'expense','title'=>'Dinner — Café de Flore','notes'=>'Classic French brasserie. Croque-monsieur and wine recommended.','cost'=>55,'cat'=>'food','status'=>'completed','duration'=>'2h'],
  ]],
  2 => ['date'=>'2025-06-13','stop'=>'Paris','items'=>[
    ['time'=>'08:00','type'=>'activity','title'=>'Sunrise at Eiffel Tower','notes'=>'Book skip-the-line. Go early to avoid crowds. Summit tickets only.','cost'=>35,'cat'=>'sightseeing','status'=>'completed','duration'=>'2h'],
    ['time'=>'11:00','type'=>'activity','title'=>'Louvre Museum','notes'=>'Pre-booked timed entry. Focus on Denon Wing — Mona Lisa, Winged Victory.','cost'=>22,'cat'=>'culture','status'=>'completed','duration'=>'3h'],
    ['time'=>'15:00','type'=>'expense','title'=>'Champs-Élysées Shopping','notes'=>'Allowance for souvenirs and boutique shopping.','cost'=>120,'cat'=>'shopping','status'=>'planned','duration'=>'2h'],
    ['time'=>'20:00','type'=>'activity','title'=>'Seine River Cruise','notes'=>'Evening illuminations cruise. 1hr. Board at Pont de l'Iéna.','cost'=>18,'cat'=>'sightseeing','status'=>'planned','duration'=>'1h'],
  ]],
  3 => ['date'=>'2025-06-14','stop'=>'Rome','items'=>[
    ['time'=>'07:00','type'=>'activity','title'=>'CDG → FCO Flight','notes'=>'Air France AF1234. Terminal 2E. Check-in 2hrs early.','cost'=>95,'cat'=>'transport','status'=>'planned','duration'=>'2h 15m'],
    ['time'=>'11:30','type'=>'activity','title'=>'Hotel Check-In — Rome','notes'=>'Check in after 2pm, room 302, breakfast included (7–10am).','cost'=>160,'cat'=>'accommodation','status'=>'planned','duration'=>'1h'],
    ['time'=>'14:00','type'=>'activity','title'=>'Colosseum & Roman Forum','notes'=>'Skip-the-line tickets booked. Guide tour 2:30pm. Wear comfy shoes.','cost'=>28,'cat'=>'sightseeing','status'=>'planned','duration'=>'3h'],
    ['time'=>'20:00','type'=>'expense','title'=>'Dinner — Trastevere District','notes'=>'Recommended: Da Enzo al 29. Try cacio e pepe and tiramisu.','cost'=>45,'cat'=>'food','status'=>'planned','duration'=>'2h'],
  ]],
];

$catColors=['transport'=>'#A855F7','accommodation'=>'#3B82F6','sightseeing'=>'#00D4FF','food'=>'#F59E0B','culture'=>'#EC4899','shopping'=>'#10B981','activity'=>'#FF6B35'];
$catIcons=['transport'=>'plane','accommodation'=>'building-2','sightseeing'=>'eye','food'=>'utensils','culture'=>'landmark','shopping'=>'shopping-bag','activity'=>'zap'];
$statusColors=['completed'=>'#10B981','planned'=>'#94A3B8','skipped'=>'#EF4444'];

$totalCost = 0;
foreach($days as $d) foreach($d['items'] as $item) $totalCost += $item['cost'];
$spentPct = min(100, round($trip['spent']/$trip['budget']*100));
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Itinerary — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Page layout */
.itinerary-layout{display:grid;grid-template-columns:1fr 300px;gap:var(--space-8);align-items:start;}
/* Toolbar */
.page-toolbar{display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);flex-wrap:wrap;}
.search-wrap{position:relative;flex:1;min-width:180px;}
.search-wrap input{width:100%;padding:var(--space-2) var(--space-4) var(--space-2) 36px;background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;}
.search-wrap input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:10px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.tbar-sel{padding:var(--space-2) var(--space-3);background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* Day header */
.day-block{margin-bottom:var(--space-8);}
.day-header{display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-5);}
.day-badge{min-width:56px;height:56px;border-radius:var(--radius-lg);background:var(--gradient-cyan);display:flex;flex-direction:column;align-items:center;justify-content:center;flex-shrink:0;}
.day-badge .day-num{font-size:var(--text-xl);font-weight:var(--font-bold);color:var(--bg-primary);line-height:1;}
.day-badge .day-lbl{font-size:9px;font-weight:var(--font-bold);color:rgba(11,16,32,0.7);text-transform:uppercase;letter-spacing:0.05em;}
/* Timeline */
.timeline{position:relative;padding-left:var(--space-8);}
.timeline::before{content:'';position:absolute;left:20px;top:0;bottom:0;width:2px;background:linear-gradient(to bottom,var(--accent-cyan),rgba(0,212,255,0.1));}
.timeline-item{position:relative;margin-bottom:var(--space-4);}
.timeline-dot{position:absolute;left:-28px;top:18px;width:12px;height:12px;border-radius:50%;border:2px solid var(--bg-primary);flex-shrink:0;}
/* Item card */
.item-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-4) var(--space-5);transition:all var(--transition-base);}
.item-card:hover{border-color:rgba(0,212,255,0.15);box-shadow:var(--shadow-md);}
.item-card.completed{opacity:0.75;}
.item-header{display:flex;align-items:flex-start;gap:var(--space-3);}
.item-icon{width:36px;height:36px;border-radius:var(--radius-md);display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.item-time{font-size:var(--text-xs);font-weight:var(--font-bold);color:var(--text-muted);white-space:nowrap;margin-top:2px;}
.item-meta{display:flex;align-items:center;gap:var(--space-3);margin-top:var(--space-2);flex-wrap:wrap;}
.item-meta span{display:flex;align-items:center;gap:3px;font-size:var(--text-xs);color:var(--text-muted);}
.item-meta i{width:11px;height:11px;}
.item-notes{font-size:var(--text-sm);color:var(--text-secondary);margin-top:var(--space-2);line-height:var(--leading-relaxed);}
.status-dot{width:8px;height:8px;border-radius:50%;flex-shrink:0;}
.item-expense-tag{font-size:10px;padding:2px 8px;border-radius:9999px;background:rgba(245,158,11,0.1);color:var(--accent-gold);border:1px solid rgba(245,158,11,0.2);}
/* Stop label */
.stop-label{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-1) var(--space-3);background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.15);border-radius:var(--radius-full);font-size:var(--text-xs);color:var(--accent-cyan);}
/* Sticky summary */
.summary-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-5);position:sticky;top:var(--space-8);margin-bottom:var(--space-5);}
.sum-stat{display:flex;justify-content:space-between;align-items:center;padding:var(--space-2) 0;border-bottom:1px solid rgba(148,163,184,0.06);font-size:var(--text-sm);}
.sum-stat:last-child{border-bottom:none;}
.prog-bar{width:100%;height:6px;background:var(--bg-elevated);border-radius:999px;overflow:hidden;margin-top:var(--space-2);}
.prog-fill{height:100%;border-radius:999px;}
/* Day nav */
.day-nav{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-4);}
.day-nav-item{display:flex;align-items:center;gap:var(--space-3);padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);cursor:pointer;font-size:var(--text-sm);color:var(--text-secondary);transition:all var(--transition-fast);text-decoration:none;}
.day-nav-item:hover,.day-nav-item.active{background:rgba(0,212,255,0.08);color:var(--accent-cyan);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Header -->
  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-6);">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> My Trips
      </a>
      <h1 style="font-size:var(--text-3xl);margin-bottom:4px;"><?= e($trip['name']) ?></h1>
      <div style="display:flex;align-items:center;gap:var(--space-4);flex-wrap:wrap;">
        <span style="font-size:var(--text-sm);color:var(--text-secondary);display:flex;align-items:center;gap:4px;">
          <i data-lucide="map-pin" style="width:12px;height:12px;"></i> <?= e($trip['destination']) ?>
        </span>
        <span style="font-size:var(--text-sm);color:var(--text-secondary);display:flex;align-items:center;gap:4px;">
          <i data-lucide="calendar" style="width:12px;height:12px;"></i> <?= e($trip['dates']) ?>
        </span>
        <span class="badge badge-purple"><?= ucfirst($trip['mood']) ?></span>
      </div>
    </div>
    <div style="display:flex;gap:var(--space-3);">
      <a href="<?= APP_URL ?>/pages/itinerary-builder.php?trip_id=<?= $tripId ?>" class="btn btn-secondary">
        <i data-lucide="edit-3" style="width:15px;height:15px;"></i> Edit
      </a>
      <a href="<?= APP_URL ?>/pages/invoice.php?trip_id=<?= $tripId ?>" class="btn btn-secondary">
        <i data-lucide="file-text" style="width:15px;height:15px;"></i> Invoice
      </a>
      <button class="btn btn-primary" onclick="window.print()">
        <i data-lucide="share-2" style="width:15px;height:15px;"></i> Share
      </button>
    </div>
  </div>

  <!-- Toolbar -->
  <div class="page-toolbar">
    <div class="search-wrap">
      <span class="search-icon"><i data-lucide="search" style="width:13px;height:13px;"></i></span>
      <input type="text" placeholder="Search bar..." oninput="searchItems(this.value)">
    </div>
    <select class="tbar-sel" onchange="groupItems(this.value)">
      <option value="">Group by...</option>
      <option value="stop">Stop / City</option>
      <option value="type">Type</option>
    </select>
    <select class="tbar-sel" onchange="filterItems(this.value)">
      <option value="">Filter...</option>
      <option value="activity">Activities</option>
      <option value="expense">Expenses</option>
      <option value="completed">Completed</option>
      <option value="planned">Planned</option>
    </select>
    <select class="tbar-sel">
      <option>Sort by...</option>
      <option>Time ↑</option>
      <option>Cost ↓</option>
    </select>
  </div>

  <div class="itinerary-layout">
    <!-- Timeline -->
    <div id="timelineContainer">
      <?php foreach($days as $dayNum => $day):
        $dayCost = array_sum(array_column($day['items'], 'cost'));
        $dayDate = date('D, M j', strtotime($day['date']));
      ?>
      <div class="day-block animate-fade-in" id="day-<?= $dayNum ?>">
        <div class="day-header">
          <div class="day-badge">
            <span class="day-num"><?= $dayNum ?></span>
            <span class="day-lbl">Day</span>
          </div>
          <div>
            <h4 style="margin:0 0 4px;"><?= $dayDate ?></h4>
            <div style="display:flex;align-items:center;gap:var(--space-3);">
              <span class="stop-label"><i data-lucide="map-pin" style="width:10px;height:10px;"></i> <?= e($day['stop']) ?></span>
              <span style="font-size:var(--text-xs);color:var(--text-muted);"><?= count($day['items']) ?> items · $<?= number_format($dayCost) ?></span>
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
                <div style="flex-shrink:0;text-align:center;">
                  <div class="item-time"><?= e($item['time']) ?></div>
                </div>
                <div class="item-icon" style="background:<?= $cc ?>18;">
                  <i data-lucide="<?= $ci ?>" style="width:16px;height:16px;color:<?= $cc ?>;"></i>
                </div>
                <div style="flex:1;">
                  <div style="display:flex;align-items:center;gap:var(--space-2);">
                    <h6 style="margin:0;font-size:var(--text-sm);"><?= e($item['title']) ?></h6>
                    <?php if($isExpense): ?><span class="item-expense-tag">Expense</span><?php endif; ?>
                  </div>
                  <div class="item-meta">
                    <span><i data-lucide="clock"></i><?= e($item['duration']) ?></span>
                    <?php if($item['cost'] > 0): ?>
                    <span style="color:<?= $isExpense?'var(--accent-gold)':'var(--accent-cyan)' ?>;font-weight:var(--font-semibold);">
                      <i data-lucide="dollar-sign"></i>$<?= number_format($item['cost']) ?>
                    </span>
                    <?php else: ?>
                    <span style="color:var(--accent-green);"><i data-lucide="check"></i>Free</span>
                    <?php endif; ?>
                    <span style="display:flex;align-items:center;gap:3px;">
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
    </div>

    <!-- Right Panel -->
    <div>
      <!-- Trip Summary -->
      <div class="summary-card">
        <h5 style="margin-bottom:var(--space-4);">Trip Summary</h5>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Duration</span>
          <span style="font-weight:bold;"><?= $trip['duration'] ?> days</span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Total Items</span>
          <span style="font-weight:bold;"><?= array_sum(array_map(fn($d)=>count($d['items']),$days)) ?></span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Budget</span>
          <span style="font-weight:bold;color:var(--accent-cyan);">$<?= number_format($trip['budget']) ?></span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Spent</span>
          <span style="font-weight:bold;color:var(--accent-orange);">$<?= number_format($trip['spent']) ?></span>
        </div>
        <div class="sum-stat">
          <span style="color:var(--text-secondary);">Remaining</span>
          <span style="font-weight:bold;color:var(--accent-green);">$<?= number_format($trip['budget']-$trip['spent']) ?></span>
        </div>
        <div class="prog-bar" style="margin-top:var(--space-4);">
          <div class="prog-fill" style="width:<?= $spentPct ?>%;background:<?= $spentPct>90?'var(--accent-red)':($spentPct>70?'var(--accent-orange)':'var(--accent-cyan)') ?>;"></div>
        </div>
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin-top:4px;"><?= $spentPct ?>% of budget used</p>
        <div style="display:flex;gap:var(--space-2);margin-top:var(--space-4);">
          <a href="<?= APP_URL ?>/pages/budget.php?trip_id=<?= $tripId ?>" class="btn btn-secondary" style="flex:1;font-size:var(--text-xs);">
            <i data-lucide="wallet" style="width:12px;height:12px;"></i> Budget
          </a>
          <a href="<?= APP_URL ?>/pages/invoice.php?trip_id=<?= $tripId ?>" class="btn btn-secondary" style="flex:1;font-size:var(--text-xs);">
            <i data-lucide="file-text" style="width:12px;height:12px;"></i> Invoice
          </a>
        </div>
      </div>

      <!-- Day Navigator -->
      <div class="day-nav">
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-3);text-transform:uppercase;letter-spacing:0.06em;">Jump to Day</p>
        <?php foreach($days as $dayNum => $day): ?>
        <a href="#day-<?= $dayNum ?>" class="day-nav-item" onclick="highlightDay(<?= $dayNum ?>)">
          <div style="width:28px;height:28px;border-radius:var(--radius-md);background:var(--gradient-cyan);display:flex;align-items:center;justify-content:center;font-size:var(--text-xs);font-weight:bold;color:var(--bg-primary);flex-shrink:0;"><?= $dayNum ?></div>
          <div>
            <p style="font-size:var(--text-xs);font-weight:var(--font-semibold);margin:0;"><?= date('D, M j', strtotime($day['date'])) ?></p>
            <p style="font-size:10px;color:var(--text-muted);margin:0;"><?= e($day['stop']) ?> · <?= count($day['items']) ?> items</p>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
    </div>
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
function groupItems(by) {}
function highlightDay(n) {
  document.querySelectorAll('.day-nav-item').forEach(a => a.classList.remove('active'));
  event.currentTarget.classList.add('active');
}
lucide.createIcons();
</script>
</body>
</html>
