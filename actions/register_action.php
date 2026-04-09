<?php
// actions/register_action.php
session_start();
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: ../register.php'); exit; }

$firstname = trim($_POST['firstname'] ?? '');
$lastname  = trim($_POST['lastname']  ?? '');
$email     = trim($_POST['email']     ?? '');
$password  = $_POST['password']       ?? '';
$confirm   = $_POST['repeat-password']?? '';
$role      = $_POST['role']           ?? 'buyer';

// Whitelist role
if (!in_array($role, ['buyer','seller'])) $role = 'buyer';

$errors = [];

if (strlen($firstname) < 2)                         $errors[] = 'First name must be at least 2 characters.';
if (!preg_match("/^[a-zA-Z\s'\-]+$/", $firstname))  $errors[] = 'First name contains invalid characters.';
if (!empty($lastname) && !preg_match("/^[a-zA-Z\s'\-]+$/", $lastname)) $errors[] = 'Last name contains invalid characters.';
if (!filter_var($email, FILTER_VALIDATE_EMAIL))      $errors[] = 'Enter a valid email address.';
if (strlen($password) < 8)                           $errors[] = 'Password must be at least 8 characters.';
if ($password !== $confirm)                          $errors[] = 'Passwords do not match.';

if (empty($errors)) {
    $stmt = $pdo->prepare('SELECT id FROM users WHERE email = $1');
    $stmt->execute([$email]);
    if ($stmt->fetch()) $errors[] = 'An account with this email already exists.';
}

if (!empty($errors)) {
    $_SESSION['errors'] = $errors;
    $_SESSION['old']    = compact('firstname','lastname','email','role');
    header('Location: ../register.php');
    exit;
}

$hashed = password_hash($password, PASSWORD_DEFAULT);
$stmt   = $pdo->prepare(
    'INSERT INTO users (firstname, lastname, email, password, role) VALUES ($1,$2,$3,$4,$5) RETURNING id'
);
$stmt->execute([$firstname, $lastname, $email, $hashed, $role]);
$row = $stmt->fetch();

$_SESSION['user_id']   = (int) $row['id'];
$_SESSION['firstname'] = $firstname;
$_SESSION['role']      = $role;
$_SESSION['success']   = "Welcome, $firstname! Your account has been created.";

header('Location: ../index.php');
exit;