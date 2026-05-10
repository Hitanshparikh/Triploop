# JourneyOS AI - Project Status & To-Do List
Based on the `Traveloop - 8 hours.excalidraw` wireframes and current codebase state.

## ✅ What is Achieved
*The foundation and core dashboards are successfully built.*

1. **Screen 1: Login Screen** (`pages/login.php`)
   - **Achieved:** The UI is completely built using the premium glassmorphism design system. It includes the Email/Password inputs, "Continue with Google" button, and 'Remember Me' features. *(Note: Backend API connection is pending).*
2. **Screen 3: Main Landing Page** (`pages/landing.php`)
   - **Achieved:** Fully built and styled. It contains the Hero banner, Mood Engine, Trip Simulation metrics, Collaborative features, and uses the premium Lucide icons.
3. **Screen 12: Admin Panel Screen** (`pages/admin/dashboard.php`)
   - **Achieved:** Fully built. It includes the "User Trends and Analytics" requested in the wireframe, with metric cards (Total Users, Trips, Revenue), Chart.js visualizations for signups and moods, and a Top Destinations table.
4. **App Dashboard & Budget Core** (`pages/dashboard.php` & `pages/budget.php`)
   - **Achieved:** The Budget Dashboard covers the budget insights (Total Budget, Spent, Remaining), expense history, and visual charts. The central routing Dashboard and the Sidebar/Navigation shell are also complete.

---

## ⏳ What is Left (To-Do List)
*These screens and features are explicitly outlined in your wireframe but have not been coded yet.*

### 1. User Authentication & Profile
- [ ] **Screen 2: Registration Screen** (`pages/signup.php`)
  - **Details:** A signup form including fields specifically requested: Photo upload, First Name, Last Name, Email Address, Phone Number, City, Country, and Additional Information.
- [ ] **Screen 7: User Profile Pages** (`pages/profile.php`)
  - **Details:** A profile view showing the User's Image, User Details with edit options, and lists of "Preplanned Trips" and "Previous Trips".

### 2. Trip Planning & Itinerary
- [ ] **Screen 4: Create a New Trip** (`pages/create-trip.php`)
  - **Details:** A trip initialization form featuring inputs for "Select a Place", Start Date, End Date, and a dynamic section to "Add another Section". It also needs a panel for AI "Suggestions for Places to Visit/Activities".
- [ ] **Screen 5: Build Itinerary Screen** (`pages/itinerary-builder.php`)
  - **Details:** The builder interface divided into "Sections" (e.g., travel, hotel, activities). Each section must include specific date ranges ("xxx to yyy") and a budget allocation.
- [ ] **Screen 6: User Trip Listing** (`pages/my-trips.php`)
  - **Details:** The "My Trips" page featuring tabs/filters for **Ongoing**, **Up-coming**, and **Completed** trips. Each card must show a "Short Overview of the Trip" along with Group/Filter/Sort/Search tools.
- [ ] **Screen 9: Itinerary View Screen** (`pages/itinerary-view.php`)
  - **Details:** A read-only timeline grouped by "Day 1", "Day 2", etc., detailing Physical Activities and Expenses for a selected place.

### 3. Discovery & Community
- [ ] **Screen 8: Activity / City Search Page** (`pages/city-search.php` & `pages/activity-search.php`)
  - **Details:** A search engine interface allowing users to look up activities (e.g., "Paragliding") and cities, complete with detailed result cards, search bars, and extensive Group/Filter/Sort options.
- [ ] **Screen 10: Community Tab Screen** (`pages/community.php`)
  - **Details:** A social feed section where users can "share their experience about a certain trip or activity", complete with filtering and search capabilities.

### 4. Trip Tools & Management
- [ ] **Screen 11: Packing Checklist** (`pages/packing.php`)
  - **Details:** An interactive checklist showing the Trip Name and a Progress counter (e.g., "5/12 items packed"). specific categories for **Documents**, **Clothing**, and **Electronics**, plus buttons to "Add item", "Reset all", and "Share Checklist".
- [ ] **Screen 13: Trip Notes / Journal Screen** (`pages/journal.php`)
  - **Details:** A journaling interface allowing users to view and add notes grouped "by Day" or "by stop" (e.g., Hotel check-in details for Rome stop).
- [ ] **Screen 14: Expense Invoice / Billing Screen** (`pages/invoice.php`)
  - **Details:** A highly detailed invoice page featuring Traveler Details (names), Generated Date, Payment Status, and an itemized table (Category, Description, Qty, Unit Cost, Amount). It must calculate Subtotal, Tax (5%), Discount, and Grand Total, with options to "Download Invoice", "Export as PDF", and "Mark as paid".

### 5. Backend & Database
- [ ] **Database Initialization:** Import the MySQL database schema to XAMPP and seed demo data.
- [ ] **API Implementation:** Write the PHP API endpoints (`api/auth.php`, `api/trips.php`, etc.) to make all the frontend forms functional and dynamic.

---

## 🚀 Further Instructions
To continue building this project systematically:

1. **Prioritize the Backend Connection First:**
   - Start your local MySQL database via XAMPP.
   - Import the `database/schema.sql` to your MySQL instance.
   - Build `api/auth.php` so the Login and Registration screens actually work.
2. **Build Missing Core Pages:**
   - Next, tackle `pages/signup.php` and `pages/my-trips.php`.
   - Then, build the complex UI for the Itinerary Builder (`pages/itinerary-builder.php`).
3. **Add the Tools:**
   - Build the secondary tools like the Packing Checklist, Trip Notes, and Expense Invoice.
4. **Final Polish:**
   - Ensure all new screens utilize the existing `includes/header.php`, `includes/sidebar.php`, and `includes/footer.php`.
   - Maintain the "dark luxury, glassmorphism" aesthetic defined in `assets/css/design-system.css`.
