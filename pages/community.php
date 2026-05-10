<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();

// Fetch public trips for the community feed
// In a real app, this would use pagination and more complex logic
$search = trim(input('q', ''));
$filter = input('filter', 'trending'); // trending, recent, top

$params = [];
$sql = "SELECT t.*, u.name as author_name, u.avatar as author_avatar, 
        (SELECT COUNT(*) FROM trip_stops WHERE trip_id = t.id) as stop_count,
        (SELECT name FROM cities c JOIN trip_stops ts ON c.id = ts.city_id WHERE ts.trip_id = t.id ORDER BY ts.order_index ASC LIMIT 1) as start_city
        FROM trips t 
        JOIN users u ON t.user_id = u.id 
        WHERE t.is_public = 1";

if ($search) {
    $sql .= " AND (t.name LIKE ? OR t.destination LIKE ? OR t.description LIKE ?)";
    $searchParam = "%{$search}%";
    $params = [$searchParam, $searchParam, $searchParam];
}

if ($filter === 'recent') {
    $sql .= " ORDER BY t.created_at DESC LIMIT 20";
} else if ($filter === 'top') {
    $sql .= " ORDER BY t.views DESC LIMIT 20";
} else {
    // Trending (mix of views and recent)
    $sql .= " ORDER BY (t.views + 1) / DATEDIFF(NOW(), t.created_at + INTERVAL 1 DAY) DESC LIMIT 20";
}

$feedTrips = db()->fetchAll($sql, $params);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Community Feed — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}

/* Filters */
.filters-bar{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-xl);padding:var(--space-5);margin-bottom:var(--space-8);display:flex;gap:var(--space-4);flex-wrap:wrap;align-items:center;justify-content:space-between;box-shadow:var(--shadow-sm);}
.filters-bar::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.tabs{display:flex;gap:var(--space-2);background:var(--bg-elevated);border-radius:var(--radius-lg);padding:3px;}
.tab-btn{padding:var(--space-2) var(--space-4);border-radius:calc(var(--radius-lg) - 3px);font-size:var(--text-sm);font-weight:600;color:var(--text-secondary);text-decoration:none;transition:all var(--transition-base);}
.tab-btn:hover{color:var(--text-primary);}
.tab-btn.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-sm);}

/* Feed Cards */
.feed-card{position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-2xl);padding:var(--space-6);margin-bottom:var(--space-6);transition:all var(--transition-base);box-shadow:var(--shadow-sm);}
.feed-card::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px solid rgba(255,255,255,0.06);pointer-events:none;}
.feed-card:hover{transform:translateY(-2px);box-shadow:var(--shadow-md);}
.feed-card:hover::before{border-color:rgba(56,189,248,0.3);}
.feed-card-header{display:flex;align-items:center;gap:var(--space-4);margin-bottom:var(--space-5);}
.feed-avatar{width:48px;height:48px;border-radius:50%;background:var(--gradient-cyan);display:flex;align-items:center;justify-content:center;color:var(--bg-primary);font-weight:700;font-size:var(--text-lg);overflow:hidden;}
.feed-author{font-weight:700;font-size:var(--text-base);color:var(--text-primary);}
.feed-time{font-size:var(--text-xs);color:var(--text-muted);margin-top:2px;}
.feed-body{font-size:var(--text-base);color:var(--text-secondary);line-height:var(--leading-relaxed);margin-bottom:var(--space-5);}
.feed-image{width:100%;height:320px;border-radius:var(--radius-xl);object-fit:cover;margin-bottom:var(--space-5);border:1px solid rgba(255,255,255,0.05);}
.feed-action{display:flex;align-items:center;gap:var(--space-2);background:transparent;border:none;color:var(--text-muted);cursor:pointer;font-size:var(--text-sm);font-weight:600;padding:var(--space-2) var(--space-3);border-radius:var(--radius-lg);transition:all var(--transition-fast);}
.feed-action:hover{color:var(--text-primary);background:rgba(255,255,255,0.05);}
.feed-action i{width:18px;height:18px;transition:all var(--transition-spring);}

/* Empty State */
.empty-state{text-align:center;padding:var(--space-12);position:relative;background:var(--bg-glass);backdrop-filter:var(--glass-blur);border-radius:var(--radius-2xl);}
.empty-state::before{content:'';position:absolute;inset:0;border-radius:inherit;border:1px dashed rgba(255,255,255,0.1);pointer-events:none;}
.empty-state-icon{width:64px;height:64px;color:var(--accent-cyan);opacity:0.5;margin-bottom:var(--space-4);}

/* Search input */
.search-wrapper{position:relative;}
.search-wrapper input{width:100%;padding:var(--space-3) var(--space-4) var(--space-3) 40px;background:rgba(255,255,255,0.03);border:1px solid rgba(255,255,255,0.08);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);transition:all var(--transition-base);}
.search-wrapper input:focus{border-color:var(--accent-cyan);background:rgba(255,255,255,0.05);outline:none;}
.search-icon{position:absolute;left:14px;top:50%;transform:translateY(-50%);color:var(--text-muted);}
.search-icon i{width:16px;height:16px;}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content page-transition">

<div style="display:flex;align-items:flex-end;justify-content:space-between;margin-bottom:var(--space-8);">
    <div>
        <h1 style="font-size:var(--text-4xl);font-weight:800;letter-spacing:-0.03em;margin-bottom:var(--space-2);">Community <span class="text-gradient-aurora">Feed</span></h1>
        <p style="color:var(--text-secondary);font-size:var(--text-lg);">Discover and get inspired by other travelers' journeys</p>
    </div>
</div>

<!-- Filters and Search -->
<div class="filters-bar">
    <div class="tabs">
        <a href="?filter=trending&q=<?= urlencode($search) ?>" class="tab-btn <?= $filter==='trending'?'active':'' ?>">Trending</a>
        <a href="?filter=recent&q=<?= urlencode($search) ?>" class="tab-btn <?= $filter==='recent'?'active':'' ?>">Recent</a>
        <a href="?filter=top&q=<?= urlencode($search) ?>" class="tab-btn <?= $filter==='top'?'active':'' ?>">Top Rated</a>
    </div>
    
    <form method="GET" action="" style="display:flex;gap:var(--space-3);flex:1;max-width:400px;">
        <input type="hidden" name="filter" value="<?= e($filter) ?>">
        <div class="search-wrapper" style="flex:1;">
            <span class="search-icon"><i data-lucide="search"></i></span>
            <input type="text" name="q" placeholder="Search destinations, moods..." value="<?= e($search) ?>">
        </div>
        <button type="submit" class="btn btn-secondary" style="padding:var(--space-2) var(--space-5);">Search</button>
    </form>
</div>

<!-- Feed -->
<div style="max-width:800px;margin:0 auto;">
    <?php if (empty($feedTrips)): ?>
        <div class="empty-state">
            <i data-lucide="compass" class="empty-state-icon" style="margin:0 auto var(--space-4);display:block;"></i>
            <h3 style="font-size:var(--text-xl);font-weight:700;margin-bottom:var(--space-2);">No trips found</h3>
            <p style="color:var(--text-secondary);margin-bottom:var(--space-6);">We couldn't find any public trips matching your search. Try different keywords or check back later!</p>
            <?php if ($search): ?>
                <a href="?filter=<?= e($filter) ?>" class="btn btn-secondary">Clear Search</a>
            <?php endif; ?>
        </div>
    <?php else: ?>
        <?php foreach ($feedTrips as $trip): 
            $theme = getMoodTheme($trip['mood']);
        ?>
        <div class="feed-card animate-fade-in">
            <div class="feed-card-header">
                <div class="feed-avatar">
                    <?= $trip['author_avatar'] ? '<img src="'.UPLOADS_URL.'/avatars/'.e($trip['author_avatar']).'" style="width:100%;height:100%;border-radius:50%;object-fit:cover;">' : substr($trip['author_name'], 0, 1) ?>
                </div>
                <div>
                    <div class="feed-author"><?= e($trip['author_name']) ?></div>
                    <div class="feed-time"><?= timeAgo($trip['created_at']) ?></div>
                </div>
                <div style="margin-left:auto;">
                    <span style="font-size:10px;padding:var(--space-1) var(--space-3);border-radius:9999px;background:<?= $theme['color'] ?>15;color:<?= $theme['color'] ?>;font-weight:600;letter-spacing:0.05em;text-transform:uppercase;border:1px solid <?= $theme['color'] ?>30;display:inline-flex;align-items:center;gap:4px;">
                        <i data-lucide="<?= e(str_replace(['<i data-lucide="', '"></i>'], '', $theme['icon'])) ?>" style="width:12px;height:12px;"></i>
                        <?= e($theme['label']) ?>
                    </span>
                </div>
            </div>
            
            <h3 style="font-size:var(--text-2xl);font-weight:700;margin-bottom:var(--space-2);"><?= e($trip['name']) ?></h3>
            <?php if ($trip['destination']): ?>
            <p style="font-size:var(--text-sm);color:var(--accent-cyan);font-weight:500;margin-bottom:var(--space-4);display:flex;align-items:center;gap:6px;">
                <i data-lucide="map-pin" style="width:16px;height:16px;"></i> <?= e($trip['destination']) ?>
            </p>
            <?php endif; ?>
            
            <div class="feed-body">
                <?= e(truncate($trip['description'] ?? 'An amazing journey mapped out using JourneyOS AI.', 200)) ?>
            </div>
            
            <?php if ($trip['cover_image']): ?>
                <img src="<?= UPLOADS_URL ?>/covers/<?= e($trip['cover_image']) ?>" class="feed-image" alt="Trip Cover">
            <?php else: ?>
                <!-- Placeholder if no cover image -->
                <div class="feed-image" style="background:var(--bg-elevated); display:flex;align-items:center;justify-content:center;height:240px;position:relative;overflow:hidden;">
                    <div style="position:absolute;inset:0;background:url('data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz4KPHBhdGggZD0iTTAgMEw4IDhaTTEgMEw4IDdaTTAgMUw3IDhaIiBzdHJva2U9IiNmZmZmZmYiIHN0cm9rZS1vcGFjaXR5PSIwLjA1IiBzdHJva2Utd2lkdGg9IjEiLz4KPC9zdmc+');"></div>
                    <i data-lucide="image" style="width:48px;height:48px;color:var(--text-muted);opacity:0.3;"></i>
                </div>
            <?php endif; ?>
            
            <div style="display:flex;gap:var(--space-4);margin-bottom:var(--space-6);flex-wrap:wrap;">
                <div class="stat-card" style="padding:var(--space-3) var(--space-4);flex:1;min-width:100px;margin:0;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:var(--space-1);">Duration</div>
                    <div style="font-weight:700;font-size:var(--text-base);color:var(--text-primary);">
                        <?= $trip['start_date'] && $trip['end_date'] ? (strtotime($trip['end_date']) - strtotime($trip['start_date'])) / 86400 . ' Days' : 'TBD' ?>
                    </div>
                </div>
                <div class="stat-card" style="padding:var(--space-3) var(--space-4);flex:1;min-width:100px;margin:0;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:var(--space-1);">Budget</div>
                    <div style="font-weight:700;font-size:var(--text-base);color:var(--text-primary);">
                        <?= ucfirst($trip['budget_level'] ?? 'Mid') ?> (<?= $trip['currency'] ?? 'USD' ?>)
                    </div>
                </div>
                <div class="stat-card" style="padding:var(--space-3) var(--space-4);flex:1;min-width:100px;margin:0;">
                    <div style="font-size:10px;color:var(--text-muted);text-transform:uppercase;letter-spacing:0.05em;font-weight:600;margin-bottom:var(--space-1);">Stops</div>
                    <div style="font-weight:700;font-size:var(--text-base);color:var(--text-primary);">
                        <?= $trip['stop_count'] ?> Cities
                    </div>
                </div>
            </div>
            
            <div style="border-top:1px solid rgba(255,255,255,0.05);padding-top:var(--space-4);display:flex;justify-content:space-between;align-items:center;">
                <div style="display:flex;gap:var(--space-4);">
                    <button class="feed-action" onclick="toggleLike(this)"><i data-lucide="heart"></i> <span><?= rand(12, 340) ?></span></button>
                    <button class="feed-action"><i data-lucide="message-circle"></i> <span><?= rand(2, 45) ?></span></button>
                    <button class="feed-action" onclick="copyShareLink('<?= e($trip['share_token']) ?>')"><i data-lucide="share-2"></i> <span>Share</span></button>
                </div>
                <a href="<?= APP_URL ?>/pages/shared-trip.php?token=<?= e($trip['share_token']) ?>" class="btn btn-secondary" style="padding:var(--space-2) var(--space-4);">
                    View Itinerary <i data-lucide="arrow-right" style="width:14px;height:14px;margin-left:4px;"></i>
                </a>
            </div>
        </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

</main>

<script>
function toggleLike(btn) {
    const icon = btn.querySelector('i');
    const span = btn.querySelector('span');
    let count = parseInt(span.textContent);
    
    if (btn.classList.contains('liked')) {
        btn.classList.remove('liked');
        btn.style.color = 'var(--text-muted)';
        icon.style.fill = 'none';
        span.textContent = count - 1;
    } else {
        btn.classList.add('liked');
        btn.style.color = 'var(--accent-red)';
        icon.style.fill = 'currentColor';
        span.textContent = count + 1;
        // Add tiny pop animation
        icon.style.transform = 'scale(1.3)';
        setTimeout(() => icon.style.transform = 'scale(1)', 200);
    }
}

function copyShareLink(token) {
    const url = '<?= APP_URL ?>/pages/shared-trip.php?token=' + token;
    navigator.clipboard.writeText(url).then(() => {
        // Show toast
        const toast = document.createElement('div');
        toast.className = 'toast toast-success';
        toast.innerHTML = `<i data-lucide="check-circle" class="toast-icon"></i> Link copied to clipboard!`;
        const container = document.querySelector('.toast-container') || document.body.appendChild(Object.assign(document.createElement('div'), {className: 'toast-container'}));
        container.appendChild(toast);
        lucide.createIcons();
        setTimeout(() => {
            toast.classList.add('removing');
            setTimeout(() => toast.remove(), 300);
        }, 3000);
    });
}
</script>
</div>
</body>
</html>
