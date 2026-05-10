<?php
/**
 * JourneyOS AI — Application Configuration EXAMPLE
 *
 * Copy this file to config/config.php and fill in your real values.
 * NEVER commit config.php to Git — it contains secrets.
 */

defined('JOURNEYOS') or define('JOURNEYOS', true);

// App Settings
define('APP_NAME', 'JourneyOS AI');
define('APP_TAGLINE', 'The Emotional Operating System for Travel');
define('APP_URL', 'http://localhost/parul'); // Change for production
define('APP_VERSION', '1.0.0');

// Database
define('DB_HOST', 'localhost');
define('DB_NAME', 'journeyos');
define('DB_USER', 'root');
define('DB_PASS', '');          // Set your MySQL password
define('DB_CHARSET', 'utf8mb4');

// Paths
define('BASE_PATH', dirname(__DIR__));
define('ASSETS_PATH', APP_URL . '/assets');
define('UPLOADS_PATH', BASE_PATH . '/uploads');
define('UPLOADS_URL', APP_URL . '/uploads');

// Security
define('CSRF_TOKEN_NAME', 'journeyos_csrf');
define('SESSION_LIFETIME', 86400);
define('HASH_COST', 12);

// Upload Limits
define('MAX_UPLOAD_SIZE', 5 * 1024 * 1024);
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/webp', 'image/gif']);

// Timezone
date_default_timezone_set('UTC');

// Error reporting (disable in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// ============================================================
// Google OAuth 2.0
// ============================================================
// 1. Go to https://console.cloud.google.com/
// 2. APIs & Services → Credentials → Create OAuth 2.0 Client ID
// 3. Application type: Web application
// 4. Authorized redirect URI: http://localhost/parul/api/auth.php?action=google_callback
// 5. Paste your credentials below
// ============================================================
define('GOOGLE_CLIENT_ID',     'YOUR_CLIENT_ID.apps.googleusercontent.com');
define('GOOGLE_CLIENT_SECRET', 'YOUR_CLIENT_SECRET');
define('GOOGLE_REDIRECT_URI',  APP_URL . '/api/auth.php?action=google_callback');
define('GOOGLE_OAUTH_ENABLED', GOOGLE_CLIENT_ID !== 'YOUR_CLIENT_ID.apps.googleusercontent.com');
