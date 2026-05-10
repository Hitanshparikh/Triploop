<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();

$tripId   = $_GET['trip_id'] ?? 1;
$viewMode = $_GET['view'] ?? 'all'; // all | day | stop
$tripName = 'Paris & Rome Adventure'; // Demo — replace with DB query

// Demo notes data
$notes = [
    ['id'=>1,'title'=>'Hotel check-in details - Rome stop','content'=>'Check in after 2pm, room 302, breakfast included (7–10am). Wi-Fi password: Roma2026. Luggage storage available at reception.','day'=>3,'stop'=>'Rome','date'=>'2025-06-14','mood'=>'happy'],
    ['id'=>2,'title'=>'Colosseum visit tips','content'=>'Book skip-the-line tickets in advance. Best light for photos is early morning. Wear comfortable shoes — lots of walking on uneven surfaces.','day'=>3,'stop'=>'Rome','date'=>'2025-06-14','mood'=>'excited'],
    ['id'=>3,'title'=>'Flight DEL → CDG notes','content'=>'Terminal 2E. Check-in closes 45 min before. Lounge access via Priority Pass. Arrive 3 hours early.','day'=>1,'stop'=>'Paris','date'=>'2025-06-12','mood'=>'calm'],
    ['id'=>4,'title'=>'Eiffel Tower evening experience','content'=>'Booked sunset slot — 7:30 PM. Bring a jacket, it gets windy at the top. Restaurant reservation at Madame Brasserie for 9 PM.','day'=>2,'stop'=>'Paris','date'=>'2025-06-13','mood'=>'grateful'],
    ['id'=>5,'title'=>'Vatican Museum booking','content'=>'Entry at 9 AM. Guide tour included. Sistine Chapel is at the end — allow 3 hours total. Dress code enforced (covered shoulders/knees).','day'=>4,'stop'=>'Rome','date'=>'2025-06-15','mood'=>'reflective'],
];

$moodColors = ['happy'=>'#F59E0B','excited'=>'#FF6B35','calm'=>'#00D4FF','reflective'=>'#A855F7','tired'=>'#94A3B8','grateful'=>'#10B981','adventurous'=>'#EC4899'];
$moodIcons  = ['happy'=>'smile','excited'=>'zap','calm'=>'leaf','reflective'=>'book-open','tired'=>'moon','grateful'=>'heart','adventurous'=>'compass'];

// Group for sidebar
$byDay  = [];
$byStop = [];
foreach($notes as $n) {
    $byDay['Day '.$n['day']][]    = $n;
    $byStop[$n['stop']][] = $n;
}

// Filter
$filtered = $notes;
if($viewMode === 'day' && isset($_GET['day'])) {
    $filtered = array_filter($notes, fn($n) => $n['day'] == $_GET['day']);
}
if($viewMode === 'stop' && isset($_GET['stop'])) {
    $filtered = array_filter($notes, fn($n) => $n['stop'] === $_GET['stop']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Trip Notes — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Journal Layout */
.journal-layout{display:grid;grid-template-columns:260px 1fr;gap:var(--space-6);align-items:start;}
/* Left Nav */
.journal-nav{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-5);position:sticky;top:var(--space-8);}
.nav-section{margin-bottom:var(--space-5);}
.nav-section-title{font-size:var(--text-xs);font-weight:var(--font-semibold);color:var(--text-muted);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:var(--space-3);}
.nav-item{display:flex;align-items:center;gap:var(--space-3);padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer;text-decoration:none;transition:all var(--transition-fast);margin-bottom:2px;}
.nav-item:hover,.nav-item.active{background:rgba(0,212,255,0.08);color:var(--accent-cyan);}
.nav-item i{width:14px;height:14px;flex-shrink:0;}
/* Toolbar */
.journal-toolbar{display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-5);flex-wrap:wrap;}
.search-box{position:relative;flex:1;min-width:200px;}
.search-box input{width:100%;padding:var(--space-3) var(--space-4) var(--space-3) 40px;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;transition:all var(--transition-base);}
.search-box input:focus{border-color:var(--accent-cyan);box-shadow:0 0 0 3px var(--accent-cyan-soft);}
.search-box input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:12px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.toolbar-select{padding:var(--space-2) var(--space-3);background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* View Tabs */
.view-tabs{display:flex;gap:0;background:var(--bg-elevated);border-radius:var(--radius-lg);padding:3px;margin-bottom:var(--space-6);}
.view-tab{padding:var(--space-2) var(--space-4);border-radius:calc(var(--radius-lg) - 3px);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;border:none;background:transparent;color:var(--text-muted);transition:all var(--transition-base);}
.view-tab.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-sm);}
/* Note Cards */
.notes-list{display:flex;flex-direction:column;gap:var(--space-4);}
.note-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-5);transition:all var(--transition-base);}
.note-card:hover{border-color:rgba(0,212,255,0.15);box-shadow:var(--shadow-md);}
.note-header{display:flex;align-items:flex-start;gap:var(--space-3);margin-bottom:var(--space-3);}
.note-mood-icon{width:36px;height:36px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
.note-meta{display:flex;align-items:center;gap:var(--space-3);margin-top:var(--space-2);flex-wrap:wrap;}
.note-meta-item{display:flex;align-items:center;gap:4px;font-size:var(--text-xs);color:var(--text-muted);}
.note-meta-item i{width:11px;height:11px;}
.note-content{font-size:var(--text-sm);color:var(--text-secondary);line-height:var(--leading-relaxed);}
.note-actions{display:flex;gap:var(--space-2);margin-top:var(--space-4);padding-top:var(--space-3);border-top:1px solid rgba(148,163,184,0.06);}
.note-action{padding:var(--space-1) var(--space-3);border-radius:var(--radius-md);font-size:var(--text-xs);font-weight:var(--font-semibold);border:1px solid rgba(148,163,184,0.12);background:var(--bg-elevated);color:var(--text-muted);cursor:pointer;display:flex;align-items:center;gap:4px;transition:all var(--transition-fast);}
.note-action:hover{border-color:var(--accent-cyan);color:var(--accent-cyan);}
.note-action.danger:hover{border-color:var(--accent-red);color:var(--accent-red);}
/* Add Note Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(11,16,32,0.85);backdrop-filter:blur(8px);z-index:400;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-card{background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-2xl);padding:var(--space-8);width:90%;max-width:560px;max-height:90vh;overflow-y:auto;animation:scaleIn 0.3s var(--transition-spring);}
.input-group{margin-bottom:var(--space-4);}
.input-group label{display:block;font-size:var(--text-sm);font-weight:var(--font-medium);color:var(--text-secondary);margin-bottom:var(--space-2);}
.mood-row{display:flex;gap:var(--space-2);flex-wrap:wrap;}
.mood-chip{padding:var(--space-1) var(--space-3);border-radius:var(--radius-full);font-size:var(--text-xs);font-weight:var(--font-semibold);border:1px solid rgba(148,163,184,0.15);background:var(--bg-glass);color:var(--text-muted);cursor:pointer;transition:all var(--transition-fast);}
.mood-chip.selected{border-color:var(--accent-cyan);color:var(--accent-cyan);background:rgba(0,212,255,0.08);}
/* Empty state */
.empty-state{text-align:center;padding:var(--space-12);color:var(--text-muted);}
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
      <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-1);">Trip <span class="text-gradient">Notes</span></h1>
      <p style="color:var(--text-secondary);">Trip: <strong><?= e($tripName) ?></strong></p>
    </div>
    <button class="btn btn-primary" onclick="openModal()">
      <i data-lucide="plus" style="width:16px;height:16px;"></i> Add Note
    </button>
  </div>

  <div class="journal-layout">

    <!-- LEFT: Navigation -->
    <div>
      <div class="journal-nav">
        <!-- All Notes -->
        <div class="nav-section">
          <a href="?trip_id=<?= $tripId ?>&view=all" class="nav-item <?= $viewMode==='all'?'active':'' ?>">
            <i data-lucide="layers"></i> All Notes
            <span style="margin-left:auto;font-size:10px;color:var(--text-muted);"><?= count($notes) ?></span>
          </a>
        </div>

        <!-- By Day -->
        <div class="nav-section">
          <div class="nav-section-title">By Day</div>
          <?php foreach($byDay as $dayLabel => $dayNotes): $dayNum = str_replace('Day ','',$dayLabel); ?>
          <a href="?trip_id=<?= $tripId ?>&view=day&day=<?= $dayNum ?>" class="nav-item <?= ($viewMode==='day'&&($_GET['day']??'')==$dayNum)?'active':'' ?>">
            <i data-lucide="calendar"></i> <?= $dayLabel ?>
            <span style="margin-left:auto;font-size:10px;color:var(--text-muted);"><?= count($dayNotes) ?></span>
          </a>
          <?php endforeach; ?>
        </div>

        <!-- By Stop -->
        <div class="nav-section">
          <div class="nav-section-title">By Stop</div>
          <?php foreach($byStop as $stopName => $stopNotes): ?>
          <a href="?trip_id=<?= $tripId ?>&view=stop&stop=<?= urlencode($stopName) ?>" class="nav-item <?= ($viewMode==='stop'&&($_GET['stop']??'')===$stopName)?'active':'' ?>">
            <i data-lucide="map-pin"></i> <?= e($stopName) ?>
            <span style="margin-left:auto;font-size:10px;color:var(--text-muted);"><?= count($stopNotes) ?></span>
          </a>
          <?php endforeach; ?>
        </div>
      </div>
    </div>

    <!-- RIGHT: Notes Feed -->
    <div>
      <!-- Toolbar -->
      <div class="journal-toolbar">
        <div class="search-box">
          <span class="search-icon"><i data-lucide="search" style="width:14px;height:14px;"></i></span>
          <input type="text" placeholder="Search notes..." id="searchInput" oninput="searchNotes()">
        </div>
        <select class="toolbar-select" id="sortSelect" onchange="sortNotes()">
          <option value="newest">Newest first</option>
          <option value="oldest">Oldest first</option>
          <option value="day">By day</option>
        </select>
        <select class="toolbar-select" id="groupSelect">
          <option value="">Group by...</option>
          <option value="day">Day</option>
          <option value="stop">Stop</option>
          <option value="mood">Mood</option>
        </select>
      </div>

      <!-- Active filter badge -->
      <?php if($viewMode !== 'all'): ?>
      <div style="display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.15);border-radius:var(--radius-full);font-size:var(--text-sm);color:var(--accent-cyan);margin-bottom:var(--space-5);">
        <i data-lucide="filter" style="width:12px;height:12px;"></i>
        Filtered by: <?= $viewMode === 'day' ? 'Day '.($_GET['day']??'') : e($_GET['stop']??'') ?>
        <a href="?trip_id=<?= $tripId ?>" style="color:var(--text-muted);margin-left:4px;">&times;</a>
      </div>
      <?php endif; ?>

      <!-- Notes List -->
      <div class="notes-list" id="notesList">
        <?php if(empty($filtered)): ?>
        <div class="empty-state">
          <i data-lucide="book-open" style="width:48px;height:48px;margin:0 auto var(--space-4);display:block;"></i>
          <h4>No notes yet</h4>
          <p style="margin:var(--space-2) 0 var(--space-4);">Start capturing your travel memories.</p>
          <button class="btn btn-primary" onclick="openModal()">Add First Note</button>
        </div>
        <?php else: ?>
          <?php foreach($filtered as $note):
            $mc = $moodColors[$note['mood']] ?? '#00D4FF';
            $mi = $moodIcons[$note['mood']] ?? 'smile';
          ?>
          <div class="note-card animate-fade-in" data-title="<?= strtolower($note['title']) ?>" data-content="<?= strtolower($note['content']) ?>" data-day="<?= $note['day'] ?>">
            <div class="note-header">
              <div class="note-mood-icon" style="background:<?= $mc ?>20;">
                <i data-lucide="<?= $mi ?>" style="width:16px;height:16px;color:<?= $mc ?>;"></i>
              </div>
              <div style="flex:1;">
                <h5 style="margin:0;font-size:var(--text-base);"><?= e($note['title']) ?></h5>
                <div class="note-meta">
                  <span class="note-meta-item"><i data-lucide="calendar"></i> Day <?= $note['day'] ?> · <?= date('M j, Y', strtotime($note['date'])) ?></span>
                  <span class="note-meta-item"><i data-lucide="map-pin"></i> <?= e($note['stop']) ?></span>
                  <span style="font-size:10px;padding:2px 8px;border-radius:9999px;background:<?= $mc ?>15;color:<?= $mc ?>;"><?= ucfirst($note['mood']) ?></span>
                </div>
              </div>
            </div>
            <p class="note-content"><?= e($note['content']) ?></p>
            <div class="note-actions">
              <button class="note-action" onclick="editNote(<?= $note['id'] ?>)">
                <i data-lucide="edit-3" style="width:11px;height:11px;"></i> Edit
              </button>
              <button class="note-action" onclick="copyNote('<?= addslashes($note['content']) ?>')">
                <i data-lucide="copy" style="width:11px;height:11px;"></i> Copy
              </button>
              <button class="note-action danger" onclick="deleteNote(<?= $note['id'] ?>)">
                <i data-lucide="trash-2" style="width:11px;height:11px;"></i>
              </button>
            </div>
          </div>
          <?php endforeach; ?>
        <?php endif; ?>
      </div>
    </div>
  </div>
</main>
</div>

<!-- Add Note Modal -->
<div class="modal-overlay" id="noteModal">
  <div class="modal-card">
    <div style="display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);">
      <h4 style="margin:0;">Add Note</h4>
      <button onclick="closeModal()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;"><i data-lucide="x" style="width:20px;height:20px;"></i></button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/api/journal.php">
      <input type="hidden" name="action" value="create">
      <input type="hidden" name="trip_id" value="<?= $tripId ?>">
      <?= csrfField() ?>
      <div class="input-group">
        <label>Note Title *</label>
        <input type="text" name="title" class="input-field" placeholder="e.g. Hotel check-in details" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);">
        <div class="input-group">
          <label>Day Number</label>
          <input type="number" name="day_number" class="input-field" placeholder="1" min="1">
        </div>
        <div class="input-group">
          <label>Stop / City</label>
          <input type="text" name="stop_name" class="input-field" placeholder="e.g. Rome">
        </div>
      </div>
      <div class="input-group">
        <label>Note Content</label>
        <textarea name="content" class="input-field" style="min-height:120px;resize:vertical;" placeholder="Write your note here..."></textarea>
      </div>
      <div class="input-group">
        <label>Mood</label>
        <div class="mood-row">
          <?php foreach($moodColors as $m => $c): ?>
          <span class="mood-chip <?= $m==='happy'?'selected':'' ?>" onclick="selectMood(this,'<?= $m ?>')" data-mood="<?= $m ?>"><?= ucfirst($m) ?></span>
          <?php endforeach; ?>
        </div>
        <input type="hidden" name="mood" id="selectedMood" value="happy">
      </div>
      <div style="display:flex;gap:var(--space-3);margin-top:var(--space-6);">
        <button type="button" class="btn btn-secondary" onclick="closeModal()">Cancel</button>
        <button type="submit" class="btn btn-primary" style="flex:1;">
          <i data-lucide="save" style="width:14px;height:14px;"></i> Save Note
        </button>
      </div>
    </form>
  </div>
</div>

<script>
function openModal() { document.getElementById('noteModal').classList.add('open'); lucide.createIcons(); }
function closeModal() { document.getElementById('noteModal').classList.remove('open'); }
function selectMood(el, mood) {
  document.querySelectorAll('.mood-chip').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('selectedMood').value = mood;
}
function searchNotes() {
  const q = document.getElementById('searchInput').value.toLowerCase();
  document.querySelectorAll('.note-card').forEach(c => {
    const t = (c.dataset.title || '') + ' ' + (c.dataset.content || '');
    c.style.display = t.includes(q) ? '' : 'none';
  });
}
function sortNotes() {
  const by = document.getElementById('sortSelect').value;
  const list = document.getElementById('notesList');
  const cards = [...list.querySelectorAll('.note-card')];
  cards.sort((a, b) => {
    if(by === 'day') return parseInt(a.dataset.day) - parseInt(b.dataset.day);
    return 0;
  });
  cards.forEach(c => list.appendChild(c));
}
function copyNote(text) {
  navigator.clipboard.writeText(text).then(() => alert('Copied to clipboard!'));
}
function deleteNote(id) {
  if(confirm('Delete this note?')) {
    fetch('<?= APP_URL ?>/api/journal.php', {
      method: 'POST',
      headers: {'Content-Type':'application/x-www-form-urlencoded'},
      body: 'action=delete&id=' + id
    }).then(() => location.reload());
  }
}
function editNote(id) { alert('Edit note #' + id + ' — coming soon!'); }
lucide.createIcons();
</script>
</body>
</html>
