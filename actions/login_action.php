<?php
// actions/login_action.php
// Expt 7 — Sessions + Remember Me cookie

session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php'); exit;
}

$email      = trim($_POST['email']    ?? '');
$password   = $_POST['password']      ?? '';
$rememberMe = isset($_POST['remember']); // ← Expt 7: Remember Me checkbox

if (empty($email) || empty($password)) {
    $_SESSION['errors'] = ['Please enter your email and password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php'); exit;
}

$stmt = $pdo->prepare('SELECT * FROM users WHERE email = $1 AND is_active = TRUE LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

if (!$user || !password_verify($password, $user['password'])) {
    $_SESSION['errors'] = ['Incorrect email or password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php'); exit;
}

//  Regenerate session ID (prevents session fixation) 
session_regenerate_id(true);

//  Store user info in session 
$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['firstname'] = $user['firstname'];
$_SESSION['role']      = $user['role'];

//  Expt 7: Remember Me Cookie 
if ($rememberMe) {
    // Generate a secure random token
    $token  = bin2hex(random_bytes(32)); // 64-char hex token
    $expiry = time() + (30 * 24 * 60 * 60); // 30 days

    // Store token in DB against the user
    // (requires remember_token column — added to schema below)
    try {
        $pdo->prepare('UPDATE users SET remember_token = $1 WHERE id = $2')
            ->execute([$token, $user['id']]);

        // Set cookie: name, value, expiry, path, domain, secure, httponly
        setcookie(
            'remember_token',   // cookie name
            $token,             // value
            $expiry,            // expires in 30 days
            '/',                // available site-wide
            '',                 // domain (empty = current)
            false,              // secure (set true in production with HTTPS)
            true                // httpOnly — JS cannot access this cookie
        );
    } catch (Exception $e) {
        // Column may not exist yet — skip silently, session still works
        error_log('Remember me token error: ' . $e->getMessage());
    }
} else {
    // Clear any existing remember cookie on fresh login without checkbox
    setcookie('remember_token', '', time() - 3600, '/');
}

// Role-based redirect 
$redirect = $_SESSION['redirect_after_login'] ?? null;
unset($_SESSION['redirect_after_login']);

if (!$redirect) {
    $redirect = match($user['role']) {
        'admin'  => '../admin/dashboard.php',
        'seller' => '../seller/dashboard.php',
        default  => '../index.php',
    };
}
header("Location: $redirect");
exit;