<?php
// login.php

session_start();

// If already logged in → redirect
if (isset($_SESSION['user_id'])) {
    $dest = match($_SESSION['role'] ?? 'buyer') {
        'admin'  => 'admin/dashboard.php',
        'seller' => 'seller/dashboard.php',
        default  => 'index.php',
    };
    header("Location: $dest");
    exit;
}

$errors = $_SESSION['errors'] ?? [];
unset($_SESSION['errors']);

$old = $_SESSION['old'] ?? [];
unset($_SESSION['old']);
?>
<!doctype html>
<html lang="en">
<head>
  <title>Sign In — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <link rel="stylesheet" href="styles/auth.css"/>

  <style>
    .field-row.error { border-color:#e53e3e; }
    .field-row.error label { background:#fff5f5; }
    .field-error { font-size:.78rem; color:#e53e3e; margin-top:-2px; margin-left:2px; min-height:16px; display:block; }
    .alert-error { background:#fff5f5; border:1.5px solid #e53e3e; color:#c53030; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:.88rem; }
  </style>
</head>

<body class="auth-page">

<div class="auth-split">

  <!-- LEFT PANEL -->
  <div class="auth-panel">

    <a href="index.php" class="auth-logo">
      <div class="logo-circle">🐾</div>
      <span>FurEver Home</span>
    </a>

    <div class="auth-form-wrap">

      <h1>Welcome Back</h1>
      <p class="auth-sub">Sign in to continue. Admins, sellers and buyers all use this page.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert-error"><?= htmlspecialchars($errors[0]) ?></div>
      <?php endif; ?>

      <form class="auth-form" id="login-form" method="POST" action="actions/login_action.php" novalidate>

        <!-- Email -->
        <div class="field-row" id="email-row">
          <label for="email-input"><span class="field-icon">@</span></label>
          <input type="email" id="email-input" name="email"
                 placeholder="Email address"
                 value="<?= htmlspecialchars($old['email'] ?? '') ?>"/>
        </div>
        <span class="field-error" id="email-error"></span>

        <!-- Password -->
        <div class="field-row" id="password-row">
          <label for="password-input"><span class="field-icon">🔒</span></label>
          <input type="password" id="password-input" name="password"
                 placeholder="Password"/>
        </div>
        <span class="field-error" id="password-error"></span>

        <!-- Meta (no remember me now) -->
        <div class="form-meta">
          <span></span>
          <a href="#" class="forgot-link">Forgot password?</a>
        </div>

        <button type="submit" class="btn btn-primary btn-lg auth-submit">
          Sign In →
        </button>
      </form>

      <p class="auth-switch">
        Don't have an account?
        <a href="register.php">Register here</a>
      </p>

      <!-- Test Accounts -->
      <div style="margin-top:24px;padding:14px;background:var(--warm);border-radius:10px;font-size:.8rem;color:var(--muted);">
        <strong style="color:var(--brown);">Test Accounts:</strong><br/>
        Admin: admin@fureverhome.in<br/>
        Seller: deepa@rescue.in<br/>
        Password: <code>Admin@123</code>
      </div>

    </div>
  </div>

  <!-- RIGHT PANEL -->
  <div class="auth-deco">
    <div class="deco-content">
      <div class="deco-emoji-grid">
        <span>🐕</span><span>🐈</span><span>🐇</span>
        <span>🦜</span><span>🐩</span><span>🐾</span>
      </div>
      <h2>"Every pet has a story.<br/>Be part of theirs."</h2>
      <p>1,240+ pets have found their forever homes through FurEver Home.</p>
    </div>
  </div>

</div>

<script>
document.getElementById('login-form').addEventListener('submit', function(e) {
  let ok = true;

  const email = document.getElementById('email-input').value.trim();
  const pass  = document.getElementById('password-input').value;

  if (!email || !/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(email)) {
    document.getElementById('email-row').classList.add('error');
    document.getElementById('email-error').textContent = 'Enter a valid email.';
    ok = false;
  } else {
    document.getElementById('email-row').classList.remove('error');
    document.getElementById('email-error').textContent = '';
  }

  if (!pass) {
    document.getElementById('password-row').classList.add('error');
    document.getElementById('password-error').textContent = 'Password is required.';
    ok = false;
  } else {
    document.getElementById('password-row').classList.remove('error');
    document.getElementById('password-error').textContent = '';
  }

  if (!ok) e.preventDefault();
});
</script>

</body>
</html>