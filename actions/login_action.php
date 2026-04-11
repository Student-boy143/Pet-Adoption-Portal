<?php
// actions/login_action.php
// Sessions + Remember Me

session_start();
require_once '../includes/db.php';

// Show errors (for debugging — remove later)
ini_set('display_errors', 1);
error_reporting(E_ALL);

// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// Get form data
$email      = trim($_POST['email'] ?? '');
$password   = $_POST['password'] ?? '';
$rememberMe = isset($_POST['remember']);

// Validate input
if (empty($email) || empty($password)) {
    $_SESSION['errors'] = ['Please enter your email and password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// ✅ FIXED QUERY (use ? instead of $1)
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND is_active = TRUE LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

// ✅ FIXED PASSWORD CHECK
if (!$user || $password !== $user['password']) {
    $_SESSION['errors'] = ['Incorrect email or password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// Regenerate session (security)
session_regenerate_id(true);

// ✅ FIXED FIELD NAME (name instead of firstname)
$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['firstname'] = $user['name'];
$_SESSION['role']      = $user['role'];

// Remember Me
if ($rememberMe) {
    $token  = bin2hex(random_bytes(32));
    $expiry = time() + (30 * 24 * 60 * 60);

    try {
        $pdo->prepare('UPDATE users SET remember_token = ? WHERE id = ?')
            ->execute([$token, $user['id']]);

        setcookie(
            'remember_token',
            $token,
            $expiry,
            '/',
            '',
            false,
            true
        );
    } catch (Exception $e) {
        error_log('Remember me token error: ' . $e->getMessage());
    }
} else {
    setcookie('remember_token', '', time() - 3600, '/');
}

// Redirect based on role
$redirect = $_SESSION['redirect_after_login'] ?? null;
unset($_SESSION['redirect_after_login']);

if (!$redirect) {
    switch ($user['role']) {
        case 'admin':
            $redirect = '../admin/dashboard.php';
            break;
        case 'seller':
            $redirect = '../seller/dashboard.php';
            break;
        default:
            $redirect = '../index.php';
    }
}

header("Location: $redirect");
exit;