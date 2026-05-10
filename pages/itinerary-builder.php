<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();
$tripId = $_GET['trip_id'] ?? null;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Build Itinerary — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Section Cards */
.itinerary-section{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-5);position:relative;transition:all var(--transition-base);}
.itinerary-section:hover{border-color:rgba(0,212,255,0.15);}
.itinerary-section.dragging{opacity:0.5;border:2px dashed var(--accent-cyan);}
.section-header{display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-5);}
.section-number{width:36px;height:36px;border-radius:50%;background:var(--gradient-cyan);display:flex;align-items:center;justify-content:center;font-weight:var(--font-bold);font-size:var(--text-sm);color:var(--bg-primary);flex-shrink:0;}
.section-type-tabs{display:flex;gap:var(--space-2);margin-bottom:var(--space-5);}
.section-type-tab{padding:var(--space-1) var(--space-3);border-radius:var(--radius-full);font-size:var(--text-xs);font-weight:var(--font-semibold);border:1px solid rgba(148,163,184,0.15);background:transparent;color:var(--text-muted);cursor:pointer;transition:all var(--transition-fast);}
.section-type-tab.active{background:rgba(0,212,255,0.1);border-color:var(--accent-cyan);color:var(--accent-cyan);}
.section-grid{display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);}
.section-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);}
.drag-handle{cursor:grab;color:var(--text-muted);padding:var(--space-1);}
.drag-handle:active{cursor:grabbing;}
.section-remove-btn{margin-left:auto;padding:var(--space-2) var(--space-3);background:rgba(239,68,68,0.08);border:1px solid rgba(239,68,68,0.15);border-radius:var(--radius-lg);color:var(--accent-red);font-size:var(--text-xs);cursor:pointer;display:flex;align-items:center;gap:4px;transition:all var(--transition-fast);}
.section-remove-btn:hover{background:rgba(239,68,68,0.2);}
/* Budget bar */
.budget-visual{margin-top:var(--space-4);padding-top:var(--space-4);border-top:1px solid rgba(148,163,184,0.08);}
.budget-bar{width:100%;height:8px;background:var(--bg-elevated);border-radius:var(--radius-full);overflow:hidden;margin-top:var(--space-2);}
.budget-fill{height:100%;background:var(--gradient-cyan);border-radius:var(--radius-full);transition:width 0.4s ease;}
/* Summary sidebar */
.summary-panel{position:sticky;top:var(--space-8);background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-6);}
.summary-stat{display:flex;justify-content:space-between;align-items:center;padding:var(--space-3) 0;border-bottom:1px solid rgba(148,163,184,0.06);}
.summary-stat:last-child{border-bottom:none;}
/* Add section button */
.add-section-btn{width:100%;padding:var(--space-5);background:rgba(0,212,255,0.03);border:2px dashed rgba(0,212,255,0.15);border-radius:var(--radius-xl);color:var(--accent-cyan);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;transition:all var(--transition-base);display:flex;align-items:center;justify-content:center;gap:var(--space-3);}
.add-section-btn:hover{background:rgba(0,212,255,0.08);border-color:rgba(0,212,255,0.3);}
/* Main layout */
.builder-layout{display:grid;grid-template-columns:1fr 300px;gap:var(--space-8);}
/* Inputs */
textarea.input-field{resize:vertical;min-height:80px;}
.input-group{margin-bottom:0;}
/* Status select */
select.input-field option{background:var(--bg-elevated);}
/* Collapse */
.section-body{overflow:hidden;transition:max-height 0.3s ease;}
.section-collapsed .section-body{max-height:0 !important;padding:0;}
/* top action bar */
.page-toolbar{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-8);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Toolbar -->
  <div class="page-toolbar">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to My Trips
      </a>
      <h1 style="font-size:var(--text-3xl);">Build <span class="text-gradient">Itinerary</span></h1>
      <p style="color:var(--text-secondary);margin-top:var(--space-1);">Drag sections to reorder · All changes auto-saved</p>
    </div>
    <div style="display:flex;gap:var(--space-3);">
      <button class="btn btn-secondary" onclick="saveItinerary()">
        <i data-lucide="save" style="width:16px;height:16px;"></i> Save Draft
      </button>
      <a href="<?= APP_URL ?>/pages/itinerary-view.php<?= $tripId ? '?trip_id='.$tripId : '' ?>" class="btn btn-primary">
        <i data-lucide="eye" style="width:16px;height:16px;"></i> Preview
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
          <!-- Section 1 (default) -->
          <div class="itinerary-section" id="section-1" draggable="true">
            <div class="section-header">
              <div class="drag-handle"><i data-lucide="grip-vertical" style="width:18px;height:18px;"></i></div>
              <div class="section-number">1</div>
              <div>
                <p style="font-weight:var(--font-semibold);margin:0;">Section 1</p>
                <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;">Click title to rename</p>
              </div>
              <div style="margin-left:auto;display:flex;gap:var(--space-2);">
                <button type="button" onclick="toggleSection('section-1')" class="btn btn-secondary" style="padding:6px 10px;">
                  <i data-lucide="chevron-up" style="width:14px;height:14px;"></i>
                </button>
                <button type="button" onclick="removeSection('section-1')" class="section-remove-btn">
                  <i data-lucide="trash-2" style="width:12px;height:12px;"></i> Remove
                </button>
              </div>
            </div>
            <div class="section-body" id="section-1-body">
              <!-- Type tabs -->
              <div class="section-type-tabs">
                <button type="button" class="section-type-tab active" onclick="setType(this,'travel')"><i data-lucide="plane" style="width:10px;height:10px;display:inline;"></i> Travel</button>
                <button type="button" class="section-type-tab" onclick="setType(this,'hotel')"><i data-lucide="building-2" style="width:10px;height:10px;display:inline;"></i> Hotel</button>
                <button type="button" class="section-type-tab" onclick="setType(this,'activity')"><i data-lucide="zap" style="width:10px;height:10px;display:inline;"></i> Activity</button>
                <button type="button" class="section-type-tab" onclick="setType(this,'food')"><i data-lucide="utensils" style="width:10px;height:10px;display:inline;"></i> Food</button>
                <button type="button" class="section-type-tab" onclick="setType(this,'other')"><i data-lucide="more-horizontal" style="width:10px;height:10px;display:inline;"></i> Other</button>
              </div>
              <div class="section-grid" style="margin-bottom:var(--space-4);">
                <div class="input-group">
                  <label style="font-size:var(--text-xs);color:var(--text-muted);">Section Title</label>
                  <input type="text" name="sections[1][title]" class="input-field" placeholder="e.g. Flight DEL → NRT">
                </div>
                <div class="input-group">
                  <label style="font-size:var(--text-xs);color:var(--text-muted);">Start Date</label>
                  <input type="date" name="sections[1][start_date]" class="input-field">
                </div>
                <div class="input-group">
                  <label style="font-size:var(--text-xs);color:var(--text-muted);">End Date</label>
                  <input type="date" name="sections[1][end_date]" class="input-field">
                </div>
              </div>
              <div class="section-grid-2" style="margin-bottom:var(--space-4);">
                <div class="input-group">
                  <label style="font-size:var(--text-xs);color:var(--text-muted);">Section Budget ($)</label>
                  <div style="position:relative;">
                    <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);">$</span>
                    <input type="number" name="sections[1][budget]" class="input-field section-budget" style="padding-left:28px;" placeholder="0.00" oninput="updateBudgetSummary()">
                  </div>
                </div>
                <div class="input-group">
                  <label style="font-size:var(--text-xs);color:var(--text-muted);">Status</label>
                  <select name="sections[1][status]" class="input-field">
                    <option value="planned">Planned</option>
                    <option value="booked">Booked</option>
                    <option value="completed">Completed</option>
                    <option value="cancelled">Cancelled</option>
                  </select>
                </div>
              </div>
              <div class="input-group">
                <label style="font-size:var(--text-xs);color:var(--text-muted);">Notes & Details</label>
                <textarea name="sections[1][notes]" class="input-field" placeholder="All the necessary information about this section. This can be anything like travel details, hotel name, booking reference, activity notes..."></textarea>
              </div>
              <!-- Budget Visual -->
              <div class="budget-visual">
                <div style="display:flex;justify-content:space-between;align-items:center;">
                  <span style="font-size:var(--text-xs);color:var(--text-muted);">Section Budget</span>
                  <span style="font-size:var(--text-xs);color:var(--accent-cyan);" class="section-budget-display">$0</span>
                </div>
                <div class="budget-bar"><div class="budget-fill" style="width:0%;"></div></div>
              </div>
            </div>
          </div>
          <!-- End Section 1 -->
        </div><!-- /sectionsContainer -->

        <!-- Add Section Button -->
        <button type="button" class="add-section-btn" onclick="addSection()">
          <i data-lucide="plus-circle" style="width:20px;height:20px;"></i>
          Add Another Section
        </button>
      </div>

      <!-- RIGHT: Summary Panel -->
      <div>
        <div class="summary-panel">
          <h5 style="margin-bottom:var(--space-5);">Trip Summary</h5>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);">Sections</span>
            <span style="font-weight:var(--font-bold);" id="summSections">1</span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);">Total Budget</span>
            <span style="font-weight:var(--font-bold);color:var(--accent-cyan);" id="summBudget">$0</span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);">Booked</span>
            <span style="font-weight:var(--font-bold);color:var(--accent-green);" id="summBooked">0</span>
          </div>
          <div class="summary-stat">
            <span style="font-size:var(--text-sm);color:var(--text-secondary);">Pending</span>
            <span style="font-weight:var(--font-bold);color:var(--accent-orange);" id="summPending">1</span>
          </div>

          <!-- Budget Breakdown by Type -->
          <div style="margin-top:var(--space-5);">
            <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-3);text-transform:uppercase;letter-spacing:0.05em;">Budget Overview</p>
            <div style="margin-bottom:var(--space-3);">
              <div style="display:flex;justify-content:space-between;font-size:var(--text-xs);color:var(--text-secondary);margin-bottom:4px;">
                <span>Allocated</span><span id="allocatedAmt">$0</span>
              </div>
              <div class="budget-bar"><div id="allocatedBar" class="budget-fill" style="width:0%;"></div></div>
            </div>
          </div>

          <!-- AI Tips -->
          <div style="margin-top:var(--space-5);padding-top:var(--space-5);border-top:1px solid rgba(148,163,184,0.08);">
            <p style="font-size:var(--text-xs);color:var(--text-muted);margin-bottom:var(--space-3);display:flex;align-items:center;gap:var(--space-2);">
              <i data-lucide="lightbulb" style="width:12px;height:12px;color:var(--accent-purple);"></i>
              AI TIPS
            </p>
            <div style="display:flex;flex-direction:column;gap:var(--space-2);" id="aiTips">
              <div style="font-size:var(--text-xs);color:var(--text-secondary);background:var(--bg-elevated);padding:var(--space-3);border-radius:var(--radius-md);">Add a rest day between long travel sections.</div>
              <div style="font-size:var(--text-xs);color:var(--text-secondary);background:var(--bg-elevated);padding:var(--space-3);border-radius:var(--radius-md);">Book accommodation early to save up to 30%.</div>
            </div>
          </div>

          <button type="submit" class="btn btn-primary w-full" style="margin-top:var(--space-5);">
            <i data-lucide="save" style="width:16px;height:16px;"></i> Save Itinerary
          </button>
        </div>
      </div>
    </div>
  </form>

</main>
</div>

<script>
let sectionCount = 1;

function addSection() {
  sectionCount++;
  const n = sectionCount;
  const html = `
  <div class="itinerary-section animate-fade-in" id="section-${n}" draggable="true">
    <div class="section-header">
      <div class="drag-handle"><i data-lucide="grip-vertical" style="width:18px;height:18px;"></i></div>
      <div class="section-number">${n}</div>
      <div>
        <p style="font-weight:var(--font-semibold);margin:0;">Section ${n}</p>
        <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;">Click title to rename</p>
      </div>
      <div style="margin-left:auto;display:flex;gap:var(--space-2);">
        <button type="button" onclick="toggleSection('section-${n}')" class="btn btn-secondary" style="padding:6px 10px;">
          <i data-lucide="chevron-up" style="width:14px;height:14px;"></i>
        </button>
        <button type="button" onclick="removeSection('section-${n}')" class="section-remove-btn">
          <i data-lucide="trash-2" style="width:12px;height:12px;"></i> Remove
        </button>
      </div>
    </div>
    <div class="section-body" id="section-${n}-body">
      <div class="section-type-tabs">
        <button type="button" class="section-type-tab active" onclick="setType(this,'travel')">Travel</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'hotel')">Hotel</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'activity')">Activity</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'food')">Food</button>
        <button type="button" class="section-type-tab" onclick="setType(this,'other')">Other</button>
      </div>
      <div class="section-grid" style="margin-bottom:var(--space-4);">
        <div class="input-group">
          <label style="font-size:var(--text-xs);color:var(--text-muted);">Section Title</label>
          <input type="text" name="sections[${n}][title]" class="input-field" placeholder="e.g. Hotel Check-in">
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);color:var(--text-muted);">Start Date</label>
          <input type="date" name="sections[${n}][start_date]" class="input-field">
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);color:var(--text-muted);">End Date</label>
          <input type="date" name="sections[${n}][end_date]" class="input-field">
        </div>
      </div>
      <div class="section-grid-2" style="margin-bottom:var(--space-4);">
        <div class="input-group">
          <label style="font-size:var(--text-xs);color:var(--text-muted);">Section Budget ($)</label>
          <div style="position:relative;">
            <span style="position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-sm);">$</span>
            <input type="number" name="sections[${n}][budget]" class="input-field section-budget" style="padding-left:28px;" placeholder="0.00" oninput="updateBudgetSummary()">
          </div>
        </div>
        <div class="input-group">
          <label style="font-size:var(--text-xs);color:var(--text-muted);">Status</label>
          <select name="sections[${n}][status]" class="input-field">
            <option value="planned">Planned</option>
            <option value="booked">Booked</option>
            <option value="completed">Completed</option>
            <option value="cancelled">Cancelled</option>
          </select>
        </div>
      </div>
      <div class="input-group">
        <label style="font-size:var(--text-xs);color:var(--text-muted);">Notes & Details</label>
        <textarea name="sections[${n}][notes]" class="input-field" placeholder="All the necessary information about this section..."></textarea>
      </div>
      <div class="budget-visual">
        <div style="display:flex;justify-content:space-between;align-items:center;">
          <span style="font-size:var(--text-xs);color:var(--text-muted);">Section Budget</span>
          <span style="font-size:var(--text-xs);color:var(--accent-cyan);" class="section-budget-display">$0</span>
        </div>
        <div class="budget-bar"><div class="budget-fill" style="width:0%;"></div></div>
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
  const el = document.getElementById(id);
  el.style.opacity = '0'; el.style.transform = 'scale(0.95)';
  setTimeout(() => { el.remove(); updateSectionNumbers(); updateBudgetSummary(); }, 250);
}

function toggleSection(id) {
  const card = document.getElementById(id);
  card.classList.toggle('section-collapsed');
  const body = document.getElementById(id + '-body');
  if(card.classList.contains('section-collapsed')) {
    body.style.maxHeight = '0';
    body.style.paddingTop = '0';
  } else {
    body.style.maxHeight = body.scrollHeight + 'px';
    body.style.paddingTop = '';
  }
}

function setType(btn, type) {
  const tabs = btn.closest('.section-type-tabs').querySelectorAll('.section-type-tab');
  tabs.forEach(t => t.classList.remove('active'));
  btn.classList.add('active');
}

function updateSectionNumbers() {
  document.querySelectorAll('.itinerary-section').forEach((s, i) => {
    s.querySelector('.section-number').textContent = i + 1;
    s.querySelector('.section-header p').textContent = 'Section ' + (i + 1);
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
  document.getElementById('allocatedAmt').textContent = '$' + total.toFixed(2);
  const booked = document.querySelectorAll('select[name*="[status]"] option[value="booked"]:checked').length;
  document.getElementById('summBooked').textContent = booked;
  document.getElementById('summPending').textContent = document.querySelectorAll('.itinerary-section').length - booked;
}

function saveItinerary() {
  const btn = event.target;
  btn.innerHTML = '<i data-lucide="check" style="width:16px;height:16px;"></i> Saved!';
  btn.style.background = 'rgba(16,185,129,0.2)';
  btn.style.color = 'var(--accent-green)';
  setTimeout(() => { btn.innerHTML = '<i data-lucide="save" style="width:16px;height:16px;"></i> Save Draft'; btn.style = ''; lucide.createIcons(); }, 2000);
}

// Drag & Drop
function initDragDrop() {
  const container = document.getElementById('sectionsContainer');
  let dragEl = null;
  container.querySelectorAll('.itinerary-section').forEach(el => {
    el.addEventListener('dragstart', () => { dragEl = el; el.classList.add('dragging'); });
    el.addEventListener('dragend', () => { dragEl = null; el.classList.remove('dragging'); updateSectionNumbers(); });
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

initDragDrop();
lucide.createIcons();
</script>
</body>
</html>
