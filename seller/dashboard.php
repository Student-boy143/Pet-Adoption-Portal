<?php
// seller/dashboard.php
require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

$sellerId = $_SESSION['user_id'];

// Stats
$totalPets   = $pdo->prepare('SELECT COUNT(*) FROM pets WHERE listed_by = $1');
$totalPets->execute([$sellerId]);
$totalPets = (int)$totalPets->fetchColumn();

$pendingReqs = $pdo->prepare(
    'SELECT COUNT(*) FROM adoption_requests ar JOIN pets p ON ar.pet_id = p.id
     WHERE p.listed_by = $1 AND ar.status = $2'
);
$pendingReqs->execute([$sellerId, 'pending']);
$pendingReqs = (int)$pendingReqs->fetchColumn();

// My pets
$stmt = $pdo->prepare('SELECT * FROM pets WHERE listed_by = $1 ORDER BY created_at DESC');
$stmt->execute([$sellerId]);
$myPets = $stmt->fetchAll();

// Incoming requests for my pets
$stmt = $pdo->prepare(
    'SELECT ar.*, p.name AS pet_name, p.image_path,
            u.firstname, u.lastname, u.email AS buyer_email
     FROM adoption_requests ar
     JOIN pets  p ON ar.pet_id  = p.id
     JOIN users u ON ar.buyer_id = u.id
     WHERE p.listed_by = $1
     ORDER BY ar.created_at DESC'
);
$stmt->execute([$sellerId]);
$requests = $stmt->fetchAll();

$flash = $_SESSION['success'] ?? ''; unset($_SESSION['success']);
$error = $_SESSION['errors'][0] ?? ''; unset($_SESSION['errors']);
?>
<!doctype html>
<html lang="en">
<head>
  <title>Seller Dashboard — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/global.css"/>
  <link rel="stylesheet" href="../styles/header.css"/>
  <link rel="stylesheet" href="../styles/dashboard.css"/>
</head>
<body>
<div class="container">

  <!-- Header -->
  <header class="header">
    <div class="header-left">
      <div class="logo-circle">🐾</div>
      <h1 class="site-title">FurEver Home</h1>
    </div>
    <nav class="header-right">
      <a href="../index.php"  class="nav-link">Home</a>
      <a href="../adopt.php"  class="nav-link">Adopt</a>
      <span style="font-size:.9rem;font-weight:700;color:var(--brown);padding:8px 14px;">Hi, <?= userName() ?>! 🏪</span>
      <a href="../actions/logout.php" class="btn btn-ghost">Logout</a>
    </nav>
  </header>

  <div class="dash-wrap">
    <div class="dash-sidebar">
      <h3>Seller Panel</h3>
      <a href="#my-pets"   class="dash-link active">🐾 My Listings</a>
      <a href="#requests"  class="dash-link">📬 Requests</a>
      <a href="add-pet.php" class="dash-link">➕ Add New Pet</a>
    </div>

    <div class="dash-main">
      <?php if ($flash): ?><div class="alert-success">✅ <?= htmlspecialchars($flash) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert-error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>

      <!-- Stats -->
      <div class="stat-cards">
        <div class="stat-card"><span class="stat-big"><?= $totalPets ?></span><span>My Listings</span></div>
        <div class="stat-card highlight"><span class="stat-big"><?= $pendingReqs ?></span><span>Pending Requests</span></div>
      </div>

      <!-- My Pets -->
      <section id="my-pets">
        <div class="section-bar">
          <h2>My Pet Listings</h2>
          <a href="add-pet.php" class="btn btn-primary btn-sm">+ Add Pet</a>
        </div>

        <?php if (empty($myPets)): ?>
          <p class="empty-msg">You haven't listed any pets yet. <a href="add-pet.php">Add your first pet →</a></p>
        <?php else: ?>
        <div class="table-wrap">
          <table class="dash-table">
            <thead><tr><th>Photo</th><th>Name</th><th>Type</th><th>City</th><th>Status</th><th>Actions</th></tr></thead>
            <tbody>
            <?php foreach ($myPets as $pet): ?>
              <tr>
                <td><img src="<?= htmlspecialchars($pet['image_path']) ?>" alt="" class="table-thumb"/></td>
                <td><strong><?= htmlspecialchars($pet['name']) ?></strong><br/><small><?= htmlspecialchars($pet['breed']) ?></small></td>
                <td><?= ucfirst($pet['type']) ?></td>
                <td><?= htmlspecialchars($pet['city']) ?></td>
                <td><span class="status-badge status-<?= $pet['status'] ?>"><?= ucfirst($pet['status']) ?></span></td>
                <td class="actions-cell">
                  <a href="edit-pet.php?id=<?= $pet['id'] ?>" class="btn btn-ghost btn-sm">✏️ Edit</a>
                  <form method="POST" action="delete-pet.php" style="display:inline"
                        onsubmit="return confirm('Delete <?= htmlspecialchars($pet['name']) ?>?')">
                    <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>"/>
                    <button type="submit" class="btn btn-danger btn-sm">🗑 Delete</button>
                  </form>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </section>

      <!-- Requests -->
      <section id="requests" style="margin-top:48px;">
        <div class="section-bar">
          <h2>Adoption Requests</h2>
        </div>

        <?php if (empty($requests)): ?>
          <p class="empty-msg">No adoption requests yet.</p>
        <?php else: ?>
        <div class="table-wrap">
          <table class="dash-table">
            <thead><tr><th>Pet</th><th>Buyer</th><th>Email</th><th>Message</th><th>Date</th><th>Status</th><th>Action</th></tr></thead>
            <tbody>
            <?php foreach ($requests as $req): ?>
              <tr>
                <td>
                  <img src="<?= htmlspecialchars($req['image_path']) ?>" alt="" class="table-thumb"/>
                  <?= htmlspecialchars($req['pet_name']) ?>
                </td>
                <td><?= htmlspecialchars($req['firstname'].' '.$req['lastname']) ?></td>
                <td><a href="mailto:<?= htmlspecialchars($req['buyer_email']) ?>"><?= htmlspecialchars($req['buyer_email']) ?></a></td>
                <td><?= htmlspecialchars(substr($req['message'] ?? '—', 0, 60)) ?>…</td>
                <td><?= date('d M Y', strtotime($req['created_at'])) ?></td>
                <td><span class="status-badge status-<?= $req['status'] ?>"><?= ucfirst($req['status']) ?></span></td>
                <td>
                  <?php if ($req['status'] === 'pending'): ?>
                  <form method="POST" action="update-request.php" style="display:flex;gap:4px;">
                    <input type="hidden" name="request_id" value="<?= $req['id'] ?>"/>
                    <button name="action" value="approved" class="btn btn-success btn-sm">✅ Approve</button>
                    <button name="action" value="rejected" class="btn btn-danger btn-sm">❌ Reject</button>
                  </form>
                  <?php else: ?>
                    <span style="font-size:.82rem;color:var(--muted);">Done</span>
                  <?php endif; ?>
                </td>
              </tr>
            <?php endforeach; ?>
            </tbody>
          </table>
        </div>
        <?php endif; ?>
      </section>
    </div>
  </div>
</div>
</body>
</html>