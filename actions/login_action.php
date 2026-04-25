<?php
// actions/login_action.php

session_start();
require_once '../includes/db.php';


// Allow only POST
if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../login.php');
    exit;
}

// Get form data
$email    = trim($_POST['email'] ?? '');
$password = $_POST['password'] ?? '';

// Validate input
if (empty($email) || empty($password)) {
    $_SESSION['errors'] = ['Please enter your email and password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// Fetch user
$stmt = $pdo->prepare('SELECT * FROM users WHERE email = ? AND is_active = TRUE LIMIT 1');
$stmt->execute([$email]);
$user = $stmt->fetch();

// See 2 cases h
// 1)user not found
// 2)Pass mismatch
if (!$user || $password !== $user['password']) {
    $_SESSION['errors'] = ['Incorrect email or password.'];
    $_SESSION['old']    = ['email' => $email];
    header('Location: ../login.php');
    exit;
}

// Regenerate session for security
// new session id
session_regenerate_id(true);

// Store session data
$_SESSION['user_id']   = (int) $user['id'];
$_SESSION['firstname'] = $user['name'];
$_SESSION['role']      = $user['role'];

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