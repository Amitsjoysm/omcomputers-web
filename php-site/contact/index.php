<?php
require_once __DIR__ . '/../inc/helpers.php';
$page_title = 'Contact Us';
$page_desc  = 'Contact OM Computers, Pune — call, WhatsApp, email or send us a message. We respond within 1 business hour.';
$nav_active = 'contact';
$S = settings();

$sent = false; $err = ''; $old = ['name'=>'','phone'=>'','service'=>'','message'=>''];
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    // Honeypot: bots fill "company"
    if (($_POST['company'] ?? '') === '') {
        $name = trim($_POST['name'] ?? '');
        $phone = trim($_POST['phone'] ?? '');
        $service = trim($_POST['service'] ?? '');
        $message = trim($_POST['message'] ?? '');
        $old = compact('name','phone','service','message');
        if ($name === '' || $phone === '') {
            $err = 'Please enter your name and phone number.';
        } else {
            try { create_message($name, $phone, $service, $message); $sent = true; $old = ['name'=>'','phone'=>'','service'=>'','message'=>'']; }
            catch (Throwable $e) { $err = 'Sorry, something went wrong. Please call or WhatsApp us instead.'; }
        }
    } else { $sent = true; }
}
$services = ['Device Repair','CCTV','Biometric','IT Support','Web Services','Hardware','Other'];
require __DIR__ . '/../inc/header.php';
?>
<section class="pagehero">
  <div class="container" style="text-align:center;">
    <p class="eyebrow">Get In Touch</p>
    <h1 style="font-size:clamp(28px,4vw,42px); font-weight:800;">Contact OM Computers</h1>
    <p class="lead" style="max-width:520px; margin:var(--sp-2) auto 0;">Call, WhatsApp, email or send a message — we respond within 1 business hour.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <div class="grid-2" style="gap:var(--sp-7); align-items:start;">
      <!-- Contact details -->
      <div>
        <h2 style="font-family:var(--font-display); font-size:22px; margin-bottom:var(--sp-4);">Reach us directly</h2>
        <a href="<?= e(tel_link()) ?>" class="contact-item" style="display:flex; gap:var(--sp-3); padding:var(--sp-4); background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); margin-bottom:var(--sp-3); color:inherit;">
          <span style="font-size:22px;">📞</span><div><div style="font-size:12px; color:var(--text-muted);">Call us</div><div style="font-size:16px; font-weight:600; color:var(--primary);"><?= e($S['phone']) ?></div></div>
        </a>
        <a href="<?= e(wa_link('Hi OM Computers!')) ?>" target="_blank" rel="noopener" class="contact-item" style="display:flex; gap:var(--sp-3); padding:var(--sp-4); background:#F0FFF4; border:1px solid #A8E6CF; border-radius:var(--radius-md); margin-bottom:var(--sp-3); color:inherit;">
          <span style="font-size:22px;">💬</span><div><div style="font-size:12px; color:var(--text-muted);">WhatsApp</div><div style="font-size:16px; font-weight:600; color:#1A7A43;">Message us now</div></div>
        </a>
        <a href="mailto:<?= e($S['email']) ?>" class="contact-item" style="display:flex; gap:var(--sp-3); padding:var(--sp-4); background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); margin-bottom:var(--sp-3); color:inherit;">
          <span style="font-size:22px;">✉️</span><div><div style="font-size:12px; color:var(--text-muted);">Email</div><div style="font-size:16px; font-weight:600; color:var(--primary);"><?= e($S['email']) ?></div></div>
        </a>
        <div style="display:flex; gap:var(--sp-3); padding:var(--sp-4); background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md); margin-bottom:var(--sp-3);">
          <span style="font-size:22px;">📍</span><div><div style="font-size:12px; color:var(--text-muted);">Visit</div><div style="font-size:15px; line-height:1.5;"><?= e($S['address']) ?></div></div>
        </div>
        <div style="display:flex; gap:var(--sp-3); padding:var(--sp-4); background:var(--surface); border:1px solid var(--border); border-radius:var(--radius-md);">
          <span style="font-size:22px;">🕒</span><div><div style="font-size:12px; color:var(--text-muted);">Hours</div><div style="font-size:15px;"><?= e($S['openHours']) ?></div></div>
        </div>
        <?php if (!empty($S['googleMaps'])): ?>
        <div style="margin-top:var(--sp-4); border-radius:var(--radius-md); overflow:hidden; border:1px solid var(--border);">
          <iframe src="<?= e($S['googleMaps']) ?>" width="100%" height="240" style="border:0; display:block;" loading="lazy" referrerpolicy="no-referrer-when-downgrade" title="Map"></iframe>
        </div>
        <?php endif; ?>
      </div>

      <!-- Form -->
      <div>
        <h2 style="font-family:var(--font-display); font-size:22px; margin-bottom:var(--sp-4);">Send Us a Message</h2>
        <?php if ($sent): ?>
          <div style="background:#E8F8EF; border:1px solid #BDEBD2; color:#1A7A43; border-radius:var(--radius-md); padding:var(--sp-5); text-align:center;">
            <div style="font-size:40px;">✅</div>
            <h3 style="font-family:var(--font-display); margin:var(--sp-2) 0;">Thank you — message received!</h3>
            <p style="font-size:14px;">We'll get back to you within 1 business hour. For anything urgent, call or WhatsApp us directly.</p>
          </div>
        <?php else: ?>
          <form method="POST" style="display:flex; flex-direction:column; gap:var(--sp-4);">
            <?php if ($err): ?><p style="background:#FDECEC; color:#B42318; border:1px solid #F5C6C6; padding:var(--sp-3); border-radius:var(--radius-sm); font-size:14px;"><?= e($err) ?></p><?php endif; ?>
            <div style="position:absolute; left:-5000px;" aria-hidden="true"><input type="text" name="company" tabindex="-1" autocomplete="off" /></div>
            <div style="display:grid; grid-template-columns:1fr 1fr; gap:var(--sp-3);">
              <div class="form-group"><label class="form-label" for="name">Full Name *</label><input class="form-input" type="text" id="name" name="name" required value="<?= e($old['name']) ?>" placeholder="Your name" /></div>
              <div class="form-group"><label class="form-label" for="phone">Phone Number *</label><input class="form-input" type="tel" id="phone" name="phone" required value="<?= e($old['phone']) ?>" placeholder="+91 XXXXX XXXXX" /></div>
            </div>
            <div class="form-group"><label class="form-label" for="service">Service Required</label>
              <select class="form-input" id="service" name="service">
                <option value="">Select a service...</option>
                <?php foreach ($services as $s): ?><option value="<?= e($s) ?>"<?= $old['service']===$s?' selected':'' ?>><?= e($s) ?></option><?php endforeach; ?>
              </select>
            </div>
            <div class="form-group"><label class="form-label" for="message">Message</label><textarea class="form-input" id="message" name="message" rows="5" style="resize:vertical; min-height:120px;" placeholder="Describe your issue or requirements..."><?= e($old['message']) ?></textarea></div>
            <button type="submit" class="btn-primary" style="width:100%; justify-content:center; padding:14px; font-size:16px;">Send Message →</button>
            <p style="font-size:13px; color:var(--text-muted); text-align:center;">We respond within 1 business hour. Your information is never shared.</p>
          </form>
        <?php endif; ?>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../inc/footer.php';
