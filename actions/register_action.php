<?php
// actions/register_action.php

session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: ../register.php');
    exit;
}

$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname'] ?? '');
$email     = trim($_POST['email'] ?? '');
$password  = $_POST['password'] ?? '';
$confirm   = $_POST['repeat-password'] ?? '';
$role      = $_POST['role'] ?? 'buyer';

// Whitelist role
if (!in_array($role, ['buyer','seller'])) $role = 'buyer';

$errors = [];

// Validation
if (strlen($firstname) < 2) $errors[] = 'First name must be at least 2 characters.';
if (!preg_match("/^[a-zA-Z\s'\-]+$/", $firstname)) $errors[] = 'First name contains invalid characters.';
if (!empty($lastname) && !preg_match("/^[a-zA-Z\s'\-]+$/", $lastname)) $errors[] = 'Last name contains invalid characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = 'Enter a valid email address.';
if (strlen($password) < 8) $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirm) $errors[] = 'Passwords do not match.';

// Check existing email
if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = ?');
    $stmt->execute([$email]);
    if ($stmt->fetch()) {
        $errors[] = 'An account with this email already exists.';
    }
}

// If errors → redirect back
if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = compact('firstname','lastname','email','role');
    header('Location: ../register.php');
    exit;
}

//  Combine name (IMPORTANT FIX)
$name = $firstname . ' ' . $lastname;

$hashed = $password;

// Correct INSERT (name instead of firstname/lastname)
$stmt = $pdo->prepare(
    'INSERT INTO users (name, email, password, role) VALUES (?, ?, ?, ?) RETURNING id'
);
$stmt->execute([$name, $email, $hashed, $role]);

$row = $stmt->fetch();

// Store session
$_SESSION['user_id']   = (int)$row['id'];
$_SESSION['firstname'] = $name;
$_SESSION['role']      = $role;
$_SESSION['success']   = "Welcome, $name! Your account has been created.";

// Redirect
header('Location: ../index.php');
exit;