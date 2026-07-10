<?php
require_once __DIR__ . '/inc/helpers.php';
$page_title = 'Computer Repair, CCTV & IT Services in Pune';
$page_desc  = 'OM Computers — trusted computer repair, CCTV, biometric, IT support, and web services in Pune. Same-day diagnosis, 6-month warranty.';
$nav_active = 'home';
$S = settings();
$services = [
  ['🔧','Device Repair','Laptops, desktops & printers fixed fast with genuine parts and a 6-month warranty.','/services/device-repair'],
  ['📹','CCTV & Surveillance','HD & IP camera systems designed, installed and maintained for homes and businesses.','/services/cctv-surveillance'],
  ['🔐','Biometric & Access','Attendance and access-control systems that keep your premises secure.','/services/biometric-access'],
  ['🛠️','IT Support & AMC','Annual maintenance contracts and on-call support to keep your office running.','/services/it-support-amc'],
  ['🌐','Web & Digital','Websites, Google listings and digital services to grow your business online.','/services/web-digital'],
  ['💻','Hardware Sales','Genuine components, upgrades and complete systems at honest prices.','/services/hardware-sales'],
];
$faqs = [
  ['Do you offer same-day repairs?','Most common laptop and desktop issues are diagnosed the same day, and many repairs are completed within 24 hours depending on parts availability.'],
  ['Is there a warranty on repairs?','Yes — repairs come with a 6-month warranty on the work performed and genuine replacement parts.'],
  ['Do you install CCTV for shops and homes?','Absolutely. We design, supply and install HD and IP camera systems for shops, offices and homes across Pune, with remote mobile viewing.'],
  ['Do you provide AMC for offices?','Yes, we offer flexible Annual Maintenance Contracts covering preventive maintenance, priority support and on-site visits.'],
];
require __DIR__ . '/inc/header.php';
?>
<!-- Hero -->
<section class="hero">
  <div class="hero-dotgrid"></div>
  <div class="container hero-inner">
    <div class="hero-content fade-up">
      <span class="hero-badge">● TRUSTED IT EXPERTS · PUNE, MAHARASHTRA</span>
      <h1>Repair. <span class="accent">Upgrade.</span> Secure.</h1>
      <p class="hero-sub">Your Tech, Our Expertise.</p>
      <p class="hero-lead">From cracked laptop screens to full CCTV installations, OM Computers handles every IT need for homes and businesses across Pune.</p>
      <div class="hero-actions">
        <a href="/contact" class="btn-primary">Book a Repair</a>
        <a href="/parts" class="btn-outline">View Parts List</a>
      </div>
      <div class="hero-trust">
        <span>✔ Genuine Parts Only</span>
        <span>✔ Same-Day Diagnosis</span>
        <span>✔ 6-Month Warranty</span>
        <span>✔ Free Assessment</span>
      </div>
      <div style="margin-top:var(--sp-4);">
        <a href="<?= e(wa_link('Hi OM Computers! I need help with')) ?>" target="_blank" rel="noopener" class="hero-wa">💬 Chat on WhatsApp <?= e($S['phone']) ?></a>
      </div>
    </div>
    <div class="hero-visual fade-up">
      <div class="hero-card">
        <h3>Why homes &amp; businesses choose us</h3>
        <div class="why-item" style="margin-bottom:16px;"><span class="ic">⚡</span><div><h4 style="color:#fff;">Fast turnaround</h4><p style="color:rgba(255,255,255,.85);">Same-day diagnosis on most devices.</p></div></div>
        <div class="why-item" style="margin-bottom:16px;"><span class="ic">🛡️</span><div><h4 style="color:#fff;">Warranty backed</h4><p style="color:rgba(255,255,255,.85);">6 months on repairs &amp; parts.</p></div></div>
        <div class="why-item"><span class="ic">💬</span><div><h4 style="color:#fff;">Real support</h4><p style="color:rgba(255,255,255,.85);">Talk to a technician, not a bot.</p></div></div>
      </div>
    </div>
  </div>
</section>

<!-- Stats -->
<section class="stats section">
  <div class="container grid-4">
    <div><div class="stat-num">500+</div><div class="stat-label">Devices Repaired</div></div>
    <div><div class="stat-num">100+</div><div class="stat-label">CCTV Cameras Installed</div></div>
    <div><div class="stat-num">6 Mo</div><div class="stat-label">Repair Warranty</div></div>
    <div><div class="stat-num">1 Day</div><div class="stat-label">Same-Day Diagnosis</div></div>
  </div>
</section>

<!-- Services -->
<section class="section">
  <div class="container">
    <div class="sec-head fade-up">
      <p class="eyebrow">What We Do</p>
      <h2 class="section-title">Complete IT services under one roof</h2>
      <p class="lead">Everything from quick repairs to full security installations — handled by one trusted local team.</p>
    </div>
    <div class="grid-3">
      <?php foreach ($services as $s): ?>
      <a href="<?= $s[3] ?>" class="card svc-card fade-up">
        <span class="svc-icon"><?= $s[0] ?></span>
        <h3><?= e($s[1]) ?></h3>
        <p><?= e($s[2]) ?></p>
        <span class="more">Learn more →</span>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- Why us -->
<section class="section-alt section">
  <div class="container">
    <div class="sec-head fade-up">
      <p class="eyebrow">Why OM Computers</p>
      <h2 class="section-title">Local, honest and reliable</h2>
    </div>
    <div class="grid-3">
      <div class="why-item fade-up"><span class="ic">🏆</span><div><h4>Experienced technicians</h4><p>Years of hands-on experience across all major brands and devices.</p></div></div>
      <div class="why-item fade-up"><span class="ic">💯</span><div><h4>Transparent pricing</h4><p>Clear quotes before any work begins — no surprises.</p></div></div>
      <div class="why-item fade-up"><span class="ic">📍</span><div><h4>Right here in Pune</h4><p>A local shop you can visit, call or WhatsApp any working day.</p></div></div>
      <div class="why-item fade-up"><span class="ic">🔩</span><div><h4>Genuine parts</h4><p>Only authentic components and quality-tested hardware.</p></div></div>
      <div class="why-item fade-up"><span class="ic">⏱️</span><div><h4>Fast turnaround</h4><p>Same-day diagnosis and quick repairs to reduce your downtime.</p></div></div>
      <div class="why-item fade-up"><span class="ic">🤝</span><div><h4>After-sales support</h4><p>We stand behind our work with a genuine warranty and real help.</p></div></div>
    </div>
  </div>
</section>

<!-- FAQ -->
<section class="section">
  <div class="container" style="max-width:820px;">
    <div class="sec-head fade-up">
      <p class="eyebrow">Questions</p>
      <h2 class="section-title">Frequently asked</h2>
    </div>
    <div class="faq">
      <?php foreach ($faqs as $i => $f): ?>
      <details class="fade-up"<?= $i===0?' open':'' ?>><summary><?= e($f[0]) ?></summary><p><?= e($f[1]) ?></p></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- CTA -->
<section class="cta-band">
  <div class="container">
    <h2>Need a repair or a quote today?</h2>
    <p>Call, WhatsApp or drop by — we'll get you sorted fast.</p>
    <div style="display:flex; gap:var(--sp-3); justify-content:center; flex-wrap:wrap;">
      <a href="<?= e(tel_link()) ?>" class="btn-white-outline btn-primary" style="background:transparent;">📞 <?= e($S['phone']) ?></a>
      <a href="<?= e(wa_link('Hi OM Computers!')) ?>" target="_blank" rel="noopener" class="btn-white btn-primary">💬 WhatsApp Us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php';
