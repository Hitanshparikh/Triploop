<?php
/**
 * JourneyOS AI — Application Configuration
 */

// Prevent direct access
defined('JOURNEYOS') or define('JOURNEYOS', true);

// App Settings
define('APP_NAME', 'JourneyOS AI');
define('APP_TAGLINE', 'The Emotional Operating System for Travel');
define('APP_URL', 'http://localhost/triploop');
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
