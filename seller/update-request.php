<?php
// seller/update-request.php

require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    header('Location: dashboard.php');
    exit;
}

$requestId = (int)($_POST['request_id'] ?? 0);
$action    = $_POST['action'] ?? '';
$sellerId  = $_SESSION['user_id'];

if (!in_array($action, ['approved','rejected'])) {
    header('Location: dashboard.php');
    exit;
}

//  FIXED QUERY (use ?)
$stmt = $pdo->prepare(
    'SELECT ar.id, ar.pet_id FROM adoption_requests ar
     JOIN pets p ON ar.pet_id = p.id
     WHERE ar.id = ? AND p.listed_by = ?'
);
$stmt->execute([$requestId, $sellerId]);
$req = $stmt->fetch();

if (!$req) {
    $_SESSION['errors'] = ['Request not found.'];
    header('Location: dashboard.php');
    exit;
}

//  Update request status
$stmt = $pdo->prepare('UPDATE adoption_requests SET status = ? WHERE id = ?');
$stmt->execute([$action, $requestId]);

// Update pet status
$petStatus = $action === 'approved' ? 'adopted' : 'available';
$stmt = $pdo->prepare('UPDATE pets SET status = ? WHERE id = ?');
$stmt->execute([$petStatus, $req['pet_id']]);

// Reject other requests if approved
if ($action === 'approved') {
    $stmt = $pdo->prepare(
        "UPDATE adoption_requests 
         SET status = 'rejected' 
         WHERE pet_id = ? AND id != ? AND status = 'pending'"
    );
    $stmt->execute([$req['pet_id'], $requestId]);
}

$_SESSION['success'] = "Adoption request has been $action.";

header('Location: dashboard.php');
exit;