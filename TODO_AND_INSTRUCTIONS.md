# JourneyOS AI - Project Status & Team To-Do List
Based on the `Traveloop - 8 hours.excalidraw` wireframes and current codebase state.

## 📊 Screens & Features Tracking

| Screen / Feature | Status | File Path | Details & Requirements |
| :--- | :---: | :--- | :--- |
| **1. Login Screen** | ✅ Completed | `pages/login.php` | Premium glassmorphism UI, Email/Password inputs, Google OAuth button, Remember Me toggle. *(Note: Backend API pending).* |
| **2. Registration Screen** | ⏳ To-Do | `pages/signup.php` | Requires specific fields: Photo upload, First Name, Last Name, Email, Phone Number, City, Country, Additional Info. |
| **3. Main Landing Page** | ✅ Completed | `pages/landing.php` | Fully styled. Hero banner, Mood Engine demo, Trip Simulation metrics, Collaborative features, Lucide premium icons. |
| **4. Create a New Trip** | ⏳ To-Do | `pages/create-trip.php` | Form for Place, Start/End Date, dynamic section addition, AI Suggestions for activities/places. |
| **5. Build Itinerary Screen** | ⏳ To-Do | `pages/itinerary-builder.php` | Builder split into "Sections" (travel/hotel/activities) with specific date ranges and budget allocations per section. |
| **6. User Trip Listing** | ⏳ To-Do | `pages/my-trips.php` | Tabs/filters for Ongoing, Up-coming, Completed trips. Each card shows a short overview with Group/Filter/Sort/Search tools. |
| **7. User Profile Pages** | ⏳ To-Do | `pages/profile.php` | User Image, editable User Details, lists of Preplanned and Previous Trips. |
| **8. Activity/City Search Page** | ⏳ To-Do | `pages/city-search.php`<br>`pages/activity-search.php` | Search interface for activities (e.g. Paragliding) and cities with detailed result cards and extensive Group/Filter/Sort. |
| **9. Itinerary View Screen** | ⏳ To-Do | `pages/itinerary-view.php` | Read-only timeline grouped by days detailing Physical Activities and Expenses for a selected place. |
| **10. Community Tab Screen** | ⏳ To-Do | `pages/community.php` | Social feed to share trip experiences with filtering and search capabilities. |
| **11. Packing Checklist** | ⏳ To-Do | `pages/packing.php` | Interactive checklist with progress counter (e.g. 5/12). Categories: Documents, Clothing, Electronics. Add item, Reset, Share features. |
| **12. Admin Panel Screen** | ✅ Completed | `pages/admin/dashboard.php` | User Trends and Analytics, metric cards (Users, Trips, Revenue), Chart.js visualizations (signups/moods), Top Destinations table. |
| **13. Trip Notes / Journal Screen** | ⏳ To-Do | `pages/journal.php` | Journaling interface to view/add notes grouped by day or by stop (e.g. Hotel check-in details). |
| **14. Expense Invoice / Billing** | ⏳ To-Do | `pages/invoice.php` | Detailed invoice: Traveler Details, Payment Status, itemized table (Category, Desc, Qty, Unit Cost, Amount), Subtotal, Tax, Discount, Grand Total, Export to PDF. |
| **App Dashboard (Core)** | ✅ Completed | `pages/dashboard.php`<br>`includes/sidebar.php` | Central routing dashboard, Sidebar/Navigation shell, quick stats, recent trips. |
| **Budget Dashboard (Core)** | ✅ Completed | `pages/budget.php` | Budget insights (Total Budget, Spent, Remaining), expense history, Chart.js pie and line charts. |
| **Database Initialization** | ⏳ To-Do | `database/schema.sql` | Import MySQL schema to XAMPP and seed demo data for testing. |
| **API Endpoints (Backend)** | ⏳ To-Do | `api/*.php` | Build PHP logic (`auth.php`, `trips.php`, etc.) to make all frontend forms functional. |

---

## 🚀 Further Instructions for the Team
To continue building this project systematically:

1. **Prioritize the Backend Connection First:**
   - Start your local MySQL database via XAMPP.
   - Import the `database/schema.sql` to your MySQL instance.
   - Build `api/auth.php` so the Login and Registration screens function correctly.
2. **Build Missing Core Pages:**
   - Next, tackle `pages/signup.php` and `pages/my-trips.php`.
   - Then, build the complex UI for the Itinerary Builder (`pages/itinerary-builder.php`).
3. **Add the Tools:**
   - Build the secondary tools like the Packing Checklist, Trip Notes, and Expense Invoice.
4. **Final Polish:**
   - Ensure all new screens utilize the existing `includes/header.php`, `includes/sidebar.php`, and `includes/footer.php`.
   - Maintain the "dark luxury, glassmorphism" aesthetic defined in `assets/css/design-system.css`.
