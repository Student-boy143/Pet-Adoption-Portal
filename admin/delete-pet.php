<?php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../includes/db.php';

$petId = (int)($_POST['pet_id'] ?? 0);

$stmt = $pdo->prepare('DELETE FROM pets WHERE id = ?');
$success = $stmt->execute([$petId]);

if ($success) {
    $_SESSION['success'] = 'Pet deleted successfully';
} else {
    $_SESSION['errors'] = ['Failed to delete pet'];
}

header('Location: dashboard.php?tab=pets');
exit;