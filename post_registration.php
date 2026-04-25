<?php
// post_registration.php
session_start();

$data   = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    // ── Collect via $_POST 
    $firstname = htmlspecialchars(trim($_POST['firstname'] ?? ''));
    $lastname  = htmlspecialchars(trim($_POST['lastname']  ?? ''));
    $email     = htmlspecialchars(trim($_POST['email']     ?? ''));
    $password  = $_POST['password'] ?? '';
    $role      = htmlspecialchars(trim($_POST['role']      ?? 'buyer'));

    // ── Server-side validation 
    if (strlen($firstname) < 2)
        $errors[] = 'First name must be at least 2 characters.';
    if (!preg_match("/^[a-zA-Z\s'\-]+$/", $firstname))
        $errors[] = 'First name contains only letters, spaces, hyphens and apostrophes.';
    if (!empty($lastname) && !preg_match("/^[a-zA-Z\s'\-]+$/", $lastname))
        $errors[] = 'Last name contains invalid characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Enter a valid email address (e.g. user@example.com).';
    if (strlen($password) < 8)
        $errors[] = 'Password must be at least 8 characters.';
    if (!in_array($role, ['buyer','seller']))
        $errors[] = 'Invalid role selected.';

    // ── Store sanitized data for display 
    $data = compact('firstname','lastname','email','role');
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>POST Registration — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <style>
    body { background: var(--cream); font-family: var(--font-body); }
    .page-wrap { max-width: 680px; margin: 60px auto; padding: 0 24px 80px; }
    h1 { font-family: var(--font-display); color: var(--brown); margin-bottom: 6px; }
    .badge { display:inline-block; background:var(--coral); color:var(--brown); font-size:.78rem; font-weight:700; padding:4px 12px; border-radius:40px; margin-bottom:24px; }
    .result-box { background:#fff; border-radius:14px; padding:28px 32px; box-shadow:0 4px 20px rgba(74,44,42,.1); margin-bottom:24px; }
    .result-box h2 { font-family:var(--font-display); color:var(--brown); font-size:1.2rem; margin-bottom:16px; }
    table { width:100%; border-collapse:collapse; font-size:.92rem; }
    th { text-align:left; padding:8px 12px; background:var(--warm); color:var(--brown); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; }
    td { padding:10px 12px; border-bottom:1px solid var(--warm); color:var(--dark); }
    tr:last-child td { border-bottom:none; }
    .alert-error { background:#fff5f5; border:1.5px solid #e53e3e; color:#c53030; border-radius:10px; padding:14px 18px; margin-bottom:20px; }
    .alert-error ul { margin:6px 0 0 16px; }
    .alert-success { background:#f0fff4; border:1.5px solid #38a169; color:#276749; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-weight:600; }
    .method-note { font-size:.82rem; background:var(--warm); border-radius:8px; padding:10px 14px; color:var(--brown); margin-bottom:20px; }
    .back-link { color:var(--rose); font-weight:700; text-decoration:none; }
    .back-link:hover { text-decoration:underline; }
    form { display:flex; flex-direction:column; gap:14px; margin-top:24px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    label { font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--brown); display:block; margin-bottom:5px; }
    input, select { width:100%; padding:11px 14px; border:2px solid var(--warm); border-radius:8px; font:inherit; font-size:.95rem; background:var(--cream); box-sizing:border-box; }
    input:focus, select:focus { outline:none; border-color:var(--coral); }
    button[type=submit] { background:var(--coral); color:var(--brown); font:inherit; font-weight:700; font-size:1rem; padding:13px; border:none; border-radius:40px; cursor:pointer; transition:.2s; }
    button[type=submit]:hover { background:#f06272; color:#fff; }
  </style>
</head>
<body>
<div class="page-wrap">
  <a href="index.php" class="back-link">← Back to Home</a>
  <br/><br/>
  <div class="badge">METHOD: $_POST</div>

  <div class="method-note">
    <strong>How it works:</strong> Form data is sent in the HTTP request body. Not visible in the URL.
    More secure than GET for sensitive data like passwords.
  </div>

  <?php if ($_SERVER['REQUEST_METHOD'] === 'POST'): ?>

    <?php if (!empty($errors)): ?>
      <div class="alert-error">
        <strong>Validation failed:</strong>
        <ul><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
      </div>
    <?php else: ?>
      <div class="alert-success">Form submitted successfully via $_POST. Data received and sanitized below.</div>
      <div class="result-box">
        <h2>Submitted Registration Data ($_POST)</h2>
        <table>
          <tr><th>Field</th><th>Value</th><th>Raw $_POST Key</th></tr>
          <tr><td>First Name</td><td><?= $data['firstname'] ?></td><td><code>$_POST['firstname']</code></td></tr>
          <tr><td>Last Name</td><td><?= $data['lastname'] ?: '—' ?></td><td><code>$_POST['lastname']</code></td></tr>
          <tr><td>Email</td><td><?= $data['email'] ?></td><td><code>$_POST['email']</code></td></tr>
          <tr><td>Password</td><td><em>(hidden for security)</em></td><td><code>$_POST['password']</code></td></tr>
          <tr><td>Role</td><td><?= ucfirst($data['role']) ?></td><td><code>$_POST['role']</code></td></tr>
        </table>
      </div>
    <?php endif; ?>

  <?php endif; ?>

  <!-- Registration Form -->
  <div class="result-box">
    <h2>Registration Form (submits via POST)</h2>
    <form method="POST" action="post_registration.php">
      <div class="form-row">
        <div>
          <label>First Name *</label>
          <input type="text" name="firstname" value="<?= htmlspecialchars($_POST['firstname'] ?? '') ?>" placeholder="e.g. Ramesh"/>
        </div>
        <div>
          <label>Last Name</label>
          <input type="text" name="lastname" value="<?= htmlspecialchars($_POST['lastname'] ?? '') ?>" placeholder="e.g. Patel"/>
        </div>
      </div>
      <div>
        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_POST['email'] ?? '') ?>" placeholder="e.g. ramesh@example.com"/>
      </div>
      <div>
        <label>Password *</label>
        <input type="password" name="password" placeholder="Min 8 characters"/>
      </div>
      <div>
        <label>I want to:</label>
        <select name="role">
          <option value="buyer"  <?= ($_POST['role'] ?? '') === 'buyer'  ? 'selected' : '' ?>>Adopt a Pet (Buyer)</option>
          <option value="seller" <?= ($_POST['role'] ?? '') === 'seller' ? 'selected' : '' ?>>List / Rescue (Seller)</option>
        </select>
      </div>
      <button type="submit">Submit via $_POST</button>
    </form>
  </div>

  <p style="font-size:.85rem;color:var(--muted);">
    See also:
    <a href="get_registration.php" class="back-link">$_GET version</a> ·
    <a href="request_registration.php" class="back-link">$_REQUEST version</a>
  </p>
</div>
</body>
</html>