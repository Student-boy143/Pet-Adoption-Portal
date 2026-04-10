<?php
// admin/delete-user.php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../includes/db.php';

$userId = (int)($_POST['user_id'] ?? 0);
$pdo->prepare('DELETE FROM users WHERE id=$1 AND role != $2')->execute([$userId,'admin']);
$_SESSION['success'] = 'User deleted.';
header('Location: dashboard.php?tab=users');
exit;