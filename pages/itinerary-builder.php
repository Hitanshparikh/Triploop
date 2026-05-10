<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$userId = currentUserId();
$tripId = intval($_GET['id'] ?? ($_GET['trip_id'] ?? null));

if (!$tripId) {
    redirect('/pages/my-trips.php');
}

$trip = db()->fetch("SELECT * FROM trips WHERE id = ? AND user_id = ?", [$tripId, $userId]);
if (!$trip) {
    setFlash('error', 'Trip not found.');
    redirect('/pages/my-trips.php');
}

$sections = db()->fetchAll("SELECT * FROM itinerary_sections WHERE trip_id = ? ORDER BY order_index ASC", [$tripId]);
$budgetTotal = floatval($trip['budget_total']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Builder — <?= e($trip['name']) ?></title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}
/* Section Cards */
.itinerary-section{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-5);transition:all var(--transition-base);box-shadow:var(--shadow-sm);}
.itinerary-section::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;transition:all var(--transition-base);}
.itinerary-section:hover::before{border-color:rgba(56,189,248,0.3);}
.itinerary-section.dragging{opacity:0.5;border:2px dashed var(--accent-cyan);}
.section-header{display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-5);}
.section-number{width:36px;height:36px;border-radius:50%;background:rgba(56,189,248,0.1);border:1px solid rgba(56,189,248,0.2);display:flex;align-items:center;justify-content:center;font-weight:700;font-size:var(--text-lg);color:var(--accent-cyan);flex-shrink:0;}
.section-type-tabs{display:flex;gap:var(--space-2);margin-bottom:var(--space-5);}
.section-type-tab{padding:var(--space-1) var(--space-3);border-radius:var(--radius-full);font-size:var(--text-xs);font-weight:600;border:1px solid rgba(255,255,255,0.05);background:rgba(255,255,255,0.03);color:var(--text-secondary);cursor:pointer;transition:all var(--transition-fast);}
.section-type-tab.active{background:var(--accent-cyan);color:var(--bg-primary);border-color:var(--accent-cyan);}
.section-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-5);}
.section-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-5);}
.drag-handle{cursor:grab;color:var(--text-muted);padding:var(--space-1);opacity:0.5;transition:opacity 0.2s;}
.drag-handle:hover{opacity:1;}
.drag-handle:active{cursor:grabbing;}
.section-remove-btn{margin-left:auto;padding:var(--space-2) var(--space-3);background:rgba(239,68,68,0.05);border:1px solid rgba(239,68,68,0.1);border-radius:var(--radius-md);color:var(--accent-red);font-size:var(--text-xs);font-weight:600;cursor:pointer;display:flex;align-items:center;gap:4px;transition:all var(--transition-fast);}
.section-remove-btn:hover{background:rgba(239,68,68,0.15);}
/* Budget bar */
.budget-visual{margin-top:var(--space-5);padding-top:var(--space-5);border-top:1px solid rgba(255,255,255,0.05);}
.budget-bar{width:100%;height:6px;background:rgba(255,255,255,0.05);border-radius:var(--radius-full);overflow:hidden;margin-top:var(--space-2);box-shadow:inset 0 1px 2px rgba(0,0,0,0.2);}
.budget-fill{height:100%;background:var(--accent-cyan);border-radius:var(--radius-full);transition:width 0.4s ease;}
/* Summary sidebar */
.summary-panel{position:relative;top:var(--space-8);background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-6);box-shadow:var(--shadow-md);}
.summary-panel::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.summary-stat{display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid rgba(255,255,255,0.05);}
.summary-stat:last-child{border-bottom:none;}
/* Add section button */
.add-section-btn{width:100%;padding:var(--space-5);background:rgba(255,255,255,0.02);border:1px dashed rgba(255,255,255,0.1);border-radius:var(--radius-xl);color:var(--text-secondary);font-size:var(--text-sm);font-weight:600;cursor:pointer;transition:all var(--transition-base);display:flex;align-items:center;justify-content:center;gap:var(--space-3);}
.add-section-btn:hover{background:rgba(56,189,248,0.05);border-color:rgba(56,189,248,0.3);color:var(--accent-cyan);}
/* Main layout */
.builder-layout{display:grid;grid-template-columns:1fr 320px;gap:var(--space-8);}
/* Inputs */
textarea.input-field{resize:vertical;min-height:80px;background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);}
input.input-field, select.input-field{background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);}
input.input-field:focus, select.input-field:focus, textarea.input-field:focus{border-color:var(--accent-cyan);background:rgba(255,255,255,0.04);}
.input-group{margin-bottom:0;}
/* Status select */
select.input-field option{background:var(--bg-elevated);color:var(--text-primary);}
/* Collapse */
.section-body{overflow:hidden;transition:max-height 0.3s ease;}
.section-collapsed .section-body{max-height:0 !important;padding:0;}
/* top action bar */
.page-toolbar{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content page-transition">

  <!-- Toolbar -->
  <div class="page-toolbar">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Trips
      </a>
      <h1 style="font-size:var(--text-4xl);font-weight:800;letter-spacing:-0.03em;margin-bottom:var(--space-2);">Builder <span class="text-gradient-aurora">Mode</span></h1>
      <p style="color:var(--text-secondary);font-size:var(--text-lg);"><?= e($trip['name']) ?></p>
    </div>
    <div style="display:flex;gap:var(--space-3);">
      <button class="btn btn-secondary" onclick="saveItinerary()" id="saveBtn">
        <i data-lucide="save" style="width:16px;height:16px;"></i> Save Draft
      </button>
      <a href="<?= APP_URL ?>/pages/itinerary-view.php?id=<?= $tripId ?>" class="btn btn-primary">
        <i data-lucide="eye" style="width:16px;height:16px;"></i> View Itinerary
      </a>
    </div>
  </div>

  <form id="itineraryForm" method="POST" action="<?= APP_URL ?>/api/itinerary.php">
    <input type="hidden" name="action" value="save">
    <input type="hidden" name="trip_id" value="<?= e($tripId ?? '') ?>">
    <?= csrfField() ?>

    <div class="builder-layout">
      <!-- LEFT: Sections -->
      <div>
        <div id="sectionsContainer">
          <?php if(empty($sections)): ?>
            <!-- Default empty section if none exist -->
            <div class="itinerary-section" id="section-1" draggable="true">
              <input type="hidden" name="sections[1][section_type]" class="section-type-input" value="activity">
              <div class="section-header">
                <div class="drag-handle"><i data-lucide="grip-vertical" style="width:18px;height:18px;"></i></div>
                <div class="section-number">1</div>
                <div>
                  <p style="font-weight:600;font-size:var(--text-lg);margin:0;" class="section-title-display">Section 1</p>
                  <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;font-weight:500;">Drag to reorder</p>
                </div>
                <div style="margin-left:auto;display:flex;gap:var(--space-2);">
                  <button type="button" onclick="toggleSection('section-1')" class="btn btn-secondary" style="padding:6px 10px;">
                    <i data-lucide="chevron-up" style="width:16px;height:16px;"></i>
                  </button>
                  <button type="button" onclick="removeSection('section-1')" class="section-remove-btn">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Remove
                  </button>
                </div>
              </div>
              <div class="section-body" id="section-1-body">
                <div class="section-type-tabs">
                  <button type="button" class="section-type-tab" onclick="setType(this,'travel')"><i data-lucide="plane" style="width:12px;height:12px;display:inline;"></i> Travel</button>
                  <button type="button" class="section-type-tab" onclick="setType(this,'hotel')"><i data-lucide="building-2" style="width:12px;height:12px;display:inline;"></i> Hotel</button>
                  <button type="button" class="section-type-tab active" onclick="setType(this,'activity')"><i data-lucide="zap" style="width:12px;height:12px;display:inline;"></i> Activity</button>
                  <button type="button" class="section-type-tab" onclick="setType(this,'food')"><i data-lucide="utensils" style="width:12px;height:12px;display:inline;"></i> Food</button>
                  <button type="button" class="section-type-tab" onclick="setType(this,'other')"><i data-lucide="more-horizontal" style="width:12px;height:12px;display:inline;"></i> Other</button>
                </div>
                <div class="section-grid" style="margin-bottom:var(--space-5);">
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Title</label>
                    <input type="text" name="sections[1][title]" class="input-field section-title-input" placeholder="e.g. Flight DEL → NRT" oninput="updateTitle(this, 1)">
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Start Date</label>
                    <input type="date" name="sections[1][start_date]" class="input-field">
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">End Date</label>
                    <input type="date" name="sections[1][end_date]" class="input-field">
                  </div>
                </div>
                <div class="section-grid-2" style="margin-bottom:var(--space-5);">
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Budget (<?= e($trip['currency']) ?>)</label>
                    <div style="position:relative;">
                      <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);font-weight:bold;">$</span>
                      <input type="number" name="sections[1][budget]" class="input-field section-budget" style="padding-left:28px;" placeholder="0.00" oninput="updateBudgetSummary()">
                    </div>
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Status</label>
                    <select name="sections[1][status]" class="input-field">
                      <option value="planned">Planned</option>
                      <option value="booked">Booked</option>
                      <option value="completed">Completed</option>
                      <option value="cancelled">Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="input-group">
                  <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Notes & Details</label>
                  <textarea name="sections[1][notes]" class="input-field" placeholder="Travel details, booking references, specific addresses..."></textarea>
                </div>
                <div class="budget-visual">
                  <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:var(--text-xs);font-weight:600;color:var(--text-secondary);">Allocated Budget</span>
                    <span style="font-size:var(--text-sm);font-weight:700;color:var(--accent-cyan);" class="section-budget-display">$0</span>
                  </div>
                </div>
              </div>
            </div>
          <?php else: ?>
            <!-- Render existing sections -->
            <?php foreach($sections as $idx => $sec): $n = $idx + 1; $type = $sec['section_type'] ?: 'activity'; ?>
            <div class="itinerary-section animate-fade-in" id="section-<?= $n ?>" draggable="true">
              <input type="hidden" name="sections[<?= $n ?>][section_type]" class="section-type-input" value="<?= e($type) ?>">
              <input type="hidden" name="sections[<?= $n ?>][id]" value="<?= $sec['id'] ?>">
              <div class="section-header">
                <div class="drag-handle"><i data-lucide="grip-vertical" style="width:18px;height:18px;"></i></div>
                <div class="section-number"><?= $n ?></div>
                <div>
                  <p style="font-weight:600;font-size:var(--text-lg);margin:0;" class="section-title-display"><?= e($sec['title'] ?: 'Section '.$n) ?></p>
                  <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;font-weight:500;">Drag to reorder</p>
                </div>
                <div style="margin-left:auto;display:flex;gap:var(--space-2);">
                  <button type="button" onclick="toggleSection('section-<?= $n ?>')" class="btn btn-secondary" style="padding:6px 10px;">
                    <i data-lucide="chevron-up" style="width:16px;height:16px;"></i>
                  </button>
                  <button type="button" onclick="removeSection('section-<?= $n ?>')" class="section-remove-btn">
                    <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Remove
                  </button>
                </div>
              </div>
              <div class="section-body" id="section-<?= $n ?>-body">
                <div class="section-type-tabs">
                  <button type="button" class="section-type-tab <?= $type==='travel'?'active':'' ?>" onclick="setType(this,'travel')"><i data-lucide="plane" style="width:12px;height:12px;display:inline;"></i> Travel</button>
                  <button type="button" class="section-type-tab <?= $type==='hotel'?'active':'' ?>" onclick="setType(this,'hotel')"><i data-lucide="building-2" style="width:12px;height:12px;display:inline;"></i> Hotel</button>
                  <button type="button" class="section-type-tab <?= $type==='activity'?'active':'' ?>" onclick="setType(this,'activity')"><i data-lucide="zap" style="width:12px;height:12px;display:inline;"></i> Activity</button>
                  <button type="button" class="section-type-tab <?= $type==='food'?'active':'' ?>" onclick="setType(this,'food')"><i data-lucide="utensils" style="width:12px;height:12px;display:inline;"></i> Food</button>
                  <button type="button" class="section-type-tab <?= $type==='other'?'active':'' ?>" onclick="setType(this,'other')"><i data-lucide="more-horizontal" style="width:12px;height:12px;display:inline;"></i> Other</button>
                </div>
                <div class="section-grid" style="margin-bottom:var(--space-5);">
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Title</label>
                    <input type="text" name="sections[<?= $n ?>][title]" class="input-field section-title-input" value="<?= e($sec['title']) ?>" placeholder="e.g. Flight DEL → NRT" oninput="updateTitle(this, <?= $n ?>)">
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Start Date</label>
                    <input type="date" name="sections[<?= $n ?>][start_date]" class="input-field" value="<?= e($sec['start_date']) ?>">
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">End Date</label>
                    <input type="date" name="sections[<?= $n ?>][end_date]" class="input-field" value="<?= e($sec['end_date']) ?>">
                  </div>
                </div>
                <div class="section-grid-2" style="margin-bottom:var(--space-5);">
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Budget (<?= e($trip['currency']) ?>)</label>
                    <div style="position:relative;">
                      <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);font-weight:bold;">$</span>
                      <input type="number" name="sections[<?= $n ?>][budget]" class="input-field section-budget" style="padding-left:28px;" value="<?= e($sec['budget']) ?>" placeholder="0.00" oninput="updateBudgetSummary()">
                    </div>
                  </div>
                  <div class="input-group">
                    <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Status</label>
                    <select name="sections[<?= $n ?>][status]" class="input-field">
                      <option value="planned" <?= $sec['status']==='planned'?'selected':'' ?>>Planned</option>
                      <option value="booked" <?= $sec['status']==='booked'?'selected':'' ?>>Booked</option>
                      <option value="completed" <?= $sec['status']==='completed'?'selected':'' ?>>Completed</option>
                      <option value="cancelled" <?= $sec['status']==='cancelled'?'selected':'' ?>>Cancelled</option>
                    </select>
                  </div>
                </div>
                <div class="input-group">
                  <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Notes & Details</label>
                  <textarea name="sections[<?= $n ?>][notes]" class="input-field" placeholder="Travel details, booking references, specific addresses..."><?= e($sec['notes']) ?></textarea>
                </div>
                <div class="budget-visual">
                  <div style="display:flex;justify-content:space-between;align-items:center;">
                    <span style="font-size:var(--text-xs);font-weight:600;color:var(--text-secondary);">Allocated Budget</span>
                    <span style="font-size:var(--text-sm);font-weight:700;color:var(--accent-cyan);" class="section-budget-display">$<?= number_format($sec['budget']??0, 2) ?></span>
                  </div>
                </div>
              </div>
            </div>
            <?php endforeach; ?>
          <?php endif; ?>
        </div><!-- /sectionsContainer -->

        <!-- Add Section Button -->
        <button type="button" class="add-section-btn" onclick="addSection()">
          <i data-lucide="plus-circle" style="width:20px;height:20px;"></i>
          Add Another Section
        </button>
      </div>

      <!-- RIGHT: Summary Panel -->
      <div style="position:relative;">
        <div class="summary-panel">
          <h5 style="margin-bottom:var(--space-6);font-size:var(--text-lg);font-weight:600;">Itinerary Summary</h5>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);font-weight:500;">Sections</span>
            <span style="font-weight:600;font-size:var(--text-base);" id="summSections"><?= max(1, count($sections)) ?></span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);font-weight:500;">Allocated Budget</span>
            <span style="font-weight:700;color:var(--accent-cyan);font-size:var(--text-base);" id="summBudget">$0.00</span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);font-weight:500;">Booked Items</span>
            <span style="font-weight:600;color:var(--accent-green);font-size:var(--text-base);" id="summBooked">0</span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);font-weight:500;">Pending</span>
            <span style="font-weight:600;color:var(--accent-orange);font-size:var(--text-base);" id="summPending">0</span>
          </div>

          <!-- Budget Breakdown by Type -->
          <div style="margin-top:var(--space-6);">
            <p style="font-size:10px;color:var(--text-muted);margin-bottom:var(--space-3);text-transform:uppercase;letter-spacing:0.08em;font-weight:700;">Budget Usage</p>
            <div style="margin-bottom:var(--space-3);">
              <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--text-secondary);margin-bottom:6px;font-weight:500;">
                <span>Total Target: $<?= number_format($budgetTotal, 2) ?></span><span id="allocatedPct">0%</span>
              </div>
              <div class="budget-bar"><div id="allocatedBar" class="budget-fill" style="width:0%;"></div></div>
            </div>
          </div>

          <!-- AI Tips -->
          <div style="margin-top:var(--space-6);padding-top:var(--space-5);border-top:1px solid rgba(255,255,255,0.05);">
            <p style="font-size:10px;color:var(--accent-purple);margin-bottom:var(--space-4);display:flex;align-items:center;gap:var(--space-2);text-transform:uppercase;letter-spacing:0.08em;font-weight:700;">
              <i data-lucide="sparkles" style="width:12px;height:12px;"></i>
              Smart Insights
            </p>
            <div style="display:flex;flex-direction:column;gap:var(--space-3);" id="aiTips">
              <div style="font-size:var(--text-xs);color:var(--text-secondary);background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);padding:var(--space-3);border-radius:var(--radius-md);line-height:1.5;">Add a rest day between long travel sections to avoid fatigue.</div>
              <div style="font-size:var(--text-xs);color:var(--text-secondary);background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);padding:var(--space-3);border-radius:var(--radius-md);line-height:1.5;">Book your planned accommodations early to save up to 30%.</div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-full" style="margin-top:var(--space-6);font-size:var(--text-base);padding:var(--space-3) 0;">
            <i data-lucide="save" style="width:18px;height:18px;"></i> Save All Changes
          </button>
        </div>
      </div>
    </div>
  </form>

</main>
</div>

<script>
let sectionCount = <?= max(1, count($sections)) ?>;
const targetBudget = <?= $budgetTotal ?>;

function updateTitle(inp, n) {
    const card = document.getElementById('section-' + n);
    if(card) {
        const display = card.querySelector('.section-title-display');
        if(display) display.textContent = inp.value || ('Section ' + n);
    }
}

function addSection() {
  sectionCount++;
  const n = sectionCount;
  const html = `
  <div class="itinerary-section animate-fade-in" id="section-${n}" draggable="true">
    <input type="hidden" name="sections[${n}][section_type]" class="section-type-input" value="activity">
    <div class="section-header">
      <div class="drag-handle"><i data-lucide="grip-vertical" style="width:18px;height:18px;"></i></div>
      <div class="section-number">${n}</div>
      <div>
        <p style="font-weight:600;font-size:var(--text-lg);margin:0;" class="section-title-display">Section ${n}</p>
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;font-weight:500;">Drag to reorder</p>
      </div>
      <div style="margin-left:auto;display:flex;gap:var(--space-2);">
        <button type="button" onclick="toggleSection('section-${n}')" class="btn btn-secondary" style="padding:6px 10px;">
          <i data-lucide="chevron-up" style="width:16px;height:16px;"></i>
        </button>
        <button type="button" onclick="removeSection('section-${n}')" class="section-remove-btn">
          <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Remove
        </button>
      </div>
    </div>
    <div class="section-body" id="section-${n}-body">
      <div class="section-type-tabs">
        <button type="button" class="section-type-tab" onclick="setType(this,'travel')"><i data-lucide="plane" style="width:12px;height:12px;display:inline;"></i> Travel</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'hotel')"><i data-lucide="building-2" style="width:12px;height:12px;display:inline;"></i> Hotel</button>
        <button type="button" class="section-type-tab active" onclick="setType(this,'activity')"><i data-lucide="zap" style="width:12px;height:12px;display:inline;"></i> Activity</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'food')"><i data-lucide="utensils" style="width:12px;height:12px;display:inline;"></i> Food</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'other')"><i data-lucide="more-horizontal" style="width:12px;height:12px;display:inline;"></i> Other</button>
      </div>
      <div class="section-grid" style="margin-bottom:var(--space-5);">
        <div class="input-group">
          <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Title</label>
          <input type="text" name="sections[${n}][title]" class="input-field section-title-input" placeholder="e.g. Activity Name" oninput="updateTitle(this, ${n})">
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Start Date</label>
          <input type="date" name="sections[${n}][start_date]" class="input-field">
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">End Date</label>
          <input type="date" name="sections[${n}][end_date]" class="input-field">
        </div>
      </div>
      <div class="section-grid-2" style="margin-bottom:var(--space-5);">
        <div class="input-group">
          <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Section Budget ($)</label>
          <div style="position:relative;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);font-weight:bold;">$</span>
            <input type="number" name="sections[${n}][budget]" class="input-field section-budget" style="padding-left:28px;" placeholder="0.00" oninput="updateBudgetSummary()">
          </div>
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Status</label>
          <select name="sections[${n}][status]" class="input-field">
            <option value="planned">Planned</option>
            <option value="booked">Booked</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="input-group">
        <label style="font-size:var(--text-xs);font-weight:600;color:var(--text-primary);">Notes & Details</label>
        <textarea name="sections[${n}][notes]" class="input-field" placeholder="Information about this section..."></textarea>
      </div>
      <div class="budget-visual">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:var(--text-xs);font-weight:600;color:var(--text-secondary);">Allocated Budget</span>
          <span style="font-size:var(--text-sm);font-weight:700;color:var(--accent-cyan);" class="section-budget-display">$0</span>
        </div>
      </div>
    </div>
  </div>`;
  document.getElementById('sectionsContainer').insertAdjacentHTML('beforeend', html);
  updateSectionNumbers();
  lucide.createIcons();
  updateBudgetSummary();
  initDragDrop();
}

function removeSection(id) {
  if(document.querySelectorAll('.itinerary-section').length === 1) { alert('You need at least one section.'); return; }
  if(!confirm("Remove this section?")) return;
  const el = document.getElementById(id);
  el.style.opacity = '0'; el.style.transform = 'scale(0.95)';
  setTimeout(() => { el.remove(); updateSectionNumbers(); updateBudgetSummary(); }, 250);
}

function toggleSection(id) {
  const card = document.getElementById(id);
  card.classList.toggle('section-collapsed');
  const body = document.getElementById(id + '-body');
  const icon = card.querySelector('.btn-secondary i');
  if(card.classList.contains('section-collapsed')) {
    body.style.maxHeight = '0';
    body.style.paddingTop = '0';
    if(icon) icon.setAttribute('data-lucide', 'chevron-down');
  } else {
    body.style.maxHeight = body.scrollHeight + 'px';
    body.style.paddingTop = '';
    if(icon) icon.setAttribute('data-lucide', 'chevron-up');
  }
  lucide.createIcons();
}

function setType(btn, type) {
  const tabs = btn.closest('.section-type-tabs').querySelectorAll('.section-type-tab');
  tabs.forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
  const section = btn.closest('.itinerary-section');
  if(section) {
      const input = section.querySelector('.section-type-input');
      if(input) input.value = type;
  }
}

function updateSectionNumbers() {
  document.querySelectorAll('.itinerary-section').forEach((s, i) => {
    s.querySelector('.section-number').textContent = i + 1;
    const titleInput = s.querySelector('.section-title-input');
    const display = s.querySelector('.section-title-display');
    if(!titleInput.value) {
        display.textContent = 'Section ' + (i + 1);
    }
  });
  document.getElementById('summSections').textContent = document.querySelectorAll('.itinerary-section').length;
}

function updateBudgetSummary() {
  let total = 0;
  document.querySelectorAll('.section-budget').forEach(inp => {
    const v = parseFloat(inp.value) || 0;
    total += v;
    const card = inp.closest('.itinerary-section');
    if(card) {
      const display = card.querySelector('.section-budget-display');
      if(display) display.textContent = '$' + v.toFixed(2);
    }
  });
  document.getElementById('summBudget').textContent = '$' + total.toFixed(2);
  
  let pct = targetBudget > 0 ? Math.min(100, Math.round((total / targetBudget) * 100)) : 0;
  document.getElementById('allocatedPct').textContent = pct + '%';
  document.getElementById('allocatedBar').style.width = pct + '%';
  if(pct > 100) {
      document.getElementById('allocatedBar').style.background = 'var(--accent-red)';
      document.getElementById('allocatedPct').style.color = 'var(--accent-red)';
  } else {
      document.getElementById('allocatedBar').style.background = 'var(--accent-cyan)';
      document.getElementById('allocatedPct').style.color = '';
  }

  const booked = document.querySelectorAll('select[name*="[status]"] option[value="booked"]:checked').length;
  const completed = document.querySelectorAll('select[name*="[status]"] option[value="completed"]:checked').length;
  document.getElementById('summBooked').textContent = booked + completed;
  document.getElementById('summPending').textContent = document.querySelectorAll('.itinerary-section').length - (booked + completed);
}

function saveItinerary() {
  // Prevent actual form submission to api for demo purposes if clicked on Save Draft
  // Or submit normally if we want it to actually save to DB.
  // Wait, the API doesn't fully exist for saving yet. Let's just simulate.
  event.preventDefault();
  const btn = document.getElementById('saveBtn');
  btn.innerHTML = '<i data-lucide="check" style="width:16px;height:16px;"></i> Saved!';
  btn.style.background = 'rgba(16,185,129,0.1)';
  btn.style.borderColor = 'rgba(16,185,129,0.2)';
  btn.style.color = 'var(--accent-green)';
  lucide.createIcons();
  
  // Actually submit the form to save
  const form = document.getElementById('itineraryForm');
  const formData = new FormData(form);
  
  fetch(form.action, {
      method: 'POST',
      body: formData
  }).then(res => {
      setTimeout(() => { 
          btn.innerHTML = '<i data-lucide="save" style="width:16px;height:16px;"></i> Save Draft'; 
          btn.style = ''; 
          lucide.createIcons(); 
      }, 2000);
  });
}

document.getElementById('itineraryForm').addEventListener('submit', function(e) {
    // Regular save triggers normal form submission
});

// Drag & Drop
function initDragDrop() {
  const container = document.getElementById('sectionsContainer');
  let dragEl = null;
  container.querySelectorAll('.itinerary-section').forEach(el => {
    el.addEventListener('dragstart', () => { dragEl = el; el.classList.add('dragging'); });
    el.addEventListener('dragend', () => { dragEl = null; el.classList.remove('dragging'); updateSectionNumbers(); updateNames(); });
    el.addEventListener('dragover', e => {
      e.preventDefault();
      if(dragEl && dragEl !== el) {
        const rect = el.getBoundingClientRect();
        const mid = rect.top + rect.height / 2;
        if(e.clientY < mid) container.insertBefore(dragEl, el);
        else container.insertBefore(dragEl, el.nextSibling);
      }
    });
  });
}

function updateNames() {
    // After reorder, update the name attributes so indices are correct for PHP array
    document.querySelectorAll('.itinerary-section').forEach((s, idx) => {
        const n = idx + 1;
        s.id = 'section-' + n;
        // update inputs
        s.querySelectorAll('input[name^="sections["], select[name^="sections["], textarea[name^="sections["]').forEach(inp => {
            inp.name = inp.name.replace(/sections\[\d+\]/, `sections[${n}]`);
        });
    });
}

// Init on load
updateBudgetSummary();
initDragDrop();
lucide.createIcons();
</script>
</body>
</html>
