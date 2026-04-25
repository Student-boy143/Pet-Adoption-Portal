<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../includes/db.php';

$userId = (int)($_POST['user_id'] ?? 0);

// Toggle directly in PostgreSQL
$stmt = $pdo->prepare("UPDATE users SET is_active = NOT is_active WHERE id = $1");
$stmt->execute([$userId]);

$_SESSION['success'] = 'User status updated.';
header('Location: dashboard.php?tab=users');
exit;