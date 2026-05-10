<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
$q    = trim($_GET['q'] ?? '');
$type = $_GET['type'] ?? 'all'; // all | city | activity
$cat  = $_GET['cat'] ?? '';
$sort = $_GET['sort'] ?? 'popular';

// Demo data
$cities = [
  ['id'=>1,'name'=>'Tokyo','country'=>'Japan','continent'=>'Asia','cost'=>'expensive','rating'=>4.9,'pop'=>980,'tags'=>['culture','food','technology'],'desc'=>'The neon-lit capital blends ancient temples with futuristic skyscrapers.','img_color'=>'#00D4FF'],
  ['id'=>2,'name'=>'Bali','country'=>'Indonesia','continent'=>'Asia','cost'=>'budget','rating'=>4.7,'pop'=>850,'tags'=>['beach','wellness','nature'],'desc'=>'Tropical paradise with lush rice terraces, sacred temples and surf breaks.','img_color'=>'#10B981'],
  ['id'=>3,'name'=>'Paris','country'=>'France','continent'=>'Europe','cost'=>'expensive','rating'=>4.8,'pop'=>920,'tags'=>['romance','art','food'],'desc'=>'The city of light — iconic landmarks, world-class cuisine and fine art.','img_color'=>'#EC4899'],
  ['id'=>4,'name'=>'Dubai','country'=>'UAE','continent'=>'Asia','cost'=>'luxury','rating'=>4.6,'pop'=>810,'tags'=>['luxury','desert','shopping'],'desc'=>'Futuristic skyline meets desert adventures and ultra-luxury experiences.','img_color'=>'#F59E0B'],
  ['id'=>5,'name'=>'New York','country'=>'USA','continent'=>'Americas','cost'=>'expensive','rating'=>4.7,'pop'=>900,'tags'=>['urban','culture','nightlife'],'desc'=>'The city that never sleeps — Times Square, Central Park, and Broadway.','img_color'=>'#A855F7'],
  ['id'=>6,'name'=>'Santorini','country'=>'Greece','continent'=>'Europe','cost'=>'expensive','rating'=>4.9,'pop'=>760,'tags'=>['romance','beach','scenic'],'desc'=>'White-washed cliffs, volcanic beaches, and breathtaking Aegean sunsets.','img_color'=>'#3B82F6'],
];
$activities = [
  ['id'=>1,'name'=>'Paragliding','city'=>'Pokhara','country'=>'Nepal','cat'=>'adventure','cost'=>80,'duration'=>'2 hrs','rating'=>4.8,'desc'=>'Tandem paragliding over the Himalayan foothills with stunning lake views.','img_color'=>'#FF6B35'],
  ['id'=>2,'name'=>'Sushi Making Class','city'=>'Tokyo','country'=>'Japan','cat'=>'food','cost'=>65,'duration'=>'3 hrs','rating'=>4.9,'desc'=>'Learn from a master chef to roll perfect sushi in a traditional kitchen.','img_color'=>'#00D4FF'],
  ['id'=>3,'name'=>'Sunrise Yoga','city'=>'Bali','country'=>'Indonesia','cat'=>'wellness','cost'=>20,'duration'=>'1 hr','rating'=>4.7,'desc'=>'Greet the sun with a guided session on a rice terrace in Ubud.','img_color'=>'#10B981'],
  ['id'=>4,'name'=>'Desert Safari','city'=>'Dubai','country'=>'UAE','cat'=>'adventure','cost'=>120,'duration'=>'6 hrs','rating'=>4.6,'desc'=>'Dune bashing, camel riding, and a Bedouin camp dinner under the stars.','img_color'=>'#F59E0B'],
  ['id'=>5,'name'=>'Eiffel Tower Tour','city'=>'Paris','country'=>'France','cat'=>'sightseeing','cost'=>35,'duration'=>'2 hrs','rating'=>4.9,'desc'=>'Skip-the-line access to all levels with a licensed guide.','img_color'=>'#EC4899'],
  ['id'=>6,'name'=>'Scuba Diving','city'=>'Koh Tao','country'=>'Thailand','cat'=>'adventure','cost'=>55,'duration'=>'4 hrs','rating'=>4.7,'desc'=>'Explore vibrant coral reefs and tropical fish in the Gulf of Thailand.','img_color'=>'#3B82F6'],
];

// Filter by search query
if($q) {
  $cities     = array_filter($cities,     fn($c) => stripos($c['name'],$q)!==false || stripos($c['country'],$q)!==false);
  $activities = array_filter($activities, fn($a) => stripos($a['name'],$q)!==false || stripos($a['city'],$q)!==false || stripos($a['cat'],$q)!==false);
}
if($cat) $activities = array_filter($activities, fn($a) => $a['cat'] === $cat);

$totalResults = count($cities) + count($activities);
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>Search — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:280px;padding:var(--space-8);background:var(--bg-primary);}
/* Hero Search */
.search-hero{background:linear-gradient(135deg,rgba(0,212,255,0.05),rgba(168,85,247,0.05));border:var(--border-subtle);border-radius:var(--radius-2xl);padding:var(--space-10) var(--space-8);margin-bottom:var(--space-8);text-align:center;position:relative;overflow:hidden;}
.hero-orb{position:absolute;border-radius:50%;filter:blur(80px);pointer-events:none;}
.search-bar-lg{display:flex;align-items:center;background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-xl);overflow:hidden;max-width:640px;margin:var(--space-6) auto 0;transition:all var(--transition-base);}
.search-bar-lg:focus-within{border-color:var(--accent-cyan);box-shadow:0 0 0 3px var(--accent-cyan-soft);}
.search-bar-lg input{flex:1;padding:var(--space-4) var(--space-5);background:transparent;border:none;color:var(--text-primary);font-size:var(--text-lg);outline:none;}
.search-bar-lg input::placeholder{color:var(--text-muted);}
.search-submit{padding:var(--space-3) var(--space-6);background:var(--gradient-cyan);color:var(--bg-primary);font-weight:var(--font-bold);border:none;cursor:pointer;font-size:var(--text-sm);}
/* Toolbar */
.search-toolbar{display:flex;align-items:center;gap:var(--space-3);margin-bottom:var(--space-6);flex-wrap:wrap;}
.type-tabs{display:flex;gap:0;background:var(--bg-elevated);border-radius:var(--radius-lg);padding:3px;}
.type-tab{padding:var(--space-2) var(--space-4);border-radius:calc(var(--radius-lg) - 3px);font-size:var(--text-sm);font-weight:var(--font-semibold);cursor:pointer;border:none;background:transparent;color:var(--text-muted);transition:all var(--transition-base);}
.type-tab.active{background:var(--bg-glass);color:var(--text-primary);box-shadow:var(--shadow-sm);}
.toolbar-select{padding:var(--space-2) var(--space-3);background:var(--bg-glass);border:var(--border-subtle);border-radius:var(--radius-lg);color:var(--text-primary);font-size:var(--text-sm);outline:none;cursor:pointer;}
/* Section header */
.section-header{display:flex;align-items:center;justify-content:space-between;margin:var(--space-6) 0 var(--space-4);}
/* Grid */
.results-grid{display:grid;grid-template-columns:repeat(auto-fill,minmax(280px,1fr));gap:var(--space-5);}
/* City Card */
.result-card{background:var(--bg-glass);backdrop-filter:var(--glass-blur);border:var(--border-subtle);border-radius:var(--radius-xl);overflow:hidden;transition:all var(--transition-base);}
.result-card:hover{transform:translateY(-4px);box-shadow:var(--shadow-glow-cyan);border-color:rgba(0,212,255,0.2);}
.card-cover{height:140px;position:relative;overflow:hidden;}
.card-cover-orb{position:absolute;border-radius:50%;filter:blur(50px);}
.card-cover-overlay{position:absolute;inset:0;background:linear-gradient(to top,rgba(11,16,32,0.85) 0%,transparent 60%);}
.card-body{padding:var(--space-4) var(--space-5);}
.card-tags{display:flex;gap:var(--space-1);flex-wrap:wrap;margin:var(--space-2) 0;}
.tag{font-size:10px;padding:2px 8px;border-radius:var(--radius-full);background:var(--bg-elevated);color:var(--text-muted);}
.card-footer{display:flex;align-items:center;justify-content:space-between;padding:var(--space-3) var(--space-5);border-top:1px solid rgba(148,163,184,0.06);}
.rating{display:flex;align-items:center;gap:4px;font-size:var(--text-sm);font-weight:var(--font-bold);}
/* Activity Card extras */
.act-meta{display:flex;align-items:center;gap:var(--space-4);margin:var(--space-3) 0;font-size:var(--text-xs);color:var(--text-muted);}
.act-meta i{width:12px;height:12px;}
/* Cost badge */
.cost-badge{font-size:var(--text-xs);padding:2px 8px;border-radius:var(--radius-full);}
.cost-budget{background:rgba(16,185,129,0.1);color:var(--accent-green);}
.cost-moderate{background:rgba(59,130,246,0.1);color:var(--accent-blue);}
.cost-expensive{background:rgba(255,107,53,0.1);color:var(--accent-orange);}
.cost-luxury{background:rgba(245,158,11,0.1);color:var(--accent-gold);}
/* Popular tags */
.pop-tags{display:flex;gap:var(--space-2);flex-wrap:wrap;margin-top:var(--space-3);}
.pop-tag{padding:var(--space-2) var(--space-4);border-radius:var(--radius-full);background:var(--bg-elevated);border:var(--border-subtle);font-size:var(--text-sm);color:var(--text-secondary);cursor:pointer;text-decoration:none;transition:all var(--transition-fast);}
.pop-tag:hover{border-color:var(--accent-cyan);color:var(--accent-cyan);}
.empty-state{text-align:center;padding:var(--space-16);color:var(--text-muted);grid-column:1/-1;}
</style>
</head>
<body>
<div class="app-layout">
<?php require_once __DIR__ . '/../includes/sidebar.php'; ?>
<main class="main-content">

  <!-- Hero Search -->
  <div class="search-hero">
    <div class="hero-orb" style="width:400px;height:400px;background:var(--accent-cyan);opacity:0.08;top:-150px;right:-100px;"></div>
    <div class="hero-orb" style="width:300px;height:300px;background:var(--accent-purple);opacity:0.08;bottom:-100px;left:-100px;"></div>
    <h1 style="font-size:var(--text-4xl);margin-bottom:var(--space-2);">Explore <span class="text-gradient">Cities & Activities</span></h1>
    <p style="color:var(--text-secondary);">Discover destinations and experiences for your next trip.</p>
    <form method="GET" action="">
      <div class="search-bar-lg">
        <i data-lucide="search" style="width:20px;height:20px;color:var(--text-muted);margin-left:var(--space-5);flex-shrink:0;"></i>
        <input type="text" name="q" value="<?= e($q) ?>" placeholder="Search cities, activities, countries..." autofocus>
        <button type="submit" class="search-submit">Search</button>
      </div>
    </form>
    <?php if(!$q): ?>
    <div>
      <p style="font-size:var(--text-xs);color:var(--text-muted);margin-top:var(--space-4);margin-bottom:var(--space-2);">Popular searches</p>
      <div class="pop-tags" style="justify-content:center;">
        <?php foreach(['Paragliding','Tokyo','Bali','Desert Safari','Yoga','Scuba Diving','Paris','Santorini'] as $pt): ?>
        <a href="?q=<?= urlencode($pt) ?>" class="pop-tag"><?= $pt ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <?php endif; ?>
  </div>

  <!-- Toolbar -->
  <div class="search-toolbar">
    <div class="type-tabs">
      <button class="type-tab <?= $type==='all'?'active':'' ?>" onclick="setType('all')">All</button>
      <button class="type-tab <?= $type==='city'?'active':'' ?>" onclick="setType('city')">Cities</button>
      <button class="type-tab <?= $type==='activity'?'active':'' ?>" onclick="setType('activity')">Activities</button>
    </div>
    <input type="text" id="liveSearch" placeholder="Search bar..." class="input-field" style="max-width:220px;padding:var(--space-2) var(--space-3);" oninput="liveFilter()" value="<?= e($q) ?>">
    <select class="toolbar-select" id="groupSel" onchange="applyFilters()">
      <option value="">Group by...</option>
      <option value="continent">Continent</option>
      <option value="cost">Cost</option>
      <option value="cat">Category</option>
    </select>
    <select class="toolbar-select" id="filterSel" onchange="applyFilters()">
      <option value="">Filter...</option>
      <option value="budget">Budget</option>
      <option value="moderate">Moderate</option>
      <option value="expensive">Expensive</option>
      <option value="luxury">Luxury</option>
    </select>
    <select class="toolbar-select" id="sortSel" onchange="applyFilters()">
      <option value="popular">Sort by: Popular</option>
      <option value="rating">Rating ↓</option>
      <option value="name">Name A–Z</option>
      <option value="cost_asc">Cost ↑</option>
    </select>
    <?php if($q || $cat): ?>
    <a href="<?= APP_URL ?>/pages/city-search.php" class="btn btn-secondary" style="font-size:var(--text-sm);">
      <i data-lucide="x" style="width:14px;height:14px;"></i> Clear
    </a>
    <?php endif; ?>
  </div>

  <?php if($q): ?>
  <p style="color:var(--text-muted);font-size:var(--text-sm);margin-bottom:var(--space-5);">
    <?= $totalResults ?> result<?= $totalResults != 1 ? 's' : '' ?> for "<strong style="color:var(--text-primary);"><?= e($q) ?></strong>"
  </p>
  <?php endif; ?>

  <!-- CITIES -->
  <?php if($type !== 'activity' && !empty($cities)): ?>
  <div id="citiesSection">
    <div class="section-header">
      <h3 style="font-size:var(--text-xl);">
        <i data-lucide="map" style="width:18px;height:18px;color:var(--accent-cyan);display:inline;margin-right:var(--space-2);"></i>
        Cities <span style="font-size:var(--text-sm);color:var(--text-muted);font-weight:normal;">(<?= count($cities) ?>)</span>
      </h3>
    </div>
    <div class="results-grid" id="citiesGrid">
      <?php foreach($cities as $city): ?>
      <div class="result-card" data-name="<?= strtolower($city['name'].' '.$city['country']) ?>" data-cost="<?= $city['cost'] ?>" data-pop="<?= $city['pop'] ?>" data-rating="<?= $city['rating'] ?>" data-continent="<?= $city['continent'] ?>">
        <div class="card-cover">
          <div class="card-cover-orb" style="width:250px;height:250px;background:<?= $city['img_color'] ?>;top:-80px;right:-80px;opacity:0.4;"></div>
          <div class="card-cover-overlay"></div>
          <div style="position:absolute;bottom:var(--space-3);left:var(--space-4);">
            <p style="font-weight:var(--font-bold);font-size:var(--text-lg);margin:0;"><?= e($city['name']) ?></p>
            <p style="font-size:var(--text-xs);color:rgba(241,245,249,0.7);margin:0;"><?= e($city['country']) ?> · <?= e($city['continent']) ?></p>
          </div>
          <div style="position:absolute;top:var(--space-3);right:var(--space-3);">
            <span class="cost-badge cost-<?= $city['cost'] ?>"><?= ucfirst($city['cost']) ?></span>
          </div>
        </div>
        <div class="card-body">
          <p style="font-size:var(--text-sm);color:var(--text-secondary);margin:0;"><?= e($city['desc']) ?></p>
          <div class="card-tags">
            <?php foreach($city['tags'] as $t): ?><span class="tag">#<?= $t ?></span><?php endforeach; ?>
          </div>
        </div>
        <div class="card-footer">
          <div class="rating">
            <i data-lucide="star" style="width:14px;height:14px;color:var(--accent-gold);"></i>
            <?= $city['rating'] ?>
          </div>
          <a href="<?= APP_URL ?>/pages/city-search.php?q=<?= urlencode($city['name']) ?>&type=activity" class="btn btn-secondary" style="font-size:var(--text-xs);padding:6px 14px;">
            <i data-lucide="eye" style="width:12px;height:12px;"></i> View
          </a>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <!-- ACTIVITIES -->
  <?php if($type !== 'city' && !empty($activities)): ?>
  <div id="activitiesSection" style="margin-top:var(--space-8);">
    <div class="section-header">
      <h3 style="font-size:var(--text-xl);">
        <i data-lucide="zap" style="width:18px;height:18px;color:var(--accent-orange);display:inline;margin-right:var(--space-2);"></i>
        Activities <span style="font-size:var(--text-sm);color:var(--text-muted);font-weight:normal;">(<?= count($activities) ?>)</span>
      </h3>
      <!-- Category quick-filter -->
      <div style="display:flex;gap:var(--space-2);">
        <?php foreach(['adventure','food','wellness','sightseeing'] as $ac): ?>
        <a href="?<?= http_build_query(array_merge($_GET,['cat'=>$ac,'type'=>'activity'])) ?>" class="tag" style="cursor:pointer;<?= $cat===$ac?'border:1px solid var(--accent-cyan);color:var(--accent-cyan);':'' ?>"><?= ucfirst($ac) ?></a>
        <?php endforeach; ?>
      </div>
    </div>
    <div class="results-grid" id="activitiesGrid">
      <?php foreach($activities as $act):
        $catC=['adventure'=>'#FF6B35','food'=>'#F59E0B','wellness'=>'#10B981','sightseeing'=>'#3B82F6','culture'=>'#A855F7','nightlife'=>'#EC4899'][$act['cat']]??'#94A3B8';
      ?>
      <div class="result-card" data-name="<?= strtolower($act['name'].' '.$act['city']) ?>" data-cat="<?= $act['cat'] ?>" data-cost="<?= $act['cost'] ?>" data-rating="<?= $act['rating'] ?>">
        <div class="card-cover">
          <div class="card-cover-orb" style="width:220px;height:220px;background:<?= $act['img_color'] ?>;top:-60px;right:-60px;opacity:0.4;"></div>
          <div class="card-cover-overlay"></div>
          <div style="position:absolute;bottom:var(--space-3);left:var(--space-4);">
            <p style="font-weight:var(--font-bold);font-size:var(--text-base);margin:0;"><?= e($act['name']) ?></p>
            <p style="font-size:var(--text-xs);color:rgba(241,245,249,0.7);margin:0;"><?= e($act['city']) ?>, <?= e($act['country']) ?></p>
          </div>
          <div style="position:absolute;top:var(--space-3);right:var(--space-3);">
            <span style="font-size:10px;padding:2px 8px;border-radius:9999px;background:<?= $catC ?>20;color:<?= $catC ?>;border:1px solid <?= $catC ?>40;"><?= ucfirst($act['cat']) ?></span>
          </div>
        </div>
        <div class="card-body">
          <p style="font-size:var(--text-sm);color:var(--text-secondary);margin:0 0 var(--space-2);"><?= e($act['desc']) ?></p>
          <div class="act-meta">
            <span style="display:flex;align-items:center;gap:3px;"><i data-lucide="clock" style="width:11px;height:11px;"></i> <?= e($act['duration']) ?></span>
            <span style="display:flex;align-items:center;gap:3px;"><i data-lucide="dollar-sign" style="width:11px;height:11px;"></i> $<?= $act['cost'] ?>/person</span>
          </div>
        </div>
        <div class="card-footer">
          <div class="rating">
            <i data-lucide="star" style="width:14px;height:14px;color:var(--accent-gold);"></i>
            <?= $act['rating'] ?>
          </div>
          <div style="display:flex;gap:var(--space-2);">
            <button class="btn btn-secondary" style="font-size:var(--text-xs);padding:6px 10px;" onclick="addToTrip(<?= $act['id'] ?>,'<?= e($act['name']) ?>')">
              <i data-lucide="plus" style="width:11px;height:11px;"></i> Add
            </button>
            <button class="btn btn-primary" style="font-size:var(--text-xs);padding:6px 14px;" onclick="viewActivity(<?= $act['id'] ?>)">
              <i data-lucide="eye" style="width:11px;height:11px;"></i> View
            </button>
          </div>
        </div>
      </div>
      <?php endforeach; ?>
    </div>
  </div>
  <?php endif; ?>

  <?php if(empty($cities) && empty($activities) && $q): ?>
  <div class="empty-state">
    <i data-lucide="search-x" style="width:56px;height:56px;margin:0 auto var(--space-4);display:block;"></i>
    <h4>No results for "<?= e($q) ?>"</h4>
    <p style="margin:var(--space-2) 0 var(--space-6);">Try a different search term or browse popular destinations.</p>
    <a href="<?= APP_URL ?>/pages/city-search.php" class="btn btn-primary">Browse All</a>
  </div>
  <?php endif; ?>

</main>
</div>

<!-- Activity Detail Modal -->
<div id="actModal" style="display:none;position:fixed;inset:0;background:rgba(11,16,32,0.9);backdrop-filter:blur(8px);z-index:400;align-items:center;justify-content:center;">
  <div style="background:var(--bg-elevated);border:var(--border-light);border-radius:var(--radius-2xl);padding:var(--space-8);max-width:480px;width:90%;">
    <div style="display:flex;justify-content:space-between;align-items:center;margin-bottom:var(--space-4);">
      <h4 id="modalTitle" style="margin:0;"></h4>
      <button onclick="document.getElementById('actModal').style.display='none'" style="background:none;border:none;color:var(--text-muted);cursor:pointer;"><i data-lucide="x" style="width:20px;height:20px;"></i></button>
    </div>
    <p id="modalBody" style="color:var(--text-secondary);font-size:var(--text-sm);"></p>
    <div style="display:flex;gap:var(--space-3);margin-top:var(--space-6);">
      <button class="btn btn-secondary" onclick="document.getElementById('actModal').style.display='none'">Close</button>
      <button class="btn btn-primary" style="flex:1;">Book Now</button>
    </div>
  </div>
</div>

<script>
let currentType = '<?= $type ?>';
const actData = <?= json_encode(array_values($activities)) ?>;

function setType(t) {
  currentType = t;
  document.querySelectorAll('.type-tab').forEach(b => b.classList.toggle('active', b.textContent.toLowerCase() === t || (t==='all' && b.textContent==='All')));
  document.getElementById('citiesSection') && (document.getElementById('citiesSection').style.display = (t==='all'||t==='city') ? '' : 'none');
  document.getElementById('activitiesSection') && (document.getElementById('activitiesSection').style.display = (t==='all'||t==='activity') ? '' : 'none');
}

function liveFilter() {
  const q = document.getElementById('liveSearch').value.toLowerCase();
  document.querySelectorAll('.result-card').forEach(c => {
    c.style.display = (c.dataset.name||'').includes(q) ? '' : 'none';
  });
}

function applyFilters() {}

function viewActivity(id) {
  const a = actData.find(x => x.id === id);
  if(!a) return;
  document.getElementById('modalTitle').textContent = a.name;
  document.getElementById('modalBody').textContent = a.desc + ' · ' + a.city + ', ' + a.country + ' · ' + a.duration + ' · $' + a.cost + '/person';
  const m = document.getElementById('actModal');
  m.style.display = 'flex';
  lucide.createIcons();
}

function addToTrip(id, name) {
  const t = prompt('Add "' + name + '" to which trip? (Enter trip name)');
  if(t) alert('"' + name + '" added to ' + t + '!');
}

lucide.createIcons();
</script>
</body>
</html>
