# JourneyOS AI - Project Handoff & Implementation Plan

## 1. Project Overview
**JourneyOS AI** is a premium, AI-driven travel operating system built using a classic LAMP stack (Vanilla PHP 8+, MySQL, Vanilla CSS/JS). It enables users to dynamically generate itineraries, track budgets, manage packing lists, and share their travel experiences in a community feed.

The goal is to create a "Trillion Dollar" UX aesthetic—often referred to as the **Dark Luxury / Arc-Browser / Linear** aesthetic. The design relies on deep zinc/slate dark modes, extremely high-contrast typography, subtle glassmorphism (`backdrop-filter: blur()`), vibrant but controlled accent colors (cyan, purple, emerald), and absolute minimalism (1px borders, smooth spring animations).

**CRITICAL RULE FOR ALL FUTURE DEVELOPMENT:**
*   **NO EMOJIS ALLOWED.** Anywhere. All visual indicators must be Lucide SVG icons.
*   **NO FRAMEWORKS.** The project uses strictly Vanilla PHP, Vanilla CSS, and Vanilla JavaScript. No Tailwind, no React, no Laravel.

---

## 2. Current Status & What Has Been Completed

The primary objective of the last phase was to overhaul the frontend UI/UX from an "AI-generated generic" look to a premium dark mode aesthetic, while simultaneously ripping out hardcoded demo data and replacing it with real database fetches. 

**Completed Milestones:**
1.  **Core Design System (`assets/css/design-system.css`)**: 
    *   Implemented deep dark themes (`--bg-primary: #09090b`), glassmorphism (`.glass-card`), and glowing accents (`--gradient-cyan`).
    *   Unified all buttons, form fields, and modal dialogs to look cohesive and premium.
2.  **Database Integration & Seeding (`setup.php` & `config/journeyos.sql`)**:
    *   Created `itinerary_sections`, `journal`, `trip_expenses`, `packing_lists`, `trips`, and `users` tables.
    *   Created a robust seeding script to populate the DB with realistic demo data.
3.  **Dashboard Overhaul (`pages/dashboard.php`)**:
    *   Fetches real stats (total trips, days traveled, upcoming trip countdown).
    *   Styled with premium stat cards and dynamic AI insight banners.
4.  **Itinerary View & Builder (`pages/itinerary-view.php`, `pages/itinerary-builder.php`)**:
    *   Fully dynamic timeline fetching `itinerary_sections` from the database.
    *   Displays start/end dates, allocated budgets, statuses, and grouped sections by day.
    *   Builder mode supports drag-and-drop reordering (UI only) and real-time budget calculation.
5.  **Budgeting Engine (`pages/budget.php`)**:
    *   Integrates **Chart.js** with real DB data from `trip_expenses`.
    *   Aggregates costs by category and plots daily spending trends.
6.  **Journal / Trip Notes (`pages/journal.php`)**:
    *   Dynamic feed of trip notes fetching from the `journal` table.
    *   Sidebar allows filtering by "Day" and "Location" dynamically generated from the DB records.
7.  **Community Feed (`pages/community.php`)**:
    *   Fetches public trips and displays them in a gorgeous social-feed UI.
    *   Includes filters (Trending, Recent, Top Rated) and mock engagement actions (Like, Share).
8.  **Packing Checklist (`pages/packing.php`)**:
    *   Fetches dynamic categories and JSON-encoded items from `packing_lists`.
    *   Interactive checkboxes that update a master progress bar.
    *   AJAX logic integrated with `/api/packing.php`.

---

## 3. From Where To Start (Next Steps)

While the frontend is highly polished and successfully queries the database (`SELECT` statements), the **backend logic (`INSERT`, `UPDATE`, `DELETE`) and API endpoints** are largely stubbed out or missing. 

**The primary focus for the next developer/AI is API Development and CRUD functionality.**

When you start working, your immediate focus should be:
1.  Navigate to the `api/` directory.
2.  Implement the PHP backend logic to process form submissions and AJAX requests.
3.  Ensure data integrity and proper redirection after form submission.

---

## 4. Full Task List (What To Work On Next)

This is the comprehensive, step-by-step checklist to make JourneyOS AI 100% functional and production-ready. 

### Phase 1: Core API & CRUD Operations ⏳ (Immediate Priority)
- [ ] **`api/itinerary.php`**:
  - Implement `action=save` to loop through the `$_POST['sections']` array and `INSERT`/`UPDATE` `itinerary_sections` in the database.
  - Implement drag-and-drop order saving (update `order_index` based on the array order).
  - Implement `action=delete` to remove a specific section.
- [ ] **`api/journal.php`**:
  - Implement `action=create` to insert a new journal note.
  - Implement `action=delete` to remove a note.
  - Ensure CSRF tokens are validated on all requests.
- [ ] **`api/budget.php`**:
  - The form in `pages/itinerary-view.php` (Add Expense Modal) posts to `/api/budget.php`. Implement the `INSERT INTO trip_expenses` logic.
  - Recalculate and update the main `trips.budget_spent` total whenever a new expense is added or deleted.
- [ ] **`api/trips.php`**:
  - Implement the "Create Trip" wizard backend logic. 
  - Save the trip to the database, generate a unique `share_token`, and redirect the user to `pages/dashboard.php`.
- [ ] **`api/packing.php`**:
  - Complete the `action=save` logic to encode the checklist array as JSON and update the `packing_lists.items` column.
  - Implement `action=init` to generate default categories (Documents, Clothing, Toiletries, Electronics) for a new trip if none exist.

### Phase 2: Frontend Logic Polish 💅
- [ ] **Create Trip Wizard (`pages/create-trip.php`)**:
  - The multi-step form currently has some UI quirks. Ensure smooth JavaScript transitions between Step 1 (Destination), Step 2 (Dates), Step 3 (Preferences), and Step 4 (Summary).
  - Connect the final submit button to `/api/trips.php`.
- [ ] **Profile Settings (`pages/profile.php`)**:
  - Update user details (Name, Bio, Preferred Currency).
  - Handle avatar image uploads (ensure it saves securely to `uploads/avatars/`).
- [ ] **Shared Trip View (`pages/shared-trip.php`)**:
  - Build a read-only version of the `itinerary-view.php` layout that loads based on a public `share_token` rather than a logged-in `user_id`.
  - Strip out all "Edit", "Delete", and "Add" buttons for this public view.
- [ ] **Dynamic AI Insights**:
  - Throughout the app (Dashboard, Budget, Itinerary), there are "AI Tips" hardcoded in the HTML. 
  - Implement a helper function `generateAiInsight($context, $tripData)` that conditionally renders tips based on real data (e.g., if `$trip['budget_spent'] > $trip['budget_total']`, output a warning about overspending).

### Phase 3: Edge Cases, Security & QA 🔒
- [ ] **Form Validation**: Add strict PHP validation to all API endpoints. Never trust user input. Use prepared statements for absolutely everything (already handled by the `db()` helper, but ensure variables are correctly bound).
- [ ] **Auth Enforcement**: Ensure `requireAuth()` is present at the top of every single page in the `/pages/` directory (except public shared links).
- [ ] **Empty States**: Verify that every page handles the "0 Trips" or "0 Notes" state gracefully. The UI components are there, but ensure the PHP logic accurately triggers them when count == 0.
- [ ] **Mobile Responsiveness**:
  - The current UI relies heavily on CSS Grid and a fixed 260px sidebar. 
  - On viewports < 1024px, the sidebar needs to collapse into a hamburger menu. Add a JS toggle and CSS media queries to handle this.
  - Ensure tables and charts (in `budget.php`) overflow-x scroll gracefully on mobile devices.

---

## 5. Quick Code Reference & Guidelines

*   **Database Access**: Always use the built-in helper.
    ```php
    // Fetch one row
    $trip = db()->fetch("SELECT * FROM trips WHERE id = ?", [$id]);
    
    // Fetch many rows
    $notes = db()->fetchAll("SELECT * FROM journal WHERE trip_id = ?", [$tripId]);
    
    // Execute INSERT/UPDATE
    db()->query("INSERT INTO trips (user_id, name) VALUES (?, ?)", [$userId, $name]);
    ```
*   **Security Outputs**: Always wrap output in the escape function `e()` to prevent XSS.
    ```php
    <h3><?= e($trip['name']) ?></h3>
    ```
*   **Flash Messages**: Use the built-in flash system for notifications.
    ```php
    setFlash('success', 'Trip created successfully.');
    redirect('/pages/dashboard.php');
    ```
*   **Icons**: We use Lucide. Ensure you call `lucide.createIcons();` at the bottom of the page or after making DOM updates via JavaScript. Do not use FontAwesome or emojis.

---
**End of Handoff Document.**
AI/Developer: Parse this document, begin with Phase 1 (Core API & CRUD Operations), and work your way down the task list systematically. Maintain the existing architecture and design constraints at all times.
