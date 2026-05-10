<?php require_once __DIR__ . '/../includes/functions.php';
if (isLoggedIn()) redirect('/pages/dashboard.php');
$flash = getFlash();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Sign In — JourneyOS AI</title>
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
    <style>
        .auth-page{min-height:100vh;display:flex;align-items:center;justify-content:center;background:var(--gradient-hero);position:relative;overflow:hidden;}
        .auth-bg-orb{position:absolute;border-radius:50%;filter:blur(100px);opacity:0.3;animation:float 8s ease-in-out infinite;}
        .auth-bg-orb-1{width:500px;height:500px;background:var(--accent-cyan);top:-150px;right:-150px;}
        .auth-bg-orb-2{width:400px;height:400px;background:var(--accent-purple);bottom:-100px;left:-100px;animation-delay:3s;}
        .auth-container{position:relative;z-index:1;width:100%;max-width:440px;padding:var(--space-4);}
        .auth-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur-heavy);border:var(--border-subtle);border-radius:var(--radius-2xl);padding:var(--space-10) var(--space-8);animation:scaleIn 0.5s ease;}
        .auth-logo{text-align:center;margin-bottom:var(--space-8);}
        .auth-logo .logo{justify-content:center;font-size:var(--text-2xl);}
        .auth-title{text-align:center;font-size:var(--text-2xl);margin-bottom:var(--space-2);}
        .auth-subtitle{text-align:center;color:var(--text-secondary);font-size:var(--text-sm);margin-bottom:var(--space-8);}
        .auth-divider{display:flex;align-items:center;gap:var(--space-4);margin:var(--space-6) 0;color:var(--text-muted);font-size:var(--text-xs);}
        .auth-divider::before,.auth-divider::after{content:'';flex:1;height:1px;background:rgba(148,163,184,0.1);}
        .auth-footer{text-align:center;margin-top:var(--space-6);font-size:var(--text-sm);color:var(--text-secondary);}
        .auth-footer a{color:var(--accent-cyan);}
        .oauth-btn{width:100%;padding:var(--space-3) var(--space-4);background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);font-weight:var(--font-medium);display:flex;align-items:center;justify-content:center;gap:var(--space-3);transition:all var(--transition-base);cursor:pointer;}
        .oauth-btn:hover{border-color:var(--accent-cyan);background:var(--bg-tertiary);}
        .flash-msg{padding:var(--space-3) var(--space-4);border-radius:var(--radius-lg);font-size:var(--text-sm);margin-bottom:var(--space-4);}
        .flash-error{background:rgba(239,68,68,0.15);color:var(--accent-red);border:1px solid rgba(239,68,68,0.2);}
        .flash-success{background:rgba(16,185,129,0.15);color:var(--accent-green);border:1px solid rgba(16,185,129,0.2);}
        .input-icon-wrapper{position:relative;}
        .input-icon-wrapper .input-field{padding-left:44px;}
        .input-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);font-size:var(--text-base);}
        .remember-row{display:flex;align-items:center;justify-content:space-between;margin-bottom:var(--space-6);font-size:var(--text-sm);}
        .remember-row label{display:flex;align-items:center;gap:var(--space-2);color:var(--text-secondary);cursor:pointer;}
        .remember-row a{color:var(--accent-cyan);font-size:var(--text-sm);}
    </style>
</head>
<body>
<div class="auth-page">
    <div class="auth-bg-orb auth-bg-orb-1"></div>
    <div class="auth-bg-orb auth-bg-orb-2"></div>
    <div class="auth-container">
        <div class="auth-card">
            <div class="auth-logo">
                <a href="<?= APP_URL ?>" class="logo"><span class="logo-icon">✦</span><span class="logo-text">JourneyOS</span><span class="logo-badge">AI</span></a>
            </div>
            <h2 class="auth-title">Welcome Back</h2>
            <p class="auth-subtitle">Sign in to continue your journey</p>

            <?php if ($flash): ?>
            <div class="flash-msg flash-<?= e($flash['type']) ?>"><?= e($flash['message']) ?></div>
            <?php endif; ?>

            <button class="oauth-btn" type="button">
                <svg width="18" height="18" viewBox="0 0 24 24"><path fill="#4285F4" d="M22.56 12.25c0-.78-.07-1.53-.2-2.25H12v4.26h5.92a5.06 5.06 0 0 1-2.2 3.32v2.77h3.57c2.08-1.92 3.28-4.74 3.28-8.1z"/><path fill="#34A853" d="M12 23c2.97 0 5.46-.98 7.28-2.66l-3.57-2.77c-.98.66-2.23 1.06-3.71 1.06-2.86 0-5.29-1.93-6.16-4.53H2.18v2.84C3.99 20.53 7.7 23 12 23z"/><path fill="#FBBC05" d="M5.84 14.09c-.22-.66-.35-1.36-.35-2.09s.13-1.43.35-2.09V7.07H2.18C1.43 8.55 1 10.22 1 12s.43 3.45 1.18 4.93l2.85-2.22.81-.62z"/><path fill="#EA4335" d="M12 5.38c1.62 0 3.06.56 4.21 1.64l3.15-3.15C17.45 2.09 14.97 1 12 1 7.7 1 3.99 3.47 2.18 7.07l3.66 2.84c.87-2.6 3.3-4.53 6.16-4.53z"/></svg>
                Continue with Google
            </button>

            <div class="auth-divider">or continue with email</div>

            <form method="POST" action="<?= APP_URL ?>/api/auth.php">
                <input type="hidden" name="action" value="login">
                <?= csrfField() ?>
                <div class="input-group">
                    <label for="email">Email</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i data-lucide="mail"></i></span>
                        <input type="email" id="email" name="email" class="input-field" placeholder="you@example.com" required autocomplete="email">
                    </div>
                </div>
                <div class="input-group">
                    <label for="password">Password</label>
                    <div class="input-icon-wrapper">
                        <span class="input-icon"><i data-lucide="lock"></i></span>
                        <input type="password" id="password" name="password" class="input-field" placeholder="••••••••" required autocomplete="current-password">
                    </div>
                </div>
                <div class="remember-row">
                    <label><input type="checkbox" name="remember" value="1"> Remember me</label>
                    <a href="<?= APP_URL ?>/pages/forgot-password.php">Forgot password?</a>
                </div>
                <button type="submit" class="btn btn-primary w-full btn-lg">Sign In</button>
            </form>

            <div class="auth-footer">
                Don't have an account? <a href="<?= APP_URL ?>/pages/signup.php">Create one</a>
            </div>
        </div>
    </div>
</div>
    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
</body>
</html>
