<?php
/**
 * One-time database setup script.
 * Run this ONCE by visiting: http://localhost/parul/setup.php
 * DELETE this file after running it!
 */
require_once __DIR__ . '/config/config.php';

$errors = [];
$success = [];

try {
    // Connect without selecting a DB first
    $pdo = new PDO(
        "mysql:host=" . DB_HOST . ";charset=" . DB_CHARSET,
        DB_USER,
        DB_PASS,
        [PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION]
    );

    // Create database
    $pdo->exec("CREATE DATABASE IF NOT EXISTS `" . DB_NAME . "` CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci");
    $pdo->exec("USE `" . DB_NAME . "`");
    $success[] = "✅ Database '" . DB_NAME . "' created / confirmed.";

    // Create all tables
    $tables = [
        "users" => "CREATE TABLE IF NOT EXISTS users (
            id INT AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(100) NOT NULL,
            email VARCHAR(255) NOT NULL UNIQUE,
            password VARCHAR(255) NOT NULL,
            avatar VARCHAR(255) DEFAULT NULL,
            phone VARCHAR(30) DEFAULT NULL,
            city VARCHAR(100) DEFAULT NULL,
            country VARCHAR(100) DEFAULT NULL,
            bio TEXT DEFAULT NULL,
            role ENUM('user','admin') DEFAULT 'user',
            preferences JSON DEFAULT NULL,
            google_id VARCHAR(100) DEFAULT NULL,
            email_verified TINYINT(1) DEFAULT 0,
            remember_token VARCHAR(255) DEFAULT NULL,
            reset_token VARCHAR(255) DEFAULT NULL,
            reset_token_expires DATETIME DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            INDEX idx_email (email),
            INDEX idx_role (role),
            INDEX idx_google (google_id)
        ) ENGINE=InnoDB",

        "cities" => "CREATE TABLE IF NOT EXISTS cities (
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
        ) ENGINE=InnoDB",

        "trips" => "CREATE TABLE IF NOT EXISTS trips (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            name VARCHAR(200) NOT NULL,
            description TEXT DEFAULT NULL,
            destination VARCHAR(200) DEFAULT NULL,
            cover_image VARCHAR(255) DEFAULT NULL,
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            mood ENUM('adventure','romantic','healing','luxury','party','spiritual','productivity','solo') DEFAULT 'adventure',
            travel_type ENUM('solo','couple','family','friends','business','group') DEFAULT 'solo',
            status ENUM('planning','active','completed','archived') DEFAULT 'planning',
            budget_total DECIMAL(12,2) DEFAULT 0,
            budget_level ENUM('budget','mid','luxury') DEFAULT 'mid',
            currency VARCHAR(3) DEFAULT 'USD',
            ai_generate TINYINT(1) DEFAULT 0,
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
        ) ENGINE=InnoDB",

        "itinerary_sections" => "CREATE TABLE IF NOT EXISTS itinerary_sections (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            title VARCHAR(200) DEFAULT NULL,
            section_type ENUM('travel','hotel','activity','food','other') DEFAULT 'travel',
            start_date DATE DEFAULT NULL,
            end_date DATE DEFAULT NULL,
            budget DECIMAL(12,2) DEFAULT 0,
            status ENUM('planned','booked','completed','cancelled') DEFAULT 'planned',
            notes TEXT DEFAULT NULL,
            order_index INT DEFAULT 0,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            INDEX idx_trip (trip_id, order_index)
        ) ENGINE=InnoDB",

        "expenses" => "CREATE TABLE IF NOT EXISTS expenses (
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
        ) ENGINE=InnoDB",

        "journals" => "CREATE TABLE IF NOT EXISTS journals (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            user_id INT NOT NULL,
            title VARCHAR(200) DEFAULT NULL,
            content TEXT DEFAULT NULL,
            stop_name VARCHAR(100) DEFAULT NULL,
            day_number INT DEFAULT 1,
            mood ENUM('happy','excited','calm','reflective','tired','grateful','adventurous') DEFAULT 'happy',
            images JSON DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            updated_at DATETIME DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_trip (trip_id)
        ) ENGINE=InnoDB",

        "packing_lists" => "CREATE TABLE IF NOT EXISTS packing_lists (
            id INT AUTO_INCREMENT PRIMARY KEY,
            trip_id INT NOT NULL,
            user_id INT NOT NULL,
            category VARCHAR(50) DEFAULT 'General',
            items JSON DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (trip_id) REFERENCES trips(id) ON DELETE CASCADE,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_trip (trip_id)
        ) ENGINE=InnoDB",

        "notifications" => "CREATE TABLE IF NOT EXISTS notifications (
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
        ) ENGINE=InnoDB",

        "bookmarks" => "CREATE TABLE IF NOT EXISTS bookmarks (
            id INT AUTO_INCREMENT PRIMARY KEY,
            user_id INT NOT NULL,
            city_id INT DEFAULT NULL,
            activity_id INT DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
            INDEX idx_user (user_id)
        ) ENGINE=InnoDB",

        "admin_analytics" => "CREATE TABLE IF NOT EXISTS admin_analytics (
            id INT AUTO_INCREMENT PRIMARY KEY,
            metric_type VARCHAR(50) NOT NULL,
            metric_value DECIMAL(12,2) DEFAULT 0,
            metric_data JSON DEFAULT NULL,
            metric_date DATE DEFAULT NULL,
            created_at DATETIME DEFAULT CURRENT_TIMESTAMP,
            INDEX idx_type_date (metric_type, metric_date)
        ) ENGINE=InnoDB",
    ];

    foreach ($tables as $name => $sql) {
        $pdo->exec($sql);
        $success[] = "✅ Table '$name' created / confirmed.";
    }

    // Clear existing data for fresh seed (since this is a setup script)
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $tablesToTruncate = ['users', 'cities', 'trips', 'itinerary_sections', 'expenses', 'journals', 'packing_lists', 'admin_analytics'];
    foreach ($tablesToTruncate as $t) {
        $pdo->exec("TRUNCATE TABLE $t");
    }
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");

    // Seed Users
    $adminPw = password_hash('password123', PASSWORD_DEFAULT, ['cost' => 12]);
    $demoPw  = password_hash('password123', PASSWORD_DEFAULT, ['cost' => 12]);
    $pref = json_encode(['default_mood'=>'adventure', 'currency'=>'USD']);
    
    $pdo->exec("INSERT INTO users (id, name, email, password, role, city, country, email_verified, preferences, created_at) VALUES
        (1, 'Admin User', 'admin@journeyos.ai', '$adminPw', 'admin', 'San Francisco', 'USA', 1, '$pref', NOW()),
        (2, 'Demo Traveler', 'demo@journeyos.ai', '$demoPw', 'user', 'London', 'UK', 1, '$pref', NOW())
    ");

    // Seed Cities
    $pdo->exec("INSERT INTO cities (id, name, country, continent, popularity, cost_index) VALUES
        (1, 'Tokyo', 'Japan', 'Asia', 98, 'expensive'),
        (2, 'Paris', 'France', 'Europe', 95, 'expensive'),
        (3, 'Bali', 'Indonesia', 'Asia', 92, 'budget'),
        (4, 'New York', 'USA', 'North America', 96, 'luxury'),
        (5, 'London', 'UK', 'Europe', 94, 'expensive'),
        (6, 'Barcelona', 'Spain', 'Europe', 88, 'moderate'),
        (7, 'Santorini', 'Greece', 'Europe', 85, 'luxury'),
        (8, 'Kyoto', 'Japan', 'Asia', 90, 'moderate'),
        (9, 'Dubai', 'UAE', 'Asia', 89, 'luxury'),
        (10, 'Rome', 'Italy', 'Europe', 93, 'moderate')
    ");

    // Seed Trips
    $shareToken1 = bin2hex(random_bytes(16));
    $shareToken2 = bin2hex(random_bytes(16));
    $pdo->exec("INSERT INTO trips (id, user_id, name, destination, description, start_date, end_date, mood, travel_type, status, budget_total, budget_level, currency, share_token, is_public, health_score, created_at) VALUES
        (1, 2, 'Neon Nights in Tokyo', 'Tokyo, Japan', 'A futuristic cyberpunk exploration of Tokyo neon streets and hidden ramen spots.', '2026-10-01', '2026-10-14', 'adventure', 'solo', 'planning', 4500.00, 'mid', 'USD', '$shareToken1', 1, 92, NOW()),
        (2, 2, 'Romantic Santorini Escape', 'Santorini, Greece', 'Sunsets, wine, and white-washed villas over the caldera.', '2026-06-15', '2026-06-22', 'romantic', 'couple', 'active', 6000.00, 'luxury', 'USD', '$shareToken2', 1, 85, DATE_SUB(NOW(), INTERVAL 10 DAY)),
        (3, 2, 'Bali Healing Retreat', 'Bali, Indonesia', 'Yoga, meditation, and surfing in Canggu.', '2025-11-05', '2025-11-20', 'healing', 'solo', 'completed', 2000.00, 'budget', 'USD', 'abcd123', 0, 98, DATE_SUB(NOW(), INTERVAL 6 MONTH))
    ");

    // Seed Itinerary Sections
    $pdo->exec("INSERT INTO itinerary_sections (trip_id, title, section_type, start_date, end_date, budget, status, order_index) VALUES
        (1, 'Arrival & Shinjuku', 'travel', '2026-10-01', '2026-10-03', 800, 'planned', 0),
        (1, 'Kyoto Bullet Train', 'travel', '2026-10-04', '2026-10-07', 1200, 'planned', 1),
        (1, 'Osaka Food Tour', 'food', '2026-10-08', '2026-10-10', 500, 'planned', 2),
        (2, 'Oia Villa Stay', 'hotel', '2026-06-15', '2026-06-18', 2500, 'booked', 0),
        (2, 'Caldera Yacht Tour', 'activity', '2026-06-19', '2026-06-19', 800, 'planned', 1)
    ");

    // Seed Expenses
    $pdo->exec("INSERT INTO expenses (trip_id, user_id, category, description, amount, currency, expense_date) VALUES
        (1, 2, 'transport', 'JAL Flight to NRT', 1200.00, 'USD', '2026-09-01'),
        (1, 2, 'accommodation', 'Shinjuku Granbell Hotel', 850.00, 'USD', '2026-09-05'),
        (1, 2, 'activities', 'teamLab Planets', 35.00, 'USD', '2026-10-02'),
        (2, 2, 'accommodation', 'Oia Luxury Cave Villa', 3000.00, 'USD', '2026-01-10'),
        (2, 2, 'food', 'Amoudi Bay Seafood Dinner', 180.00, 'USD', '2026-06-16')
    ");

    // Seed Packing Lists
    $items = json_encode([['name'=>'Passport','checked'=>true], ['name'=>'Camera','checked'=>false]]);
    $pdo->exec("INSERT INTO packing_lists (trip_id, user_id, category, items) VALUES (1, 2, 'Essentials', '$items')");

    $success[] = "✅ Massive demo dataset seeded successfully (Users, Cities, Trips, Itineraries, Expenses).";

    $success[] = "<br><strong>🎉 Setup complete! You can now log in and use the app.</strong>";
    $success[] = "<br>⚠️ <strong>DELETE this file (setup.php) now for security!</strong>";

} catch (PDOException $e) {
    $errors[] = "❌ Database error: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8"><meta name="viewport" content="width=device-width,initial-scale=1.0">
<title>JourneyOS AI — Database Setup</title>
<style>
body{font-family:'Segoe UI',sans-serif;background:#0B1020;color:#F1F5F9;min-height:100vh;display:flex;align-items:center;justify-content:center;margin:0;}
.card{background:rgba(18,24,38,0.95);border:1px solid rgba(148,163,184,0.1);border-radius:16px;padding:48px;max-width:640px;width:90%;}
h1{color:#00D4FF;margin-bottom:8px;}
p{color:#94A3B8;margin-bottom:32px;}
.msg{padding:12px 16px;border-radius:8px;margin-bottom:8px;font-size:14px;line-height:1.6;}
.msg.ok{background:rgba(16,185,129,0.1);border:1px solid rgba(16,185,129,0.2);}
.msg.err{background:rgba(239,68,68,0.1);border:1px solid rgba(239,68,68,0.2);color:#EF4444;}
.btn{display:inline-block;margin-top:24px;padding:12px 32px;background:linear-gradient(135deg,#00D4FF,#3B82F6);color:#0B1020;font-weight:700;border-radius:8px;text-decoration:none;}
</style>
</head>
<body>
<div class="card">
    <h1>✦ JourneyOS AI</h1>
    <p>Database Setup & Migration</p>
    <?php foreach($errors as $e): ?>
        <div class="msg err"><?= $e ?></div>
    <?php endforeach; ?>
    <?php foreach($success as $s): ?>
        <div class="msg ok"><?= $s ?></div>
    <?php endforeach; ?>
    <?php if(empty($errors)): ?>
        <a href="<?= APP_URL ?>/pages/login.php" class="btn">→ Go to Login</a>
    <?php endif; ?>
</div>
</body>
</html>
