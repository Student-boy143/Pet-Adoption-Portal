<?php
// adopt-request.php

require_once 'includes/auth_check.php';
requireRole('buyer');
require_once 'includes/db.php';

$petId   = (int)($_GET['pet_id'] ?? 0);
$buyerId = $_SESSION['user_id'];

if (!$petId) {
    header('Location: adopt.php');
    exit;
}

//  FIXED QUERY
$stmt = $pdo->prepare(
    'SELECT p.*, u.name AS seller_name, u.email AS seller_email
     FROM pets p 
     LEFT JOIN users u ON p.listed_by = u.id
     WHERE p.id = ? AND p.status = ? LIMIT 1'
);
$stmt->execute([$petId, 'available']);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['errors'] = ['This pet is no longer available for adoption.'];
    header('Location: adopt.php');
    exit;
}

//  FIXED buyer_id → user_id
$dup = $pdo->prepare('SELECT id FROM adoption_requests WHERE pet_id = ? AND user_id = ?');
$dup->execute([$petId, $buyerId]);
$alreadyRequested = (bool)$dup->fetch();

$success = '';
$formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($alreadyRequested) {
        $formError = 'You have already sent a request for this pet.';
    } else {
        $message = trim($_POST['message'] ?? '');

        //  FIXED INSERT
        $pdo->prepare(
            'INSERT INTO adoption_requests (pet_id, user_id, message) VALUES (?, ?, ?)'
        )->execute([$petId, $buyerId, $message]);

        // Update pet status
        $pdo->prepare("UPDATE pets SET status = 'pending' WHERE id = ?")
            ->execute([$petId]);

        $success = "Your request for {$pet['name']} has been sent!";
        $alreadyRequested = true;
    }
}

function ageStr(float $y): string {
    if ($y < 1) return round($y * 12) . ' months';
    return $y == 1 ? '1 year' : "{$y} years";
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Adopt <?= htmlspecialchars($pet['name']) ?> — FurEver Home</title>
  <link rel="stylesheet" href="styles/global.css"/>
</head>
<body>

<h1>Adopt <?= htmlspecialchars($pet['name']) ?></h1>

<img src="<?= htmlspecialchars($pet['image']) ?>" width="200">

<p><?= htmlspecialchars($pet['name']) ?></p>
<p>Seller: <?= htmlspecialchars($pet['seller_name'] ?? '—') ?></p>

<?php if ($success): ?>
  <p><?= $success ?></p>
<?php elseif ($alreadyRequested): ?>
  <p>You already requested this pet.</p>
<?php else: ?>

<form method="POST">
  <textarea name="message" placeholder="Why do you want this pet?"></textarea><br>
  <button type="submit">Send Request</button>
</form>

<?php endif; ?>

</body>
</html>