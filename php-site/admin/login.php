<?php
require_once __DIR__ . '/../inc/auth.php';
require_once __DIR__ . '/../inc/helpers.php';

if (is_logged_in()) { header('Location: /admin/'); exit; }

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $err = 'Session expired — please try again.';
    } elseif (!login_allowed()) {
        $err = 'Too many attempts. Please wait 15 minutes and try again.';
    } elseif (check_password($_POST['password'] ?? '')) {
        clear_login_fails(); login();
        header('Location: /admin/'); exit;
    } else {
        record_login_fail(); $err = 'Incorrect password.';
    }
}
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title>Admin Login · OM Computers</title>
  <link rel="icon" href="/favicon.ico" sizes="any" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;700;800&family=DM+Sans:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/global.css" />
</head>
<body style="background:var(--surface-2); min-height:100vh; display:flex; align-items:center; justify-content:center; padding:var(--sp-5);">
  <main style="width:100%; max-width:380px;">
    <div style="background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-lg); padding:var(--sp-6); box-shadow:var(--shadow-card);">
      <div style="text-align:center; margin-bottom:var(--sp-5);">
        <img src="/logo.png" alt="OM Computers" width="72" height="72" style="margin:0 auto var(--sp-3);" />
        <h1 style="font-family:var(--font-display); font-size:20px; font-weight:800;">Admin Login</h1>
        <p style="font-size:13px; color:var(--text-muted); margin-top:var(--sp-1);">OM Computers content manager</p>
      </div>
      <?php if ($err): ?><p role="alert" style="background:#FDECEC; color:#B42318; border:1px solid #F5C6C6; padding:var(--sp-3); border-radius:var(--radius-sm); font-size:13px; margin-bottom:var(--sp-4);"><?= e($err) ?></p><?php endif; ?>
      <form method="POST">
        <?= csrf_field() ?>
        <label for="password" style="display:block; font-size:13px; font-weight:600; color:var(--text-secondary); margin-bottom:var(--sp-1);">Password</label>
        <input type="password" id="password" name="password" required autofocus autocomplete="current-password" style="width:100%; padding:11px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:14px; font-family:var(--font-body); margin-bottom:var(--sp-4);" />
        <button type="submit" class="btn-primary" style="width:100%; justify-content:center;">Log in</button>
      </form>
    </div>
    <p style="text-align:center; font-size:12px; color:var(--text-muted); margin-top:var(--sp-4);"><a href="/" style="color:var(--text-muted);">← Back to website</a></p>
  </main>
</body>
</html>
