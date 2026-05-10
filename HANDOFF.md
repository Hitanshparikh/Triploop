# JourneyOS AI — Complete Handoff & Task Guide
## For AI Agents / Teammates to Continue Building

> **Read this entire file carefully before starting any work.**
> This file tells you exactly what has been built, what's remaining, and how to continue.

---

## 🔗 Project Info
- **Repo**: https://github.com/Hitanshparikh/Triploop.git
- **Local path**: `c:\xampp\htdocs\triploop`
- **URL when running**: `http://localhost/triploop`
- **Tech stack**: HTML5 + CSS3 + Vanilla JS + PHP 8+ + MySQL (XAMPP)
- **NO React, NO Node.js, NO TypeScript. Plain PHP + MySQL only.**
- **Reference prompt**: See `prompt.txt` in root for the full product vision

---

## 🎯 What This Project Is
**JourneyOS AI** — "The Emotional Operating System for Travel"
- A hackathon travel platform that must look like a **billion-dollar startup**
- **Dark luxury theme**, glassmorphism, neon glow, cinematic animations
- **Emotionally adaptive** — a Mood Engine changes the UI based on user mood
- Must impress judges in **first 10 seconds**

---

## ✅ WHAT HAS BEEN COMPLETED

### 1. Project Structure (DONE ✅)
All directories created:
```
triploop/
├── assets/css/          ← design-system.css, animations.css, pages/landing.css
├── assets/js/           ← landing.js
├── assets/images/       ← empty, needs generated images
├── assets/fonts/        ← empty (using Google Fonts via CDN)
├── config/              ← config.php, database.php, session.php
├── includes/            ← functions.php (core helpers)
├── database/            ← schema.sql (COMPLETE, 20 tables)
├── api/                 ← empty, needs all API endpoints
├── pages/               ← landing.php, login.php (partial)
├── pages/admin/         ← empty
├── uploads/             ← avatars/, covers/, journal/ with .gitkeep
├── components/          ← empty
├── index.php            ← entry point (redirects to landing or dashboard)
├── .gitignore
└── prompt.txt           ← full requirements doc
```

### 2. Design System (DONE ✅)
**`assets/css/design-system.css`** — Complete with:
- CSS custom properties: colors, gradients, typography, spacing, shadows, borders, z-index
- Background: `#0B1020`, `#121826`; Accents: Electric Cyan `#00D4FF`, Purple `#A855F7`, Orange `#FF6B35`, Blue `#3B82F6`
- Google Fonts: Inter (body), Space Grotesk (headings)
- Glass card component (`.glass-card`)
- Button variants (`.btn-primary`, `.btn-secondary`, `.btn-ghost`, `.btn-lg`, `.btn-xl`)
- Form inputs (`.input-field`, `.input-group`)
- Badges (`.badge-cyan`, `.badge-purple`, `.badge-green`, `.badge-orange`, `.badge-red`)
- Layout utilities (flex, grid, spacing, etc.)
- Custom scrollbar, selection styles

### 3. Animation System (DONE ✅)
**`assets/css/animations.css`** — Complete with:
- Keyframes: fadeIn, fadeInUp/Down/Left/Right, scaleIn, popIn, float, pulse, glow, shimmer, rotate, particleFloat, slideIn/Out, typing
- Utility classes: `.animate-fade-in`, `.animate-float`, `.animate-pulse-glow`, etc.
- Stagger delays: `.delay-100` through `.delay-800`
- Skeleton loading: `.skeleton`, `.skeleton-text`, `.skeleton-card`
- Scroll reveal: `.reveal`, `.reveal-left`, `.reveal-right`, `.reveal-scale` (needs JS observer)
- Hover effects: `.hover-lift`, `.hover-glow`, `.hover-scale`
- Loading spinner: `.spinner`

### 4. Database Schema (DONE ✅ — NOT YET IMPORTED)
**`database/schema.sql`** — 20 tables:
- `users`, `cities`, `trips`, `trip_stops`, `activities`, `itinerary_items`
- `expenses`, `budgets`, `journals`, `packing_lists`
- `collaborators`, `votes`, `comments`, `notifications`
- `ai_suggestions`, `trip_simulations`, `shared_itineraries`
- `bookmarks`, `memories`, `admin_analytics`

> [!IMPORTANT]
> **The database has NOT been imported yet** — MySQL was not running when we tried. Your teammate must:
> 1. Start XAMPP (Apache + MySQL)
> 2. Run: `mysql -u root < database/schema.sql` (or import via phpMyAdmin)
> 3. Then create `database/seed.sql` with demo data and import it

### 5. Config Files (DONE ✅)
- **`config/config.php`** — App settings, DB credentials (root/no password), paths, security constants
- **`config/database.php`** — PDO singleton with `query()`, `fetch()`, `fetchAll()`, `insert()`, `update()`, `delete()` helpers
- **`config/session.php`** — Secure sessions, CSRF token generation/validation, `isLoggedIn()`, `currentUser()`, `isAdmin()`, `loginUser()`, `logoutUser()`, flash messages, `e()` for XSS prevention

### 6. Helper Functions (DONE ✅)
**`includes/functions.php`** — Includes:
- `redirect()`, `jsonResponse()`, `requireAuth()`, `requireAdmin()`
- `isAjaxRequest()`, `input()`, `formatCurrency()`, `formatDate()`, `timeAgo()`
- `generateToken()`, `uploadFile()`, `truncate()`, `getMoodTheme()`

### 7. Landing Page (DONE ✅)
**`pages/landing.php`** + **`assets/css/pages/landing.css`** + **`assets/js/landing.js`**
- Fixed nav with scroll effect
- Hero section with floating orbs, particle system, gradient text, animated counters
- Features grid (6 cards with glassmorphism)
- Mood Engine interactive demo (8 moods with live preview)
- Trip Simulation section with health score ring and metric bars
- Collaboration section
- CTA section with glowing card
- Footer
- Fully responsive
- Scroll reveal animations, smooth scroll

### 8. Login Page (DONE ✅ — UI only, backend NOT connected)
**`pages/login.php`**
- Glassmorphism auth card with animated background orbs
- Google OAuth button (visual only)
- Email/password form with CSRF token
- Remember me + forgot password link
- Flash message display
- Responsive
- **BUT**: The form submits to `api/auth.php` which does NOT exist yet

---

## ❌ WHAT NEEDS TO BE BUILT (in priority order)

### PRIORITY 1: Make the app functional (Auth + Dashboard + Trips)

#### 1.1 Seed Data — `database/seed.sql` (CRITICAL)
Create realistic demo data:
- **3 users**: admin (admin@journeyos.ai / password123), demo user (demo@journeyos.ai / password123), collaborator
- **8-10 cities**: Tokyo, Paris, Bali, New York, London, Barcelona, Dubai, Santorini, Rome, Kyoto — with real lat/lng, descriptions, cost_index, popularity scores, weather_data JSON
- **50+ activities** across all cities — varied categories (sightseeing, food, adventure, culture, etc.), with costs, ratings, durations, budget_labels
- **3-4 sample trips** with full itineraries, stops, expenses, budgets, journal entries
- **AI suggestions** and **trip simulations** for demo trips
- **Admin analytics** sample data
- **Notifications** for demo users

#### 1.2 Auth API — `api/auth.php` (CRITICAL)
Handle POST requests with `action` parameter:
- `action=login`: Validate email/password with `password_verify()`, call `loginUser()`, redirect to dashboard
- `action=signup`: Validate inputs, check email uniqueness, `password_hash()`, insert user, auto-login, redirect
- `action=logout`: Call `logoutUser()`, redirect to landing
- `action=forgot_password`: Generate reset token, store in DB (in real app would email, for demo just show token)
- `action=reset_password`: Validate token + expiry, update password
- All actions must validate CSRF token
- All SQL via prepared statements

#### 1.3 Signup Page — `pages/signup.php`
- Same glassmorphism auth card style as login
- Fields: name, email, password, confirm password
- CSRF token
- Form submits to `api/auth.php` with `action=signup`
- Link to login page

#### 1.4 Forgot Password Page — `pages/forgot-password.php`
- Simple email input form
- Glassmorphism style matching auth pages

#### 1.5 Shared Includes — `includes/header.php`, `includes/sidebar.php`, `includes/footer.php`
**header.php**: Common HTML head with all CSS includes, meta tags
**sidebar.php**: App sidebar navigation (for all authenticated pages):
- Logo
- Nav items: Dashboard, My Trips, Create Trip, City Search, Activity Search, Budget, Journal, Packing
- Admin section (if admin role)
- User avatar + name at bottom
- Collapsible on mobile (hamburger menu)
- Active state highlighting based on current page
- Style: glassmorphism sidebar, 280px wide, fixed position

**footer.php**: Close HTML tags, include common JS files

#### 1.6 Dashboard — `pages/dashboard.php` (HIGH PRIORITY)
The main app page after login. Must include:
- Sidebar (include sidebar.php)
- **Welcome banner** with user name and greeting based on time of day
- **Recent trips** — 3-4 trip cards with cover images, dates, mood badge, progress
- **Quick actions** — buttons for Create Trip, Search Cities, AI Suggestions
- **Travel stats** — animated counters: trips count, countries visited, days traveled, total budget
- **Trending cities** — horizontal scroll carousel of city cards
- **AI recommendations** panel — simulated AI suggestions
- **Trip health summaries** — mini gauges for active trips
- **Upcoming trips** timeline
- All data fetched from database via PHP
- Responsive layout: sidebar + main content area
- Page-specific CSS in `assets/css/pages/dashboard.css`

#### 1.7 Create Trip — `pages/create-trip.php`
Multi-step wizard form:
- **Step 1**: Trip name, description, cover image upload
- **Step 2**: Date picker (start/end date), travel type selector (solo/couple/family/friends/business)
- **Step 3**: Mood selector (8 moods with visual preview, same as landing page mood engine)
- **Step 4**: Budget setting, AI trip generation option
- Submits to `api/trips.php`
- Each step has animated transitions
- Progress indicator at top

#### 1.8 Trips API — `api/trips.php`
- `action=create`: Insert trip, redirect to itinerary builder
- `action=update`: Update trip details
- `action=delete`: Delete trip (with confirmation)
- `action=share`: Generate share_token, toggle is_public
- `action=list`: Return user's trips as JSON (for AJAX)

#### 1.9 My Trips — `pages/my-trips.php`
- Grid of trip cards with cover images
- Each card: trip name, dates, mood badge, status badge, progress bar, action buttons (edit/delete/share)
- Hover animations (lift + glow)
- Filter by status (planning/active/completed)
- Empty state if no trips
- Links to itinerary builder/view

### PRIORITY 2: Itinerary & Budget (Core Features)

#### 2.1 Itinerary Builder — `pages/itinerary-builder.php`
- Drag-and-drop interface using vanilla JS (HTML5 Drag and Drop API)
- Left panel: list of cities/stops in the trip
- Center: timeline view with day columns
- Right panel: activity search/add
- Drag activities onto day slots
- Reorder stops and activities
- Add notes per item
- Auto-save via AJAX to `api/itinerary.php`
- Calendar timeline toggle
- AI optimization suggestions panel
- CSS: `assets/css/pages/itinerary.css`
- JS: `assets/js/drag-drop.js`

#### 2.2 Itinerary View — `pages/itinerary-view.php`
- Beautiful read-only cinematic timeline
- Grouped by city/stop
- Each day shows activities with times, cost tags, duration
- Transportation visualization between cities (icons: ✈️🚂🚗)
- Toggle: list view vs calendar view
- Share button
- Export to PDF option

#### 2.3 Itinerary API — `api/itinerary.php`
- CRUD for itinerary items
- Reorder items (update order_index)
- Add/remove activities
- Update status (planned/completed/skipped)

#### 2.4 Budget Dashboard — `pages/budget.php`
**MUST follow the SVG wireframe layout** (`Traveloop - 8 hours.svg` in root):
- **Budget insights panel** at top: total budget, total spent, remaining
- **Charts**: Use Chart.js (include via CDN)
  - Pie chart: spending by category
  - Line chart: daily spending trend
  - Bar chart: budget vs actual per category
- **Expense list** table with add/edit/delete
- **Analytics cards**: avg daily spend, biggest expense, budget health score
- **Export buttons**: Download PDF, Export CSV
- **Overbudget alerts** with visual warnings
- API: `api/budget.php` for expense CRUD
- CSS: `assets/css/pages/budget.css`
- JS: `assets/js/charts.js`

### PRIORITY 3: Discovery & Content

#### 3.1 City Search — `pages/city-search.php`
- Search input with autocomplete (AJAX to `api/cities.php`)
- City cards grid: image, name, country, popularity bar, cost index badge, weather preview
- Filters: continent, cost level, popularity
- Click to view city details + activities

#### 3.2 Activity Search — `pages/activity-search.php`
- Category filter tabs (sightseeing, food, adventure, culture, etc.)
- Activity cards: image, name, duration, rating stars, cost, budget label
- Add to trip button (select which trip)
- Search input with filtering

#### 3.3 Search APIs — `api/cities.php`, `api/activities.php`
- Search by name (LIKE query)
- Filter by category, cost, popularity
- Return JSON for AJAX

### PRIORITY 4: Social & Journal

#### 4.1 Packing Checklist — `pages/packing.php`
- Categories: Essentials, Clothing, Electronics, Toiletries, Documents, Other
- Checklist items with check/uncheck animations (checkbox → strikethrough)
- Add custom items
- Template suggestions based on trip type/destination
- API: `api/packing.php`

#### 4.2 Trip Journal — `pages/journal.php`
- List of journal entries for a trip
- Create/edit entries: title, content (textarea with basic markdown), mood selector, image upload
- Timestamps
- AI Memory Summary button (simulated: generates a summary paragraph from entry titles)
- API: `api/journal.php`

#### 4.3 Shared Trip — `pages/shared-trip.php`
- Public page (no auth required)
- Takes `?token=xxx` parameter
- Beautiful read-only itinerary layout
- Trip name, dates, mood, cities visited
- Copy itinerary button
- Social sharing links (WhatsApp, Twitter, copy link)
- View counter

#### 4.4 User Profile — `pages/profile.php`
- Editable name, email
- Avatar upload
- Preferences JSON (preferred mood, currency)
- Dark/light theme toggle (store in preferences)
- Saved destinations (from bookmarks table)
- Change password form
- API: `api/profile.php`

### PRIORITY 5: AI & Intelligence Systems

#### 5.1 Mood Engine — `assets/js/mood-engine.js`
- Global JS module that reads the current trip's mood
- Dynamically overrides CSS custom properties (--accent-cyan → mood color)
- Changes animation speeds
- Updates recommendation content
- Applies to all authenticated pages via sidebar/header include
- 8 mood themes defined in `includes/functions.php` → `getMoodTheme()` (already done)

#### 5.2 AI Companion — `assets/js/ai-companion.js`
- Floating chat bubble (bottom-right corner)
- Click to expand chat panel
- Pre-written contextual responses based on current page:
  - Dashboard: "Welcome back! You have 2 trips coming up."
  - Itinerary: "Looks like Day 3 is packed. Want me to suggest a lighter schedule?"
  - Budget: "You're 15% over budget on food. Consider local street food markets."
- Typing animation effect
- Glassmorphism chat panel
- Include on all authenticated pages

#### 5.3 Trip Simulation — `api/ai.php`
- `action=simulate`: Calculate scores based on trip data:
  - stress_score: based on number of activities per day
  - fatigue_score: based on travel distances and walking activities
  - budget_burn_rate: actual vs allocated budget percentage
  - weather_risk: random 10-50 (simulated)
  - crowd_intensity: based on city popularity
  - health_score: weighted average of all scores
  - suggestions: array of optimization tips
- Store in `trip_simulations` table
- Return JSON for frontend display

#### 5.4 AI Suggestions — Part of `api/ai.php`
- `action=suggest`: Generate contextual suggestions for a trip:
  - Hidden gems (random activities with high rating, low cost)
  - Pacing recommendations (detect overloaded days)
  - Budget tips
- Store in `ai_suggestions` table

### PRIORITY 6: Admin & Analytics

#### 6.1 Admin Dashboard — `pages/admin/dashboard.php`
- Admin-only page (use `requireAdmin()`)
- **Metrics cards**: Total users, total trips, active trips, total revenue
- **Charts** (Chart.js):
  - User signups over time (line chart)
  - Trips by mood (pie chart)
  - Top 10 destinations (bar chart)
  - Daily active users (line chart)
- **Recent activity** feed
- **Top cities** table with popularity/trip counts
- **Engagement stats**: avg trips per user, avg activities per trip
- CSS: `assets/css/pages/admin.css`

### PRIORITY 7: Polish & Final

#### 7.1 Responsive Design
- All pages must work on mobile (< 768px), tablet (768-1024px), desktop (> 1024px)
- Mobile: sidebar becomes bottom nav or hamburger menu
- CSS file: `assets/css/responsive.css` (create if needed, or add to page-specific CSS)

#### 7.2 Components CSS — `assets/css/components.css`
- Sidebar component styles
- Modal component
- Toast/notification component
- Card variants
- Table styles
- Pagination
- Progress bars
- Gauge/meter components

#### 7.3 Empty States & Loading
- Every page needs an empty state (when no data): illustration + message + CTA
- Every data-loading section needs skeleton loading animation
- Error states for failed API calls

#### 7.4 Upload Handler — `api/upload.php`
- Handle avatar, cover image, journal photo uploads
- Validate file type and size (see config.php constants)
- Generate unique filenames
- Return JSON with file URL
- The `uploadFile()` function in functions.php already handles the logic

#### 7.5 Notification System — `assets/js/notifications.js`
- Toast notifications (top-right corner)
- Show on: successful save, errors, AI suggestions
- Auto-dismiss after 5 seconds
- Notification bell in sidebar with unread count

---

## 🎨 Design Reference
- **Primary prompt**: `prompt.txt` in repo root
- **SVG wireframe**: `Traveloop - 8 hours.svg` — use for Budget Dashboard and Admin Dashboard layout structure
- **Color system**: All defined in `assets/css/design-system.css` root variables
- **Typography**: Inter (body), Space Grotesk (headings) — loaded via Google Fonts in each page's `<head>`
- **Design philosophy**: Apple-level UX, Airbnb travel experience, Linear smoothness, Arc Browser futurism

---

## 🛠️ How to Set Up & Run
1. Clone repo: `git clone https://github.com/Hitanshparikh/Triploop.git`
2. Place in `c:\xampp\htdocs\triploop\`
3. Start XAMPP (Apache + MySQL)
4. Import database: Open phpMyAdmin → Import → select `database/schema.sql`
5. Create and import `database/seed.sql` with demo data
6. Visit: `http://localhost/triploop`

---

## 📝 Key Architecture Notes
- **All SQL must use prepared statements** (PDO) — see `config/database.php`
- **Use `db()` helper** to get database instance: `db()->fetch("SELECT...", [$param])`
- **Use `e()` for all output** to prevent XSS: `<?= e($user['name']) ?>`
- **Use `csrfField()` in all forms** and `validateCsrfToken()` in API handlers
- **Use `requireAuth()` at top of protected pages**
- **Use `requireAdmin()` at top of admin pages**
- **JSON responses from APIs**: `jsonResponse(['success' => true, 'data' => $data])`
- **File uploads**: Use `uploadFile($file, 'avatars')` helper
- **Mood themes**: Use `getMoodTheme($mood)` to get color/gradient/icon for a mood

---

## 🚀 Start Here
**If you're continuing this project, start with:**
1. Create `database/seed.sql` and import both SQL files
2. Build `api/auth.php`
3. Build `pages/signup.php`
4. Build `includes/header.php` and `includes/sidebar.php`
5. Build `pages/dashboard.php`
6. Then work through Priority 2-7 in order

**The goal: Every page must feel alive, futuristic, cinematic, and premium. Not a student project. A real startup.**
