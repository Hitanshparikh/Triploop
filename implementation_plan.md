# Implementation Plan

## Goal Description
The objective is to finalize the JourneyOS AI platform. The frontend UI has been completely overhauled to the "Dark Luxury" aesthetic. The database schema has been initialized and seeded. The frontend screens now fetch data from the database. The remaining work involves implementing the backend CRUD operations and finalizing the frontend logic for full functionality.

## Current State
- UI styling (design-system.css) completed and applied to all pages.
- Pages completed: Dashboard, Itinerary View, Itinerary Builder, Budget, Journal, Community, Packing Checklist.
- Database (`triploop` on XAMPP) created and seeded with test data via `setup.php`.
- Frontend reads from the database.

## Proposed Changes

### [API Endpoints / Backend Logic]
Implement the PHP backend logic to process form submissions and AJAX requests.

#### [NEW] `api/itinerary.php`
- Implement `action=save` to `INSERT`/`UPDATE` `itinerary_sections`.
- Implement `action=delete` to remove sections.

#### [NEW] `api/journal.php`
- Implement `action=create` to insert a new journal note.
- Implement `action=delete` to remove a note.

#### [NEW] `api/budget.php`
- Implement `INSERT INTO trip_expenses` logic.

#### [NEW] `api/trips.php`
- Implement the "Create Trip" wizard backend logic.

#### [NEW] `api/packing.php`
- Complete `action=save` to encode the checklist array as JSON and update `packing_lists.items`.
- Implement `action=init` to generate default categories.

### [Frontend Polish]
Finalize complex frontend interactions and edge cases.

#### [MODIFY] `pages/create-trip.php`
- Ensure smooth JavaScript transitions between wizard steps.
- Connect form submit to `/api/trips.php`.

#### [MODIFY] `pages/profile.php`
- Handle profile updates and avatar uploads.

#### [MODIFY] `pages/shared-trip.php`
- Build a read-only view of the itinerary based on `share_token`.

## Verification Plan
1. Test creating a new trip.
2. Test adding/editing/deleting itinerary sections.
3. Test adding expenses and verifying budget charts update.
4. Test adding/deleting journal notes.
5. Test packing checklist progress saving.
