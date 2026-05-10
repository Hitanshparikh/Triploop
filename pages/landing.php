<?php require_once __DIR__ . '/../includes/functions.php'; ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>JourneyOS AI — The Emotional Operating System for Travel</title>
    <meta name="description" content="JourneyOS AI adapts to how you want to feel. Plan smarter, travel deeper, remember forever.">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
    <link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/pages/landing.css">
</head>
<body>
    <!-- Nav -->
    <nav class="landing-nav" id="mainNav">
        <div class="container flex items-center justify-between">
            <a href="<?= APP_URL ?>" class="logo">
                <span class="logo-icon">✦</span>
                <span class="logo-text">JourneyOS</span>
                <span class="logo-badge">AI</span>
            </a>
            <div class="nav-links">
                <a href="#features">Features</a>
                <a href="#mood">Mood Engine</a>
                <a href="#simulation">Simulation</a>
                <a href="#collab">Collaborate</a>
            </div>
            <div class="nav-actions">
                <a href="<?= APP_URL ?>/pages/login.php" class="btn btn-ghost">Sign In</a>
                <a href="<?= APP_URL ?>/pages/signup.php" class="btn btn-primary">Get Started</a>
            </div>
            <button class="mobile-menu-btn" id="mobileMenuBtn" aria-label="Menu">
                <span></span><span></span><span></span>
            </button>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero" id="hero">
        <div class="hero-bg">
            <div class="hero-gradient"></div>
            <div class="particles" id="particles"></div>
            <div class="hero-orb hero-orb-1"></div>
            <div class="hero-orb hero-orb-2"></div>
            <div class="hero-orb hero-orb-3"></div>
        </div>
        <div class="container hero-content">
            <div class="hero-badge animate-fade-in">
                <span class="pulse-dot"></span>
                The Future of Intelligent Travel
            </div>
            <h1 class="hero-title animate-fade-in-up">
                Travel That <span class="text-gradient-aurora">Understands</span><br>
                How You <span class="text-gradient">Feel</span>
            </h1>
            <p class="hero-subtitle animate-fade-in-up delay-200">
                JourneyOS AI adapts to your mood, energy, and pace — crafting trips that feel
                emotionally perfect. Plan smarter. Travel deeper. Remember forever.
            </p>
            <div class="hero-cta animate-fade-in-up delay-400">
                <a href="<?= APP_URL ?>/pages/signup.php" class="btn btn-primary btn-xl">
                    Start Your Journey
                    <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                </a>
                <a href="#features" class="btn btn-secondary btn-xl">Explore Features</a>
            </div>
            <div class="hero-stats animate-fade-in-up delay-600">
                <div class="stat"><span class="stat-num" data-count="50000">0</span><span class="stat-label">Trips Planned</span></div>
                <div class="stat-divider"></div>
                <div class="stat"><span class="stat-num" data-count="120">0</span><span class="stat-label">Countries</span></div>
                <div class="stat-divider"></div>
                <div class="stat"><span class="stat-num" data-count="98">0</span><span class="stat-label">% Happy Travelers</span></div>
            </div>
        </div>
        <div class="hero-scroll-indicator">
            <div class="scroll-mouse"><div class="scroll-dot"></div></div>
            <span>Scroll to explore</span>
        </div>
    </section>

    <!-- Features Section -->
    <section class="section features-section" id="features">
        <div class="container">
            <div class="section-header text-center reveal">
                <span class="section-tag badge badge-cyan">✦ Core Features</span>
                <h2 class="mt-4">Everything You Need,<br><span class="text-gradient">Nothing You Don't</span></h2>
                <p class="section-desc mt-4">A complete travel operating system that goes far beyond planning.</p>
            </div>
            <div class="features-grid mt-12">
                <div class="feature-card glass-card reveal delay-100">
                    <div class="feature-icon" style="background: var(--gradient-cyan);"><i data-lucide="map"></i></div>
                    <h4>Smart Itineraries</h4>
                    <p>AI-optimized day plans that balance pacing, budget, and energy levels.</p>
                </div>
                <div class="feature-card glass-card reveal delay-200">
                    <div class="feature-icon" style="background: var(--gradient-purple);"><i data-lucide="brain"></i></div>
                    <h4>Mood Engine</h4>
                    <p>The app adapts colors, pace, and recommendations to how you want to feel.</p>
                </div>
                <div class="feature-card glass-card reveal delay-300">
                    <div class="feature-icon" style="background: var(--gradient-warm);"><i data-lucide="bar-chart-2"></i></div>
                    <h4>Trip Simulation</h4>
                    <p>Predict stress, fatigue, and budget burn before you even leave.</p>
                </div>
                <div class="feature-card glass-card reveal delay-400">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #10B981, #6EE7B7);"><i data-lucide="users"></i></div>
                    <h4>Live Collaboration</h4>
                    <p>Plan together in real-time. Vote, comment, and split expenses.</p>
                </div>
                <div class="feature-card glass-card reveal delay-500">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #EC4899, #F43F5E);"><i data-lucide="book-open"></i></div>
                    <h4>Travel Journal</h4>
                    <p>Emotional journaling with AI-generated memory timelines.</p>
                </div>
                <div class="feature-card glass-card reveal delay-600">
                    <div class="feature-icon" style="background: linear-gradient(135deg, #F59E0B, #D97706);"><i data-lucide="coins"></i></div>
                    <h4>Budget Intelligence</h4>
                    <p>Real-time spending analytics with overbudget warnings.</p>
                </div>
            </div>
        </div>
    </section>

    <!-- Mood Engine Section -->
    <section class="section mood-section" id="mood">
        <div class="container">
            <div class="section-header text-center reveal">
                <span class="section-tag badge badge-purple"><i data-lucide="brain"></i> Mood Engine</span>
                <h2 class="mt-4">Your Mood.<br><span class="text-gradient-purple">Your Experience.</span></h2>
                <p class="section-desc mt-4">Select how you want to feel. The entire app transforms to match.</p>
            </div>
            <div class="mood-selector mt-12 reveal">
                <div class="mood-grid">
                    <button class="mood-btn active" data-mood="adventure" style="--mood-color: #FF6B35;">
                        <span class="mood-emoji"><i data-lucide="zap"></i></span><span class="mood-label">Adventure</span>
                    </button>
                    <button class="mood-btn" data-mood="romantic" style="--mood-color: #EC4899;">
                        <span class="mood-emoji"><i data-lucide="heart"></i></span><span class="mood-label">Romantic</span>
                    </button>
                    <button class="mood-btn" data-mood="healing" style="--mood-color: #10B981;">
                        <span class="mood-emoji"><i data-lucide="leaf"></i></span><span class="mood-label">Healing</span>
                    </button>
                    <button class="mood-btn" data-mood="luxury" style="--mood-color: #F59E0B;">
                        <span class="mood-emoji"><i data-lucide="sparkles"></i></span><span class="mood-label">Luxury</span>
                    </button>
                    <button class="mood-btn" data-mood="party" style="--mood-color: #8B5CF6;">
                        <span class="mood-emoji"><i data-lucide="party-popper"></i></span><span class="mood-label">Party</span>
                    </button>
                    <button class="mood-btn" data-mood="spiritual" style="--mood-color: #6366F1;">
                        <span class="mood-emoji"><i data-lucide="flower-2"></i></span><span class="mood-label">Spiritual</span>
                    </button>
                    <button class="mood-btn" data-mood="productivity" style="--mood-color: #3B82F6;">
                        <span class="mood-emoji"><i data-lucide="briefcase"></i></span><span class="mood-label">Productivity</span>
                    </button>
                    <button class="mood-btn" data-mood="solo" style="--mood-color: #00D4FF;">
                        <span class="mood-emoji"><i data-lucide="globe"></i></span><span class="mood-label">Solo</span>
                    </button>
                </div>
                <div class="mood-preview glass-card-static mt-8" id="moodPreview">
                    <div class="mood-preview-header">
                        <h4 id="moodTitle">Adventure Mode</h4>
                        <span class="badge" id="moodBadge" style="background: rgba(255,107,53,0.15); color: #FF6B35;">Active</span>
                    </div>
                    <p id="moodDesc" class="mt-2" style="color: var(--text-secondary);">Bold colors, faster transitions, intense activity suggestions, and adrenaline-pumping destinations.</p>
                    <div class="mood-demo-cards mt-6" id="moodCards">
                        <div class="mood-demo-card" style="border-color: rgba(255,107,53,0.3);">
                            <span><i data-lucide="mountain"></i></span><p>Hiking Machu Picchu</p>
                        </div>
                        <div class="mood-demo-card" style="border-color: rgba(255,107,53,0.3);">
                            <span><i data-lucide="waves"></i></span><p>Scuba in Great Barrier Reef</p>
                        </div>
                        <div class="mood-demo-card" style="border-color: rgba(255,107,53,0.3);">
                            <span><i data-lucide="wind"></i></span><p>Skydiving in Dubai</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Trip Simulation Section -->
    <section class="section simulation-section" id="simulation">
        <div class="container">
            <div class="grid" style="grid-template-columns: 1fr 1fr; gap: var(--space-16); align-items: center;">
                <div class="reveal-left">
                    <span class="section-tag badge badge-orange"><i data-lucide="bar-chart-2"></i> Trip Simulation</span>
                    <h2 class="mt-4">Know Before<br><span class="text-gradient-aurora">You Go</span></h2>
                    <p class="mt-4" style="color: var(--text-secondary); font-size: var(--text-lg);">
                        Our AI predicts your trip experience before you leave. Get actionable insights to optimize every moment.
                    </p>
                    <div class="sim-metrics mt-8">
                        <div class="sim-metric"><span class="sim-icon"><i data-lucide="frown"></i></span><div><strong>Stress Level</strong><div class="sim-bar"><div class="sim-fill" style="width:35%; background: var(--accent-green);"></div></div></div></div>
                        <div class="sim-metric"><span class="sim-icon"><i data-lucide="footprints"></i></span><div><strong>Walking Load</strong><div class="sim-bar"><div class="sim-fill" style="width:60%; background: var(--accent-orange);"></div></div></div></div>
                        <div class="sim-metric"><span class="sim-icon"><i data-lucide="banknote"></i></span><div><strong>Budget Burn</strong><div class="sim-bar"><div class="sim-fill" style="width:45%; background: var(--accent-cyan);"></div></div></div></div>
                        <div class="sim-metric"><span class="sim-icon"><i data-lucide="cloud-rain"></i></span><div><strong>Weather Risk</strong><div class="sim-bar"><div class="sim-fill" style="width:25%; background: var(--accent-purple);"></div></div></div></div>
                    </div>
                </div>
                <div class="reveal-right">
                    <div class="sim-card glass-card-static">
                        <div class="sim-score-ring">
                            <svg viewBox="0 0 120 120" class="score-svg">
                                <circle cx="60" cy="60" r="52" fill="none" stroke="var(--bg-elevated)" stroke-width="8"/>
                                <circle cx="60" cy="60" r="52" fill="none" stroke="url(#scoreGradient)" stroke-width="8" stroke-dasharray="280" stroke-dashoffset="56" stroke-linecap="round" transform="rotate(-90 60 60)"/>
                                <defs><linearGradient id="scoreGradient" x1="0%" y1="0%" x2="100%" y2="0%"><stop offset="0%" stop-color="#00D4FF"/><stop offset="100%" stop-color="#A855F7"/></linearGradient></defs>
                            </svg>
                            <div class="score-text">
                                <span class="score-num">82</span>
                                <span class="score-label">Health Score</span>
                            </div>
                        </div>
                        <div class="sim-suggestions mt-6">
                            <div class="sim-suggestion"><span><i data-lucide="lightbulb"></i></span> Add a rest day between Tokyo and Kyoto</div>
                            <div class="sim-suggestion"><span><i data-lucide="lightbulb"></i></span> Book morning activities — less crowd</div>
                            <div class="sim-suggestion"><span><i data-lucide="lightbulb"></i></span> Budget $40/day buffer for hidden costs</div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Collaboration Section -->
    <section class="section collab-section" id="collab">
        <div class="container text-center">
            <div class="reveal">
                <span class="section-tag badge badge-green"><i data-lucide="users"></i> Collaborative</span>
                <h2 class="mt-4">Plan Together.<br><span class="text-gradient">Travel Together.</span></h2>
                <p class="section-desc mt-4">Real-time collaboration rooms where friends can vote, comment, and plan simultaneously.</p>
            </div>
            <div class="collab-features mt-12">
                <div class="collab-card glass-card reveal delay-100">
                    <div class="collab-icon"><i data-lucide="check-square"></i></div><h5>Vote on Activities</h5><p>Everyone gets a say</p>
                </div>
                <div class="collab-card glass-card reveal delay-200">
                    <div class="collab-icon"><i data-lucide="message-square"></i></div><h5>Live Comments</h5><p>Discuss in real-time</p>
                </div>
                <div class="collab-card glass-card reveal delay-300">
                    <div class="collab-icon"><i data-lucide="pen-tool"></i></div><h5>Edit Together</h5><p>Simultaneous editing</p>
                </div>
                <div class="collab-card glass-card reveal delay-400">
                    <div class="collab-icon"><i data-lucide="coins"></i></div><h5>Split Expenses</h5><p>Fair and transparent</p>
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="section cta-section">
        <div class="container text-center">
            <div class="cta-card glass-card-static reveal">
                <h2>Ready to Transform<br>How You <span class="text-gradient-aurora">Travel</span>?</h2>
                <p class="mt-4" style="color: var(--text-secondary); font-size: var(--text-lg); max-width: 500px; margin: var(--space-4) auto 0;">
                    Join thousands of emotionally intelligent travelers. It's free to start.
                </p>
                <div class="mt-8">
                    <a href="<?= APP_URL ?>/pages/signup.php" class="btn btn-primary btn-xl">
                        Start Planning Free
                        <svg width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><path d="M5 12h14M12 5l7 7-7 7"/></svg>
                    </a>
                </div>
            </div>
        </div>
    </section>

    <!-- Footer -->
    <footer class="landing-footer">
        <div class="container">
            <div class="footer-top flex justify-between items-center">
                <div class="logo"><span class="logo-icon">✦</span><span class="logo-text">JourneyOS</span><span class="logo-badge">AI</span></div>
                <p style="color: var(--text-tertiary);">© <?= date('Y') ?> JourneyOS AI. The future of intelligent travel.</p>
            </div>
        </div>
    </footer>

    <script src="https://unpkg.com/lucide@latest"></script>
    <script>lucide.createIcons();</script>
    <script src="<?= ASSETS_PATH ?>/js/landing.js"></script>
</body>
</html>
