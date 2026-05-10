<?php
/**
 * JourneyOS AI — Auth API
 * Handles: login, signup, logout, google_redirect, google_callback, forgot_password
 */
require_once __DIR__ . '/../includes/functions.php';

$action = $_GET['action'] ?? $_POST['action'] ?? '';

switch ($action) {

    // =====================================================
    // GOOGLE: Redirect to Google consent screen
    // =====================================================
    case 'google_redirect':
        if (!GOOGLE_OAUTH_ENABLED) {
            setFlash('error', 'Google login is not configured yet. Please contact the administrator.');
            redirect('/pages/login.php');
        }
        $state = bin2hex(random_bytes(16));
        $_SESSION['oauth_state'] = $state;
        $params = http_build_query([
            'client_id'     => GOOGLE_CLIENT_ID,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'response_type' => 'code',
            'scope'         => 'openid email profile',
            'access_type'   => 'online',
            'state'         => $state,
            'prompt'        => 'select_account',
        ]);
        header('Location: https://accounts.google.com/o/oauth2/v2/auth?' . $params);
        exit;

    // =====================================================
    // GOOGLE: Handle callback from Google
    // =====================================================
    case 'google_callback':
        if (!GOOGLE_OAUTH_ENABLED) {
            setFlash('error', 'Google login is not configured.');
            redirect('/pages/login.php');
        }
        // Validate state (CSRF protection)
        if (empty($_GET['state']) || $_GET['state'] !== ($_SESSION['oauth_state'] ?? '')) {
            setFlash('error', 'Invalid OAuth state. Please try again.');
            redirect('/pages/login.php');
        }
        unset($_SESSION['oauth_state']);

        if (isset($_GET['error'])) {
            setFlash('error', 'Google login was cancelled.');
            redirect('/pages/login.php');
        }

        $code = $_GET['code'] ?? '';
        if (!$code) {
            setFlash('error', 'No authorization code received from Google.');
            redirect('/pages/login.php');
        }

        // Exchange code for access token
        $tokenResponse = httpPost('https://oauth2.googleapis.com/token', [
            'code'          => $code,
            'client_id'     => GOOGLE_CLIENT_ID,
            'client_secret' => GOOGLE_CLIENT_SECRET,
            'redirect_uri'  => GOOGLE_REDIRECT_URI,
            'grant_type'    => 'authorization_code',
        ]);

        if (!$tokenResponse || empty($tokenResponse['access_token'])) {
            setFlash('error', 'Failed to exchange token with Google. Please try again.');
            redirect('/pages/login.php');
        }

        // Fetch user info from Google
        $googleUser = httpGet(
            'https://www.googleapis.com/oauth2/v2/userinfo',
            $tokenResponse['access_token']
        );

        if (!$googleUser || empty($googleUser['email'])) {
            setFlash('error', 'Could not retrieve your Google account information.');
            redirect('/pages/login.php');
        }

        // Find or create user in our database
        $existingUser = db()->fetch(
            "SELECT * FROM users WHERE email = ? LIMIT 1",
            [$googleUser['email']]
        );

        if ($existingUser) {
            // Log them in
            loginUser($existingUser);
            setFlash('success', 'Welcome back, ' . $existingUser['name'] . '!');
        } else {
            // Register new user via Google
            $newUserId = db()->insert('users', [
                'name'         => $googleUser['name'] ?? $googleUser['email'],
                'email'        => $googleUser['email'],
                'password'     => password_hash(bin2hex(random_bytes(32)), PASSWORD_DEFAULT), // random unusable pw
                'avatar'       => '', // could download google picture
                'role'         => 'user',
                'google_id'    => $googleUser['id'] ?? '',
                'email_verified' => 1,
                'created_at'   => date('Y-m-d H:i:s'),
            ]);
            $newUser = db()->fetch("SELECT * FROM users WHERE id = ?", [$newUserId]);
            loginUser($newUser);
            setFlash('success', 'Welcome to JourneyOS AI, ' . ($googleUser['name'] ?? 'Traveler') . '!');
        }
        redirect('/pages/dashboard.php');
        break;

    // =====================================================
    // EMAIL LOGIN
    // =====================================================
    case 'login':
        validateCsrfToken();
        if (requestMethod() !== 'POST') { redirect('/pages/login.php'); }

        $email    = trim(input('email', ''));
        $password = input('password', '');
        $remember = input('remember', 0);

        if (!$email || !$password) {
            setFlash('error', 'Please enter your email and password.');
            redirect('/pages/login.php');
        }

        $user = db()->fetch("SELECT * FROM users WHERE email = ? LIMIT 1", [$email]);

        if (!$user || !password_verify($password, $user['password'])) {
            setFlash('error', 'Invalid email or password.');
            redirect('/pages/login.php');
        }

        loginUser($user, (bool)$remember);
        setFlash('success', 'Welcome back, ' . $user['name'] . '!');
        redirect('/pages/dashboard.php');
        break;

    // =====================================================
    // SIGNUP
    // =====================================================
    case 'signup':
        validateCsrfToken();
        if (requestMethod() !== 'POST') { redirect('/pages/signup.php'); }

        $firstName = trim(input('first_name', ''));
        $lastName  = trim(input('last_name', ''));
        $email     = strtolower(trim(input('email', '')));
        $password  = input('password', '');
        $confirm   = input('confirm_password', '');
        $phone     = trim(input('phone', ''));
        $city      = trim(input('city', ''));
        $country   = trim(input('country', ''));
        $bio       = trim(input('bio', ''));
        $mood      = input('default_mood', 'adventure');

        // Validate
        if (!$firstName || !$lastName || !$email || !$password) {
            setFlash('error', 'Please fill in all required fields.');
            redirect('/pages/signup.php');
        }
        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            redirect('/pages/signup.php');
        }
        if (strlen($password) < 8) {
            setFlash('error', 'Password must be at least 8 characters.');
            redirect('/pages/signup.php');
        }
        if ($password !== $confirm) {
            setFlash('error', 'Passwords do not match.');
            redirect('/pages/signup.php');
        }

        // Check email uniqueness
        $existing = db()->fetch("SELECT id FROM users WHERE email = ? LIMIT 1", [$email]);
        if ($existing) {
            setFlash('error', 'An account with this email already exists. Please sign in.');
            redirect('/pages/signup.php');
        }

        // Handle avatar upload
        $avatarFilename = '';
        if (!empty($_FILES['photo']['name'])) {
            $upload = uploadFile($_FILES['photo'], 'avatars');
            if (isset($upload['filename'])) {
                $avatarFilename = $upload['filename'];
            }
        }

        // Build preferences JSON
        $preferences = json_encode([
            'default_mood'  => $mood,
            'currency'      => 'USD',
            'budget_level'  => 'mid',
        ]);

        $userId = db()->insert('users', [
            'name'        => $firstName . ' ' . $lastName,
            'email'       => $email,
            'password'    => password_hash($password, PASSWORD_DEFAULT, ['cost' => HASH_COST]),
            'avatar'      => $avatarFilename,
            'phone'       => $phone,
            'city'        => $city,
            'country'     => $country,
            'bio'         => $bio,
            'role'        => 'user',
            'preferences' => $preferences,
            'created_at'  => date('Y-m-d H:i:s'),
        ]);

        $newUser = db()->fetch("SELECT * FROM users WHERE id = ?", [$userId]);
        loginUser($newUser);
        setFlash('success', 'Welcome to JourneyOS AI, ' . $firstName . '! Your journey begins now.');
        redirect('/pages/dashboard.php');
        break;

    // =====================================================
    // LOGOUT
    // =====================================================
    case 'logout':
        logoutUser();
        redirect('/pages/landing.php');
        break;

    // =====================================================
    // FORGOT PASSWORD
    // =====================================================
    case 'forgot_password':
        validateCsrfToken();
        $email = strtolower(trim(input('email', '')));
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            setFlash('error', 'Please enter a valid email address.');
            redirect('/pages/forgot-password.php');
        }
        $user = db()->fetch("SELECT id, name FROM users WHERE email = ? LIMIT 1", [$email]);
        // Always show success (security: don't reveal if email exists)
        if ($user) {
            $token = generateToken(32);
            $expiry = date('Y-m-d H:i:s', time() + 3600); // 1 hour
            db()->update('users',
                ['reset_token' => $token, 'reset_token_expires' => $expiry],
                'id = ?', [$user['id']]
            );
            // In production: send email. For demo: show the token.
            setFlash('success', 'Password reset link: <a href="' . APP_URL . '/pages/reset-password.php?token=' . $token . '">Click here</a> (demo mode — normally sent via email)');
        } else {
            setFlash('success', 'If that email exists, a reset link has been sent.');
        }
        redirect('/pages/forgot-password.php');
        break;

    default:
        redirect('/pages/login.php');
}

// =====================================================
// Helper: HTTP POST (for token exchange)
// =====================================================
function httpPost(string $url, array $data): ?array {
    $ctx = stream_context_create(['http' => [
        'method'  => 'POST',
        'header'  => "Content-Type: application/x-www-form-urlencoded\r\n",
        'content' => http_build_query($data),
        'timeout' => 10,
    ]]);
    $res = @file_get_contents($url, false, $ctx);
    return $res ? json_decode($res, true) : null;
}

// =====================================================
// Helper: HTTP GET with Bearer token
// =====================================================
function httpGet(string $url, string $accessToken): ?array {
    $ctx = stream_context_create(['http' => [
        'method'  => 'GET',
        'header'  => "Authorization: Bearer $accessToken\r\n",
        'timeout' => 10,
    ]]);
    $res = @file_get_contents($url, false, $ctx);
    return $res ? json_decode($res, true) : null;
}
