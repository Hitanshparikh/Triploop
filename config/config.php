<?php
/**
 * JourneyOS AI — Application Configuration
 */

// Prevent direct access
defined('JOURNEYOS') or define('JOURNEYOS', true);

// App Settings
define('APP_NAME', 'JourneyOS AI');
define('APP_TAGLINE', 'The Emotional Operating System for Travel');
define('APP_URL', 'http://localhost/parul');
define('APP_VERSION', '1.0.0');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'journeyos');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('ASSETS_PATH', APP_URL . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', APP_URL . '/uploads');

// Security
define('CSRF_TOKEN_NAME', 'journeyos_csrf');
define('SESSION_LIFETIME', 86400); // 24 hours
define('HASH_COST', 12);

// Upload Limits
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================================
// Google OAuth 2.0
// ============================================================
// Step 1: Go to https://console.cloud.google.com/
// Step 2: Create a project → APIs & Services → Credentials
// Step 3: Create OAuth 2.0 Client ID (Web application)
// Step 4: Add Authorized redirect URI:
//         http://localhost/parul/api/auth.php?action=google_callback
// Step 5: Paste your Client ID and Secret below
// ============================================================
define('GOOGLE_CLIENT_ID',     'PASTE_YOUR_GOOGLE_CLIENT_ID_HERE');
define('GOOGLE_CLIENT_SECRET', 'PASTE_YOUR_GOOGLE_CLIENT_SECRET_HERE');
define('GOOGLE_REDIRECT_URI',  APP_URL . '/api/auth.php?action=google_callback');
define('GOOGLE_OAUTH_ENABLED', GOOGLE_CLIENT_ID !== 'PASTE_YOUR_GOOGLE_CLIENT_ID_HERE');
