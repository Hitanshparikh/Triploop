<?php
require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('/pages/dashboard.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
<title>Create Account — JourneyOS AI</title>
<meta name="description" content="Join JourneyOS AI and start planning emotionally intelligent trips.">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--gradient-hero);position:relative;overflow:hidden;padding:var(--space-8) var(--space-4);}
.auth-bg-orb{position:absolute;border-radius:50%;filter:blur(120px);opacity:0.25;pointer-events:none;}
.auth-bg-orb-1{width:600px;height:600px;background:var(--accent-cyan);top:-200px;right:-200px;animation:float 10s ease-in-out infinite;}
.auth-bg-orb-2{width:500px;height:500px;background:var(--accent-purple);bottom:-150px;left:-150px;animation:float 8s ease-in-out infinite;animation-delay:3s;}
.auth-bg-orb-3{width:300px;height:300px;background:var(--accent-orange);top:50%;left:50%;animation:float 12s ease-in-out infinite;animation-delay:6s;opacity:0.1;}
.auth-container{position:relative;z-index:1;width:100%;max-width:540px;}
.auth-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur-heavy);-webkit-backdrop-filter:var(--glass-blur-heavy);border:var(--border-subtle);border-radius:var(--radius-2xl);padding:var(--space-10) var(--space-8);animation:scaleIn 0.5s var(--transition-spring);}
/* Logo */
.auth-logo{text-align:center;margin-bottom:var(--space-6);}
.logo{display:inline-flex;align-items:center;gap:var(--space-2);text-decoration:none;}
.logo-icon{font-size:1.5rem;background:var(--gradient-cyan);-webkit-background-clip:text;-webkit-text-fill-color:transparent;background-clip:text;}
.logo-text{font-family:var(--font-display);font-size:var(--text-xl);font-weight:var(--font-bold);color:var(--text-primary);}
.logo-badge{font-size:var(--text-xs);font-weight:var(--font-bold);background:var(--gradient-cyan);color:var(--bg-primary);padding:2px 8px;border-radius:var(--radius-full);}
/* Steps */
.step-indicator{display:flex;align-items:center;justify-content:center;gap:var(--space-2);margin-bottom:var(--space-8);}
.step-dot{width:8px;height:8px;border-radius:50%;background:rgba(148,163,184,0.2);transition:all var(--transition-base);}
.step-dot.active{width:24px;border-radius:4px;background:var(--gradient-cyan);}
.step-dot.done{background:var(--accent-green);}
/* Form */
.form-row{display:grid;grid-template-columns:1fr 1fr;gap:var(--space-4);}
.input-icon-wrapper{position:relative;}
.input-icon-wrapper .input-field{padding-left:44px;}
.input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);width:16px;height:16px;}
/* Avatar Upload */
.avatar-upload-zone{display:flex;flex-direction:column;align-items:center;gap:var(--space-3);padding:var(--space-6);border:2px dashed rgba(148,163,184,0.15);border-radius:var(--radius-xl);cursor:pointer;transition:all var(--transition-base);}
.avatar-upload-zone:hover{border-color:rgba(0,212,255,0.3);background:rgba(0,212,255,0.03);}
.avatar-preview{width:80px;height:80px;border-radius:50%;object-fit:cover;border:2px solid rgba(0,212,255,0.3);display:none;}
.avatar-placeholder-icon{width:64px;height:64px;border-radius:50%;background:var(--bg-elevated);display:flex;align-items:center;justify-content:center;}
/* Divider */
.auth-divider{display:flex;align-items:center;gap:var(--space-4);margin:var(--space-5) 0;color:var(--text-muted);font-size:var(--text-xs);}
.auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:rgba(148,163,184,0.1);}
/* OAuth */
.oauth-btn{width:100%;padding:var(--space-3) var(--space-4);background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);font-weight:var(--font-medium);display:flex;align-items:center;justify-content:center;gap:var(--space-3);transition:all var(--transition-base);cursor:pointer;}
.oauth-btn:hover{border-color:var(--accent-cyan);background:var(--bg-tertiary);}
/* Flash */
.flash-msg{padding:var(--space-3) var(--space-4);border-radius:var(--radius-lg);font-size:var(--text-sm);margin-bottom:var(--space-5);}
.flash-error{background:rgba(239,68,68,0.15);color:var(--accent-red);border:1px solid rgba(239,68,68,0.2);}
.flash-success{background:rgba(16,185,129,0.15);color:var(--accent-green);border:1px solid rgba(16,185,129,0.2);}
/* Password strength */
.pw-strength-bar{height:4px;border-radius:var(--radius-full);background:var(--bg-elevated);margin-top:6px;overflow:hidden;}
.pw-strength-fill{height:100%;border-radius:var(--radius-full);transition:width 0.3s,background 0.3s;width:0;}
/* Footer */
.auth-footer{text-align:center;margin-top:var(--space-6);font-size:var(--text-sm);color:var(--text-secondary);}
.auth-footer a{color:var(--accent-cyan);}
/* Panel transitions */
.form-panel{display:none;}
.form-panel.active{display:block;animation:fadeInUp 0.3s ease;}
@keyframes fadeInUp{from{opacity:0;transform:translateY(10px)}to{opacity:1;transform:translateY(0)}}
</style>
</head>
<body>
<div class="auth-page">
  <div class="auth-bg-orb auth-bg-orb-1"></div>
  <div class="auth-bg-orb auth-bg-orb-2"></div>
  <div class="auth-bg-orb auth-bg-orb-3"></div>

  <div class="auth-container">
    <div class="auth-card">

      <!-- Logo -->
      <div class="auth-logo">
        <a href="<?= APP_URL ?>" class="logo">
          <span class="logo-icon">✦</span>
          <span class="logo-text">JourneyOS</span>
          <span class="logo-badge">AI</span>
        </a>
      </div>

      <!-- Step Indicator -->
      <div class="step-indicator">
        <div class="step-dot active" id="dot1"></div>
        <div class="step-dot" id="dot2"></div>
        <div class="step-dot" id="dot3"></div>
      </div>

      <?php if ($flash): ?>
      <div class="flash-msg flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
      <?php endif; ?>

      <form method="POST" action="<?= APP_URL ?>/api/auth.php" enctype="multipart/form-data" id="signupForm">
        <input type="hidden" name="action" value="signup">
        <?= csrfField() ?>

        <!-- ===== STEP 1: Account ===== -->
        <div class="form-panel active" id="step1">
          <h2 style="text-align:center;font-size:var(--text-2xl);margin-bottom:var(--space-1);">Create Account</h2>
          <p style="text-align:center;color:var(--text-secondary);font-size:var(--text-sm);margin-bottom:var(--space-6);">Start your journey with JourneyOS AI</p>

          <!-- Google OAuth -->
          <button type="button" class="oauth-btn">
            <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
            Continue with Google
          </button>

          <div class="auth-divider">or sign up with email</div>

          <div class="form-row">
            <div class="input-group">
              <label for="first_name">First Name *</label>
              <div class="input-icon-wrapper">
                <i data-lucide="user" class="input-icon"></i>
                <input type="text" id="first_name" name="first_name" class="input-field" placeholder="John" required>
              </div>
            </div>
            <div class="input-group">
              <label for="last_name">Last Name *</label>
              <div class="input-icon-wrapper">
                <i data-lucide="user" class="input-icon"></i>
                <input type="text" id="last_name" name="last_name" class="input-field" placeholder="Doe" required>
              </div>
            </div>
          </div>
          <div class="input-group">
            <label for="email">Email Address *</label>
            <div class="input-icon-wrapper">
              <i data-lucide="mail" class="input-icon"></i>
              <input type="email" id="email" name="email" class="input-field" placeholder="you@example.com" required autocomplete="email">
            </div>
          </div>
          <div class="input-group">
            <label for="password">Password *</label>
            <div class="input-icon-wrapper">
              <i data-lucide="lock" class="input-icon"></i>
              <input type="password" id="password" name="password" class="input-field" placeholder="Min. 8 characters" required oninput="checkStrength(this.value)" autocomplete="new-password">
            </div>
            <div class="pw-strength-bar"><div class="pw-strength-fill" id="pwFill"></div></div>
            <p id="pwLabel" style="font-size:var(--text-xs);color:var(--text-muted);margin-top:4px;"></p>
          </div>
          <div class="input-group" style="margin-bottom:var(--space-6);">
            <label for="confirm_password">Confirm Password *</label>
            <div class="input-icon-wrapper">
              <i data-lucide="lock" class="input-icon"></i>
              <input type="password" id="confirm_password" name="confirm_password" class="input-field" placeholder="Repeat password" required autocomplete="new-password">
            </div>
          </div>
          <button type="button" class="btn btn-primary w-full btn-lg" onclick="goStep(2)">
            Continue <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
          </button>
        </div>

        <!-- ===== STEP 2: Personal Info ===== -->
        <div class="form-panel" id="step2">
          <h2 style="text-align:center;font-size:var(--text-2xl);margin-bottom:var(--space-1);">About You</h2>
          <p style="text-align:center;color:var(--text-secondary);font-size:var(--text-sm);margin-bottom:var(--space-6);">Help us personalise your travel experience</p>

          <!-- Photo Upload -->
          <div class="input-group">
            <label>Profile Photo</label>
            <div class="avatar-upload-zone" id="avatarZone" onclick="document.getElementById('photoInput').click()">
              <img id="avatarPreview" class="avatar-preview" alt="Preview">
              <div class="avatar-placeholder-icon" id="avatarIcon">
                <i data-lucide="camera" style="width:24px;height:24px;color:var(--text-muted);"></i>
              </div>
              <p style="font-size:var(--text-sm);color:var(--text-muted);">Click to upload photo</p>
              <p style="font-size:var(--text-xs);color:var(--text-tertiary);">PNG, JPG up to 5MB</p>
            </div>
            <input type="file" id="photoInput" name="photo" accept="image/*" style="display:none;" onchange="previewPhoto(this)">
          </div>

          <div class="input-group">
            <label for="phone">Phone Number</label>
            <div class="input-icon-wrapper">
              <i data-lucide="phone" class="input-icon"></i>
              <input type="tel" id="phone" name="phone" class="input-field" placeholder="+1 234 567 8900">
            </div>
          </div>

          <div class="form-row">
            <div class="input-group">
              <label for="city">City</label>
              <div class="input-icon-wrapper">
                <i data-lucide="map-pin" class="input-icon"></i>
                <input type="text" id="city" name="city" class="input-field" placeholder="Your city">
              </div>
            </div>
            <div class="input-group">
              <label for="country">Country</label>
              <div class="input-icon-wrapper">
                <i data-lucide="globe" class="input-icon"></i>
                <input type="text" id="country" name="country" class="input-field" placeholder="Your country">
              </div>
            </div>
          </div>

          <div class="input-group" style="margin-bottom:var(--space-6);">
            <label for="bio">Additional Information</label>
            <textarea id="bio" name="bio" class="input-field" style="resize:vertical;min-height:80px;" placeholder="Tell us about your travel style, interests, or anything else..."></textarea>
          </div>

          <div style="display:flex;gap:var(--space-3);">
            <button type="button" class="btn btn-secondary" onclick="goStep(1)">
              <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back
            </button>
            <button type="button" class="btn btn-primary" style="flex:1;" onclick="goStep(3)">
              Continue <i data-lucide="arrow-right" style="width:16px;height:16px;"></i>
            </button>
          </div>
        </div>

        <!-- ===== STEP 3: Preferences ===== -->
        <div class="form-panel" id="step3">
          <h2 style="text-align:center;font-size:var(--text-2xl);margin-bottom:var(--space-1);">Your Vibe</h2>
          <p style="text-align:center;color:var(--text-secondary);font-size:var(--text-sm);margin-bottom:var(--space-6);">Pick a mood — you can always change it later</p>

          <div style="display:grid;grid-template-columns:repeat(4,1fr);gap:var(--space-2);margin-bottom:var(--space-6);">
            <?php
            $moods = [
              ['key'=>'adventure','label'=>'Adventure','icon'=>'zap','color'=>'#FF6B35'],
              ['key'=>'romantic','label'=>'Romantic','icon'=>'heart','color'=>'#EC4899'],
              ['key'=>'healing','label'=>'Healing','icon'=>'leaf','color'=>'#10B981'],
              ['key'=>'luxury','label'=>'Luxury','icon'=>'sparkles','color'=>'#F59E0B'],
              ['key'=>'party','label'=>'Party','icon'=>'music','color'=>'#8B5CF6'],
              ['key'=>'spiritual','label'=>'Spiritual','icon'=>'flower-2','color'=>'#6366F1'],
              ['key'=>'productivity','label'=>'Work','icon'=>'briefcase','color'=>'#3B82F6'],
              ['key'=>'solo','label'=>'Solo','icon'=>'globe','color'=>'#00D4FF'],
            ];
            foreach($moods as $m): ?>
            <div class="mood-chip" data-mood="<?= $m['key'] ?>" style="--mc:<?= $m['color'] ?>;" onclick="pickMood(this)">
              <i data-lucide="<?= $m['icon'] ?>" style="width:20px;height:20px;color:<?= $m['color'] ?>;display:block;margin:0 auto var(--space-1);"></i>
              <p style="font-size:10px;font-weight:var(--font-semibold);"><?= $m['label'] ?></p>
            </div>
            <?php endforeach; ?>
          </div>

          <input type="hidden" name="default_mood" id="defaultMood" value="adventure">

          <!-- Terms -->
          <label style="display:flex;align-items:flex-start;gap:var(--space-3);font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer;margin-bottom:var(--space-6);">
            <input type="checkbox" name="terms" id="termsCheck" style="margin-top:2px;accent-color:var(--accent-cyan);flex-shrink:0;" required>
            <span>I agree to the <a href="#" style="color:var(--accent-cyan);">Terms of Service</a> and <a href="#" style="color:var(--accent-cyan);">Privacy Policy</a></span>
          </label>

          <div style="display:flex;gap:var(--space-3);">
            <button type="button" class="btn btn-secondary" onclick="goStep(2)">
              <i data-lucide="arrow-left" style="width:16px;height:16px;"></i> Back
            </button>
            <button type="submit" class="btn btn-primary btn-lg" style="flex:1;" id="submitBtn">
              <i data-lucide="rocket" style="width:16px;height:16px;"></i> Launch My Journey
            </button>
          </div>
        </div>

      </form>

      <div class="auth-footer">
        Already have an account? <a href="<?= APP_URL ?>/pages/login.php">Sign in</a>
      </div>
    </div>
  </div>
</div>

<style>
.mood-chip{padding:var(--space-3) var(--space-2);background:var(--bg-elevated);border:2px solid rgba(148,163,184,0.1);border-radius:var(--radius-lg);text-align:center;cursor:pointer;transition:all var(--transition-base);}
.mood-chip:hover,.mood-chip.selected{border-color:var(--mc);box-shadow:0 0 14px color-mix(in srgb,var(--mc) 40%,transparent);}
.mood-chip.selected{background:color-mix(in srgb,var(--mc) 10%,transparent);}
</style>

<script>
let currentStep = 1;

function goStep(n) {
  // Validate step 1 before proceeding
  if(n > 1 && currentStep === 1) {
    const pw = document.getElementById('password').value;
    const cpw = document.getElementById('confirm_password').value;
    const email = document.getElementById('email').value;
    const fn = document.getElementById('first_name').value;
    const ln = document.getElementById('last_name').value;
    if(!fn || !ln || !email || !pw) { alert('Please fill in all required fields.'); return; }
    if(pw.length < 8) { alert('Password must be at least 8 characters.'); return; }
    if(pw !== cpw) { alert('Passwords do not match.'); return; }
  }

  document.getElementById('step' + currentStep).classList.remove('active');
  document.getElementById('dot' + currentStep).classList.remove('active');
  if(n > currentStep) document.getElementById('dot' + currentStep).classList.add('done');
  else document.getElementById('dot' + (currentStep)).classList.remove('done');

  currentStep = n;
  document.getElementById('step' + currentStep).classList.add('active');
  document.getElementById('dot' + currentStep).classList.add('active');
  document.getElementById('dot' + currentStep).classList.remove('done');
  window.scrollTo({top:0,behavior:'smooth'});
}

function checkStrength(pw) {
  let score = 0;
  if(pw.length >= 8) score++;
  if(/[A-Z]/.test(pw)) score++;
  if(/[0-9]/.test(pw)) score++;
  if(/[^A-Za-z0-9]/.test(pw)) score++;
  const configs = [
    {w:'0%',c:'var(--accent-red)',l:''},
    {w:'25%',c:'var(--accent-red)',l:'Weak — add more characters'},
    {w:'50%',c:'var(--accent-orange)',l:'Fair — add uppercase or numbers'},
    {w:'75%',c:'var(--accent-gold)',l:'Good — almost there!'},
    {w:'100%',c:'var(--accent-green)',l:'Strong password ✓'},
  ];
  document.getElementById('pwFill').style.width = configs[score].w;
  document.getElementById('pwFill').style.background = configs[score].c;
  document.getElementById('pwLabel').textContent = configs[score].l;
  document.getElementById('pwLabel').style.color = configs[score].c;
}

function previewPhoto(input) {
  if(input.files && input.files[0]) {
    const reader = new FileReader();
    reader.onload = e => {
      const preview = document.getElementById('avatarPreview');
      preview.src = e.target.result;
      preview.style.display = 'block';
      document.getElementById('avatarIcon').style.display = 'none';
    };
    reader.readAsDataURL(input.files[0]);
  }
}

function pickMood(el) {
  document.querySelectorAll('.mood-chip').forEach(c => c.classList.remove('selected'));
  el.classList.add('selected');
  document.getElementById('defaultMood').value = el.dataset.mood;
}

// Pre-select adventure mood
document.addEventListener('DOMContentLoaded', () => {
  const adv = document.querySelector('[data-mood="adventure"]');
  if(adv) adv.classList.add('selected');
  lucide.createIcons();
});
</script>
</body>
</html>
