<?php
// seller/add-pet.php
require_once '../includes/auth_check.php';
requireRole('seller');
require_once '../includes/db.php';

$errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']);
$old    = $_SESSION['old']    ?? []; unset($_SESSION['old']);

function oldVal($key, $default='') {
    global $old;
    return htmlspecialchars($old[$key] ?? $default);
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>Add Pet — FurEver Home</title>
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
      <a href="dashboard.php" class="nav-link">← Dashboard</a>
      <a href="../actions/logout.php" class="btn btn-ghost">Logout</a>
    </nav>
  </header>

  <div class="form-page">
    <h1>Add a New Pet</h1>
    <p class="form-sub">Fill in the details below to list a pet for adoption.</p>

    <?php if (!empty($errors)): ?>
      <div class="alert-error"><ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul></div>
    <?php endif; ?>

    <form method="POST" action="save-pet.php" enctype="multipart/form-data" class="pet-form">
      <input type="hidden" name="action" value="add"/>

      <div class="form-grid">
        <div class="form-group">
          <label>Pet Name *</label>
          <input type="text" name="name" value="<?= oldVal('name') ?>" required/>
        </div>
        <div class="form-group">
          <label>Type *</label>
          <select name="type" required>
            <option value="">Select type</option>
            <?php foreach(['dog','cat','rabbit','bird','other'] as $t): ?>
              <option value="<?=$t?>" <?= oldVal('type')===$t?'selected':'' ?>><?= ucfirst($t) ?></option>
            <?php endforeach; ?>
          </select>
        </div>
        <div class="form-group">
          <label>Breed</label>
          <input type="text" name="breed" value="<?= oldVal('breed') ?>"/>
        </div>
        <div class="form-group">
          <label>Age (years) *</label>
          <input type="number" name="age_years" min="0" max="30" step="0.1" value="<?= oldVal('age_years','1') ?>" required/>
        </div>
        <div class="form-group">
          <label>Gender *</label>
          <select name="gender" required>
            <option value="">Select</option>
            <option value="male"   <?= oldVal('gender')==='male'  ?'selected':'' ?>>Male</option>
            <option value="female" <?= oldVal('gender')==='female'?'selected':'' ?>>Female</option>
          </select>
        </div>
        <div class="form-group">
          <label>City *</label>
          <input type="text" name="city" value="<?= oldVal('city') ?>" required/>
        </div>
      </div>

      <div class="form-group full">
        <label>Description *</label>
        <textarea name="description" rows="4" required><?= oldVal('description') ?></textarea>
      </div>

      <div class="form-group full">
        <label>Health Information</label>
        <textarea name="health_info" rows="3" placeholder="Vaccinated? Neutered? Any conditions?"><?= oldVal('health_info') ?></textarea>
      </div>

      <div class="form-group full">
        <label>Pet Photo</label>
        <input type="file" name="pet_image" accept="image/jpeg,image/png,image/webp"/>
        <small style="color:var(--muted)">JPG/PNG/WEBP, max 2MB. Leave blank to use default image.</small>
      </div>

      <div class="form-actions">
        <button type="submit" class="btn btn-primary btn-lg">List Pet 🐾</button>
        <a href="dashboard.php" class="btn btn-ghost btn-lg">Cancel</a>
      </div>
    </form>
  </div>
</div>
</body>
</html>