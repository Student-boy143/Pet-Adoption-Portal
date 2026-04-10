<?php
// seller/update-request.php
require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') { header('Location: dashboard.php'); exit; }

$requestId = (int)($_POST['request_id'] ?? 0);
$action    = $_POST['action'] ?? '';
$sellerId  = $_SESSION['user_id'];

if (!in_array($action, ['approved','rejected'])) { header('Location: dashboard.php'); exit; }

// Verify this request belongs to a pet listed by this seller
$stmt = $pdo->prepare(
    'SELECT ar.id, ar.pet_id FROM adoption_requests ar
     JOIN pets p ON ar.pet_id = p.id
     WHERE ar.id=$1 AND p.listed_by=$2'
);
$stmt->execute([$requestId, $sellerId]);
$req = $stmt->fetch();

if (!$req) { $_SESSION['errors'] = ['Request not found.']; header('Location: dashboard.php'); exit; }

// Update request status
$pdo->prepare('UPDATE adoption_requests SET status=$1 WHERE id=$2')
    ->execute([$action, $requestId]);

// If approved → mark pet as adopted; if rejected → back to available
$petStatus = $action === 'approved' ? 'adopted' : 'available';
$pdo->prepare('UPDATE pets SET status=$1 WHERE id=$2')
    ->execute([$petStatus, $req['pet_id']]);

// If one request approved, reject all other pending requests for that pet
if ($action === 'approved') {
    $pdo->prepare(
        "UPDATE adoption_requests SET status='rejected' WHERE pet_id=$1 AND id != $2 AND status='pending'"
    )->execute([$req['pet_id'], $requestId]);
}

$_SESSION['success'] = "Adoption request has been $action.";
header('Location: dashboard.php');
exit;