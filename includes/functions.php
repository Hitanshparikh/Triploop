<?php
/**
 * JourneyOS AI — Helper Functions
 */

require_once __DIR__ . '/../config/database.php';
require_once __DIR__ . '/../config/session.php';

/**
 * Redirect helper
 */
function redirect($path) {
    header('Location: ' . APP_URL . $path);
    exit;
}

/**
 * JSON response
 */
function jsonResponse($data, $status = 200) {
    http_response_code($status);
    header('Content-Type: application/json');
    echo json_encode($data);
    exit;
}

/**
 * Require authentication
 */
function requireAuth() {
    if (!isLoggedIn()) {
        if (isAjaxRequest()) {
            jsonResponse(['error' => 'Unauthorized'], 401);
        }
        redirect('/pages/login.php');
    }
}

/**
 * Require admin role
 */
function requireAdmin() {
    requireAuth();
    if (!isAdmin()) {
        if (isAjaxRequest()) {
            jsonResponse(['error' => 'Forbidden'], 403);
        }
        redirect('/pages/dashboard.php');
    }
}

/**
 * Check if AJAX request
 */
function isAjaxRequest() {
    return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
}

/**
 * Get request method
 */
function requestMethod() {
    return strtoupper($_SERVER['REQUEST_METHOD']);
}

/**
 * Get POST/GET input safely
 */
function input($key, $default = null) {
    return $_POST[$key] ?? $_GET[$key] ?? $default;
}

/**
 * Format currency
 */
function formatCurrency($amount, $currency = 'USD') {
    return '$' . number_format((float)$amount, 2);
}

/**
 * Format date
 */
function formatDate($date, $format = 'M j, Y') {
    return date($format, strtotime($date));
}

/**
 * Time ago
 */
function timeAgo($datetime) {
    $now = new DateTime();
    $ago = new DateTime($datetime);
    $diff = $now->diff($ago);
    if ($diff->y > 0) return $diff->y . 'y ago';
    if ($diff->m > 0) return $diff->m . 'mo ago';
    if ($diff->d > 0) return $diff->d . 'd ago';
    if ($diff->h > 0) return $diff->h . 'h ago';
    if ($diff->i > 0) return $diff->i . 'm ago';
    return 'just now';
}

/**
 * Generate unique token
 */
function generateToken($length = 32) {
    return bin2hex(random_bytes($length));
}

/**
 * Handle file upload
 */
function uploadFile($file, $directory, $allowedTypes = null) {
    $allowedTypes = $allowedTypes ?? ALLOWED_IMAGE_TYPES;

    if ($file['error'] !== UPLOAD_ERR_OK) {
        return ['error' => 'Upload failed'];
    }
    if ($file['size'] > MAX_UPLOAD_SIZE) {
        return ['error' => 'File too large (max 5MB)'];
    }
    if (!in_array($file['type'], $allowedTypes)) {
        return ['error' => 'Invalid file type'];
    }

    $ext = pathinfo($file['name'], PATHINFO_EXTENSION);
    $filename = generateToken(16) . '.' . $ext;
    $uploadDir = UPLOADS_PATH . '/' . $directory;

    if (!is_dir($uploadDir)) {
        mkdir($uploadDir, 0755, true);
    }

    $destination = $uploadDir . '/' . $filename;
    if (move_uploaded_file($file['tmp_name'], $destination)) {
        return ['success' => true, 'filename' => $filename, 'url' => UPLOADS_URL . '/' . $directory . '/' . $filename];
    }
    return ['error' => 'Failed to save file'];
}

/**
 * Truncate text
 */
function truncate($text, $length = 100) {
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, $length) . '...';
}

/**
 * Get mood theme data
 */
function getMoodTheme($mood) {
    $themes = [
        'adventure' => ['color' => '#FF6B35', 'gradient' => 'linear-gradient(135deg, #FF6B35, #F59E0B)', 'icon' => '<i data-lucide="zap"></i>', 'label' => 'Adventure'],
        'romantic' => ['color' => '#EC4899', 'gradient' => 'linear-gradient(135deg, #EC4899, #F43F5E)', 'icon' => '<i data-lucide="heart"></i>', 'label' => 'Romantic'],
        'healing' => ['color' => '#10B981', 'gradient' => 'linear-gradient(135deg, #10B981, #6EE7B7)', 'icon' => '<i data-lucide="leaf"></i>', 'label' => 'Healing'],
        'luxury' => ['color' => '#F59E0B', 'gradient' => 'linear-gradient(135deg, #F59E0B, #D97706)', 'icon' => '<i data-lucide="sparkles"></i>', 'label' => 'Luxury'],
        'party' => ['color' => '#8B5CF6', 'gradient' => 'linear-gradient(135deg, #8B5CF6, #EC4899)', 'icon' => '<i data-lucide="party-popper"></i>', 'label' => 'Party'],
        'spiritual' => ['color' => '#6366F1', 'gradient' => 'linear-gradient(135deg, #6366F1, #818CF8)', 'icon' => '<i data-lucide="flower-2"></i>', 'label' => 'Spiritual'],
        'productivity' => ['color' => '#3B82F6', 'gradient' => 'linear-gradient(135deg, #3B82F6, #2563EB)', 'icon' => '<i data-lucide="briefcase"></i>', 'label' => 'Productivity'],
        'solo' => ['color' => '#00D4FF', 'gradient' => 'linear-gradient(135deg, #00D4FF, #3B82F6)', 'icon' => '<i data-lucide="globe"></i>', 'label' => 'Solo Exploration'],
    ];
    return $themes[$mood] ?? $themes['adventure'];
}
/**
 * Call Google Gemini API
 * @param string $prompt The user prompt
 * @param string $systemInstruction Optional system instruction to guide the model
 * @param bool $jsonMode If true, enforces JSON output
 * @return string|array|null Returns decoded JSON array if $jsonMode is true, otherwise string. Null on failure.
 */
function callGemini($prompt, $systemInstruction = null, $jsonMode = false) {
    if (!defined('GEMINI_API_KEY') || GEMINI_API_KEY === 'PASTE_YOUR_GEMINI_API_KEY_HERE') {
        return null;
    }

    $url = "https://generativelanguage.googleapis.com/v1beta/models/gemini-2.5-flash:generateContent?key=" . GEMINI_API_KEY;

    $payload = [
        'contents' => [
            [
                'parts' => [
                    ['text' => $prompt]
                ]
            ]
        ]
    ];

    if ($systemInstruction) {
        $payload['systemInstruction'] = [
            'parts' => [
                ['text' => $systemInstruction]
            ]
        ];
    }

    if ($jsonMode) {
        $payload['generationConfig'] = [
            'responseMimeType' => 'application/json'
        ];
    }

    $curl = curl_init($url);
    curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($curl, CURLOPT_POST, true);
    curl_setopt($curl, CURLOPT_POSTFIELDS, json_encode($payload));
    curl_setopt($curl, CURLOPT_HTTPHEADER, [
        'Content-Type: application/json'
    ]);

    $response = curl_exec($curl);
    $error = curl_error($curl);
    curl_close($curl);

    if ($error) {
        error_log("Gemini CURL Error: " . $error);
        return null;
    }

    $data = json_decode($response, true);
    
    if (isset($data['candidates'][0]['content']['parts'][0]['text'])) {
        $text = $data['candidates'][0]['content']['parts'][0]['text'];
        if ($jsonMode) {
            $text = preg_replace('/^```json\s*/i', '', $text);
            $text = preg_replace('/```\s*$/', '', $text);
            $text = trim($text);
            $parsed = json_decode($text, true);
            if ($parsed === null) {
                error_log("Gemini JSON Decode Error. Raw text: " . $text);
            }
            return $parsed ? $parsed : null;
        }
        return $text;
    }

    error_log("Gemini API Error. Full response: " . $response);
    return null;
}
