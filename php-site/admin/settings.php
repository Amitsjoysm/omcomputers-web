<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $phone = trim($_POST['phone'] ?? ''); $email = trim($_POST['email'] ?? '');
    if ($phone === '' || $email === '') $err = 'Phone and email are required.';
    else {
        save_settings([
            'phone' => $phone,
            'whatsapp' => preg_replace('/\D/', '', $_POST['whatsapp'] ?? ''),
            'email' => $email,
            'address' => trim($_POST['address'] ?? ''),
            'googleMaps' => trim($_POST['googleMaps'] ?? ''),
            'openHours' => trim($_POST['openHours'] ?? ''),
        ]);
        header('Location: /admin/settings.php?saved=1'); exit;
    }
}
$s = settings();
$saved = isset($_GET['saved']);
admin_head('Site Settings', 'settings');
?>
<h1 class="admin-h1">Site Settings</h1>
<p style="color:var(--text-secondary); font-size:14px; margin:-8px 0 var(--sp-4);">Shown in the header, footer and contact page across the whole site. Changes appear immediately.</p>
<?php if ($saved): ?><p class="flash flash-ok">Settings saved — live on the site now.</p><?php endif; ?>
<?php if ($err): ?><p class="flash flash-err"><?= e($err) ?></p><?php endif; ?>
<form method="POST" class="admin-card" style="max-width:640px;">
  <?= csrf_field() ?>
  <div class="field-row">
    <div class="field"><label for="phone">Phone number *</label><input type="text" id="phone" name="phone" required value="<?= e($s['phone']) ?>" /></div>
    <div class="field"><label for="whatsapp">WhatsApp number</label><input type="text" id="whatsapp" name="whatsapp" value="<?= e($s['whatsapp']) ?>" /><p class="hint">Digits only, with country code — e.g. 919876543210</p></div>
  </div>
  <div class="field"><label for="email">Email address *</label><input type="email" id="email" name="email" required value="<?= e($s['email']) ?>" /></div>
  <div class="field"><label for="address">Full address</label><textarea id="address" name="address" rows="2"><?= e($s['address']) ?></textarea></div>
  <div class="field"><label for="googleMaps">Google Maps embed URL</label><input type="url" id="googleMaps" name="googleMaps" value="<?= e($s['googleMaps']) ?>" /><p class="hint">Google Maps → Share → Embed a map → copy the src="…" address.</p></div>
  <div class="field"><label for="openHours">Opening hours</label><input type="text" id="openHours" name="openHours" value="<?= e($s['openHours']) ?>" placeholder="Monday to Saturday, 9 AM – 7 PM" /></div>
  <button type="submit" class="btn-primary">Save settings</button>
</form>
<?php admin_foot();
