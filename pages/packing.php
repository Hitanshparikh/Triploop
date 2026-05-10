<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

// Get user's trips for the trip selector
$trips = db()->fetchAll("SELECT id, name, destination, start_date FROM trips WHERE user_id=? ORDER BY created_at DESC", [currentUserId()]);
$selectedTrip = intval(input('trip_id', $trips[0]['id'] ?? 0));
$packingLists = [];
if ($selectedTrip) {
    $packingLists = db()->fetchAll("SELECT * FROM packing_lists WHERE trip_id=? AND user_id=? ORDER BY category", [$selectedTrip, currentUserId()]);
    foreach ($packingLists as &$pl) {
        $pl['items'] = json_decode($pl['items'] ?? '[]', true);
    }
}
// Calculate totals
$totalItems = 0; $checkedItems = 0;
foreach ($packingLists as $pl) {
    foreach ($pl['items'] as $item) { $totalItems++; if ($item['checked'] ?? false) $checkedItems++; }
}
$progress = $totalItems > 0 ? round(($checkedItems / $totalItems) * 100) : 0;
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Packing List — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}

/* Page Header */
.page-toolbar{display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);}

/* Progress Panel */
.progress-panel{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-8);display:flex;align-items:center;gap:var(--space-8);box-shadow:var(--shadow-sm);}
.progress-panel::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.progress-bar{height:6px;background:rgba(255,255,255,0.05);border-radius:var(--radius-full);overflow:hidden;margin-top:var(--space-2);box-shadow:inset 0 1px 2px rgba(0,0,0,0.2);}
.progress-fill{height:100%;background:var(--accent-cyan);border-radius:var(--radius-full);transition:width 0.4s ease;}

/* Category Card */
.packing-category{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-2xl);padding:var(--space-6);box-shadow:var(--shadow-sm);display:flex;flex-direction:column;}
.packing-category::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.checklist-items{display:flex;flex-direction:column;gap:var(--space-2);flex:1;}
.checklist-item{display:flex;align-items:flex-start;gap:var(--space-3);padding:var(--space-2) var(--space-3);border-radius:var(--radius-lg);cursor:pointer;transition:all var(--transition-fast);}
.checklist-item:hover{background:rgba(255,255,255,0.03);}
.checklist-checkbox{width:20px;height:20px;border-radius:6px;border:1px solid rgba(255,255,255,0.15);display:flex;align-items:center;justify-content:center;flex-shrink:0;margin-top:2px;transition:all var(--transition-fast);}
.checklist-item.checked .checklist-checkbox{background:var(--accent-cyan);border-color:var(--accent-cyan);}
.checklist-text{font-size:var(--text-base);color:var(--text-secondary);transition:all var(--transition-fast);line-height:1.4;}
.checklist-item.checked .checklist-text{color:var(--text-muted);text-decoration:line-through;}

/* Inputs */
.add-item-wrapper{margin-top:var(--space-5);padding-top:var(--space-4);border-top:1px solid rgba(255,255,255,0.05);display:flex;gap:var(--space-2);}
.add-item-input{flex:1;padding:var(--space-2) var(--space-3);background:rgba(255,255,255,0.02);border:1px solid rgba(255,255,255,0.05);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);transition:all var(--transition-base);}
.add-item-input:focus{border-color:var(--accent-cyan);background:rgba(255,255,255,0.04);outline:none;}
.add-item-btn{padding:var(--space-2);background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.05);border-radius:var(--radius-lg);color:var(--text-muted);cursor:pointer;transition:all var(--transition-base);}
.add-item-btn:hover{background:var(--accent-cyan);border-color:var(--accent-cyan);color:var(--bg-primary);}

/* Empty State */
.empty-state{text-align:center;padding:var(--space-12);position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-2xl);max-width:600px;margin:0 auto;}
.empty-state::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px dashed rgba(255,255,255,0.1);pointer-events:none;}
.empty-state-icon{width:64px;height:64px;color:var(--accent-cyan);opacity:0.5;margin-bottom:var(--space-4);}

/* Select */
select.input-field{background:var(--bg-glass);border:1px solid rgba(255,255,255,0.1);border-radius:var(--radius-lg);color:var(--text-primary);padding:var(--space-2) var(--space-4);outline:none;}
select.input-field option{background:var(--bg-elevated);color:var(--text-primary);}
</style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content page-transition">

<div class="page-toolbar">
    <div>
        <h1 style="font-size:var(--text-4xl);font-weight:800;letter-spacing:-0.03em;margin-bottom:var(--space-2);">Packing <span class="text-gradient-aurora">List</span></h1>
        <p style="color:var(--text-secondary);font-size:var(--text-lg);">Stay organized — never forget the essentials</p>
    </div>
    <div style="display:flex;gap:var(--space-3);align-items:center;">
        <select id="tripSelector" class="input-field" style="width:220px;padding:var(--space-2) var(--space-4);" onchange="location.href='?trip_id='+this.value">
            <?php if (empty($trips)): ?><option>No trips yet</option><?php endif; ?>
            <?php foreach ($trips as $t): ?>
            <option value="<?= $t['id'] ?>" <?= $t['id'] == $selectedTrip ? 'selected' : '' ?>><?= e($t['name']) ?></option>
            <?php endforeach; ?>
        </select>
        <?php if ($selectedTrip && empty($packingLists)): ?>
        <a href="<?= APP_URL ?>/api/packing.php?action=init&trip_id=<?= $selectedTrip ?>" class="btn btn-primary" style="font-size:var(--text-sm);">
            <i data-lucide="plus" style="width:16px;height:16px;"></i> Generate Defaults
        </a>
        <?php endif; ?>
    </div>
</div>

<?php if ($selectedTrip && !empty($packingLists)): ?>
<!-- Progress Bar -->
<div class="progress-panel">
    <div style="flex:1;">
        <div style="display:flex;justify-content:space-between;margin-bottom:var(--space-2);">
            <span style="font-size:var(--text-sm);font-weight:600;color:var(--text-secondary);">Packing Progress</span>
            <span style="font-size:var(--text-sm);color:var(--accent-cyan);font-weight:700;" id="progressText"><?= $checkedItems ?>/<?= $totalItems ?> items</span>
        </div>
        <div class="progress-bar"><div class="progress-fill" id="progressBar" style="width:<?= $progress ?>%;"></div></div>
    </div>
    <div style="display:flex;gap:var(--space-3);">
        <button class="btn btn-secondary" onclick="resetAll()">
            <i data-lucide="rotate-ccw" style="width:16px;height:16px;"></i> Reset
        </button>
        <button class="btn btn-secondary" onclick="shareList()">
            <i data-lucide="share-2" style="width:16px;height:16px;"></i> Copy to Clipboard
        </button>
    </div>
</div>

<!-- Category Columns -->
<div style="display:grid;grid-template-columns:repeat(auto-fill, minmax(320px, 1fr));gap:var(--space-6);">
<?php foreach ($packingLists as $pl): ?>
<div class="packing-category animate-fade-in" data-list-id="<?= $pl['id'] ?>" data-category="<?= e($pl['category']) ?>">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-5);">
        <h4 style="font-size:var(--text-lg);font-weight:600;display:flex;align-items:center;gap:var(--space-3);">
            <div style="width:32px;height:32px;border-radius:8px;background:rgba(56,189,248,0.1);display:flex;align-items:center;justify-content:center;">
                <i data-lucide="<?= getCategoryIcon($pl['category']) ?>" style="width:16px;height:16px;color:var(--accent-cyan);"></i>
            </div>
            <?= e($pl['category']) ?>
        </h4>
        <span class="cat-count" style="font-size:11px;font-weight:600;color:var(--text-muted);background:rgba(255,255,255,0.05);padding:var(--space-1) var(--space-3);border-radius:9999px;">
            <?php $catChecked = count(array_filter($pl['items'], fn($i) => $i['checked'] ?? false)); ?>
            <?= $catChecked ?>/<?= count($pl['items']) ?>
        </span>
    </div>
    <div class="checklist-items">
        <?php foreach ($pl['items'] as $idx => $item): ?>
        <div class="checklist-item <?= ($item['checked'] ?? false) ? 'checked' : '' ?>" data-index="<?= $idx ?>" onclick="toggleItem(this, <?= $pl['id'] ?>)">
            <div class="checklist-checkbox">
                <?php if ($item['checked'] ?? false): ?><i data-lucide="check" style="width:12px;height:12px;color:var(--bg-primary);"></i><?php endif; ?>
            </div>
            <span class="checklist-text"><?= e($item['name']) ?></span>
        </div>
        <?php endforeach; ?>
    </div>
    <!-- Add Item -->
    <div class="add-item-wrapper">
        <input type="text" class="add-item-input" placeholder="Add item..." onkeypress="if(event.key==='Enter')addItem(this, <?= $pl['id'] ?>, '<?= e($pl['category']) ?>')">
        <button class="add-item-btn" onclick="addItem(this.previousElementSibling, <?= $pl['id'] ?>, '<?= e($pl['category']) ?>')">
            <i data-lucide="plus" style="width:16px;height:16px;"></i>
        </button>
    </div>
</div>
<?php endforeach; ?>
</div>

<?php elseif ($selectedTrip && empty($packingLists)): ?>
<div class="empty-state">
    <i data-lucide="luggage" class="empty-state-icon" style="margin:0 auto var(--space-4);display:block;"></i>
    <h3 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-2);">No Packing List Yet</h3>
    <p style="color:var(--text-secondary);margin-bottom:var(--space-6);font-size:var(--text-lg);">Click "Generate Defaults" above to create a starter packing list for this trip.</p>
</div>
<?php elseif (empty($trips)): ?>
<div class="empty-state">
    <i data-lucide="map-pin-off" class="empty-state-icon" style="margin:0 auto var(--space-4);display:block;"></i>
    <h3 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-2);">No Trips Found</h3>
    <p style="color:var(--text-secondary);margin-bottom:var(--space-6);font-size:var(--text-lg);">Create a trip first, then come back to manage your packing list.</p>
    <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-primary" style="padding:var(--space-3) var(--space-6);font-size:var(--text-base);">Create Trip</a>
</div>
<?php endif; ?>

</main>
</div>

<script>
const APP_URL = '<?= APP_URL ?>';
const TRIP_ID = <?= $selectedTrip ?: 0 ?>;

function toggleItem(el, listId) {
    el.classList.toggle('checked');
    const isChecked = el.classList.contains('checked');
    const cb = el.querySelector('.checklist-checkbox');
    cb.innerHTML = isChecked ? '<svg xmlns="http://www.w3.org/2000/svg" width="14" height="14" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="3" stroke-linecap="round" stroke-linejoin="round" style="color:var(--bg-primary)"><polyline points="20 6 9 17 4 12"></polyline></svg>' : '';
    saveList(el.closest('.packing-category'));
    updateProgress();
}

function addItem(input, listId, category) {
    const name = input.value.trim();
    if (!name) return;
    const container = input.closest('.packing-category').querySelector('.checklist-items');
    const idx = container.children.length;
    const div = document.createElement('div');
    div.className = 'checklist-item';
    div.dataset.index = idx;
    div.onclick = function(){ toggleItem(this, listId); };
    div.innerHTML = '<div class="checklist-checkbox"></div><span class="checklist-text">' + name + '</span>';
    container.appendChild(div);
    input.value = '';
    saveList(input.closest('.packing-category'));
    updateProgress();
    lucide.createIcons();
}

function saveList(categoryEl) {
    const listId = categoryEl.dataset.listId;
    const category = categoryEl.dataset.category;
    const items = [];
    categoryEl.querySelectorAll('.checklist-item').forEach(el => {
        items.push({ name: el.querySelector('.checklist-text').textContent, checked: el.classList.contains('checked') });
    });
    // Update count badge
    const checked = items.filter(i => i.checked).length;
    categoryEl.querySelector('.cat-count').textContent = checked + '/' + items.length;
    
    fetch(APP_URL + '/api/packing.php', {
        method: 'POST',
        headers: {'Content-Type':'application/x-www-form-urlencoded','X-Requested-With':'XMLHttpRequest'},
        body: 'action=save&trip_id=' + TRIP_ID + '&category=' + encodeURIComponent(category) + '&items=' + encodeURIComponent(JSON.stringify(items))
    });
}

function updateProgress() {
    let total = 0, checked = 0;
    document.querySelectorAll('.checklist-item').forEach(el => { total++; if (el.classList.contains('checked')) checked++; });
    const pct = total > 0 ? Math.round((checked/total)*100) : 0;
    document.getElementById('progressText').textContent = checked + '/' + total + ' items';
    document.getElementById('progressBar').style.width = pct + '%';
}

function resetAll() {
    if (!confirm('Reset all items to unchecked?')) return;
    document.querySelectorAll('.checklist-item').forEach(el => {
        el.classList.remove('checked');
        el.querySelector('.checklist-checkbox').innerHTML = '';
    });
    document.querySelectorAll('.packing-category').forEach(el => saveList(el));
    updateProgress();
}

function shareList() {
    let text = 'Packing List\n\n';
    document.querySelectorAll('.packing-category').forEach(cat => {
        text += cat.dataset.category + ':\n';
        cat.querySelectorAll('.checklist-item').forEach(el => {
            const checked = el.classList.contains('checked') ? '[x]' : '[ ]';
            text += '  ' + checked + ' ' + el.querySelector('.checklist-text').textContent + '\n';
        });
        text += '\n';
    });
    navigator.clipboard.writeText(text).then(() => alert('Packing list copied to clipboard!'));
}
</script>

<?php
function getCategoryIcon($cat) {
    $icons = ['Documents'=>'file-text','Clothing'=>'shirt','Electronics'=>'smartphone','Toiletries'=>'droplets','Essentials'=>'backpack','General'=>'box'];
    return $icons[$cat] ?? 'package';
}
?>
</body>
</html>
