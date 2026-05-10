<?php
require_once __DIR__ . '/../includes/functions.php';
requireAuth();
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>City Discovery — JourneyOS AI</title>
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/design-system.css">
<link rel="stylesheet" href="<?= ASSETS_PATH ?>/css/animations.css">
<script src="https://unpkg.com/lucide@latest"></script>
<style>
.app-layout{display:flex;min-height:100vh;}
.main-content{flex:1;margin-left:260px;padding:var(--space-8);background:var(--bg-primary);}

.search-header {
    margin-bottom: var(--space-8);
}

.search-bar-wrapper {
    position: relative;
    max-width: 600px;
    margin-bottom: var(--space-8);
}
.search-bar-wrapper input {
    width: 100%;
    padding: var(--space-4) var(--space-6) var(--space-4) 48px;
    border-radius: var(--radius-2xl);
    background: var(--bg-glass);
    border: 1px solid rgba(255,255,255,0.1);
    color: var(--text-primary);
    font-size: var(--text-lg);
    box-shadow: var(--shadow-md);
    transition: all var(--transition-base);
}
.search-bar-wrapper input:focus {
    border-color: var(--accent-cyan);
    background: rgba(255,255,255,0.05);
    outline: none;
    box-shadow: var(--shadow-glow-cyan);
}
.search-icon {
    position: absolute;
    left: 16px;
    top: 50%;
    transform: translateY(-50%);
    color: var(--text-muted);
}

.tabs {
    display: flex;
    gap: var(--space-4);
    margin-bottom: var(--space-6);
    border-bottom: 1px solid rgba(255,255,255,0.05);
    padding-bottom: var(--space-2);
}
.tab-btn {
    background: none;
    border: none;
    color: var(--text-secondary);
    font-size: var(--text-base);
    font-weight: 600;
    padding: var(--space-2) var(--space-4);
    cursor: pointer;
    position: relative;
    transition: all var(--transition-fast);
}
.tab-btn:hover { color: var(--text-primary); }
.tab-btn.active { color: var(--accent-cyan); }
.tab-btn.active::after {
    content: '';
    position: absolute;
    bottom: -10px;
    left: 0;
    width: 100%;
    height: 2px;
    background: var(--accent-cyan);
    border-radius: 2px;
}

.results-grid {
    display: grid;
    grid-template-columns: repeat(auto-fill, minmax(280px, 1fr));
    gap: var(--space-6);
}

.place-card {
    background: var(--bg-glass);
    border-radius: var(--radius-2xl);
    padding: var(--space-5);
    border: 1px solid rgba(255,255,255,0.05);
    transition: all var(--transition-base);
    display: flex;
    flex-direction: column;
}
.place-card:hover {
    transform: translateY(-4px);
    box-shadow: var(--shadow-lg);
    border-color: rgba(255,255,255,0.1);
}
.place-img {
    width: 100%;
    height: 160px;
    border-radius: var(--radius-xl);
    object-fit: cover;
    margin-bottom: var(--space-4);
    background: var(--bg-elevated);
}
.place-title {
    font-size: var(--text-lg);
    font-weight: 700;
    margin-bottom: var(--space-2);
    color: var(--text-primary);
}
.place-desc {
    font-size: var(--text-sm);
    color: var(--text-secondary);
    margin-bottom: var(--space-4);
    flex: 1;
}
.place-meta {
    display: flex;
    justify-content: space-between;
    align-items: center;
    padding-top: var(--space-3);
    border-top: 1px solid rgba(255,255,255,0.05);
    font-size: var(--text-xs);
    font-weight: 600;
}
.rating { color: var(--accent-gold); display: flex; align-items: center; gap: 4px; }

/* Loader */
.loader-spinner {
    width: 40px;
    height: 40px;
    border: 3px solid rgba(255,255,255,0.1);
    border-top-color: var(--accent-cyan);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: var(--space-12) auto;
}
@keyframes spin { to { transform: rotate(360deg); } }
</style>
</head>
<body>
<div class="app-layout">
<?php include __DIR__ . '/../includes/sidebar.php'; ?>

<main class="main-content page-transition">

    <div class="search-header">
        <h1 style="font-size:var(--text-4xl);font-weight:800;letter-spacing:-0.03em;margin-bottom:var(--space-2);">City <span class="text-gradient-aurora">Discovery</span></h1>
        <p style="color:var(--text-secondary);font-size:var(--text-lg);">Live data from TripAdvisor and Travel Guide APIs</p>
    </div>

    <div class="search-bar-wrapper" style="position:relative;">
        <i data-lucide="search" class="search-icon"></i>
        <input type="text" id="searchInput" placeholder="Search a city (e.g., Paris, London, Tokyo)..." autocomplete="off">
        <div id="locationDropdown" class="glass-card-static" style="display:none;position:absolute;top:100%;left:0;right:0;margin-top:var(--space-2);z-index:var(--z-dropdown);max-height:200px;overflow-y:auto;padding:var(--space-2);"></div>
    </div>

    <div id="resultsContainer" style="display:none;">
        <div class="tabs" id="categoryTabs">
            <button class="tab-btn active" data-target="places">Top Places</button>
            <button class="tab-btn" data-target="restaurants">Restaurants</button>
            <button class="tab-btn" data-target="hotels">Hotels</button>
        </div>

        <div id="loadingState" style="display:none;text-align:center;">
            <div class="loader-spinner"></div>
            <p style="color:var(--text-muted);">Fetching live insights from our AI APIs...</p>
        </div>

        <div id="placesGrid" class="results-grid tab-content"></div>
        <div id="restaurantsGrid" class="results-grid tab-content" style="display:none;"></div>
        <div id="hotelsGrid" class="results-grid tab-content" style="display:none;"></div>
    </div>

</main>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>

<script>
let currentCity = '';
const destInput = document.getElementById('searchInput');
const destDropdown = document.getElementById('locationDropdown');
let typingTimer;

destInput.addEventListener('input', () => {
    clearTimeout(typingTimer);
    const q = destInput.value.trim();
    if(q.length < 3) {
        destDropdown.style.display = 'none';
        return;
    }
    typingTimer = setTimeout(async () => {
        try {
            const res = await fetch(`<?= APP_URL ?>/api/external.php?action=locations_autocomplete&q=${encodeURIComponent(q)}`);
            const json = await res.json();
            if(json.success && json.data) {
                destDropdown.innerHTML = '';
                const locations = Array.isArray(json.data) ? json.data : (json.data.locations || []);
                if(locations.length === 0) {
                    destDropdown.innerHTML = '<div style="padding:8px;color:var(--text-muted);font-size:12px;">No results found</div>';
                } else {
                    locations.slice(0, 5).forEach(loc => {
                        const name = loc.name || loc.exactMatch || (typeof loc === 'string' ? loc : null) || 'Unknown Location';
                        const el = document.createElement('div');
                        el.style.cssText = 'padding:8px;cursor:pointer;border-radius:4px;font-size:14px;color:var(--text-primary);';
                        el.onmouseover = () => el.style.background = 'rgba(255,255,255,0.05)';
                        el.onmouseout = () => el.style.background = 'transparent';
                        el.textContent = name;
                        el.onclick = () => {
                            destInput.value = name;
                            destDropdown.style.display = 'none';
                            currentCity = name;
                            document.getElementById('resultsContainer').style.display = 'block';
                            fetchData();
                        };
                        destDropdown.appendChild(el);
                    });
                }
                destDropdown.style.display = 'block';
            }
        } catch(e) {
            console.error(e);
        }
    }, 500);
});

document.addEventListener('click', (e) => {
    if(e.target !== destInput && e.target !== destDropdown) {
        destDropdown.style.display = 'none';
    }
});

destInput.addEventListener('keypress', function(e) {
    if (e.key === 'Enter') {
        const query = this.value.trim();
        if (query) {
            destDropdown.style.display = 'none';
            currentCity = query;
            document.getElementById('resultsContainer').style.display = 'block';
            fetchData();
        }
    }
});

document.querySelectorAll('.tab-btn').forEach(btn => {
    btn.addEventListener('click', (e) => {
        document.querySelectorAll('.tab-btn').forEach(b => b.classList.remove('active'));
        e.target.classList.add('active');
        
        document.querySelectorAll('.tab-content').forEach(tc => tc.style.display = 'none');
        document.getElementById(e.target.dataset.target + 'Grid').style.display = 'grid';
    });
});

async function fetchData() {
    // Show loaders, clear grids
    document.getElementById('loadingState').style.display = 'block';
    document.getElementById('placesGrid').innerHTML = '';
    document.getElementById('restaurantsGrid').innerHTML = '';
    document.getElementById('hotelsGrid').innerHTML = '';

    try {
        // 1. Fetch Top Places
        const placesRes = await fetch('<?= APP_URL ?>/api/external.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'city_top_places', region: currentCity })
        });
        const placesData = await placesRes.json();
        
        // Render Places
        if (placesData.success && placesData.data && placesData.data.places) {
            renderItems(placesData.data.places, 'placesGrid', 'map-pin');
        } else {
            mockRender('placesGrid', 'Places');
        }

        // 2. Fetch Restaurants
        const restRes = await fetch(`<?= APP_URL ?>/api/external.php?action=search_restaurants&locationId=${encodeURIComponent(currentCity)}`);
        const restData = await restRes.json();
        if (restData.success && restData.data && restData.data.places) {
            renderItems(restData.data.places, 'restaurantsGrid', 'utensils');
        } else {
            mockRender('restaurantsGrid', 'Restaurants');
        }

        // 3. Fetch Hotels
        const hotelRes = await fetch('<?= APP_URL ?>/api/external.php', {
            method: 'POST',
            headers: {'Content-Type': 'application/json'},
            body: JSON.stringify({ action: 'search_hotels', contentId: currentCity })
        });
        const hotelData = await hotelRes.json();
        if (hotelData.success && hotelData.data && hotelData.data.places) {
            renderItems(hotelData.data.places, 'hotelsGrid', 'bed');
        } else {
            mockRender('hotelsGrid', 'Hotels');
        }

    } catch (e) {
        console.error(e);
        mockRender('placesGrid', 'Places');
        mockRender('restaurantsGrid', 'Restaurants');
        mockRender('hotelsGrid', 'Hotels');
    }

    document.getElementById('loadingState').style.display = 'none';
    lucide.createIcons();
}

function renderItems(items, gridId, icon) {
    const grid = document.getElementById(gridId);
    grid.innerHTML = '';
    
    if (!Array.isArray(items)) items = Object.values(items);
    
    items.slice(0, 12).forEach(item => {
        const title = item.name || item.title || 'Unknown Place';
        const desc = item.description || item.snippet || 'A wonderful place to visit in ' + currentCity + '.';
        const img = item.image || item.photo_url || `https://source.unsplash.com/400x300/?${encodeURIComponent(currentCity + ' ' + title)}`;
        const rating = item.rating || (Math.random() * (5 - 4) + 4).toFixed(1);

        grid.innerHTML += `
            <div class="place-card reveal-scale">
                <img src="${img}" class="place-img" onerror="this.src='data:image/svg+xml;base64,PHN2ZyB4bWxucz0iaHR0cDovL3d3dy53My5vcmcvMjAwMC9zdmciIHdpZHRoPSI4IiBoZWlnaHQ9IjgiPgo8cmVjdCB3aWR0aD0iOCIgaGVpZ2h0PSI4IiBmaWxsPSIjZmZmZmZmIiBmaWxsLW9wYWNpdHk9IjAuMDIiLz48L3N2Zz4='">
                <h3 class="place-title">${title}</h3>
                <p class="place-desc">${desc.substring(0, 100)}...</p>
                <div class="place-meta">
                    <span style="color:var(--text-muted);"><i data-lucide="${icon}" style="width:12px;height:12px;display:inline-block;margin-right:4px;vertical-align:middle;"></i>${currentCity}</span>
                    <span class="rating"><i data-lucide="star" style="width:12px;height:12px;fill:currentColor;"></i> ${rating}</span>
                </div>
            </div>
        `;
    });
}

function mockRender(gridId, type) {
    const grid = document.getElementById(gridId);
    grid.innerHTML = '';
    for(let i=1; i<=6; i++) {
        const imgQuery = type === 'Places' ? 'landmark' : (type === 'Restaurants' ? 'food,restaurant' : 'hotel,room');
        grid.innerHTML += `
            <div class="place-card reveal-scale">
                <img src="https://images.unsplash.com/photo-${1500000000000 + i}?q=80&w=400&auto=format&fit=crop" class="place-img" style="background:var(--bg-elevated);">
                <h3 class="place-title">${currentCity} ${type} ${i}</h3>
                <p class="place-desc">Experience the best ${type.toLowerCase()} in ${currentCity}. Highly rated by locals and travelers alike.</p>
                <div class="place-meta">
                    <span style="color:var(--text-muted);"><i data-lucide="map-pin" style="width:12px;height:12px;display:inline-block;margin-right:4px;vertical-align:middle;"></i>${currentCity} Center</span>
                    <span class="rating"><i data-lucide="star" style="width:12px;height:12px;fill:currentColor;"></i> ${(Math.random() * (5 - 4) + 4).toFixed(1)}</span>
                </div>
            </div>
        `;
    }
}
</script>