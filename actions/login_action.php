<?php
// actions/login_action.php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../login.php'); exit; }

$email    = trim($_POST['email']    ?? '');
$password = $_POST['password']      ?? '';

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

session_regenerate_id(true);
$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['firstname'] = $user['firstname'];
$_SESSION['role']      = $user['role'];

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