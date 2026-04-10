<?php
// request_registration.php
// Expt 5 — Processes registration using $_REQUEST method
// 
// $_REQUEST merges $_GET, $_POST and $_COOKIE.
// Works regardless of whether form uses GET or POST.

$data      = [];
$errors    = [];
$method    = $_SERVER['REQUEST_METHOD'];
$submitted = ($method === 'POST' || isset($_GET['firstname']));

if ($submitted) {

    //  Collect via $_REQUEST 
    $firstname = htmlspecialchars(trim($_REQUEST['firstname'] ?? ''));
    $lastname  = htmlspecialchars(trim($_REQUEST['lastname']  ?? ''));
    $email     = htmlspecialchars(trim($_REQUEST['email']     ?? ''));
    $password  = $_REQUEST['password'] ?? '';
    $role      = htmlspecialchars(trim($_REQUEST['role']      ?? 'buyer'));

    //  Validation 
    if (strlen($firstname) < 2)
        $errors[] = 'First name must be at least 2 characters.';
    if (!preg_match("/^[a-zA-Z\s'\-]+$/", $firstname))
        $errors[] = 'First name must contain only letters, spaces, hyphens or apostrophes.';
    if (!empty($lastname) && !preg_match("/^[a-zA-Z\s'\-]+$/", $lastname))
        $errors[] = 'Last name contains invalid characters.';
    if (!filter_var($email, FILTER_VALIDATE_EMAIL))
        $errors[] = 'Enter a valid email address.';
    if (strlen($password) < 8)
        $errors[] = 'Password must be at least 8 characters.';
    if (!in_array($role, ['buyer','seller']))
        $errors[] = 'Invalid role selected.';

    $data = compact('firstname','lastname','email','role');
}
?>
<!doctype html>
<html lang="en">
<head>
  <title>REQUEST Registration — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <style>
    body { background: var(--cream); font-family: var(--font-body); }
    .page-wrap { max-width: 680px; margin: 60px auto; padding: 0 24px 80px; }
    h1 { font-family: var(--font-display); color: var(--brown); margin-bottom: 6px; }
    .badge { display:inline-block; background:#f3e5f5; color:#6a1b9a; font-size:.78rem; font-weight:700; padding:4px 12px; border-radius:40px; margin-bottom:24px; }
    .result-box { background:#fff; border-radius:14px; padding:28px 32px; box-shadow:0 4px 20px rgba(74,44,42,.1); margin-bottom:24px; }
    .result-box h2 { font-family:var(--font-display); color:var(--brown); font-size:1.2rem; margin-bottom:16px; }
    table { width:100%; border-collapse:collapse; font-size:.92rem; }
    th { text-align:left; padding:8px 12px; background:var(--warm); color:var(--brown); font-size:.78rem; text-transform:uppercase; letter-spacing:.05em; }
    td { padding:10px 12px; border-bottom:1px solid var(--warm); color:var(--dark); }
    tr:last-child td { border-bottom:none; }
    .alert-error { background:#fff5f5; border:1.5px solid #e53e3e; color:#c53030; border-radius:10px; padding:14px 18px; margin-bottom:20px; }
    .alert-error ul { margin:6px 0 0 16px; }
    .alert-success { background:#f0fff4; border:1.5px solid #38a169; color:#276749; border-radius:10px; padding:14px 18px; margin-bottom:20px; font-weight:600; }
    .method-note { font-size:.82rem; background:#f3e5f5; border-radius:8px; padding:10px 14px; color:#6a1b9a; margin-bottom:20px; }
    .comparison-table { width:100%; border-collapse:collapse; font-size:.82rem; margin-top:8px; }
    .comparison-table th { background:#6a1b9a; color:#fff; padding:8px 12px; text-align:left; }
    .comparison-table td { padding:8px 12px; border-bottom:1px solid #f3e5f5; }
    .comparison-table tr:last-child td { border-bottom:none; }
    .back-link { color:var(--rose); font-weight:700; text-decoration:none; }
    .back-link:hover { text-decoration:underline; }
    form { display:flex; flex-direction:column; gap:14px; margin-top:24px; }
    .form-row { display:grid; grid-template-columns:1fr 1fr; gap:14px; }
    .method-toggle { display:flex; gap:12px; margin-bottom:4px; }
    .method-toggle label { text-transform:none; font-size:.9rem; font-weight:600; display:flex; align-items:center; gap:6px; cursor:pointer; }
    label { font-size:.82rem; font-weight:700; text-transform:uppercase; letter-spacing:.05em; color:var(--brown); display:block; margin-bottom:5px; }
    input[type=text], input[type=email], input[type=password], select { width:100%; padding:11px 14px; border:2px solid var(--warm); border-radius:8px; font:inherit; font-size:.95rem; background:var(--cream); box-sizing:border-box; }
    input:focus, select:focus { outline:none; border-color:#ba68c8; }
    button[type=submit] { background:#8e24aa; color:#fff; font:inherit; font-weight:700; font-size:1rem; padding:13px; border:none; border-radius:40px; cursor:pointer; transition:.2s; }
    button[type=submit]:hover { background:#6a1b9a; }
  </style>
</head>
<body>
<div class="page-wrap">
  <a href="index.php" class="back-link">← Back to Home</a>
  <br/><br/>
  <h1>Expt 5 — $_REQUEST Method</h1>
  <div class="badge">METHOD: $_REQUEST</div>

  <div class="method-note">
    <strong>How it works:</strong> <code>$_REQUEST</code> is a superglobal that combines
    <code>$_GET</code>, <code>$_POST</code>, and <code>$_COOKIE</code>. It works regardless
    of which method the form uses. You can switch the form between GET and POST below — the
    PHP processing code stays exactly the same.
  </div>

  <?php if ($submitted && empty($errors)): ?>
    <div class="alert-success">
      ✅ Data received via $_REQUEST (form used <strong><?= $method ?></strong> method).
    </div>
    <div class="result-box">
      <h2>Submitted Data ($_REQUEST)</h2>
      <table>
        <tr><th>Field</th><th>Value</th><th>Source</th></tr>
        <tr><td>First Name</td><td><?= $data['firstname'] ?></td><td><code>$_REQUEST['firstname']</code></td></tr>
        <tr><td>Last Name</td><td><?= $data['lastname'] ?: '—' ?></td><td><code>$_REQUEST['lastname']</code></td></tr>
        <tr><td>Email</td><td><?= $data['email'] ?></td><td><code>$_REQUEST['email']</code></td></tr>
        <tr><td>Password</td><td><em>(not displayed)</em></td><td><code>$_REQUEST['password']</code></td></tr>
        <tr><td>Role</td><td><?= ucfirst($data['role']) ?></td><td><code>$_REQUEST['role']</code></td></tr>
        <tr><td>HTTP Method Used</td><td><?= $method ?></td><td><code>$_SERVER['REQUEST_METHOD']</code></td></tr>
      </table>
    </div>
  <?php elseif ($submitted && !empty($errors)): ?>
    <div class="alert-error">
      <strong>Validation failed:</strong>
      <ul><?php foreach($errors as $e): ?><li><?= $e ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <!-- Comparison table -->
  <div class="result-box">
    <h2>$_POST vs $_GET vs $_REQUEST</h2>
    <table class="comparison-table">
      <tr><th>Feature</th><th>$_POST</th><th>$_GET</th><th>$_REQUEST</th></tr>
      <tr><td>Data location</td><td>Request body</td><td>URL query string</td><td>Both</td></tr>
      <tr><td>Visible in URL</td><td>No</td><td>Yes</td><td>Depends on form method</td></tr>
      <tr><td>Max data size</td><td>Large</td><td>~2000 chars</td><td>Depends on method</td></tr>
      <tr><td>Bookmarkable</td><td>No</td><td>Yes</td><td>Depends</td></tr>
      <tr><td>Use for passwords</td><td>✅ Yes</td><td>❌ No</td><td>⚠ Avoid</td></tr>
      <tr><td>Security</td><td>More secure</td><td>Less secure</td><td>Moderate</td></tr>
    </table>
  </div>

  <div class="result-box">
    <h2>Registration Form (try switching POST ↔ GET)</h2>
    <!-- Toggle method: POST or GET -->
    <div class="method-toggle">
      <label><input type="radio" name="m" id="use-post" checked onchange="document.getElementById('req-form').method='POST'"/> Use POST</label>
      <label><input type="radio" name="m" id="use-get"       onchange="document.getElementById('req-form').method='GET'"/>  Use GET</label>
    </div>
    <form method="POST" action="request_registration.php" id="req-form">
      <div class="form-row">
        <div>
          <label>First Name *</label>
          <input type="text" name="firstname" value="<?= htmlspecialchars($_REQUEST['firstname'] ?? '') ?>" placeholder="e.g. Ramesh"/>
        </div>
        <div>
          <label>Last Name</label>
          <input type="text" name="lastname" value="<?= htmlspecialchars($_REQUEST['lastname'] ?? '') ?>" placeholder="e.g. Patel"/>
        </div>
      </div>
      <div>
        <label>Email *</label>
        <input type="email" name="email" value="<?= htmlspecialchars($_REQUEST['email'] ?? '') ?>" placeholder="e.g. ramesh@example.com"/>
      </div>
      <div>
        <label>Password *</label>
        <input type="password" name="password" placeholder="Min 8 characters"/>
      </div>
      <div>
        <label>Role</label>
        <select name="role">
          <option value="buyer"  <?= ($_REQUEST['role'] ?? 'buyer') === 'buyer'  ? 'selected' : '' ?>>Adopt a Pet (Buyer)</option>
          <option value="seller" <?= ($_REQUEST['role'] ?? '')       === 'seller' ? 'selected' : '' ?>>List / Rescue (Seller)</option>
        </select>
      </div>
      <button type="submit">Submit via $_REQUEST</button>
    </form>
  </div>

  <p style="font-size:.85rem;color:var(--muted);">
    See also:
    <a href="post_registration.php" class="back-link">$_POST version</a> ·
    <a href="get_registration.php"  class="back-link">$_GET version</a>
  </p>
</div>
</body>
</html>