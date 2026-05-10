<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$userId = currentUserId();
$tripId = intval($_GET['id'] ?? ($_GET['trip_id'] ?? 1));
$viewMode = $_GET['view'] ?? 'all'; // all | day | stop

// Fetch Trip
$trip = db()->fetch("SELECT * FROM trips WHERE id = ? AND user_id = ?", [$tripId, $userId]);
if (!$trip) {
    setFlash('error', 'Trip not found.');
    redirect('/pages/my-trips.php');
}

// Fetch Notes
$notes = db()->fetchAll("SELECT * FROM journal WHERE trip_id = ? ORDER BY created_at DESC", [$tripId]);

$moodColors = ['happy'=>'#F59E0B','excited'=>'#FF6B35','calm'=>'#00D4FF','reflective'=>'#A855F7','tired'=>'#94A3B8','grateful'=>'#10B981','adventurous'=>'#EC4899'];
$moodIcons  = ['happy'=>'smile','excited'=>'zap','calm'=>'leaf','reflective'=>'book-open','tired'=>'moon','grateful'=>'heart','adventurous'=>'compass'];

// Group for sidebar
$byDay  = [];
$byStop = [];
foreach($notes as $n) {
    $day = $n['day_number'] ?? 'N/A';
    $stop = $n['stop_name'] ?: 'General';
    $byDay['Day '.$day][] = $n;
    $byStop[$stop][] = $n;
}
ksort($byDay);
ksort($byStop);

// Filter
$filtered = $notes;
if($viewMode === 'day' && isset($_GET['day'])) {
    $filtered = array_filter($notes, fn($n) => ($n['day_number'] ?? 'N/A') == $_GET['day']);
}
if($viewMode === 'stop' && isset($_GET['stop'])) {
    $filtered = array_filter($notes, fn($n) => ($n['stop_name'] ?: 'General') === $_GET['stop']);
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Trip Notes — <?= e($trip['name']) ?></title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}
/* Journal Layout */
.journal-layout{display:grid;grid-template-columns:260px 1fr;gap:var(--space-6);align-items:start;}
/* Left Nav */
.journal-nav{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-5);position:sticky;top:var(--space-8);box-shadow:var(--shadow-sm);}
.journal-nav::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.nav-section{margin-bottom:var(--space-5);}
.nav-section-title{font-size:var(--text-xs);font-weight:var(--font-bold);color:var(--text-secondary);text-transform:uppercase;letter-spacing:0.08em;margin-bottom:var(--space-3);}
.nav-item{display:flex;align-items:center;gap:var(--space-3);padding:var(--space-2) var(--space-3);border-radius:var(--radius-md);font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer;text-decoration:none;transition:all var(--transition-fast);margin-bottom:2px;}
.nav-item:hover,.nav-item.active{background:rgba(255,255,255,0.05);color:var(--text-primary);}
.nav-item.active i{color:var(--accent-cyan);}
.nav-item i{width:16px;height:16px;flex-shrink:0;}
/* Toolbar */
.journal-toolbar{display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);flex-wrap:wrap;}
.search-box{position:relative;flex:1;min-width:200px;}
.search-box input{width:100%;padding:var(--space-3) var(--space-4) var(--space-3) 40px;background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;transition:all var(--transition-base);box-shadow:var(--shadow-sm);}
.search-box input:focus{border-color:var(--accent-cyan);}
.search-box input::placeholder{color:var(--text-muted);}
.search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.toolbar-select{padding:var(--space-2) var(--space-3);background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* View Tabs */
.view-tabs{display:flex;gap:0;background:var(--bg-elevated);border-radius:var(--radius-lg);padding:3px;margin-bottom:var(--space-6);}
.view-tab{padding:var(--space-2) var(--space-4);border-radius:calc(var(--radius-lg) - 3px);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;border:none;background:transparent;color:var(--text-muted);transition:all var(--transition-base);}
.view-tab.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-sm);}
/* Note Cards */
.notes-list{display:flex;flex-direction:column;gap:var(--space-5);}
.note-card{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-6);transition:all var(--transition-base);box-shadow:var(--shadow-sm);}
.note-card::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.note-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.note-card:hover::before{border-color:rgba(56,189,248,0.3);}
.note-header{display:flex;align-items:flex-start;gap:var(--space-4);margin-bottom:var(--space-4);}
.note-mood-icon{width:42px;height:42px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;border:1px solid rgba(255,255,255,0.05);}
.note-meta{display:flex;align-items:center;gap:var(--space-3);margin-top:var(--space-2);flex-wrap:wrap;}
.note-meta-item{display:flex;align-items:center;gap:4px;font-size:var(--text-xs);color:var(--text-secondary);font-weight:500;}
.note-meta-item i{width:12px;height:12px;}
.note-content{font-size:var(--text-base);color:var(--text-secondary);line-height:var(--leading-relaxed);padding-left:calc(42px + var(--space-4)); white-space:pre-wrap;}
.note-actions{display:flex;gap:var(--space-2);margin-top:var(--space-5);padding-top:var(--space-4);border-top:1px solid rgba(255,255,255,0.05);padding-left:calc(42px + var(--space-4));}
.note-action{padding:var(--space-1) var(--space-3);border-radius:var(--radius-md);font-size:var(--text-xs);font-weight:600;border:1px solid rgba(255,255,255,0.05);background:rgba(255,255,255,0.03);color:var(--text-secondary);cursor:pointer;display:flex;align-items:center;gap:4px;transition:all var(--transition-fast);}
.note-action:hover{border-color:var(--text-primary);color:var(--text-primary);background:rgba(255,255,255,0.08);}
.note-action.danger:hover{border-color:var(--accent-red);color:var(--accent-red);background:rgba(239,68,68,0.1);}
/* Add Note Modal */
.modal-overlay{display:none;position:fixed;inset:0;background:rgba(9,9,11,0.9);backdrop-filter:blur(12px);z-index:400;align-items:center;justify-content:center;}
.modal-overlay.open{display:flex;}
.modal-card{position:relative;background:var(--bg-elevated);border-radius:var(--radius-2xl);padding:var(--space-8);width:100%;max-width:600px;max-height:90vh;overflow-y:auto;animation:scaleIn 0.3s var(--transition-spring);box-shadow:var(--shadow-xl);}
.modal-card::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.08);pointer-events:none;}
.input-group{margin-bottom:var(--space-5);}
.input-group label{display:block;font-size:var(--text-sm);font-weight:600;color:var(--text-primary);margin-bottom:var(--space-2);}
.mood-row{display:flex;gap:var(--space-2);flex-wrap:wrap;}
.mood-chip{padding:var(--space-1) var(--space-4);border-radius:var(--radius-full);font-size:var(--text-sm);font-weight:500;border:1px solid rgba(255,255,255,0.1);background:rgba(255,255,255,0.03);color:var(--text-secondary);cursor:pointer;transition:all var(--transition-fast);}
.mood-chip.selected{border-color:var(--accent-cyan);color:var(--bg-primary);background:var(--accent-cyan);}
/* Empty state */
.empty-state{text-align:center;padding:var(--space-12);color:var(--text-muted);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content page-transition">

  <!-- Header -->
  <div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);">
    <div>
      <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-3);text-decoration:none;">
        <i data-lucide="arrow-left" style="width:14px;height:14px;"></i> Back to Trips
      </a>
      <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-2);font-weight:800;letter-spacing:-0.03em;">Journal <span class="text-gradient-aurora">Notes</span></h1>
      <p style="color:var(--text-secondary);font-size:var(--text-lg);"><?= e($trip['name']) ?></p>
    </div>
    <button class="btn btn-primary" onclick="openModal()">
      <i data-lucide="plus" style="width:16px;height:16px;"></i> Add Entry
    </button>
  </div>

  <div class="journal-layout">

    <!-- LEFT: Navigation -->
    <div>
      <div class="journal-nav">
        <!-- All Notes -->
        <div class="nav-section">
          <a href="?id=<?= $tripId ?>&view=all" class="nav-item <?= $viewMode==='all'?'active':'' ?>">
            <i data-lucide="layers"></i> All Notes
            <span style="margin-left:auto;font-size:11px;font-weight:600;color:var(--text-muted);"><?= count($notes) ?></span>
          </a>
        </div>

        <!-- By Day -->
        <?php if(!empty($byDay)): ?>
        <div class="nav-section">
          <div class="nav-section-title">By Day</div>
          <?php foreach($byDay as $dayLabel => $dayNotes): $dayNum = str_replace('Day ','',$dayLabel); ?>
          <a href="?id=<?= $tripId ?>&view=day&day=<?= $dayNum ?>" class="nav-item <?= ($viewMode==='day'&&($_GET['day']??'')==$dayNum)?'active':'' ?>">
            <i data-lucide="calendar"></i> <?= $dayLabel ?>
            <span style="margin-left:auto;font-size:11px;font-weight:600;color:var(--text-muted);"><?= count($dayNotes) ?></span>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>

        <!-- By Stop -->
        <?php if(!empty($byStop)): ?>
        <div class="nav-section">
          <div class="nav-section-title">By Location</div>
          <?php foreach($byStop as $stopName => $stopNotes): ?>
          <a href="?id=<?= $tripId ?>&view=stop&stop=<?= urlencode($stopName) ?>" class="nav-item <?= ($viewMode==='stop'&&($_GET['stop']??'')===$stopName)?'active':'' ?>">
            <i data-lucide="map-pin"></i> <?= e($stopName) ?>
            <span style="margin-left:auto;font-size:11px;font-weight:600;color:var(--text-muted);"><?= count($stopNotes) ?></span>
          </a>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
    </div>

    <!-- RIGHT: Notes Feed -->
    <div>
      <!-- Toolbar -->
      <div class="journal-toolbar">
        <div class="search-box">
          <span class="search-icon"><i data-lucide="search" style="width:16px;height:16px;"></i></span>
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
          <option value="stop">Location</option>
          <option value="mood">Mood</option>
        </select>
      </div>

      <!-- Active filter badge -->
      <?php if($viewMode !== 'all'): ?>
      <div style="display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);background:rgba(56,189,248,0.08);border:1px solid rgba(56,189,248,0.15);border-radius:var(--radius-full);font-size:var(--text-sm);color:var(--accent-cyan);margin-bottom:var(--space-5);font-weight:500;">
        <i data-lucide="filter" style="width:14px;height:14px;"></i>
        Filtered by: <?= $viewMode === 'day' ? 'Day '.($_GET['day']??'') : e($_GET['stop']??'') ?>
        <a href="?id=<?= $tripId ?>" style="color:var(--accent-cyan);opacity:0.7;margin-left:4px;text-decoration:none;">&times; Clear</a>
      </div>
      <?php endif; ?>

      <!-- Notes List -->
      <div class="notes-list" id="notesList">
        <?php if(empty($filtered)): ?>
        <div class="empty-state stat-card" style="margin-top:0;">
          <i data-lucide="book-open" style="width:48px;height:48px;margin:0 auto var(--space-4);display:block;opacity:0.5;"></i>
          <h4 style="font-size:var(--text-xl);font-weight:600;margin-bottom:var(--space-2);">No journal entries yet</h4>
          <p style="margin-bottom:var(--space-6);color:var(--text-secondary);">Start capturing your travel memories and important details.</p>
          <button class="btn btn-primary" onclick="openModal()">Add First Entry</button>
        </div>
        <?php else: ?>
          <?php foreach($filtered as $note):
            $mc = $moodColors[$note['mood']] ?? '#38bdf8';
            $mi = $moodIcons[$note['mood']] ?? 'smile';
          ?>
          <div class="note-card animate-fade-in" data-title="<?= strtolower($note['title']) ?>" data-content="<?= strtolower($note['content']) ?>" data-day="<?= $note['day_number'] ?? 0 ?>">
            <div class="note-header">
              <div class="note-mood-icon" style="background:<?= $mc ?>15; border-color:<?= $mc ?>30;">
                <i data-lucide="<?= $mi ?>" style="width:20px;height:20px;color:<?= $mc ?>;"></i>
              </div>
              <div style="flex:1;">
                <h5 style="margin:0;font-size:var(--text-lg);font-weight:600;"><?= e($note['title']) ?></h5>
                <div class="note-meta">
                  <span class="note-meta-item"><i data-lucide="calendar"></i> <?= $note['day_number'] ? 'Day '.$note['day_number'].' · ' : '' ?><?= date('M j, Y', strtotime($note['created_at'])) ?></span>
                  <?php if($note['stop_name']): ?>
                  <span class="note-meta-item"><i data-lucide="map-pin"></i> <?= e($note['stop_name']) ?></span>
                  <?php endif; ?>
                  <span style="font-size:10px;padding:2px 8px;border-radius:9999px;background:<?= $mc ?>15;color:<?= $mc ?>;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;border:1px solid <?= $mc ?>30;"><?= ucfirst($note['mood']) ?></span>
                </div>
              </div>
            </div>
            <p class="note-content"><?= e($note['content']) ?></p>
            <div class="note-actions">
              <button class="note-action" onclick="copyNote('<?= addslashes(str_replace(["\r", "\n"], ["", "\\n"], $note['content'])) ?>')">
                <i data-lucide="copy" style="width:14px;height:14px;"></i> Copy
              </button>
              <form method="POST" action="<?= APP_URL ?>/api/journal.php" style="margin:0;" onsubmit="return confirm('Delete this entry?');">
                <input type="hidden" name="action" value="delete">
                <input type="hidden" name="id" value="<?= $note['id'] ?>">
                <?= csrfField() ?>
                <button type="submit" class="note-action danger">
                  <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Delete
                </button>
              </form>
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
      <h4 style="margin:0;font-size:var(--text-2xl);font-weight:700;">New Journal Entry</h4>
      <button onclick="closeModal()" style="background:none;border:none;color:var(--text-muted);cursor:pointer;transition:color var(--transition-fast);"><i data-lucide="x" style="width:24px;height:24px;"></i></button>
    </div>
    <form method="POST" action="<?= APP_URL ?>/api/journal.php">
      <input type="hidden" name="action" value="create">
      <input type="hidden" name="trip_id" value="<?= $tripId ?>">
      <?= csrfField() ?>
      <div class="input-group">
        <label>Entry Title</label>
        <input type="text" name="title" class="input-field" placeholder="e.g. Hidden gems in Trastevere" required>
      </div>
      <div style="display:grid;grid-template-columns:1fr 1fr;gap:var(--space-5);">
        <div class="input-group">
          <label>Day Number (Optional)</label>
          <input type="number" name="day_number" class="input-field" placeholder="1" min="1">
        </div>
        <div class="input-group">
          <label>Location (Optional)</label>
          <input type="text" name="stop_name" class="input-field" placeholder="e.g. Rome">
        </div>
      </div>
      <div class="input-group">
        <label>Content</label>
        <textarea name="content" class="input-field" style="min-height:160px;resize:vertical;" placeholder="Write your thoughts, memories, or important details here..." required></textarea>
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
      <div style="display:flex;gap:var(--space-4);margin-top:var(--space-8);">
        <button type="button" class="btn btn-secondary" onclick="closeModal()" style="padding:var(--space-3) var(--space-6);">Cancel</button>
        <button type="submit" class="btn btn-primary" style="flex:1;padding:var(--space-3) var(--space-6);font-size:var(--text-base);">
          <i data-lucide="save" style="width:18px;height:18px;"></i> Save Entry
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
    if(by === 'day') return parseInt(a.dataset.day||0) - parseInt(b.dataset.day||0);
    return 0; // Default is already sorted by created_at DESC from DB
  });
  cards.forEach(c => list.appendChild(c));
}
function copyNote(text) {
  navigator.clipboard.writeText(text).then(() => {
      const el = event.currentTarget;
      const originalHtml = el.innerHTML;
      el.innerHTML = '<i data-lucide="check" style="width:14px;height:14px;"></i> Copied';
      lucide.createIcons();
      el.style.color = 'var(--accent-green)';
      setTimeout(() => {
          el.innerHTML = originalHtml;
          el.style.color = '';
          lucide.createIcons();
      }, 2000);
  });
}
lucide.createIcons();
</script>
</body>
</html>
