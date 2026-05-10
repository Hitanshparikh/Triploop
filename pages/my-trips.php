<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();

// Active tab from URL
$activeTab = $_GET['tab'] ?? 'ongoing';
$allowedTabs = ['ongoing','upcoming','completed'];
if (!in_array($activeTab, $allowedTabs)) $activeTab = 'ongoing';

// Demo trip data (replace with DB queries when backend ready)
$trips = [
    'ongoing' => [
        ['id'=>1,'name'=>'Tokyo & Kyoto Adventure','destination'=>'Japan','start_date'=>'2026-05-01','end_date'=>'2026-05-20','mood'=>'adventure','mood_color'=>'#FF6B35','cover'=>'','progress'=>65,'sections'=>8,'budget'=>5000,'spent'=>3200,'type'=>'solo'],
        ['id'=>2,'name'=>'Bali Healing Retreat','destination'=>'Indonesia','start_date'=>'2026-05-10','end_date'=>'2026-05-18','mood'=>'healing','mood_color'=>'#10B981','cover'=>'','progress'=>40,'sections'=>5,'budget'=>2000,'spent'=>850,'type'=>'couple'],
    ],
    'upcoming' => [
        ['id'=>3,'name'=>'Paris Romantic Getaway','destination'=>'France','start_date'=>'2026-07-14','end_date'=>'2026-07-22','mood'=>'romantic','mood_color'=>'#EC4899','cover'=>'','progress'=>20,'sections'=>6,'budget'=>4500,'spent'=>900,'type'=>'couple'],
        ['id'=>4,'name'=>'Dubai Luxury Experience','destination'=>'UAE','start_date'=>'2026-08-05','end_date'=>'2026-08-12','mood'=>'luxury','mood_color'=>'#F59E0B','cover'=>'','progress'=>10,'sections'=>4,'budget'=>8000,'spent'=>1200,'type'=>'friends'],
        ['id'=>5,'name'=>'Santorini Group Trip','destination'=>'Greece','start_date'=>'2026-09-01','end_date'=>'2026-09-10','mood'=>'party','mood_color'=>'#8B5CF6','cover'=>'','progress'=>5,'sections'=>3,'budget'=>3500,'spent'=>200,'type'=>'group'],
    ],
    'completed' => [
        ['id'=>6,'name'=>'New York City Break','destination'=>'USA','start_date'=>'2026-02-10','end_date'=>'2026-02-17','mood'=>'productivity','mood_color'=>'#3B82F6','cover'=>'','progress'=>100,'sections'=>7,'budget'=>3000,'spent'=>2850,'type'=>'solo'],
        ['id'=>7,'name'=>'Barcelona Food Tour','destination'=>'Spain','start_date'=>'2026-01-20','end_date'=>'2026-01-28','mood'=>'adventure','mood_color'=>'#FF6B35','cover'=>'','progress'=>100,'sections'=>5,'budget'=>2200,'spent'=>2100,'type'=>'friends'],
    ],
];

$counts = ['ongoing'=>count($trips['ongoing']),'upcoming'=>count($trips['upcoming']),'completed'=>count($trips['completed'])];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Trips — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Toolbar */
.trips-toolbar{display:flex;align-items:center;gap:var(--space-3);flex-wrap:wrap;margin-bottom:var(--space-6);}
.search-box{position:relative;flex:1;min-width:220px;}
.search-box input{width:100%;padding:var(--space-3) var(--space-4) var(--space-3) 44px;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);transition:all var(--transition-base);outline:none;}
.search-box input:focus{border-color:var(--accent-cyan);box-shadow:0 0 0 3px var(--accent-cyan-soft);}
.search-box input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.toolbar-select{padding:var(--space-3) var(--space-4);background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* Tabs */
.tab-nav{display:flex;gap:0;background:var(--bg-elevated);border-radius:var(--radius-xl);padding:4px;margin-bottom:var(--space-8);width:fit-content;}
.tab-btn{padding:var(--space-3) var(--space-6);border-radius:calc(var(--radius-xl) - 4px);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;transition:all var(--transition-base);color:var(--text-muted);border:none;background:transparent;display:flex;align-items:center;gap:var(--space-2);}
.tab-btn.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-md);}
.tab-btn.ongoing.active{color:var(--accent-orange);}
.tab-btn.upcoming.active{color:var(--accent-cyan);}
.tab-btn.completed.active{color:var(--accent-green);}
.count-badge{min-width:20px;height:20px;border-radius:var(--radius-full);font-size:11px;font-weight:bold;display:inline-flex;align-items:center;justify-content:center;padding:0 6px;}
/* Trip Cards Grid */
.trips-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(320px,1fr));gap:var(--space-5);}
/* Trip Card */
.trip-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);overflow:hidden;transition:all var(--transition-base);cursor:pointer;}
.trip-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-glow-cyan);border-color:rgba(0,212,255,0.2);}
.trip-card-cover{height:160px;background:linear-gradient(135deg,#1a0b2e,#0b1020);position:relative;overflow:hidden;}
.trip-card-cover-gradient{position:absolute;inset:0;opacity:0.6;}
.trip-card-cover-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(11,16,32,0.9) 0%,transparent 60%);}
.trip-card-badges{position:absolute;top:var(--space-3);right:var(--space-3);display:flex;gap:var(--space-2);}
.trip-card-body{padding:var(--space-5);}
.trip-meta{display:flex;align-items:center;gap:var(--space-2);margin-bottom:var(--space-3);font-size:var(--text-xs);color:var(--text-muted);}
.trip-meta i{width:12px;height:12px;}
.trip-stats{display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-2);margin:var(--space-4) 0;}
.trip-stat{text-align:center;padding:var(--space-2);background:var(--bg-elevated);border-radius:var(--radius-md);}
.trip-stat-val{font-size:var(--text-sm);font-weight:var(--font-bold);}
.trip-stat-lbl{font-size:10px;color:var(--text-muted);margin-top:2px;}
/* Progress */
.progress-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:6px;font-size:var(--text-xs);color:var(--text-muted);}
.progress-bar{width:100%;height:6px;background:var(--bg-elevated);border-radius:var(--radius-full);overflow:hidden;}
.progress-fill{height:100%;border-radius:var(--radius-full);transition:width 0.6s ease;}
/* Card Actions */
.trip-actions{display:flex;gap:var(--space-2);margin-top:var(--space-4);padding-top:var(--space-4);border-top:1px solid rgba(148,163,184,0.06);}
.trip-action-btn{flex:1;padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);font-size:var(--text-xs);font-weight:var(--font-semibold);cursor:pointer;border:1px solid rgba(148,163,184,0.12);background:var(--bg-elevated);color:var(--text-secondary);transition:all var(--transition-fast);display:flex;align-items:center;justify-content:center;gap:4px;text-decoration:none;}
.trip-action-btn:hover{border-color:var(--accent-cyan);color:var(--accent-cyan);}
.trip-action-btn.danger:hover{border-color:var(--accent-red);color:var(--accent-red);}
/* Empty state */
.empty-state{text-align:center;padding:var(--space-16) var(--space-8);grid-column:1/-1;}
/* Cover orbs */
.cover-orb{position:absolute;border-radius:50%;filter:blur(40px);opacity:0.5;}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Page Header -->
  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);">
    <div>
      <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-2);">My <span class="text-gradient">Trips</span></h1>
      <p style="color:var(--text-secondary);">All your journeys, past and future.</p>
    </div>
    <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-primary btn-lg">
      <i data-lucide="plus" style="width:18px;height:18px;"></i> Plan New Trip
    </a>
  </div>

  <!-- Toolbar: Search, Filter, Sort, Group -->
  <div class="trips-toolbar">
    <div class="search-box">
      <span class="search-icon"><i data-lucide="search" style="width:16px;height:16px;"></i></span>
      <input type="text" id="searchInput" placeholder="Search trips..." oninput="filterTrips()">
    </div>
    <select class="toolbar-select" id="sortSelect" onchange="filterTrips()">
      <option value="">Sort by...</option>
      <option value="name">Name A–Z</option>
      <option value="date">Date</option>
      <option value="budget">Budget</option>
      <option value="progress">Progress</option>
    </select>
    <select class="toolbar-select" id="groupSelect" onchange="filterTrips()">
      <option value="">Group by...</option>
      <option value="mood">Mood</option>
      <option value="type">Travel Type</option>
    </select>
    <select class="toolbar-select" id="filterSelect" onchange="filterTrips()">
      <option value="">Filter...</option>
      <option value="solo">Solo</option>
      <option value="couple">Couple</option>
      <option value="friends">Friends</option>
      <option value="group">Group</option>
    </select>
  </div>

  <!-- Tab Navigation -->
  <div class="tab-nav">
    <button class="tab-btn ongoing <?= $activeTab==='ongoing'?'active':'' ?>" onclick="switchTab('ongoing')">
      <i data-lucide="navigation" style="width:14px;height:14px;"></i>
      Ongoing
      <span class="count-badge" style="background:rgba(255,107,53,0.15);color:var(--accent-orange);"><?= $counts['ongoing'] ?></span>
    </button>
    <button class="tab-btn upcoming <?= $activeTab==='upcoming'?'active':'' ?>" onclick="switchTab('upcoming')">
      <i data-lucide="calendar-clock" style="width:14px;height:14px;"></i>
      Up-coming
      <span class="count-badge" style="background:rgba(0,212,255,0.15);color:var(--accent-cyan);"><?= $counts['upcoming'] ?></span>
    </button>
    <button class="tab-btn completed <?= $activeTab==='completed'?'active':'' ?>" onclick="switchTab('completed')">
      <i data-lucide="check-circle-2" style="width:14px;height:14px;"></i>
      Completed
      <span class="count-badge" style="background:rgba(16,185,129,0.15);color:var(--accent-green);"><?= $counts['completed'] ?></span>
    </button>
  </div>

  <!-- Tab Panels -->
  <?php foreach(['ongoing','upcoming','completed'] as $tab):
    $isActive = $activeTab === $tab;
    $tabTrips = $trips[$tab];
    $tabColor = ['ongoing'=>'#FF6B35','upcoming'=>'#00D4FF','completed'=>'#10B981'][$tab];
  ?>
  <div class="tab-panel" id="panel-<?= $tab ?>" style="display:<?= $isActive?'block':'none' ?>;">
    <div class="trips-grid" id="grid-<?= $tab ?>">
      <?php if(empty($tabTrips)): ?>
        <div class="empty-state">
          <i data-lucide="compass" style="width:56px;height:56px;color:var(--text-muted);margin:0 auto var(--space-4);display:block;"></i>
          <h4 style="margin-bottom:var(--space-2);">No <?= $tab ?> trips</h4>
          <p style="color:var(--text-secondary);margin-bottom:var(--space-6);">
            <?= $tab==='ongoing' ? "You don't have any active trips right now." : ($tab==='upcoming' ? "No upcoming trips planned yet." : "No completed trips yet.") ?>
          </p>
          <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-primary">Plan a Trip</a>
        </div>
      <?php else: ?>
        <?php foreach($tabTrips as $trip):
          $pct = $trip['progress'];
          $spentPct = $trip['budget'] > 0 ? round($trip['spent']/$trip['budget']*100) : 0;
          $progressColor = $tab==='completed' ? '#10B981' : ($pct > 70 ? '#FF6B35' : '#00D4FF');
          $daysLeft = max(0, (strtotime($trip['end_date']) - time()) / 86400);
          $duration = round((strtotime($trip['end_date']) - strtotime($trip['start_date'])) / 86400);
        ?>
        <div class="trip-card animate-fade-in" data-name="<?= strtolower($trip['name']) ?>" data-type="<?= $trip['type'] ?>" data-mood="<?= $trip['mood'] ?>" data-budget="<?= $trip['budget'] ?>" data-progress="<?= $pct ?>">
          <!-- Cover -->
          <div class="trip-card-cover">
            <div class="cover-orb" style="width:200px;height:200px;background:<?= $trip['mood_color'] ?>;top:-50px;right:-50px;"></div>
            <div class="cover-orb" style="width:150px;height:150px;background:var(--accent-purple);bottom:-30px;left:-30px;"></div>
            <div class="trip-card-cover-overlay"></div>
            <div class="trip-card-badges">
              <span class="badge" style="background:rgba(0,0,0,0.5);color:<?= $trip['mood_color'] ?>;border:1px solid <?= $trip['mood_color'] ?>40;backdrop-filter:blur(8px);">
                <?= ucfirst($trip['mood']) ?>
              </span>
              <span class="badge" style="background:rgba(0,0,0,0.5);color:var(--text-secondary);backdrop-filter:blur(8px);">
                <?= ucfirst($trip['type']) ?>
              </span>
            </div>
            <div style="position:absolute;bottom:var(--space-3);left:var(--space-4);">
              <p style="font-size:var(--text-lg);font-weight:var(--font-bold);margin:0;"><?= e($trip['name']) ?></p>
              <p style="font-size:var(--text-xs);color:rgba(241,245,249,0.7);margin:0;display:flex;align-items:center;gap:4px;">
                <i data-lucide="map-pin" style="width:10px;height:10px;"></i> <?= e($trip['destination']) ?>
              </p>
            </div>
          </div>
          <!-- Body -->
          <div class="trip-card-body">
            <!-- Meta -->
            <div class="trip-meta">
              <i data-lucide="calendar"></i>
              <?= date('M j', strtotime($trip['start_date'])) ?> – <?= date('M j, Y', strtotime($trip['end_date'])) ?>
              <span style="margin:0 4px;">·</span>
              <i data-lucide="clock"></i> <?= $duration ?> days
              <?php if($tab==='ongoing' && $daysLeft > 0): ?>
                <span style="margin:0 4px;">·</span>
                <span style="color:var(--accent-orange);"><?= round($daysLeft) ?>d left</span>
              <?php endif; ?>
            </div>
            <!-- Stats -->
            <div class="trip-stats">
              <div class="trip-stat">
                <div class="trip-stat-val"><?= $trip['sections'] ?></div>
                <div class="trip-stat-lbl">Sections</div>
              </div>
              <div class="trip-stat">
                <div class="trip-stat-val" style="color:var(--accent-cyan);">$<?= number_format($trip['budget']) ?></div>
                <div class="trip-stat-lbl">Budget</div>
              </div>
              <div class="trip-stat">
                <div class="trip-stat-val" style="color:<?= $spentPct>90?'var(--accent-red)':'var(--accent-orange)' ?>;">$<?= number_format($trip['spent']) ?></div>
                <div class="trip-stat-lbl">Spent</div>
              </div>
            </div>
            <!-- Progress -->
            <div class="progress-row">
              <span>Progress</span>
              <span style="color:<?= $progressColor ?>;"><?= $pct ?>%</span>
            </div>
            <div class="progress-bar">
              <div class="progress-fill" style="width:<?= $pct ?>%;background:<?= $progressColor ?>;"></div>
            </div>
            <!-- Actions -->
            <div class="trip-actions">
              <a href="<?= APP_URL ?>/pages/itinerary-builder.php?trip_id=<?= $trip['id'] ?>" class="trip-action-btn">
                <i data-lucide="edit-3" style="width:12px;height:12px;"></i> Edit
              </a>
              <a href="<?= APP_URL ?>/pages/itinerary-view.php?trip_id=<?= $trip['id'] ?>" class="trip-action-btn">
                <i data-lucide="eye" style="width:12px;height:12px;"></i> View
              </a>
              <a href="<?= APP_URL ?>/pages/budget.php?trip_id=<?= $trip['id'] ?>" class="trip-action-btn">
                <i data-lucide="wallet" style="width:12px;height:12px;"></i> Budget
              </a>
              <button class="trip-action-btn danger" onclick="confirmDelete(<?= $trip['id'] ?>)">
                <i data-lucide="trash-2" style="width:12px;height:12px;"></i>
              </button>
            </div>
          </div>
        </div>
        <?php endforeach; ?>
      <?php endif; ?>
    </div>
  </div>
  <?php endforeach; ?>

</main>
</div>

<!-- Delete Modal -->
<div id="deleteModal" style="display:none;position:fixed;inset:0;background:rgba(11,16,32,0.85);backdrop-filter:blur(8px);z-index:400;align-items:center;justify-content:center;">
  <div style="background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-2xl);padding:var(--space-8);max-width:400px;width:90%;text-align:center;">
    <i data-lucide="alert-triangle" style="width:48px;height:48px;color:var(--accent-red);margin:0 auto var(--space-4);display:block;"></i>
    <h4 style="margin-bottom:var(--space-2);">Delete Trip?</h4>
    <p style="color:var(--text-secondary);margin-bottom:var(--space-6);">This action cannot be undone. All itinerary data, expenses, and journal entries will be permanently deleted.</p>
    <div style="display:flex;gap:var(--space-3);justify-content:center;">
      <button class="btn btn-secondary" onclick="closeModal()">Cancel</button>
      <a id="deleteConfirmBtn" href="#" class="btn" style="background:rgba(239,68,68,0.15);color:var(--accent-red);border:1px solid rgba(239,68,68,0.2);">
        <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Delete Trip
      </a>
    </div>
  </div>
</div>

<script>
function switchTab(tab) {
  document.querySelectorAll('.tab-panel').forEach(p => p.style.display = 'none');
  document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
  document.getElementById('panel-' + tab).style.display = 'block';
  document.querySelector('.tab-btn.' + tab).classList.add('active');
  history.replaceState(null,'','?tab=' + tab);
}

function filterTrips() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  const typeFilter = document.getElementById('filterSelect').value;
  const sortBy = document.getElementById('sortSelect').value;

  document.querySelectorAll('.tab-panel').forEach(panel => {
    const cards = [...panel.querySelectorAll('.trip-card')];
    cards.forEach(card => {
      const name = card.dataset.name || '';
      const type = card.dataset.type || '';
      const matchQ = !q || name.includes(q);
      const matchType = !typeFilter || type === typeFilter;
      card.style.display = matchQ && matchType ? '' : 'none';
    });
    if (sortBy) {
      const grid = panel.querySelector('.trips-grid');
      const visible = cards.filter(c => c.style.display !== 'none');
      visible.sort((a, b) => {
        if (sortBy === 'name') return a.dataset.name.localeCompare(b.dataset.name);
        if (sortBy === 'budget') return parseFloat(b.dataset.budget) - parseFloat(a.dataset.budget);
        if (sortBy === 'progress') return parseFloat(b.dataset.progress) - parseFloat(a.dataset.progress);
        return 0;
      });
      visible.forEach(c => grid.appendChild(c));
    }
  });
}

function confirmDelete(id) {
  const modal = document.getElementById('deleteModal');
  modal.style.display = 'flex';
  document.getElementById('deleteConfirmBtn').href = '<?= APP_URL ?>/api/trips.php?action=delete&id=' + id;
}
function closeModal() { document.getElementById('deleteModal').style.display = 'none'; }

lucide.createIcons();
</script>
</body>
</html>
