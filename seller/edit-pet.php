<?php
// seller/edit-pet.php

require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

$petId    = (int)($_GET['id'] ?? 0);
$sellerId = $_SESSION['user_id'];

//  FIXED QUERY (use ? instead of $1)
$stmt = $pdo->prepare('SELECT * FROM pets WHERE id = ? AND listed_by = ?');
$stmt->execute([$petId, $sellerId]);
$pet = $stmt->fetch();

if (!$pet) {
    header('Location: dashboard.php');
    exit;
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$old = $_SESSION['old'] ?? $pet;
unset($_SESSION['old']);

function oval($key, $default = '') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default);
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Edit <?= htmlspecialchars($pet['name']) ?> — FurEver Home</title>
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
    <a href="dashboard.php" class="nav-link">← Dashboard</a>
    <a href="../actions/logout.php" class="btn btn-ghost">Logout</a>
  </nav>
</header>

<div class="form-page">
  <h1>Edit — <?= htmlspecialchars($pet['name']) ?></h1>
  <p class="form-sub">Update the pet details below.</p>

  <?php if (!empty($errors)): ?>
    <div class="alert-error">
      <ul>
        <?php foreach($errors as $e): ?>
          <li><?= htmlspecialchars($e) ?></li>
        <?php endforeach; ?>
      </ul>
    </div>
  <?php endif; ?>

  <form method="POST" action="save-pet.php" enctype="multipart/form-data" class="pet-form">
    <input type="hidden" name="action" value="edit"/>
    <input type="hidden" name="pet_id" value="<?= $pet['id'] ?>"/>

    <div class="form-grid">

      <div class="form-group">
        <label>Pet Name *</label>
        <input type="text" name="name" value="<?= oval('name') ?>" required/>
      </div>

      <div class="form-group">
        <label>Type *</label>
        <select name="type" required>
          <?php foreach(['dog','cat','rabbit','bird','other'] as $t): ?>
            <option value="<?= $t ?>" <?= oval('type') === $t ? 'selected' : '' ?>>
              <?= ucfirst($t) ?>
            </option>
          <?php endforeach; ?>
        </select>
      </div>

      <div class="form-group">
        <label>Breed</label>
        <input type="text" name="breed" value="<?= oval('breed') ?>"/>
      </div>

      <div class="form-group">
        <label>Age (years) *</label>
        <input type="number" name="age_years" step="0.1" min="0" value="<?= oval('age_years') ?>" required/>
      </div>

      <div class="form-group">
        <label>Gender *</label>
        <select name="gender" required>
          <option value="male" <?= oval('gender') === 'male' ? 'selected' : '' ?>>Male</option>
          <option value="female" <?= oval('gender') === 'female' ? 'selected' : '' ?>>Female</option>
        </select>
      </div>

      <div class="form-group">
        <label>City *</label>
        <input type="text" name="city" value="<?= oval('city') ?>" required/>
      </div>

    </div>

    <div class="form-group full">
      <label>Description *</label>
      <textarea name="description" rows="4" required><?= oval('description') ?></textarea>
    </div>

    <div class="form-group full">
      <label>Health Information</label>
      <textarea name="health_info" rows="3"><?= oval('health_info') ?></textarea>
    </div>

    <div class="form-group full">
      <label>Current Photo</label>
      <div style="display:flex;align-items:center;gap:16px;margin-bottom:8px;">
        <!-- ✅ FIXED image -->
        <img src="<?= htmlspecialchars($pet['image']) ?>" 
             alt="" 
             style="width:80px;height:80px;object-fit:cover;border-radius:8px;"/>
        <span style="color:var(--muted);font-size:.88rem;">Current photo</span>
      </div>

      <label>Change Photo</label>
      <input type="file" name="pet_image" accept="image/jpeg,image/png,image/webp"/>
      <small style="color:var(--muted)">Leave blank to keep current photo.</small>
    </div>

    <div class="form-actions">
      <button type="submit" class="btn btn-primary btn-lg">Save Changes</button>
      <a href="dashboard.php" class="btn btn-ghost btn-lg">Cancel</a>
    </div>

  </form>
</div>

</div>
</body>
</html>