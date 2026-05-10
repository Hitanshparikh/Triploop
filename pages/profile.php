<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$user = currentUser();
$flash = getFlash();

// Demo data for profile page
$preplannedTrips = [
    ['id'=>3,'name'=>'Paris Romantic Getaway','destination'=>'France','start_date'=>'2026-07-14','mood'=>'romantic','mood_color'=>'#EC4899'],
    ['id'=>4,'name'=>'Dubai Luxury Experience','destination'=>'UAE','start_date'=>'2026-08-05','mood'=>'luxury','mood_color'=>'#F59E0B'],
];
$previousTrips = [
    ['id'=>6,'name'=>'New York City Break','destination'=>'USA','start_date'=>'2026-02-10','end_date'=>'2026-02-17','mood'=>'productivity','mood_color'=>'#3B82F6'],
    ['id'=>7,'name'=>'Barcelona Food Tour','destination'=>'Spain','start_date'=>'2026-01-20','end_date'=>'2026-01-28','mood'=>'adventure','mood_color'=>'#FF6B35'],
];
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>My Profile — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Profile Layout */
.profile-layout{display:grid;grid-template-columns:320px 1fr;gap:var(--space-8);align-items:start;}
/* Avatar card */
.avatar-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-2xl);padding:var(--space-8);text-align:center;position:sticky;top:var(--space-8);}
.avatar-wrapper{position:relative;width:120px;height:120px;margin:0 auto var(--space-5);}
.avatar-img{width:120px;height:120px;border-radius:50%;object-fit:cover;border:3px solid rgba(0,212,255,0.3);box-shadow:0 0 30px rgba(0,212,255,0.15);}
.avatar-placeholder{width:120px;height:120px;border-radius:50%;background:var(--gradient-cyan);display:flex;align-items:center;justify-content:center;font-size:var(--text-4xl);font-weight:var(--font-bold);color:var(--bg-primary);border:3px solid rgba(0,212,255,0.3);box-shadow:0 0 30px rgba(0,212,255,0.15);}
.avatar-edit-btn{position:absolute;bottom:4px;right:4px;width:32px;height:32px;border-radius:50%;background:var(--gradient-cyan);border:none;color:var(--bg-primary);cursor:pointer;display:flex;align-items:center;justify-content:center;box-shadow:var(--shadow-md);}
.profile-stats{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-3);margin-top:var(--space-6);}
.profile-stat{background:var(--bg-elevated);border-radius:var(--radius-lg);padding:var(--space-3);text-align:center;}
.profile-stat-val{font-size:var(--text-xl);font-weight:var(--font-bold);}
.profile-stat-lbl{font-size:var(--text-xs);color:var(--text-muted);margin-top:2px;}
/* Right panel tabs */
.profile-tabs{display:flex;gap:0;background:var(--bg-elevated);border-radius:var(--radius-xl);padding:4px;margin-bottom:var(--space-6);width:fit-content;}
.profile-tab{padding:var(--space-2) var(--space-5);border-radius:calc(var(--radius-xl) - 4px);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;transition:all var(--transition-base);color:var(--text-muted);border:none;background:transparent;}
.profile-tab.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-md);}
/* Sections */
.profile-section{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);padding:var(--space-6);margin-bottom:var(--space-5);}
.section-title{font-size:var(--text-lg);font-weight:var(--font-semibold);margin-bottom:var(--space-5);display:flex;align-items:center;gap:var(--space-3);}
.section-title i{width:20px;height:20px;color:var(--accent-cyan);}
/* Form grid */
.form-grid-2{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);}
.input-group{margin-bottom:var(--space-4);}
.input-group label{display:block;font-size:var(--text-sm);font-weight:var(--font-medium);color:var(--text-secondary);margin-bottom:var(--space-2);}
/* Trip rows */
.trip-row{display:flex;align-items:center;gap:var(--space-4);padding:var(--space-4);background:var(--bg-elevated);border-radius:var(--radius-lg);margin-bottom:var(--space-3);transition:all var(--transition-fast);text-decoration:none;color:inherit;}
.trip-row:hover{background:rgba(0,212,255,0.05);border-left:3px solid var(--accent-cyan);padding-left:calc(var(--space-4) - 3px);}
.trip-row-icon{width:40px;height:40px;border-radius:50%;display:flex;align-items:center;justify-content:center;flex-shrink:0;}
/* Preference chips */
.pref-chip{display:inline-flex;align-items:center;gap:var(--space-2);padding:var(--space-2) var(--space-4);border-radius:var(--radius-full);font-size:var(--text-sm);border:1px solid rgba(148,163,184,0.15);background:var(--bg-elevated);color:var(--text-secondary);cursor:pointer;transition:all var(--transition-fast);margin:4px;}
.pref-chip.active{background:rgba(0,212,255,0.1);border-color:var(--accent-cyan);color:var(--accent-cyan);}
/* Password strength */
.pw-strength{height:4px;border-radius:var(--radius-full);background:var(--bg-elevated);margin-top:6px;overflow:hidden;}
.pw-fill{height:100%;border-radius:var(--radius-full);transition:width 0.3s,background 0.3s;}
/* Flash */
.flash-msg{padding:var(--space-3) var(--space-4);border-radius:var(--radius-lg);font-size:var(--text-sm);margin-bottom:var(--space-5);}
.flash-success{background:rgba(16,185,129,0.15);color:var(--accent-green);border:1px solid rgba(16,185,129,0.2);}
.flash-error{background:rgba(239,68,68,0.15);color:var(--accent-red);border:1px solid rgba(239,68,68,0.2);}
/* Danger zone */
.danger-zone{border:1px solid rgba(239,68,68,0.2);border-radius:var(--radius-xl);padding:var(--space-5);}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Header -->
  <div style="margin-bottom:var(--space-8);">
    <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-2);">My <span class="text-gradient">Profile</span></h1>
    <p style="color:var(--text-secondary);">Manage your account, preferences, and travel history.</p>
  </div>

  <?php if($flash): ?>
  <div class="flash-msg flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
  <?php endif; ?>

  <div class="profile-layout">

    <!-- LEFT: Avatar Card -->
    <div>
      <div class="avatar-card">
        <div class="avatar-wrapper">
          <?php if(!empty($user['avatar'])): ?>
            <img src="<?= UPLOADS_URL ?>/avatars/<?= e($user['avatar']) ?>" alt="Avatar" class="avatar-img" id="avatarImg">
          <?php else: ?>
            <div class="avatar-placeholder" id="avatarPlaceholder"><?= strtoupper(substr($user['name'] ?? 'U', 0, 1)) ?></div>
          <?php endif; ?>
          <button class="avatar-edit-btn" onclick="document.getElementById('avatarInput').click()" title="Change photo">
            <i data-lucide="camera" style="width:14px;height:14px;"></i>
          </button>
        </div>
        <form method="POST" action="<?= APP_URL ?>/api/profile.php" enctype="multipart/form-data" id="avatarForm">
          <input type="hidden" name="action" value="upload_avatar">
          <?= csrfField() ?>
          <input type="file" id="avatarInput" name="avatar" accept="image/*" style="display:none;" onchange="uploadAvatar()">
        </form>

        <h4 style="margin-bottom:4px;"><?= e($user['name'] ?? 'Traveler') ?></h4>
        <p style="color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-2);"><?= e($user['email'] ?? '') ?></p>
        <span class="badge badge-cyan"><?= ($user['role'] ?? 'user') === 'admin' ? 'Admin' : 'Explorer' ?></span>

        <div class="profile-stats">
          <div class="profile-stat">
            <div class="profile-stat-val text-gradient"><?= count($preplannedTrips) + count($previousTrips) ?></div>
            <div class="profile-stat-lbl">Total Trips</div>
          </div>
          <div class="profile-stat">
            <div class="profile-stat-val" style="color:var(--accent-purple);"><?= count($preplannedTrips) ?></div>
            <div class="profile-stat-lbl">Planned</div>
          </div>
          <div class="profile-stat">
            <div class="profile-stat-val" style="color:var(--accent-green);"><?= count($previousTrips) ?></div>
            <div class="profile-stat-lbl">Completed</div>
          </div>
          <div class="profile-stat">
            <div class="profile-stat-val" style="color:var(--accent-orange);">4</div>
            <div class="profile-stat-lbl">Countries</div>
          </div>
        </div>

        <div style="margin-top:var(--space-6);">
          <a href="<?= APP_URL ?>/api/auth.php?action=logout" class="btn btn-secondary w-full" style="font-size:var(--text-sm);">
            <i data-lucide="log-out" style="width:14px;height:14px;"></i> Sign Out
          </a>
        </div>
      </div>
    </div>

    <!-- RIGHT: Tabs -->
    <div>
      <div class="profile-tabs">
        <button class="profile-tab active" id="tab-details" onclick="switchTab('details')">Account Details</button>
        <button class="profile-tab" id="tab-trips" onclick="switchTab('trips')">My Trips</button>
        <button class="profile-tab" id="tab-prefs" onclick="switchTab('prefs')">Preferences</button>
        <button class="profile-tab" id="tab-security" onclick="switchTab('security')">Security</button>
      </div>

      <!-- Tab: Account Details -->
      <div id="panel-details">
        <form method="POST" action="<?= APP_URL ?>/api/profile.php">
          <input type="hidden" name="action" value="update_profile">
          <?= csrfField() ?>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="user"></i> Personal Information</div>
            <div class="form-grid-2">
              <div class="input-group">
                <label>First Name</label>
                <?php $nameParts = explode(' ', $user['name'] ?? '', 2); ?>
                <input type="text" name="first_name" class="input-field" value="<?= e($nameParts[0] ?? '') ?>" placeholder="First name">
              </div>
              <div class="input-group">
                <label>Last Name</label>
                <input type="text" name="last_name" class="input-field" value="<?= e($nameParts[1] ?? '') ?>" placeholder="Last name">
              </div>
            </div>
            <div class="input-group">
              <label>Email Address</label>
              <input type="email" name="email" class="input-field" value="<?= e($user['email'] ?? '') ?>">
            </div>
            <div class="form-grid-2">
              <div class="input-group">
                <label>Phone Number</label>
                <input type="tel" name="phone" class="input-field" value="<?= e($user['phone'] ?? '') ?>" placeholder="+1 234 567 8900">
              </div>
              <div class="input-group">
                <label>Date of Birth</label>
                <input type="date" name="dob" class="input-field" value="<?= e($user['dob'] ?? '') ?>">
              </div>
            </div>
          </div>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="map-pin"></i> Location</div>
            <div class="form-grid-2">
              <div class="input-group">
                <label>City</label>
                <input type="text" name="city" class="input-field" value="<?= e($user['city'] ?? '') ?>" placeholder="Your city">
              </div>
              <div class="input-group">
                <label>Country</label>
                <input type="text" name="country" class="input-field" value="<?= e($user['country'] ?? '') ?>" placeholder="Your country">
              </div>
            </div>
          </div>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="info"></i> Additional Information</div>
            <div class="input-group" style="margin-bottom:0;">
              <label>Bio</label>
              <textarea name="bio" class="input-field" style="min-height:90px;resize:vertical;" placeholder="Tell other travelers about yourself..."><?= e($user['bio'] ?? '') ?></textarea>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-lg">
            <i data-lucide="save" style="width:16px;height:16px;"></i> Save Changes
          </button>
        </form>
      </div>

      <!-- Tab: My Trips -->
      <div id="panel-trips" style="display:none;">
        <!-- Preplanned -->
        <div class="profile-section">
          <div class="section-title"><i data-lucide="calendar-clock"></i> Preplanned Trips</div>
          <?php if(empty($preplannedTrips)): ?>
            <p style="color:var(--text-muted);text-align:center;padding:var(--space-6);">No upcoming trips planned.</p>
          <?php else: ?>
            <?php foreach($preplannedTrips as $t): ?>
            <a href="<?= APP_URL ?>/pages/itinerary-view.php?trip_id=<?= $t['id'] ?>" class="trip-row">
              <div class="trip-row-icon" style="background:<?= $t['mood_color'] ?>20;">
                <i data-lucide="map" style="width:18px;height:18px;color:<?= $t['mood_color'] ?>;"></i>
              </div>
              <div style="flex:1;">
                <p style="font-weight:var(--font-semibold);margin:0;"><?= e($t['name']) ?></p>
                <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;"><?= e($t['destination']) ?> · From <?= date('M j, Y', strtotime($t['start_date'])) ?></p>
              </div>
              <span class="badge" style="background:<?= $t['mood_color'] ?>20;color:<?= $t['mood_color'] ?>;"><?= ucfirst($t['mood']) ?></span>
              <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-muted);"></i>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
          <div style="margin-top:var(--space-3);">
            <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-secondary" style="font-size:var(--text-sm);">
              <i data-lucide="plus" style="width:14px;height:14px;"></i> Plan New Trip
            </a>
          </div>
        </div>
        <!-- Previous -->
        <div class="profile-section">
          <div class="section-title"><i data-lucide="check-circle-2"></i> Previous Trips</div>
          <?php if(empty($previousTrips)): ?>
            <p style="color:var(--text-muted);text-align:center;padding:var(--space-6);">No completed trips yet. Start exploring!</p>
          <?php else: ?>
            <?php foreach($previousTrips as $t): ?>
            <a href="<?= APP_URL ?>/pages/itinerary-view.php?trip_id=<?= $t['id'] ?>" class="trip-row">
              <div class="trip-row-icon" style="background:rgba(16,185,129,0.1);">
                <i data-lucide="check-circle" style="width:18px;height:18px;color:var(--accent-green);"></i>
              </div>
              <div style="flex:1;">
                <p style="font-weight:var(--font-semibold);margin:0;"><?= e($t['name']) ?></p>
                <p style="font-size:var(--text-xs);color:var(--text-muted);margin:0;"><?= e($t['destination']) ?> · <?= date('M j', strtotime($t['start_date'])) ?> – <?= date('M j, Y', strtotime($t['end_date'])) ?></p>
              </div>
              <i data-lucide="chevron-right" style="width:16px;height:16px;color:var(--text-muted);"></i>
            </a>
            <?php endforeach; ?>
          <?php endif; ?>
        </div>
      </div>

      <!-- Tab: Preferences -->
      <div id="panel-prefs" style="display:none;">
        <form method="POST" action="<?= APP_URL ?>/api/profile.php">
          <input type="hidden" name="action" value="update_preferences">
          <?= csrfField() ?>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="palette"></i> Mood & Style</div>
            <label style="font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--space-3);display:block;">Default Mood</label>
            <div style="display:flex;flex-wrap:wrap;gap:4px;">
              <?php $moods=[['key'=>'adventure','label'=>'Adventure','color'=>'#FF6B35'],['key'=>'romantic','label'=>'Romantic','color'=>'#EC4899'],['key'=>'healing','label'=>'Healing','color'=>'#10B981'],['key'=>'luxury','label'=>'Luxury','color'=>'#F59E0B'],['key'=>'party','label'=>'Party','color'=>'#8B5CF6'],['key'=>'spiritual','label'=>'Spiritual','color'=>'#6366F1'],['key'=>'productivity','label'=>'Productivity','color'=>'#3B82F6'],['key'=>'solo','label'=>'Solo','color'=>'#00D4FF']];
              foreach($moods as $m): ?>
              <span class="pref-chip <?= $m['key']==='adventure'?'active':'' ?>" onclick="togglePref(this)" data-key="mood:<?= $m['key'] ?>" style="--chip-color:<?= $m['color'] ?>;"><?= $m['label'] ?></span>
              <?php endforeach; ?>
            </div>
          </div>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="dollar-sign"></i> Currency & Budget</div>
            <div class="form-grid-2">
              <div class="input-group">
                <label>Preferred Currency</label>
                <select name="currency" class="input-field">
                  <option value="USD">USD — US Dollar</option>
                  <option value="EUR">EUR — Euro</option>
                  <option value="GBP">GBP — Pound</option>
                  <option value="INR">INR — Indian Rupee</option>
                  <option value="JPY">JPY — Yen</option>
                </select>
              </div>
              <div class="input-group">
                <label>Budget Level</label>
                <select name="budget_level" class="input-field">
                  <option value="budget">Budget</option>
                  <option value="mid" selected>Mid-Range</option>
                  <option value="luxury">Luxury</option>
                </select>
              </div>
            </div>
          </div>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="sun"></i> Theme</div>
            <div style="display:flex;gap:var(--space-3);">
              <div style="flex:1;padding:var(--space-4);background:var(--bg-elevated);border:2px solid var(--accent-cyan);border-radius:var(--radius-lg);text-align:center;cursor:pointer;">
                <i data-lucide="moon" style="width:20px;height:20px;color:var(--accent-cyan);display:block;margin:0 auto var(--space-2);"></i>
                <p style="font-size:var(--text-sm);font-weight:var(--font-semibold);">Dark</p>
                <span class="badge badge-cyan" style="margin-top:4px;">Active</span>
              </div>
              <div style="flex:1;padding:var(--space-4);background:var(--bg-elevated);border:var(--border-subtle);border-radius:var(--radius-lg);text-align:center;cursor:pointer;opacity:0.5;">
                <i data-lucide="sun" style="width:20px;height:20px;display:block;margin:0 auto var(--space-2);"></i>
                <p style="font-size:var(--text-sm);font-weight:var(--font-semibold);">Light</p>
                <span style="font-size:10px;color:var(--text-muted);">Coming soon</span>
              </div>
            </div>
          </div>
          <button type="submit" class="btn btn-primary btn-lg">
            <i data-lucide="save" style="width:16px;height:16px;"></i> Save Preferences
          </button>
        </form>
      </div>

      <!-- Tab: Security -->
      <div id="panel-security" style="display:none;">
        <form method="POST" action="<?= APP_URL ?>/api/profile.php">
          <input type="hidden" name="action" value="change_password">
          <?= csrfField() ?>
          <div class="profile-section">
            <div class="section-title"><i data-lucide="lock"></i> Change Password</div>
            <div class="input-group">
              <label>Current Password</label>
              <input type="password" name="current_password" class="input-field" placeholder="••••••••">
            </div>
            <div class="input-group">
              <label>New Password</label>
              <input type="password" name="new_password" id="newPw" class="input-field" placeholder="Min. 8 characters" oninput="checkPwStrength(this.value)">
              <div class="pw-strength"><div id="pwFill" class="pw-fill" style="width:0;background:var(--accent-red);"></div></div>
              <p id="pwLabel" style="font-size:var(--text-xs);color:var(--text-muted);margin-top:4px;"></p>
            </div>
            <div class="input-group">
              <label>Confirm New Password</label>
              <input type="password" name="confirm_password" class="input-field" placeholder="Repeat new password">
            </div>
            <button type="submit" class="btn btn-primary">
              <i data-lucide="shield-check" style="width:16px;height:16px;"></i> Update Password
            </button>
          </div>
        </form>
        <div class="danger-zone">
          <h5 style="color:var(--accent-red);margin-bottom:var(--space-2);">Danger Zone</h5>
          <p style="font-size:var(--text-sm);color:var(--text-secondary);margin-bottom:var(--space-4);">Permanently delete your account and all associated data. This cannot be undone.</p>
          <button class="btn" style="background:rgba(239,68,68,0.1);color:var(--accent-red);border:1px solid rgba(239,68,68,0.2);" onclick="confirmDelete()">
            <i data-lucide="trash-2" style="width:14px;height:14px;"></i> Delete Account
          </button>
        </div>
      </div>

    </div>
  </div>

</main>
</div>

<script>
function switchTab(name) {
  ['details','trips','prefs','security'].forEach(t => {
    document.getElementById('panel-' + t).style.display = t === name ? 'block' : 'none';
    document.getElementById('tab-' + t).classList.toggle('active', t === name);
  });
}

function uploadAvatar() {
  const input = document.getElementById('avatarInput');
  if(!input.files || !input.files[0]) return;
  const reader = new FileReader();
  reader.onload = e => {
    const ph = document.getElementById('avatarPlaceholder');
    let img = document.getElementById('avatarImg');
    if(!img) {
      img = document.createElement('img');
      img.id = 'avatarImg'; img.className = 'avatar-img';
      if(ph) ph.replaceWith(img); else document.querySelector('.avatar-wrapper').prepend(img);
    }
    img.src = e.target.result;
  };
  reader.readAsDataURL(input.files[0]);
  document.getElementById('avatarForm').submit();
}

function togglePref(el) { el.classList.toggle('active'); }

function checkPwStrength(pw) {
  let score = 0;
  if(pw.length >= 8) score++;
  if(/[A-Z]/.test(pw)) score++;
  if(/[0-9]/.test(pw)) score++;
  if(/[^A-Za-z0-9]/.test(pw)) score++;
  const fill = document.getElementById('pwFill');
  const label = document.getElementById('pwLabel');
  const configs = [
    {w:'0%',c:'var(--accent-red)',l:''},
    {w:'25%',c:'var(--accent-red)',l:'Weak'},
    {w:'50%',c:'var(--accent-orange)',l:'Fair'},
    {w:'75%',c:'var(--accent-gold)',l:'Good'},
    {w:'100%',c:'var(--accent-green)',l:'Strong'},
  ];
  fill.style.width = configs[score].w;
  fill.style.background = configs[score].c;
  label.textContent = configs[score].l;
  label.style.color = configs[score].c;
}

function confirmDelete() {
  if(confirm('Are you absolutely sure? This will permanently delete your account.')) {
    window.location.href = '<?= APP_URL ?>/api/profile.php?action=delete_account';
  }
}

lucide.createIcons();
</script>
</body>
</html>
