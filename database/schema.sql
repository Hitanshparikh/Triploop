-- ============================================
-- JOURNEYOS AI — Complete MySQL Schema
-- "The Emotional Operating System for Travel"
-- ============================================

CREATE DATABASE IF NOT EXISTS journeyos CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE journeyos;

-- Users
CREATE TABLE IF NOT EXISTS users (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    email VARCHAR(255) NOT NULL UNIQUE,
    password_hash VARCHAR(255) NOT NULL,
    avatar VARCHAR(255) DEFAULT NULL,
    role ENUM('user','admin') DEFAULT 'user',
    preferences JSON DEFAULT NULL,
    remember_token VARCHAR(255) DEFAULT NULL,
    email_verified_at DATETIME DEFAULT NULL,
    reset_token VARCHAR(255) DEFAULT NULL,
    reset_token_expires DATETIME DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    INDEX idx_email (email),
    INDEX idx_role (role)
) ENGINE=InnoDB;

-- Cities
CREATE TABLE IF NOT EXISTS cities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    name VARCHAR(100) NOT NULL,
    country VARCHAR(100) NOT NULL,
    continent VARCHAR(50) DEFAULT NULL,
    latitude DECIMAL(10,7) DEFAULT NULL,
    longitude DECIMAL(10,7) DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    description TEXT DEFAULT NULL,
    popularity INT DEFAULT 0,
    cost_index ENUM('budget','moderate','expensive','luxury') DEFAULT 'moderate',
    weather_data JSON DEFAULT NULL,
    tags JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_name (name),
    INDEX idx_country (country),
    INDEX idx_popularity (popularity DESC)
) ENGINE=InnoDB;

-- Trips
CREATE TABLE IF NOT EXISTS trips (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    cover_image VARCHAR(255) DEFAULT NULL,
    start_date DATE DEFAULT NULL,
    end_date DATE DEFAULT NULL,
    mood ENUM('adventure','romantic','healing','luxury','party','spiritual','productivity','solo') DEFAULT 'adventure',
    travel_type ENUM('solo','couple','family','friends','business') DEFAULT 'solo',
    status ENUM('planning','active','completed','archived') DEFAULT 'planning',
    budget_total DECIMAL(12,2) DEFAULT 0,
    health_score INT DEFAULT NULL,
    share_token VARCHAR(64) DEFAULT NULL UNIQUE,
    is_public TINYINT(1) DEFAULT 0,
    views INT DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id),
    INDEX idx_status (status),
    INDEX idx_mood (mood),
    INDEX idx_share_token (share_token)
) ENGINE=InnoDB;

-- Trip Stops (cities in a trip)
CREATE TABLE IF NOT EXISTS trip_stops (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    city_id INT NOT NULL,
    order_index INT DEFAULT 0,
    arrival_date DATE DEFAULT NULL,
    departure_date DATE DEFAULT NULL,
    transport_mode ENUM('flight','train','bus','car','boat','walk') DEFAULT 'flight',
    transport_cost DECIMAL(10,2) DEFAULT 0,
    notes TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id),
    INDEX idx_order (trip_id, order_index)
) ENGINE=InnoDB;

-- Activities
CREATE TABLE IF NOT EXISTS activities (
    id INT AUTO_INCREMENT PRIMARY KEY,
    city_id INT NOT NULL,
    name VARCHAR(200) NOT NULL,
    description TEXT DEFAULT NULL,
    image VARCHAR(255) DEFAULT NULL,
    category ENUM('sightseeing','food','adventure','culture','relaxation','nightlife','shopping','nature') DEFAULT 'sightseeing',
    duration_hours DECIMAL(4,1) DEFAULT 2.0,
    cost DECIMAL(10,2) DEFAULT 0,
    rating DECIMAL(2,1) DEFAULT 4.0,
    budget_label ENUM('free','budget','moderate','expensive','luxury') DEFAULT 'moderate',
    mood_tags JSON DEFAULT NULL,
    location VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (city_id) REFERENCES cities(id) ON DELETE CASCADE,
    INDEX idx_city (city_id),
    INDEX idx_category (category),
    INDEX idx_rating (rating DESC)
) ENGINE=InnoDB;

-- Itinerary Items
CREATE TABLE IF NOT EXISTS itinerary_items (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    stop_id INT DEFAULT NULL,
    activity_id INT DEFAULT NULL,
    day_number INT DEFAULT 1,
    time_slot VARCHAR(20) DEFAULT 'morning',
    order_index INT DEFAULT 0,
    custom_title VARCHAR(200) DEFAULT NULL,
    notes TEXT DEFAULT NULL,
    status ENUM('planned','completed','skipped') DEFAULT 'planned',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (stop_id) REFERENCES trip_stops(id) ON DELETE SET NULL,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE SET NULL,
    INDEX idx_trip_day (trip_id, day_number, order_index)
) ENGINE=InnoDB;

-- Expenses
CREATE TABLE IF NOT EXISTS expenses (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    category ENUM('transport','accommodation','food','activities','shopping','other') DEFAULT 'other',
    description VARCHAR(255) DEFAULT NULL,
    amount DECIMAL(12,2) NOT NULL,
    currency VARCHAR(3) DEFAULT 'USD',
    expense_date DATE DEFAULT NULL,
    is_paid TINYINT(1) DEFAULT 0,
    receipt_image VARCHAR(255) DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id),
    INDEX idx_category (category)
) ENGINE=InnoDB;

-- Budgets (per category per trip)
CREATE TABLE IF NOT EXISTS budgets (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    category ENUM('transport','accommodation','food','activities','shopping','other') NOT NULL,
    allocated_amount DECIMAL(12,2) DEFAULT 0,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    UNIQUE KEY uk_trip_category (trip_id, category)
) ENGINE=InnoDB;

-- Journals
CREATE TABLE IF NOT EXISTS journals (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    title VARCHAR(200) DEFAULT NULL,
    content TEXT DEFAULT NULL,
    mood ENUM('happy','excited','calm','reflective','tired','grateful','adventurous') DEFAULT 'happy',
    images JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id)
) ENGINE=InnoDB;

-- Packing Lists
CREATE TABLE IF NOT EXISTS packing_lists (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    category VARCHAR(50) DEFAULT 'General',
    items JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id)
) ENGINE=InnoDB;

-- Collaborators
CREATE TABLE IF NOT EXISTS collaborators (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    role ENUM('viewer','editor','admin') DEFAULT 'editor',
    joined_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_trip_user (trip_id, user_id)
) ENGINE=InnoDB;

-- Votes
CREATE TABLE IF NOT EXISTS votes (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    activity_id INT NOT NULL,
    user_id INT NOT NULL,
    vote_type ENUM('up','down') DEFAULT 'up',
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (activity_id) REFERENCES activities(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    UNIQUE KEY uk_vote (trip_id, activity_id, user_id)
) ENGINE=InnoDB;

-- Comments
CREATE TABLE IF NOT EXISTS comments (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    user_id INT NOT NULL,
    content TEXT NOT NULL,
    parent_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id)
) ENGINE=InnoDB;

-- Notifications
CREATE TABLE IF NOT EXISTS notifications (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    type VARCHAR(50) DEFAULT 'info',
    title VARCHAR(200) DEFAULT NULL,
    message TEXT DEFAULT NULL,
    link VARCHAR(255) DEFAULT NULL,
    is_read TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user_read (user_id, is_read)
) ENGINE=InnoDB;

-- AI Suggestions
CREATE TABLE IF NOT EXISTS ai_suggestions (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    type ENUM('optimization','hidden_gem','warning','pacing','budget','activity') DEFAULT 'optimization',
    content JSON DEFAULT NULL,
    is_applied TINYINT(1) DEFAULT 0,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id)
) ENGINE=InnoDB;

-- Trip Simulations
CREATE TABLE IF NOT EXISTS trip_simulations (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    stress_score INT DEFAULT 50,
    fatigue_score INT DEFAULT 50,
    budget_burn_rate DECIMAL(5,2) DEFAULT 50.00,
    weather_risk INT DEFAULT 30,
    crowd_intensity INT DEFAULT 50,
    health_score INT DEFAULT 75,
    walking_load DECIMAL(6,1) DEFAULT 0,
    hidden_costs DECIMAL(10,2) DEFAULT 0,
    suggestions JSON DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    INDEX idx_trip (trip_id)
) ENGINE=InnoDB;

-- Shared Itineraries
CREATE TABLE IF NOT EXISTS shared_itineraries (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    share_token VARCHAR(64) NOT NULL UNIQUE,
    views INT DEFAULT 0,
    copies INT DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
    INDEX idx_token (share_token)
) ENGINE=InnoDB;

-- Bookmarks
CREATE TABLE IF NOT EXISTS bookmarks (
    id INT AUTO_INCREMENT PRIMARY KEY,
    user_id INT NOT NULL,
    city_id INT DEFAULT NULL,
    activity_id INT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    INDEX idx_user (user_id)
) ENGINE=InnoDB;

-- Memories
CREATE TABLE IF NOT EXISTS memories (
    id INT AUTO_INCREMENT PRIMARY KEY,
    trip_id INT NOT NULL,
    timeline JSON DEFAULT NULL,
    summary_text TEXT DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE
) ENGINE=InnoDB;

-- Admin Analytics (for precomputed metrics)
CREATE TABLE IF NOT EXISTS admin_analytics (
    id INT AUTO_INCREMENT PRIMARY KEY,
    metric_type VARCHAR(50) NOT NULL,
    metric_value DECIMAL(12,2) DEFAULT 0,
    metric_data JSON DEFAULT NULL,
    metric_date DATE DEFAULT NULL,
    created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX idx_type_date (metric_type, metric_date)
) ENGINE=InnoDB;
