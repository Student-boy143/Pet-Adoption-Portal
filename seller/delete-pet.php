<?php
require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $petId = (int)($_POST['pet_id'] ?? 0);
    $sellerId = $_SESSION['user_id'];

    // Delete only if pet belongs to seller
    $stmt = $pdo->prepare('DELETE FROM pets WHERE id = ? AND listed_by = ?');
    $stmt->execute([$petId, $sellerId]);

    $_SESSION['success'] = "Pet deleted successfully.";
}

header('Location: dashboard.php');
exit;