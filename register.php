<?php
// register.php
session_start();
if (isset($_SESSION['user_id'])) { header('Location: index.php'); exit; }

$errors = $_SESSION['errors'] ?? []; unset($_SESSION['errors']);
$old    = $_SESSION['old']    ?? []; unset($_SESSION['old']);

function old($k, $d='') { global $old; return htmlspecialchars($old[$k] ?? $d); }
?>
<!doctype html>
<html lang="en">
<head>
  <title>Register — FurEver Home</title>
  <link href="https://fonts.googleapis.com/css2?family=Playfair+Display:wght@700;900&family=Nunito:wght@400;600;700;800&display=swap" rel="stylesheet"/>
  <link rel="stylesheet" href="styles/global.css"/>
  <link rel="stylesheet" href="styles/auth.css"/>
  <style>
    .field-row.error   { border-color:#e53e3e; }
    .field-row.success { border-color:#38a169; }
    .field-row.error   label { background:#fff5f5; }
    .field-row.success label { background:#f0fff4; }
    .field-error { font-size:.78rem; color:#e53e3e; margin-top:-2px; margin-left:2px; min-height:16px; display:block; }
    .alert-error { background:#fff5f5; border:1.5px solid #e53e3e; color:#c53030; border-radius:10px; padding:12px 16px; margin-bottom:16px; font-size:.88rem; }
    .alert-error ul { margin:6px 0 0 16px; }
    .strength-wrap { margin-top:-2px; margin-bottom:2px; }
    .strength-bar-bg { height:4px; background:#f0e8d8; border-radius:4px; overflow:hidden; margin-bottom:3px; }
    .strength-bar { height:100%; width:0%; border-radius:4px; transition:width .3s,background-color .3s; }
    .strength-text { font-size:.75rem; color:#7a6a5a; }
    .strength-wrap.weak   .strength-bar { width:33%; background:#e53e3e; }
    .strength-wrap.medium .strength-bar { width:66%; background:#d97706; }
    .strength-wrap.strong .strength-bar { width:100%; background:#38a169; }
    .strength-wrap.weak   .strength-text { color:#e53e3e; }
    .strength-wrap.medium .strength-text { color:#d97706; }
    .strength-wrap.strong .strength-text { color:#38a169; }
  </style>
</head>
<body class="auth-page">
<div class="auth-split">
  <div class="auth-panel">
    <a href="index.php" class="auth-logo">
      <div class="logo-circle">🐾</div><span>FurEver Home</span>
    </a>
    <div class="auth-form-wrap">
      <h1>Create Account</h1>
      <p class="auth-sub">Join thousands of pet lovers finding their furever match.</p>

      <?php if (!empty($errors)): ?>
        <div class="alert-error"><strong>Please fix the following:</strong>
          <ul><?php foreach($errors as $e): ?><li><?= htmlspecialchars($e) ?></li><?php endforeach; ?></ul>
        </div>
      <?php endif; ?>

      <form class="auth-form" id="register-form" method="POST" action="actions/register_action.php" novalidate>

        <div class="field-row" id="firstname-row">
          <label for="firstname-input"><span class="field-icon">👤</span></label>
          <input type="text" id="firstname-input" name="firstname" placeholder="First Name" value="<?= old('firstname') ?>"/>
        </div>
        <span class="field-error" id="firstname-error"></span>

        <div class="field-row" id="lastname-row">
          <label for="lastname-input"><span class="field-icon">👤</span></label>
          <input type="text" id="lastname-input" name="lastname" placeholder="Last Name (optional)" value="<?= old('lastname') ?>"/>
        </div>
        <span class="field-error" id="lastname-error"></span>

        <div class="field-row" id="email-row">
          <label for="email-input"><span class="field-icon">@</span></label>
          <input type="email" id="email-input" name="email" placeholder="Email address" value="<?= old('email') ?>"/>
        </div>
        <span class="field-error" id="email-error"></span>

        <div class="field-row" id="password-row">
          <label for="password-input"><span class="field-icon">🔒</span></label>
          <input type="password" id="password-input" name="password" placeholder="Password"/>
        </div>
        <span class="field-error" id="password-error"></span>
        <div class="strength-wrap" id="strength-wrap" style="display:none;">
          <div class="strength-bar-bg"><div class="strength-bar"></div></div>
          <span class="strength-text" id="strength-text"></span>
        </div>

        <div class="field-row" id="confirm-row">
          <label for="repeat-password-input"><span class="field-icon">🔒</span></label>
          <input type="password" id="repeat-password-input" name="repeat-password" placeholder="Confirm Password"/>
        </div>
        <span class="field-error" id="confirm-error"></span>

        <!-- Role selector -->
        <div class="account-type">
          <p class="account-type-label">I want to:</p>
          <div class="account-type-options">
            <label class="type-option <?= (old('role','buyer')==='buyer')?'selected':'' ?>">
              <input type="radio" name="role" value="buyer" <?= (old('role','buyer')==='buyer')?'checked':'' ?>/>
              <span class="type-icon">🏠</span><span>Adopt a Pet</span>
            </label>
            <label class="type-option <?= (old('role')==='seller')?'selected':'' ?>">
              <input type="radio" name="role" value="seller" <?= (old('role')==='seller')?'checked':'' ?>/>
              <span class="type-icon">🤝</span><span>List / Rescue</span>
            </label>
          </div>
        </div>

        <button type="submit" class="btn btn-primary btn-lg auth-submit">Create Account →</button>
      </form>
      <p class="auth-switch">Already have an account? <a href="login.php">Sign in</a></p>
    </div>
  </div>

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
  function showError(r,e,m){document.getElementById(r).classList.add('error');document.getElementById(r).classList.remove('success');document.getElementById(e).textContent=m;}
  function showSuccess(r,e){document.getElementById(r).classList.add('success');document.getElementById(r).classList.remove('error');document.getElementById(e).textContent='';}
  function clearState(r,e){document.getElementById(r).classList.remove('error','success');document.getElementById(e).textContent='';}

  function vFirst(){const v=document.getElementById('firstname-input').value.trim();if(!v){showError('firstname-row','firstname-error','⚠ First name required.');return false;}if(v.length<2){showError('firstname-row','firstname-error','⚠ Min 2 characters.');return false;}if(!/^[a-zA-Z\s'\-]+$/.test(v)){showError('firstname-row','firstname-error','⚠ Letters only.');return false;}showSuccess('firstname-row','firstname-error');return true;}
  function vLast(){const v=document.getElementById('lastname-input').value.trim();if(!v){clearState('lastname-row','lastname-error');return true;}if(!/^[a-zA-Z\s'\-]+$/.test(v)){showError('lastname-row','lastname-error','⚠ Letters only.');return false;}showSuccess('lastname-row','lastname-error');return true;}
  function vEmail(){const v=document.getElementById('email-input').value.trim();if(!v){showError('email-row','email-error','⚠ Email required.');return false;}if(!/^[^\s@]+@[^\s@]+\.[^\s@]{2,}$/.test(v)){showError('email-row','email-error','⚠ Enter a valid email.');return false;}showSuccess('email-row','email-error');return true;}
  function vPass(){const v=document.getElementById('password-input').value;if(!v){showError('password-row','password-error','⚠ Password required.');return false;}if(v.length<8){showError('password-row','password-error','⚠ Min 8 characters.');return false;}showSuccess('password-row','password-error');return true;}
  function vConfirm(){const p=document.getElementById('password-input').value,c=document.getElementById('repeat-password-input').value;if(!c){showError('confirm-row','confirm-error','⚠ Please confirm password.');return false;}if(p!==c){showError('confirm-row','confirm-error','⚠ Passwords do not match.');return false;}showSuccess('confirm-row','confirm-error');return true;}

  document.getElementById('password-input').addEventListener('input',function(){
    const v=this.value,wrap=document.getElementById('strength-wrap'),lbl=document.getElementById('strength-text');
    if(!v.length){wrap.style.display='none';return;}wrap.style.display='block';
    let s=0;if(v.length>=8)s++;if(/[A-Z]/.test(v))s++;if(/[0-9]/.test(v))s++;if(/[^a-zA-Z0-9]/.test(v))s++;
    wrap.className='strength-wrap '+(s<=1?'weak':s<=3?'medium':'strong');
    lbl.textContent=s<=1?'Weak':s<=3?'Medium':'Strong ✓';
  });

  document.getElementById('firstname-input').addEventListener('blur',vFirst);
  document.getElementById('lastname-input').addEventListener('blur',vLast);
  document.getElementById('email-input').addEventListener('blur',vEmail);
  document.getElementById('password-input').addEventListener('blur',vPass);
  document.getElementById('repeat-password-input').addEventListener('blur',vConfirm);
  document.getElementById('password-input').addEventListener('input',()=>{if(document.getElementById('repeat-password-input').value)vConfirm();});

  document.getElementById('register-form').addEventListener('submit',function(e){
    if(![vFirst(),vLast(),vEmail(),vPass(),vConfirm()].every(Boolean))e.preventDefault();
  });

  document.querySelectorAll('.type-option').forEach(opt=>{
    opt.addEventListener('click',()=>{
      document.querySelectorAll('.type-option').forEach(o=>o.classList.remove('selected'));
      opt.classList.add('selected');
    });
  });
</script>
</body>
</html>