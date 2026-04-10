<?php
// admin/toggle-user.php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../includes/db.php';

$userId   = (int)($_POST['user_id']   ?? 0);
$isActive = (int)($_POST['is_active'] ?? 1);

$pdo->prepare('UPDATE users SET is_active=$1 WHERE id=$2')->execute([$isActive, $userId]);
$_SESSION['success'] = 'User status updated.';
header('Location: dashboard.php?tab=users');
exit;