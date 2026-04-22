<?php
// buyer/my-requests.php

require_once '../includes/auth_check.php';
requireRole('buyer');
require_once '../includes/db.php';

$buyerId = $_SESSION['user_id'];

// ✅ FIXED QUERY
$stmt = $pdo->prepare(
    'SELECT ar.*, p.name AS pet_name, p.image, p.type, p.breed, p.city,
            u.name AS seller_name, u.email AS seller_email
     FROM adoption_requests ar
     JOIN pets  p ON ar.pet_id    = p.id
     JOIN users u ON p.listed_by  = u.id
     WHERE ar.user_id = ?
     ORDER BY ar.created_at DESC'
);
$stmt->execute([$buyerId]);
$requests = $stmt->fetchAll();

$flash = $_SESSION['success'] ?? '';
unset($_SESSION['success']);
?>

<!doctype html>
<html lang="en">
<head>
  <title>My Requests — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/global.css"/>
  <link rel="stylesheet" href="../styles/header.css"/>
  <link rel="stylesheet" href="../styles/dashboard.css"/>
</head>
<body>
<div class="container">

  <header class="header">
    <div class="header-left">
      <div class="logo-circle">🐾</div>
      <h1 class="site-title">FurEver Home</h1>
    </div>
    <nav class="header-right">
      <a href="../index.php"  class="nav-link">Home</a>
      <a href="../adopt.php"  class="nav-link">Adopt</a>
      <span style="font-size:.9rem;font-weight:700;color:var(--brown);padding:8px 14px;">
        Hi, <?= userName() ?>! 👋
      </span>
      <a href="../actions/logout.php" class="btn btn-ghost">Logout</a>
    </nav>
  </header>

  <div class="dash-wrap">
    <div class="dash-sidebar">
      <h3>Buyer Panel</h3>
      <a href="../adopt.php"    class="dash-link">🐾 Browse Pets</a>
      <a href="my-requests.php" class="dash-link active">📬 My Requests</a>
    </div>

    <div class="dash-main">

      <?php if ($flash): ?>
        <div class="alert-success"> <?= htmlspecialchars($flash) ?></div>
      <?php endif; ?>

      <div class="section-bar">
        <h2>My Adoption Requests</h2>
        <span><?= count($requests) ?> request<?= count($requests) !== 1 ? 's' : '' ?></span>
      </div>

      <?php if (empty($requests)): ?>
        <div style="text-align:center;padding:60px 20px;color:var(--muted);">
          <div style="font-size:3rem;margin-bottom:16px;">📭</div>
          <h3 style="font-family:var(--font-display);color:var(--brown);margin-bottom:8px;">
            No requests yet
          </h3>
          <p>Browse pets and send your first request!</p>
          <a href="../adopt.php" class="btn btn-primary btn-lg" style="margin-top:20px;">
            Browse Pets
          </a>
        </div>

      <?php else: ?>

        <div class="table-wrap">
          <table class="dash-table">
            <thead>
              <tr>
                <th>Pet</th>
                <th>Details</th>
                <th>Seller</th>
                <th>Message</th>
                <th>Date</th>
                <th>Status</th>
              </tr>
            </thead>
            <tbody>

            <?php foreach ($requests as $r): ?>
              <tr>

                <!-- Pet -->
                <td>
                  <img src="../<?= htmlspecialchars($r['image']) ?>" class="table-thumb" alt="">
                  <strong><?= htmlspecialchars($r['pet_name']) ?></strong>
                </td>

                <!-- Details -->
                <td>
                  <?= ucfirst($r['type']) ?> · <?= htmlspecialchars($r['breed']) ?><br>
                  <small> <?= htmlspecialchars($r['city']) ?></small>
                </td>

                <!-- Seller -->
                <td>
                  <?= htmlspecialchars($r['seller_name']) ?><br>
                  <small><?= htmlspecialchars($r['seller_email']) ?></small>
                </td>

                <!-- Message -->
                <td>
                  <?= htmlspecialchars(substr($r['message'] ?? '—', 0, 60)) ?>
                </td>

                <!-- Date -->
                <td>
                  <?= date('d M Y', strtotime($r['created_at'])) ?>
                </td>

                <!-- Status -->
                <td>
                  <strong><?= strtoupper($r['status']) ?></strong>

                  <?php if ($r['status'] === 'approved'): ?>
                    <div style="color:green;">Contact seller</div>
                  <?php elseif ($r['status'] === 'rejected'): ?>
                    <div style="color:red;">Try another pet</div>
                  <?php endif; ?>

                </td>

              </tr>
            <?php endforeach; ?>

            </tbody>
          </table>
        </div>

      <?php endif; ?>

    </div>
  </div>

</div>
</body>
</html>