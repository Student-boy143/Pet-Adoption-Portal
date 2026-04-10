<?php
// admin/dashboard.php
require_once '../includes/auth_check.php';
requireRole('admin');
require_once '../includes/db.php';

//  Stats 
$totalUsers   = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role != 'admin'")->fetchColumn();
$totalBuyers  = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='buyer'")->fetchColumn();
$totalSellers = (int)$pdo->query("SELECT COUNT(*) FROM users WHERE role='seller'")->fetchColumn();
$totalPets    = (int)$pdo->query("SELECT COUNT(*) FROM pets")->fetchColumn();
$availPets    = (int)$pdo->query("SELECT COUNT(*) FROM pets WHERE status='available'")->fetchColumn();
$adoptedPets  = (int)$pdo->query("SELECT COUNT(*) FROM pets WHERE status='adopted'")->fetchColumn();
$totalReqs    = (int)$pdo->query("SELECT COUNT(*) FROM adoption_requests")->fetchColumn();
$pendingReqs  = (int)$pdo->query("SELECT COUNT(*) FROM adoption_requests WHERE status='pending'")->fetchColumn();

//  All Users 
$users = $pdo->query(
    "SELECT * FROM users WHERE role != 'admin' ORDER BY created_at DESC"
)->fetchAll();

//  All Pets ─
$pets = $pdo->query(
    'SELECT p.*, u.firstname AS seller_name FROM pets p
     LEFT JOIN users u ON p.listed_by=u.id ORDER BY p.created_at DESC'
)->fetchAll();

//  All Requests 
$requests = $pdo->query(
    "SELECT ar.*, p.name AS pet_name, p.image_path,
            u.firstname AS buyer_name, u.email AS buyer_email,
            s.firstname AS seller_name
     FROM adoption_requests ar
     JOIN pets  p ON ar.pet_id   = p.id
     JOIN users u ON ar.buyer_id = u.id
     LEFT JOIN users s ON p.listed_by = s.id
     ORDER BY ar.created_at DESC"
)->fetchAll();

$flash = $_SESSION['success'] ?? ''; unset($_SESSION['success']);
$error = $_SESSION['errors'][0] ?? ''; unset($_SESSION['errors']);

$tab = $_GET['tab'] ?? 'overview';
?>
<!doctype html>
<html lang="en">
<head>
  <title>Admin Dashboard — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="../styles/global.css"/>
  <link rel="stylesheet" href="../styles/header.css"/>
  <link rel="stylesheet" href="../styles/dashboard.css"/>
</head>
<body>
<div class="container">

  <header class="header">
    <div class="header-left"><div class="logo-circle">🐾</div><h1 class="site-title">FurEver Home</h1></div>
    <nav class="header-right">
      <a href="../index.php" class="nav-link">Home</a>
      <span style="font-size:.9rem;font-weight:700;color:var(--brown);padding:8px 14px;">Admin Panel 🛡️</span>
      <a href="../actions/logout.php" class="btn btn-ghost">Logout</a>
    </nav>
  </header>

  <div class="dash-wrap">
    <div class="dash-sidebar">
      <h3>Admin Panel</h3>
      <a href="?tab=overview"  class="dash-link <?= $tab==='overview' ?'active':'' ?>">📊 Overview</a>
      <a href="?tab=users"     class="dash-link <?= $tab==='users'    ?'active':'' ?>">👥 Users</a>
      <a href="?tab=pets"      class="dash-link <?= $tab==='pets'     ?'active':'' ?>">🐾 All Pets</a>
      <a href="?tab=requests"  class="dash-link <?= $tab==='requests' ?'active':'' ?>">📬 Requests</a>
    </div>

    <div class="dash-main">
      <?php if ($flash): ?><div class="alert-success">✅ <?= htmlspecialchars($flash) ?></div><?php endif; ?>
      <?php if ($error): ?><div class="alert-error">⚠ <?= htmlspecialchars($error) ?></div><?php endif; ?>

      <!--  OVERVIEW  -->
      <?php if ($tab === 'overview'): ?>
      <h2>System Overview</h2>
      <div class="stat-cards" style="grid-template-columns:repeat(4,1fr)">
        <div class="stat-card"><span class="stat-big"><?= $totalUsers ?></span><span>Total Users</span></div>
        <div class="stat-card"><span class="stat-big"><?= $totalPets ?></span><span>Total Pets</span></div>
        <div class="stat-card highlight"><span class="stat-big"><?= $availPets ?></span><span>Available</span></div>
        <div class="stat-card"><span class="stat-big"><?= $adoptedPets ?></span><span>Adopted</span></div>
        <div class="stat-card"><span class="stat-big"><?= $totalBuyers ?></span><span>Buyers</span></div>
        <div class="stat-card"><span class="stat-big"><?= $totalSellers ?></span><span>Sellers</span></div>
        <div class="stat-card highlight"><span class="stat-big"><?= $pendingReqs ?></span><span>Pending Requests</span></div>
        <div class="stat-card"><span class="stat-big"><?= $totalReqs ?></span><span>Total Requests</span></div>
      </div>

      <!-- ── USERS  -->
      <?php elseif ($tab === 'users'): ?>
      <div class="section-bar">
        <h2>Manage Users</h2>
        <span id="result-count"><?= count($users) ?> users</span>
      </div>
      <div class="table-wrap">
        <table class="dash-table">
          <thead><tr><th>ID</th><th>Name</th><th>Email</th><th>Role</th><th>Joined</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($users as $u): ?>
            <tr>
              <td>#<?= $u['id'] ?></td>
              <td><?= htmlspecialchars($u['firstname'].' '.$u['lastname']) ?></td>
              <td><?= htmlspecialchars($u['email']) ?></td>
              <td><span class="status-badge status-<?= $u['role'] ?>"><?= ucfirst($u['role']) ?></span></td>
              <td><?= date('d M Y', strtotime($u['created_at'])) ?></td>
              <td><span class="status-badge status-<?= $u['is_active']?'available':'rejected' ?>">
                <?= $u['is_active'] ? 'Active' : 'Suspended' ?>
              </span></td>
              <td>
                <form method="POST" action="toggle-user.php" style="display:inline">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                  <input type="hidden" name="is_active" value="<?= $u['is_active']?'0':'1' ?>"/>
                  <button class="btn btn-sm <?= $u['is_active']?'btn-danger':'btn-success' ?>">
                    <?= $u['is_active']?'Suspend':'Activate' ?>
                  </button>
                </form>
                <form method="POST" action="delete-user.php" style="display:inline"
                      onsubmit="return confirm('Delete this user and all their data?')">
                  <input type="hidden" name="user_id" value="<?= $u['id'] ?>"/>
                  <button class="btn btn-danger btn-sm">🗑 Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ── PETS  -->
      <?php elseif ($tab === 'pets'): ?>
      <div class="section-bar">
        <h2>All Pet Listings</h2>
        <span id="result-count"><?= count($pets) ?> pets</span>
      </div>
      <div class="table-wrap">
        <table class="dash-table">
          <thead><tr><th>Photo</th><th>Name</th><th>Type</th><th>City</th><th>Seller</th><th>Status</th><th>Actions</th></tr></thead>
          <tbody>
          <?php foreach ($pets as $p): ?>
            <tr>
              <td><img src="../<?= htmlspecialchars($p['image_path']) ?>" class="table-thumb" alt=""/></td>
              <td><strong><?= htmlspecialchars($p['name']) ?></strong><br/><small><?= htmlspecialchars($p['breed']) ?></small></td>
              <td><?= ucfirst($p['type']) ?></td>
              <td><?= htmlspecialchars($p['city']) ?></td>
              <td><?= htmlspecialchars($p['seller_name'] ?? '—') ?></td>
              <td><span class="status-badge status-<?= $p['status'] ?>"><?= ucfirst($p['status']) ?></span></td>
              <td>
                <form method="POST" action="delete-pet.php" style="display:inline"
                      onsubmit="return confirm('Delete <?= htmlspecialchars($p['name']) ?>?')">
                  <input type="hidden" name="pet_id" value="<?= $p['id'] ?>"/>
                  <button class="btn btn-danger btn-sm">🗑 Delete</button>
                </form>
              </td>
            </tr>
          <?php endforeach; ?>
          </tbody>
        </table>
      </div>

      <!-- ── REQUESTS  -->
      <?php elseif ($tab === 'requests'): ?>
      <div class="section-bar">
        <h2>All Adoption Requests</h2>
        <span id="result-count"><?= count($requests) ?> requests</span>
      </div>
      <div class="table-wrap">
        <table class="dash-table">
          <thead><tr><th>Pet</th><th>Buyer</th><th>Seller</th><th>Message</th><th>Date</th><th>Status</th></tr></thead>
          <tbody>
          <?php foreach ($requests as $r): ?>
            <tr>
              <td>
                <img src="../<?= htmlspecialchars($r['image_path']) ?>" class="table-thumb" alt=""/>
                <?= htmlspecialchars($r['pet_name']) ?>
              </td>
              <td><?= htmlspecialchars($r['buyer_name']) ?><br/><small><?= htmlspecialchars($r['buyer_email']) ?></small></td>
              <td><?= htmlspecialchars($r['seller_name'] ?? '—') ?></td>
              <td><?= htmlspecialchars(substr($r['message'] ?? '—', 0, 50)) ?>…</td>
              <td><?= date('d M Y', strtotime($r['created_at'])) ?></td>
              <td><span class="status-badge status-<?= $r['status'] ?>"><?= ucfirst($r['status']) ?></span></td>
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