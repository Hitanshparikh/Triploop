<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Trip — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Wizard Steps */
.wizard-steps{display:flex;align-items:center;gap:0;margin-bottom:var(--space-10);}
.step-item{display:flex;align-items:center;flex:1;}
.step-circle{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;font-weight:var(--font-bold);font-size:var(--text-sm);border:2px solid rgba(148,163,184,0.2);background:var(--bg-elevated);color:var(--text-muted);transition:all var(--transition-base);flex-shrink:0;}
.step-circle.active{background:var(--gradient-cyan);border-color:var(--accent-cyan);color:var(--bg-primary);box-shadow:var(--shadow-glow-cyan);}
.step-circle.done{background:rgba(16,185,129,0.15);border-color:var(--accent-green);color:var(--accent-green);}
.step-label{font-size:var(--text-xs);color:var(--text-muted);margin-top:4px;text-align:center;}
.step-connector{flex:1;height:2px;background:rgba(148,163,184,0.1);margin:0 var(--space-2);}
.step-connector.done{background:var(--accent-green);}
.step-wrapper{display:flex;flex-direction:column;align-items:center;}
/* Form Panels */
.form-panel{display:none;animation:fadeIn 0.3s ease;}
.form-panel.active{display:block;}
/* Section Builder */
.trip-section-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-4);position:relative;transition:all var(--transition-base);}
.trip-section-card:hover{border-color:rgba(0,212,255,0.2);}
.section-remove{position:absolute;top:var(--space-4);right:var(--space-4);width:28px;height:28px;border-radius:50%;background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:var(--accent-red);display:flex;align-items:center;justify-content:center;cursor:pointer;transition:all var(--transition-fast);}
.section-remove:hover{background:rgba(239,68,68,0.2);}
/* AI Suggestions */
.suggestion-chip{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);background:rgba(0,212,255,0.08);border:1px solid rgba(0,212,255,0.15);border-radius:var(--radius-full);font-size:var(--text-sm);color:var(--accent-cyan);cursor:pointer;transition:all var(--transition-fast);}
.suggestion-chip:hover{background:rgba(0,212,255,0.15);transform:translateY(-1px);}
/* Travel Type */
.type-grid{display:grid;grid-template-columns:repeat(3,1fr);gap:var(--space-3);}
.type-btn{padding:var(--space-4);background:var(--bg-elevated);border:2px solid rgba(148,163,184,0.1);border-radius:var(--radius-lg);text-align:center;cursor:pointer;transition:all var(--transition-base);}
.type-btn:hover,.type-btn.selected{border-color:var(--accent-cyan);background:rgba(0,212,255,0.05);}
.type-btn i{display:block;margin:0 auto var(--space-2);}
/* Mood Grid */
.mood-grid-sm{display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-3);}
.mood-btn-sm{padding:var(--space-3);background:var(--bg-elevated);border:2px solid rgba(148,163,184,0.1);border-radius:var(--radius-lg);text-align:center;cursor:pointer;transition:all var(--transition-base);}
.mood-btn-sm:hover,.mood-btn-sm.selected{border-color:var(--mood-color,var(--accent-cyan));box-shadow:0 0 12px var(--mood-color,var(--accent-cyan))40;}
/* Grid helpers */
.grid-2c{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);}
.grid-3c{display:grid;grid-template-columns:1fr 1fr 1fr;gap:var(--space-4);}
/* Textarea */
textarea.input-field{resize:vertical;min-height:100px;}
/* Nav buttons */
.wizard-nav{display:flex;justify-content:space-between;margin-top:var(--space-8);}
@keyframes fadeIn{from{opacity:0;transform:translateY(8px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Page Header -->
  <div style="margin-bottom:var(--space-8);">
    <a href="<?= APP_URL ?>/pages/my-trips.php" style="display:inline-flex;align-items:center;gap:var(--space-2);color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-4);">
      <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back to My Trips
    </a>
    <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-2);">Plan a <span class="text-gradient">New Trip</span></h1>
    <p style="color:var(--text-secondary);">Build your perfect journey, step by step.</p>
  </div>

  <!-- Wizard Steps -->
  <div class="wizard-steps">
    <div class="step-wrapper">
      <div class="step-circle active" id="stepCircle1">1</div>
      <div class="step-label">Basics</div>
    </div>
    <div class="step-connector" id="conn1"></div>
    <div class="step-wrapper">
      <div class="step-circle" id="stepCircle2">2</div>
      <div class="step-label">Dates & Type</div>
    </div>
    <div class="step-connector" id="conn2"></div>
    <div class="step-wrapper">
      <div class="step-circle" id="stepCircle3">3</div>
      <div class="step-label">Mood</div>
    </div>
    <div class="step-connector" id="conn3"></div>
    <div class="step-wrapper">
      <div class="step-circle" id="stepCircle4">4</div>
      <div class="step-label">Budget & AI</div>
    </div>
  </div>

  <form id="createTripForm" method="POST" action="<?= APP_URL ?>/api/trips.php" enctype="multipart/form-data">
    <input type="hidden" name="action" value="create">
    <?= csrfField() ?>
    <input type="hidden" name="travel_type" id="hiddenTravelType" value="solo">
    <input type="hidden" name="mood" id="hiddenMood" value="adventure">

    <!-- STEP 1: Basics -->
    <div class="form-panel active" id="panel1">
      <div class="glass-card-static">
        <h3 style="margin-bottom:var(--space-6);">Trip Basics</h3>
        <div class="input-group">
          <label>Trip Name *</label>
          <div style="position:relative;">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);"><i data-lucide="map-pin" style="width:16px;height:16px;"></i></span>
            <input type="text" name="name" class="input-field" style="padding-left:44px;" placeholder="e.g. Tokyo Adventure 2026" required>
          </div>
        </div>
        <div class="input-group">
          <label>Select Destination(s) *</label>
          <div style="position:relative;">
            <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);"><i data-lucide="search" style="width:16px;height:16px;"></i></span>
            <input type="text" name="destination" id="destinationInput" class="input-field" style="padding-left:44px;" placeholder="Search city or country..." required>
          </div>
        </div>
        <!-- AI Suggestions Panel -->
        <div id="aiSuggestionsPanel" style="margin-top:var(--space-4);">
          <p style="font-size:var(--text-sm);color:var(--text-muted);margin-bottom:var(--space-3);display:flex;align-items:center;gap:var(--space-2);">
            <i data-lucide="sparkles" style="width:14px;height:14px;color:var(--accent-purple);"></i> AI Suggestions
          </p>
          <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);" id="suggestionChips">
            <span class="suggestion-chip" onclick="pickSuggestion(this)">🗼 Tokyo, Japan</span>
            <span class="suggestion-chip" onclick="pickSuggestion(this)">🗽 New York, USA</span>
            <span class="suggestion-chip" onclick="pickSuggestion(this)">🏝 Bali, Indonesia</span>
            <span class="suggestion-chip" onclick="pickSuggestion(this)">🗺 Paris, France</span>
            <span class="suggestion-chip" onclick="pickSuggestion(this)">🏔 Santorini, Greece</span>
            <span class="suggestion-chip" onclick="pickSuggestion(this)">✨ Dubai, UAE</span>
          </div>
        </div>
        <div class="input-group" style="margin-top:var(--space-5);">
          <label>Trip Description</label>
          <textarea name="description" class="input-field" placeholder="What's the vibe? Who's coming? Any special occasions?"></textarea>
        </div>
        <div class="input-group">
          <label>Cover Image</label>
          <div style="border:2px dashed rgba(148,163,184,0.15);border-radius:var(--radius-lg);padding:var(--space-8);text-align:center;cursor:pointer;transition:all var(--transition-base);" id="coverDropZone" onclick="document.getElementById('coverInput').click()">
            <i data-lucide="image" style="width:32px;height:32px;color:var(--text-muted);margin:0 auto var(--space-3);"></i>
            <p style="color:var(--text-muted);font-size:var(--text-sm);">Click to upload or drag & drop</p>
            <p style="color:var(--text-tertiary);font-size:var(--text-xs);margin-top:var(--space-1);">PNG, JPG, WEBP up to 5MB</p>
          </div>
          <input type="file" id="coverInput" name="cover_image" accept="image/*" style="display:none;" onchange="previewCover(this)">
          <img id="coverPreview" style="display:none;width:100%;height:180px;object-fit:cover;border-radius:var(--radius-lg);margin-top:var(--space-3);">
        </div>
        <div class="wizard-nav">
          <span></span>
          <button type="button" class="btn btn-primary" onclick="goStep(2)">Next: Dates & Type <i data-lucide="arrow-right" style="width:16px;height:16px;"></i></button>
        </div>
      </div>
    </div>

    <!-- STEP 2: Dates & Type -->
    <div class="form-panel" id="panel2">
      <div class="glass-card-static">
        <h3 style="margin-bottom:var(--space-6);">Dates & Travel Type</h3>
        <div class="grid-2c">
          <div class="input-group">
            <label>Start Date *</label>
            <input type="date" name="start_date" class="input-field" required>
          </div>
          <div class="input-group">
            <label>End Date *</label>
            <input type="date" name="end_date" class="input-field" required>
          </div>
        </div>
        <div class="input-group" style="margin-top:var(--space-4);">
          <label style="margin-bottom:var(--space-4);">Travel Type</label>
          <div class="type-grid">
            <div class="type-btn selected" data-type="solo" onclick="selectType(this)">
              <i data-lucide="user" style="width:24px;height:24px;color:var(--accent-cyan);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Solo</p>
            </div>
            <div class="type-btn" data-type="couple" onclick="selectType(this)">
              <i data-lucide="heart" style="width:24px;height:24px;color:var(--accent-orange);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Couple</p>
            </div>
            <div class="type-btn" data-type="family" onclick="selectType(this)">
              <i data-lucide="users" style="width:24px;height:24px;color:var(--accent-green);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Family</p>
            </div>
            <div class="type-btn" data-type="friends" onclick="selectType(this)">
              <i data-lucide="smile" style="width:24px;height:24px;color:var(--accent-purple);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Friends</p>
            </div>
            <div class="type-btn" data-type="business" onclick="selectType(this)">
              <i data-lucide="briefcase" style="width:24px;height:24px;color:var(--accent-blue);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Business</p>
            </div>
            <div class="type-btn" data-type="group" onclick="selectType(this)">
              <i data-lucide="users-2" style="width:24px;height:24px;color:var(--accent-gold);"></i>
              <p style="font-weight:var(--font-semibold);margin-top:var(--space-2);">Group</p>
            </div>
          </div>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-secondary" onclick="goStep(1)"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back</button>
          <button type="button" class="btn btn-primary" onclick="goStep(3)">Next: Mood <i data-lucide="arrow-right" style="width:16px;height:16px;"></i></button>
        </div>
      </div>
    </div>

    <!-- STEP 3: Mood -->
    <div class="form-panel" id="panel3">
      <div class="glass-card-static">
        <h3 style="margin-bottom:var(--space-2);">What's Your Mood?</h3>
        <p style="color:var(--text-secondary);margin-bottom:var(--space-6);">The entire app adapts to match how you want to feel on this trip.</p>
        <div class="mood-grid-sm">
          <?php
          $moods = [
            ['key'=>'adventure','label'=>'Adventure','icon'=>'zap','color'=>'#FF6B35'],
            ['key'=>'romantic','label'=>'Romantic','icon'=>'heart','color'=>'#EC4899'],
            ['key'=>'healing','label'=>'Healing','icon'=>'leaf','color'=>'#10B981'],
            ['key'=>'luxury','label'=>'Luxury','icon'=>'sparkles','color'=>'#F59E0B'],
            ['key'=>'party','label'=>'Party','icon'=>'music','color'=>'#8B5CF6'],
            ['key'=>'spiritual','label'=>'Spiritual','icon'=>'flower-2','color'=>'#6366F1'],
            ['key'=>'productivity','label'=>'Productivity','icon'=>'briefcase','color'=>'#3B82F6'],
            ['key'=>'solo','label'=>'Solo','icon'=>'globe','color'=>'#00D4FF'],
          ];
          foreach($moods as $m): ?>
          <div class="mood-btn-sm <?= $m['key']==='adventure'?'selected':'' ?>" data-mood="<?= $m['key'] ?>" style="--mood-color:<?= $m['color'] ?>;" onclick="selectMood(this)">
            <i data-lucide="<?= $m['icon'] ?>" style="width:24px;height:24px;color:<?= $m['color'] ?>;margin:0 auto var(--space-2);display:block;"></i>
            <p style="font-size:var(--text-sm);font-weight:var(--font-semibold);"><?= $m['label'] ?></p>
          </div>
          <?php endforeach; ?>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-secondary" onclick="goStep(2)"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back</button>
          <button type="button" class="btn btn-primary" onclick="goStep(4)">Next: Budget <i data-lucide="arrow-right" style="width:16px;height:16px;"></i></button>
        </div>
      </div>
    </div>

    <!-- STEP 4: Budget & AI -->
    <div class="form-panel" id="panel4">
      <div class="glass-card-static">
        <h3 style="margin-bottom:var(--space-6);">Budget & AI Generation</h3>
        <div class="grid-3c">
          <div class="input-group">
            <label>Total Budget</label>
            <div style="position:relative;">
              <span style="position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-weight:bold;">$</span>
              <input type="number" name="budget" class="input-field" style="padding-left:32px;" placeholder="5000">
            </div>
          </div>
          <div class="input-group">
            <label>Currency</label>
            <select name="currency" class="input-field">
              <option value="USD">USD — US Dollar</option>
              <option value="EUR">EUR — Euro</option>
              <option value="GBP">GBP — British Pound</option>
              <option value="INR">INR — Indian Rupee</option>
              <option value="JPY">JPY — Japanese Yen</option>
            </select>
          </div>
          <div class="input-group">
            <label>Budget Level</label>
            <select name="budget_level" class="input-field">
              <option value="budget">Budget Traveler</option>
              <option value="mid" selected>Mid-Range</option>
              <option value="luxury">Luxury</option>
            </select>
          </div>
        </div>
        <!-- AI Generation Toggle -->
        <div style="background:linear-gradient(135deg,rgba(168,85,247,0.05),rgba(0,212,255,0.05));border:1px solid rgba(168,85,247,0.2);border-radius:var(--radius-xl);padding:var(--space-6);margin-top:var(--space-4);">
          <div style="display:flex;align-items:center;justify-content:space-between;">
            <div>
              <div style="display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-2);">
                <i data-lucide="sparkles" style="width:20px;height:20px;color:var(--accent-purple);"></i>
                <h5 style="margin:0;">AI Trip Generation</h5>
              </div>
              <p style="color:var(--text-secondary);font-size:var(--text-sm);">Let AI auto-generate your itinerary, activities, and daily schedule based on your mood and budget.</p>
            </div>
            <label style="position:relative;display:inline-block;width:52px;height:28px;flex-shrink:0;">
              <input type="checkbox" name="ai_generate" id="aiToggle" style="opacity:0;width:0;height:0;" onchange="toggleAI(this)">
              <span id="aiSlider" style="position:absolute;cursor:pointer;inset:0;background:var(--bg-elevated);border-radius:28px;transition:0.3s;border:var(--border-light);"></span>
            </label>
          </div>
          <div id="aiOptions" style="display:none;margin-top:var(--space-4);border-top:1px solid rgba(148,163,184,0.1);padding-top:var(--space-4);">
            <p style="font-size:var(--text-sm);color:var(--text-muted);margin-bottom:var(--space-3);">AI will suggest activities for:</p>
            <div style="display:flex;flex-wrap:wrap;gap:var(--space-2);">
              <?php $activities = ['Sightseeing','Local Food','Adventure Sports','Cultural Sites','Nightlife','Shopping','Nature','Wellness']; foreach($activities as $a): ?>
              <label style="display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-3);background:var(--bg-elevated);border-radius:var(--radius-full);font-size:var(--text-sm);cursor:pointer;">
                <input type="checkbox" name="activity_prefs[]" value="<?= strtolower($a) ?>" style="accent-color:var(--accent-cyan);"> <?= $a ?>
              </label>
              <?php endforeach; ?>
            </div>
          </div>
        </div>
        <div class="wizard-nav">
          <button type="button" class="btn btn-secondary" onclick="goStep(3)"><i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back</button>
          <button type="submit" class="btn btn-primary btn-lg">
            <i data-lucide="check-circle" style="width:18px;height:18px;"></i> Create Trip
          </button>
        </div>
      </div>
    </div>
  </form>

</main>
</div>

<script>
let currentStep = 1;
const totalSteps = 4;

function goStep(n) {
  document.getElementById('panel' + currentStep).classList.remove('active');
  document.getElementById('stepCircle' + currentStep).classList.remove('active');
  document.getElementById('stepCircle' + currentStep).classList.add('done');
  if (n < currentStep) {
    document.getElementById('stepCircle' + currentStep).classList.remove('done');
    document.getElementById('stepCircle' + n).classList.remove('done');
  }
  currentStep = n;
  document.getElementById('panel' + currentStep).classList.add('active');
  document.getElementById('stepCircle' + currentStep).classList.remove('done');
  document.getElementById('stepCircle' + currentStep).classList.add('active');
  // connectors
  for(let i=1;i<totalSteps;i++){
    const c = document.getElementById('conn'+i);
    if(c) c.classList.toggle('done', i < n);
  }
  window.scrollTo({top:0,behavior:'smooth'});
}

function pickSuggestion(el) {
  document.getElementById('destinationInput').value = el.textContent.trim().replace(/^\S+\s/,'');
}

function selectType(el) {
  document.querySelectorAll('.type-btn').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('hiddenTravelType').value = el.dataset.type;
}

function selectMood(el) {
  document.querySelectorAll('.mood-btn-sm').forEach(b => b.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('hiddenMood').value = el.dataset.mood;
}

function toggleAI(cb) {
  const slider = document.getElementById('aiSlider');
  const opts = document.getElementById('aiOptions');
  if(cb.checked) { slider.style.background='var(--gradient-cyan)'; opts.style.display='block'; }
  else { slider.style.background='var(--bg-elevated)'; opts.style.display='none'; }
}

function previewCover(input) {
  if(input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const img = document.getElementById('coverPreview');
      img.src = e.target.result;
      img.style.display = 'block';
      document.getElementById('coverDropZone').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

lucide.createIcons();
</script>
</body>
</html>
