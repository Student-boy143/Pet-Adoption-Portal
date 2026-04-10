<?php
// get_registration.php
// Expt 5 — Processes registration form using $_GET method
// Place at: fureverhome/get_registration.php
// ─────────────────────────────────────────────────────────

$data   = [];
$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'GET' && isset($_GET['firstname'])) {

    // ── Collect via $_GET ─────────────────────
    $firstname = htmlspecialchars(trim($_GET['firstname'] ?? ''));
    $lastname  = htmlspecialchars(trim($_GET['lastname']  ?? ''));
    $email     = htmlspecialchars(trim($_GET['email']     ?? ''));
    $password  = $_GET['password'] ?? '';
    $role      = htmlspecialchars(trim($_GET['role']      ?? 'buyer'));

    // ── Server-side validation ────────────────
    if (strlen($firstname) < 2)
        $errors[] = 'First name must be at least 2 characters.';
    if (!preg_match("/^[a-zA-Z\s'\-]+$/", $firstname))
        $errors[] = 'First name must contain only letters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8)
        $errors[] = 'Password must be at least 8 characters.';
    if (!in_array($role, ['buyer','seller']))
        $errors[] = 'Invalid role selected.';

    $data = compact('firstname','lastname','email','role');
}

$submitted = isset($_GET['firstname']);
?>
<!doctype html>
<html lang="en">
<head>
  <title>GET Registration — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <style>
    body { background: var(--cream); font-family: var(--font-body); }
    .page-wrap { max-width: 680px; margin: 60px auto; padding: 0 24px 80px; }
    h1 { font-family: var(--font-display); color: var(--brown); margin-bottom: 6px; }
    .badge { display:inline-block; background:#e0f7fa; color:#00695c; font-size:.78rem; font-weight:700; padding:4px 12px; border-radius:40px; margin-bottom:24px; }
    .result-box { background:#fff; border-radius:14px; padding:28px 32px; box-shadow:0 4px 20px rgba(74,44,42,.1); margin-bottom:24px; }
    .result-box h2 { font-family:var(--font-display); color:var(--brown); font-size:1.2rem; margin-bottom:16px; }
    table { width:100%; border-collapse:collapse; font-size:.92rem; }
    th { text-align:left; padding:8px 12px; background:var(--warm); color:var(--brown); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; }
    td { padding:10px 12px; border-bottom:1px solid var(--warm); color:var(--dark); }
    tr:last-child td { border-bottom:none; }
    .alert-error { background:#fff5f5; border:1.5px solid #e53e3e; color:#c53030; border-radius:10px; padding:14px 18px; margin-bottom:20px; }
    .alert-error ul { margin:6px 0 0 16px; }
    .alert-success { background:#f0fff4; border:1.5px solid #38a169; color:#276749; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-weight:600; }
    .method-note { font-size:.82rem; background:#e0f7fa; border-radius:8px; padding:10px 14px; color:#00695c; margin-bottom:20px; }
    .url-display { font-size:.78rem; background:#f5f5f5; border-radius:6px; padding:8px 12px; font-family:monospace; word-break:break-all; color:#555; margin-bottom:12px; }
    .warn-note { font-size:.78rem; background:#fff8e1; border-radius:6px; padding:8px 12px; color:#f57f17; margin-bottom:20px; }
    .back-link { color:var(--rose); font-weight:700; text-decoration:none; }
    .back-link:hover { text-decoration:underline; }
    form { display:flex; flex-direction:column; gap:14px; margin-top:24px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    label { font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--brown); display:block; margin-bottom:5px; }
    input, select { width:100%; padding:11px 14px; border:2px solid var(--warm); border-radius:8px; font:inherit; font-size:.95rem; background:var(--cream); box-sizing:border-box; }
    input:focus, select:focus { outline:none; border-color:#26c6da; }
    button[type=submit] { background:#26c6da; color:#fff; font:inherit; font-weight:700; font-size:1rem; padding:13px; border:none; border-radius:40px; cursor:pointer; transition:.2s; }
    button[type=submit]:hover { background:#00acc1; }
  </style>
</head>
<body>
<div class="page-wrap">
  <a href="index.php" class="back-link">← Back to Home</a>
  <br/><br/>
  <h1>Expt 5 — $_GET Method</h1>
  <div class="badge">METHOD: $_GET</div>

  <div class="method-note">
    <strong>How it works:</strong> Form data is appended to the URL as query parameters
    (e.g. <code>?firstname=Ramesh&email=...</code>). Visible in the browser address bar.
    Not suitable for passwords or sensitive data.
  </div>

  <?php if ($submitted): ?>
    <!-- Show current URL so students can see GET params -->
    <div class="url-display">
      <strong>Current URL:</strong> <?= htmlspecialchars($_SERVER['REQUEST_URI']) ?>
    </div>
    <div class="warn-note">
      ⚠ Notice: Your data (including password) is visible in the URL above. This is why GET is not recommended for login/registration in real apps.
    </div>
  <?php endif; ?>

  <?php if ($submitted): ?>
    <?php if (!empty($errors)): ?>
      <div class="alert-error">
        <strong>Validation failed:</strong>
        <ul><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
      </div>
    <?php else: ?>
      <div class="alert-success">✅ Form submitted via $_GET. Data received below.</div>
      <div class="result-box">
        <h2>Submitted Registration Data ($_GET)</h2>
        <table>
          <tr><th>Field</th><th>Value</th><th>Raw $_GET Key</th></tr>
          <tr><td>First Name</td><td><?= $data['firstname'] ?></td><td><code>$_GET['firstname']</code></td></tr>
          <tr><td>Last Name</td><td><?= $data['lastname'] ?: '—' ?></td><td><code>$_GET['lastname']</code></td></tr>
          <tr><td>Email</td><td><?= $data['email'] ?></td><td><code>$_GET['email']</code></td></tr>
          <tr><td>Password</td><td><em>(visible in URL — security risk!)</em></td><td><code>$_GET['password']</code></td></tr>
          <tr><td>Role</td><td><?= ucfirst($data['role']) ?></td><td><code>$_GET['role']</code></td></tr>
        </table>
      </div>
    <?php endif; ?>
  <?php endif; ?>

  <div class="result-box">
    <h2>Registration Form (submits via GET)</h2>
    <form method="GET" action="get_registration.php">
      <div class="form-row">
        <div>
          <label>First Name *</label>
          <input type="text" name="firstname" value="<?= htmlspecialchars($_GET['firstname'] ?? '') ?>" placeholder="e.g. Ramesh"/>
        </div>
        <div>
          <label>Last Name</label>
          <input type="text" name="lastname" value="<?= htmlspecialchars($_GET['lastname'] ?? '') ?>" placeholder="e.g. Patel"/>
        </div>
      </div>
      <div>
        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_GET['email'] ?? '') ?>" placeholder="e.g. ramesh@example.com"/>
      </div>
      <div>
        <label>Password *</label>
        <input type="password" name="password" placeholder="Min 8 characters"/>
      </div>
      <div>
        <label>I want to:</label>
        <select name="role">
          <option value="buyer"  <?= ($_GET['role'] ?? '') === 'buyer'  ? 'selected' : '' ?>>Adopt a Pet (Buyer)</option>
          <option value="seller" <?= ($_GET['role'] ?? '') === 'seller' ? 'selected' : '' ?>>List / Rescue (Seller)</option>
        </select>
      </div>
      <button type="submit">Submit via $_GET</button>
    </form>
  </div>

  <p style="font-size:.85rem;color:var(--muted);">
    See also:
    <a href="post_registration.php" class="back-link">$_POST version</a> ·
    <a href="request_registration.php" class="back-link">$_REQUEST version</a>
  </p>
</div>
</body>
</html>