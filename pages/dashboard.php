<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
require_once __DIR__ . '/../includes/header.php';
require_once __DIR__ . '/../includes/sidebar.php';

$user = currentUser();
$userId = currentUserId();
$hour = date('H');
if ($hour < 12) $greeting = "Good morning";
elseif ($hour < 18) $greeting = "Good afternoon";
else $greeting = "Good evening";

// Fetch Stats
$stats = db()->fetch("
    SELECT 
        COUNT(*) as total_trips,
        COUNT(DISTINCT destination) as total_cities,
        SUM(DATEDIFF(end_date, start_date)) as total_days
    FROM trips 
    WHERE user_id = ? AND status != 'archived'
", [$userId]);

// Fetch Recent Trips
$recentTrips = db()->fetchAll("
    SELECT * FROM trips 
    WHERE user_id = ? 
    ORDER BY created_at DESC LIMIT 3
", [$userId]);
?>

<main class="main-content page-transition">
    <div class="page-header" style="margin-bottom: var(--space-8);">
        <div>
            <h1 class="text-gradient-aurora" style="font-size: var(--text-4xl); margin-bottom: var(--space-2); letter-spacing: -0.03em;"><?= $greeting ?>, <?= e($user['name'] ?? 'Traveler') ?></h1>
            <p style="color: var(--text-secondary); font-size: var(--text-lg);">Where are we escaping to next?</p>
        </div>
    </div>

    <!-- Stats -->
    <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(240px, 1fr)); gap: var(--space-6); margin-bottom: var(--space-10);">
        <div class="stat-card">
            <div class="stat-card-icon" style="background: rgba(56, 189, 248, 0.1); color: var(--accent-cyan);">
                <i data-lucide="map"></i>
            </div>
            <div class="stat-card-label">Trips Planned</div>
            <div class="stat-card-value"><?= $stats['total_trips'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background: rgba(192, 132, 252, 0.1); color: var(--accent-purple);">
                <i data-lucide="globe"></i>
            </div>
            <div class="stat-card-label">Unique Destinations</div>
            <div class="stat-card-value"><?= $stats['total_cities'] ?? 0 ?></div>
        </div>
        <div class="stat-card">
            <div class="stat-card-icon" style="background: rgba(251, 146, 60, 0.1); color: var(--accent-orange);">
                <i data-lucide="calendar-days"></i>
            </div>
            <div class="stat-card-label">Days Traveled</div>
            <div class="stat-card-value"><?= $stats['total_days'] ?? 0 ?></div>
        </div>
    </div>

    <div style="display: grid; grid-template-columns: 2fr 1fr; gap: var(--space-8);">
        <!-- Left Column -->
        <div>
            <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: var(--space-6);">
                <h3 style="font-size: var(--text-2xl); font-weight: 600;">Recent Journeys</h3>
                <a href="<?= APP_URL ?>/pages/my-trips.php" style="color: var(--text-secondary); text-decoration: none; font-size: var(--text-sm); display:flex; align-items:center; gap: 4px; transition: color var(--transition-fast);">
                    View all <i data-lucide="arrow-right" style="width: 14px; height: 14px;"></i>
                </a>
            </div>
            
            <div style="display: grid; grid-template-columns: repeat(auto-fit, minmax(280px, 1fr)); gap: var(--space-6);">
                <?php if (empty($recentTrips)): ?>
                    <div class="glass-card-static text-center" style="padding: var(--space-8); grid-column: 1 / -1; display: flex; flex-direction: column; align-items: center;">
                        <i data-lucide="compass" style="width: 48px; height: 48px; color: var(--text-muted); margin-bottom: var(--space-4);"></i>
                        <h4 style="margin-bottom: var(--space-2);">No trips yet</h4>
                        <p style="color: var(--text-secondary); margin-bottom: var(--space-4);">Start your next adventure today.</p>
                        <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn btn-primary">Create Trip</a>
                    </div>
                <?php else: ?>
                    <?php foreach ($recentTrips as $trip): 
                        $theme = getMoodTheme($trip['mood']);
                    ?>
                    <a href="<?= APP_URL ?>/pages/itinerary-view.php?id=<?= $trip['id'] ?>" class="trip-card" style="display: block; text-decoration: none; color: inherit;">
                        <div class="trip-card-cover" style="position: relative; background: linear-gradient(135deg, rgba(0,0,0,0.8), rgba(0,0,0,0.2)), url('<?= $trip['cover_image'] ? UPLOADS_URL.'/covers/'.$trip['cover_image'] : 'https://images.unsplash.com/photo-1506929562872-bb421503ef21?auto=format&fit=crop&w=600&q=80' ?>') center/cover;">
                            <div style="position: absolute; top: var(--space-3); right: var(--space-3);">
                                <span class="badge" style="background: rgba(11,16,32,0.8); border: 1px solid rgba(255,255,255,0.1); color: <?= $theme['color'] ?>; backdrop-filter: blur(8px);">
                                    <?= e($theme['label']) ?>
                                </span>
                            </div>
                        </div>
                        <div class="trip-card-body" style="padding: var(--space-5);">
                            <h4 class="trip-card-title" style="margin-bottom: var(--space-1);"><?= e($trip['name']) ?></h4>
                            <div class="trip-card-meta" style="margin-bottom: var(--space-4);">
                                <i data-lucide="map-pin"></i> <?= e($trip['destination'] ?? 'Unknown') ?>
                            </div>
                            <div style="display: flex; justify-content: space-between; align-items: center; border-top: 1px solid rgba(255,255,255,0.05); padding-top: var(--space-4);">
                                <div style="font-size: var(--text-xs); color: var(--text-muted);">
                                    <?= $trip['start_date'] ? date('M d', strtotime($trip['start_date'])) : 'TBD' ?> 
                                    <?= $trip['end_date'] ? '- ' . date('M d, Y', strtotime($trip['end_date'])) : '' ?>
                                </div>
                                <div class="badge" style="background: rgba(255,255,255,0.05); color: var(--text-secondary);">
                                    <?= ucfirst($trip['status']) ?>
                                </div>
                            </div>
                        </div>
                    </a>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column -->
        <div style="display: flex; flex-direction: column; gap: var(--space-6);">
            <!-- AI Insights Panel -->
            <div class="stat-card" style="padding: var(--space-6); background: radial-gradient(circle at top right, rgba(192, 132, 252, 0.05), transparent 70%); border-color: rgba(192, 132, 252, 0.1);">
                <div style="display: flex; align-items: center; gap: var(--space-2); margin-bottom: var(--space-4);">
                    <i data-lucide="sparkles" style="color: var(--accent-purple);"></i>
                    <h3 style="font-size: var(--text-lg); margin: 0; font-weight: 600;">Intelligence</h3>
                </div>
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: var(--space-4); border-radius: var(--radius-lg); font-size: var(--text-sm); line-height: 1.5; color: var(--text-secondary);">
                        Based on your recent <span style="color: var(--accent-cyan);">Kyoto</span> trip, you might enjoy the historic temples of <span style="color: var(--text-primary);">Chiang Mai, Thailand</span> next.
                    </div>
                    <div style="background: rgba(0,0,0,0.2); border: 1px solid rgba(255,255,255,0.03); padding: var(--space-4); border-radius: var(--radius-lg); font-size: var(--text-sm); line-height: 1.5; color: var(--text-secondary);">
                        Flight prices to Europe are dipping 15% for September dates. <a href="#" style="color: var(--accent-cyan);">Set an alert.</a>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="stat-card" style="padding: var(--space-6);">
                <h3 style="font-size: var(--text-lg); margin-bottom: var(--space-4); font-weight: 600;">Quick Actions</h3>
                <div style="display: flex; flex-direction: column; gap: var(--space-3);">
                    <a href="<?= APP_URL ?>/pages/create-trip.php" class="btn" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); color: var(--text-primary); justify-content: flex-start; padding: var(--space-4);">
                        <div style="background: var(--bg-primary); padding: 6px; border-radius: 6px; margin-right: 8px;"><i data-lucide="plus" style="width: 16px; height: 16px;"></i></div> New Journey
                    </a>
                    <a href="<?= APP_URL ?>/pages/city-search.php" class="btn" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); color: var(--text-primary); justify-content: flex-start; padding: var(--space-4);">
                        <div style="background: var(--bg-primary); padding: 6px; border-radius: 6px; margin-right: 8px;"><i data-lucide="search" style="width: 16px; height: 16px;"></i></div> Explore Destinations
                    </a>
                    <a href="<?= APP_URL ?>/pages/community.php" class="btn" style="background: rgba(255,255,255,0.03); border: 1px solid rgba(255,255,255,0.05); color: var(--text-primary); justify-content: flex-start; padding: var(--space-4);">
                        <div style="background: var(--bg-primary); padding: 6px; border-radius: 6px; margin-right: 8px;"><i data-lucide="users" style="width: 16px; height: 16px;"></i></div> Community Feed
                    </a>
                </div>
            </div>
        </div>
    </div>
</main>

<?php require_once __DIR__ . '/../includes/footer.php'; ?>
