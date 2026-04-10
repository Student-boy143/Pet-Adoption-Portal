<?php
// adopt-request.php  (root level)
require_once 'includes/auth_check.php';
requireRole('buyer');
require_once 'includes/db.php';

$petId   = (int)($_GET['pet_id'] ?? 0);
$buyerId = $_SESSION['user_id'];

if (!$petId) { header('Location: adopt.php'); exit; }

$stmt = $pdo->prepare(
    'SELECT p.*, u.firstname AS seller_name, u.email AS seller_email
     FROM pets p LEFT JOIN users u ON p.listed_by = u.id
     WHERE p.id = $1 AND p.status = $2 LIMIT 1'
);
$stmt->execute([$petId, 'available']);
$pet = $stmt->fetch();

if (!$pet) {
    $_SESSION['errors'] = ['This pet is no longer available for adoption.'];
    header('Location: adopt.php'); exit;
}

$dup = $pdo->prepare('SELECT id FROM adoption_requests WHERE pet_id=$1 AND buyer_id=$2');
$dup->execute([$petId, $buyerId]);
$alreadyRequested = (bool)$dup->fetch();

$success = ''; $formError = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if ($alreadyRequested) {
        $formError = 'You have already sent a request for this pet.';
    } else {
        $message = trim($_POST['message'] ?? '');
        $pdo->prepare('INSERT INTO adoption_requests (pet_id,buyer_id,message) VALUES ($1,$2,$3)')
            ->execute([$petId, $buyerId, $message]);
        $pdo->prepare("UPDATE pets SET status='pending' WHERE id=$1")->execute([$petId]);
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
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <link rel="stylesheet" href="styles/header.css"/>
  <link rel="stylesheet" href="styles/dashboard.css"/>
  <style>
    .req-card { background:var(--white); border-radius:var(--radius-md); box-shadow:var(--shadow-md); overflow:hidden; display:flex; margin-bottom:32px; }
    .req-card img { width:220px; height:220px; object-fit:cover; flex-shrink:0; }
    .req-card-info { padding:24px 28px; display:flex; flex-direction:column; justify-content:center; gap:6px; }
    .req-card-info h2 { font-family:var(--font-display); color:var(--brown); font-size:1.6rem; }
    .req-card-info p  { color:var(--muted); font-size:.9rem; }
    .req-form textarea { width:100%; padding:14px; border:2px solid var(--warm); border-radius:var(--radius-sm); font:inherit; font-size:.95rem; resize:vertical; min-height:110px; transition:border-color .2s; }
    .req-form textarea:focus { outline:none; border-color:var(--coral); }
    .req-form label { display:block; font-weight:700; font-size:.82rem; text-transform:uppercase; letter-spacing:.05em; color:var(--brown); margin-bottom:6px; }
    @media(max-width:600px){ .req-card{flex-direction:column} .req-card img{width:100%;height:200px} }
  </style>
</head>
<body>
<div class="container">
  <header class="header">
    <div class="header-left"><div class="logo-circle">🐾</div><h1 class="site-title">FurEver Home</h1></div>
    <nav class="header-right">
      <a href="index.php" class="nav-link">Home</a>
      <a href="adopt.php" class="nav-link">Adopt</a>
      <a href="buyer/my-requests.php" class="nav-link">My Requests</a>
      <a href="actions/logout.php" class="btn btn-ghost">Logout</a>
    </nav>
  </header>

  <div class="request-page">
    <h1 style="font-family:var(--font-display);color:var(--brown);margin-bottom:6px;">Adopt <?= htmlspecialchars($pet['name']) ?> 🐾</h1>
    <p style="color:var(--muted);margin-bottom:28px;">Tell the seller why you'd be a great match.</p>

    <div class="req-card">
      <img src="<?= htmlspecialchars($pet['image_path']) ?>" alt="<?= htmlspecialchars($pet['name']) ?>"/>
      <div class="req-card-info">
        <h2><?= htmlspecialchars($pet['name']) ?></h2>
        <p><?= ucfirst($pet['type']) ?> · <?= htmlspecialchars($pet['breed']) ?></p>
        <p><?= ucfirst($pet['gender']) ?> · <?= ageStr((float)$pet['age_years']) ?></p>
        <p>📍 <?= htmlspecialchars($pet['city']) ?></p>
        <?php if ($pet['health_info']): ?>
          <p>🩺 <?= htmlspecialchars($pet['health_info']) ?></p>
        <?php endif; ?>
        <p style="margin-top:8px;">Seller: <strong><?= htmlspecialchars($pet['seller_name'] ?? '—') ?></strong></p>
      </div>
    </div>

    <?php if ($success): ?>
      <div class="alert-success">✅ <?= htmlspecialchars($success) ?> The seller will contact you soon.</div>
      <div style="display:flex;gap:12px;margin-top:16px;">
        <a href="buyer/my-requests.php" class="btn btn-primary btn-lg">View My Requests →</a>
        <a href="adopt.php" class="btn btn-ghost btn-lg">Browse More Pets</a>
      </div>
    <?php elseif ($alreadyRequested): ?>
      <div class="alert-error">⚠ You have already sent an adoption request for this pet.</div>
      <a href="buyer/my-requests.php" class="btn btn-primary" style="margin-top:12px;">View Request Status →</a>
    <?php else: ?>
      <?php if ($formError): ?><div class="alert-error">⚠ <?= htmlspecialchars($formError) ?></div><?php endif; ?>
      <form method="POST" class="req-form">
        <div style="margin-bottom:20px;">
          <label for="message">Your message (optional)</label>
          <textarea id="message" name="message"
            placeholder="Hi! I'm very interested in adopting <?= htmlspecialchars($pet['name']) ?>. I live in a spacious home and have experience with pets..."></textarea>
        </div>
        <div style="display:flex;gap:12px;">
          <button type="submit" class="btn btn-primary btn-lg">Send Request 🐾</button>
          <a href="adopt.php" class="btn btn-ghost btn-lg">Cancel</a>
        </div>
      </form>
    <?php endif; ?>
  </div>
</div>
</body>
</html>