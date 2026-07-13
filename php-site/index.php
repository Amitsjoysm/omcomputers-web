<?php
require_once __DIR__ . '/inc/helpers.php';
$page_title = 'Computer Repair, CCTV & IT Services in Pune';
$page_desc  = 'OM Computers — trusted computer repair, CCTV, biometric, IT support, and web services in Pune. Same-day diagnosis, 6-month warranty.';
$S = settings();
$phone = $S['phone']; $whatsapp = preg_replace('/\D/', '', $S['whatsapp'] ?? '');
$telnum = preg_replace('/\s/', '', $phone);

$hero_services = [
  ['🖥️','Device Repair','Laptops · Desktops','/services/device-repair','#1E88E5','#E3F2FD'],
  ['📡','CCTV Systems','HD · IP · NVR','/services/cctv-surveillance','#2ECC71','#E8F8EF'],
  ['🔐','Biometric Access','Attendance · Doors','/services/biometric-access','#F5A623','#FFF8E7'],
  ['💻','IT Support','AMC · Helpdesk','/services/it-support-amc','#8E44AD','#F5EEF8'],
  ['🌐','Web Services','Sites · SEO · Ads','/services/web-digital','#00ACC1','#E0F7FA'],
  ['🔧','Hardware Parts','RAM · SSD · More','/parts','#E53935','#FEECEC'],
];
$trust = ['Genuine Parts Only','Same-Day Diagnosis','6-Month Warranty','Free Assessment'];

$grid_services = [
  ['🖥️','Device Repair','Laptops · Desktops · Printers','/services/device-repair',['Screen replacement (all brands)','Motherboard and charging port repair','OS reinstall with data backup before repair','6-month warranty, genuine parts'],'#E3F2FD'],
  ['📡','CCTV & Surveillance','HD · IP · DVR/NVR Systems','/services/cctv-surveillance',['Full site survey and camera placement design','DVR/NVR setup with remote viewing config','Indoor/outdoor IR night vision cameras','AMC contracts available'],'#E8F5E9'],
  ['🔐','Biometric & Access Control','Attendance · Entry Systems','/services/biometric-access',['Fingerprint and face-recognition terminals','Door access controllers','Payroll software integration','On-site installation and staff training'],'#FFF3E0'],
  ['💻','IT Support & AMC','For Homes & Businesses','/services/it-support-amc',['Annual Maintenance Contracts','On-site and remote helpdesk support','Network setup and server configuration','Patch management and endpoint security'],'#F3E5F5'],
  ['🌐','Web & Digital Services','Websites · SEO · Meta Ads','/services/web-digital',['Business website design and e-commerce stores','Google SEO and Meta Ads management','Marketplace onboarding (Amazon, Flipkart)','Monthly performance reporting'],'#E8EAF6'],
  ['🔧','Hardware Sales & Upgrades','RAM · SSD · Accessories','/services/hardware-sales',['Certified genuine components','Upgrade consultation and installation included','Bulk procurement for offices','All brands: Intel, AMD, Kingston, Seagate'],'#E0F7FA'],
];
$reasons = [
  ['🔩','Genuine & Certified Parts','No grey-market components; every repair backed by 6-month warranty on parts and labour.'],
  ['💬','Transparent Pricing','Written quote before any work begins; no surprise charges or hidden fees.'],
  ['🛠️','Experienced Technicians','500+ repairs handled across all major brands: HP, Dell, Lenovo, ASUS, Apple, and more.'],
  ['📍','Local & Reliable','Based in Pune; fast on-site response for homes and offices across the city.'],
];
$steps = [
  ['01','📞','Book or Drop In','Call, WhatsApp, or walk in. No appointment needed. We are open Monday to Saturday, 9 AM – 7 PM.'],
  ['02','🔍','Free Diagnosis','We inspect your device and give you a clear written quote with exact parts and labour costs. No obligation.'],
  ['03','✅','Repair & Return','Fixed with genuine parts, fully tested, and returned same or next business day. Backed by 6-month warranty.'],
];
$testimonials = [
  ['OM Computers fixed my laptop screen in under 4 hours. Genuine part, clean job, backed by a 6-month warranty. Highly recommended.','Rahul S.','Homeowner, Pune','RS','#1E88E5'],
  ['We had our entire office CCTV done by OM Computers. Professional installation, remote viewing set up, and they explained everything clearly.','Meena P.','Small Business Owner, Pune','MP','#2ECC71'],
  ['The biometric attendance system works flawlessly six months later. On-time installation and clean payroll integration.','Vikash T.','HR Manager, Pune','VT','#F5A623'],
];
$faqs = [
  ['What devices do you repair?','We repair laptops, desktops, and printers across all major brands: HP, Dell, Lenovo, ASUS, Acer, Apple, and more. We handle screen replacements, motherboard repairs, charging ports, keyboard issues, and software problems.'],
  ['Do you use genuine parts?','Yes, always. Every repair uses genuine or OEM-certified parts. No grey-market or counterfeit components. This is why we can offer a 6-month warranty on all repairs.'],
  ['How long does a typical repair take?','Most common repairs (screen replacement, keyboard replacement, OS reinstall) are completed same-day. Complex motherboard repairs or parts that need ordering are typically ready within 2 to 3 business days. We always give you a clear timeline upfront.'],
  ['Do you offer Annual Maintenance Contracts (AMC)?','Yes. Our AMC plans cover scheduled maintenance visits, priority helpdesk support, software updates, and emergency on-site response. Ideal for offices with 5+ computers or CCTV systems. Call us for a custom quote.'],
  ['Can you install CCTV or biometric systems at our location?','Absolutely. We handle everything from site survey and camera placement design to installation, DVR/NVR configuration, and remote viewing setup. For biometric systems, we include payroll software integration and staff training.'],
  ['Is there a charge for the diagnosis?','The initial diagnosis is completely free with no obligation. We inspect your device, identify the issue, and give you a clear written quote before any work begins. You only pay if you choose to proceed.'],
];
require __DIR__ . '/inc/header.php';
?>
<!-- ── Hero ── -->
<section aria-label="Hero" style="position:relative; overflow:hidden; background:#fff; display:flex; align-items:center; padding:28px 0 40px; border-bottom:1px solid #E0E7EF;">
  <div aria-hidden="true" style="position:absolute; inset:0; pointer-events:none; background:radial-gradient(ellipse 65% 60% at 80% 0%, rgba(30,136,229,0.09) 0%, transparent 70%);"></div>
  <div aria-hidden="true" style="position:absolute; inset:0; pointer-events:none; background:radial-gradient(ellipse 45% 40% at 5% 100%, rgba(30,136,229,0.06) 0%, transparent 65%);"></div>
  <div aria-hidden="true" style="position:absolute; inset:0; pointer-events:none; background-image:radial-gradient(circle, rgba(30,136,229,0.10) 1px, transparent 1px); background-size:30px 30px;"></div>
  <div style="max-width:1200px; margin:0 auto; padding:0 24px; width:100%; position:relative; z-index:1;">
    <div class="hero-grid">
      <!-- LEFT -->
      <div style="display:flex; flex-direction:column; gap:18px;">
        <div>
          <span style="display:inline-flex; align-items:center; gap:8px; padding:5px 13px; background:#E3F2FD; border:1px solid rgba(30,136,229,0.25); border-radius:999px; font-size:12px; font-weight:700; letter-spacing:0.06em; text-transform:uppercase; color:#1565C0; font-family:var(--font-body);">
            <span style="width:6px; height:6px; border-radius:50%; background:#1E88E5; display:inline-block; box-shadow:0 0 0 3px rgba(30,136,229,0.2);"></span>
            Trusted IT Experts · Pune, Maharashtra
          </span>
        </div>
        <h1 style="font-family:var(--font-display); font-size:clamp(34px,4.8vw,56px); font-weight:800; line-height:1.09; margin:0; color:#0D1B2A; letter-spacing:-0.02em;">
          Repair. <span style="background-image:linear-gradient(135deg,#1E88E5 0%,#1565C0 55%,#0D47A1 100%); -webkit-background-clip:text; -webkit-text-fill-color:transparent; background-clip:text;">Upgrade.</span> Secure.
          <br><span style="font-size:65%; font-weight:700; color:#4A5568; letter-spacing:-0.01em;">Your Tech, Our Expertise.</span>
        </h1>
        <p style="font-size:16px; line-height:1.7; color:#4A5568; max-width:480px; margin:0; font-family:var(--font-body);">
          From cracked laptop screens to full CCTV installations, OM Computers handles every IT need for homes and businesses across Pune.
        </p>
        <div style="display:flex; gap:10px; flex-wrap:wrap;">
          <a href="/contact" class="h-btn-primary" style="display:inline-flex; align-items:center; gap:8px; padding:12px 26px; background:#1E88E5; color:#fff; font-weight:700; font-size:15px; border-radius:8px; border:2px solid #1E88E5; box-shadow:0 4px 16px rgba(30,136,229,0.28); text-decoration:none; font-family:var(--font-body);">Book a Repair</a>
          <a href="/parts" class="h-btn-outline" style="display:inline-flex; align-items:center; gap:8px; padding:12px 26px; background:#fff; color:#1E88E5; font-weight:700; font-size:15px; border-radius:8px; border:2px solid #1E88E5; text-decoration:none; font-family:var(--font-body);">View Parts List</a>
        </div>
        <div style="display:flex; flex-wrap:wrap; gap:6px 16px; padding-top:14px; border-top:1px solid #E0E7EF;">
          <?php foreach ($trust as $label): ?>
          <span style="display:inline-flex; align-items:center; gap:5px; font-size:13px; font-weight:600; color:#4A5568; font-family:var(--font-body);">
            <svg width="14" height="14" viewBox="0 0 14 14" fill="none"><circle cx="7" cy="7" r="7" fill="#E3F2FD"/><path d="M4 7l2 2 4-4" stroke="#1E88E5" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg>
            <?= e($label) ?>
          </span>
          <?php endforeach; ?>
        </div>
        <div>
          <a href="<?= e(wa_link('Hi OM Computers! I need IT help.')) ?>" target="_blank" rel="noopener" class="h-btn-wa" style="display:inline-flex; align-items:center; gap:9px; padding:8px 14px; background:#F0FFF4; border:1px solid #A8E6CF; border-radius:999px; text-decoration:none;">
            <svg width="17" height="17" viewBox="0 0 24 24" fill="#25D366"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/><path d="M12 0C5.373 0 0 5.373 0 12c0 2.123.558 4.109 1.533 5.83L.057 23.926a.5.5 0 0 0 .609.61l6.253-1.633A11.943 11.943 0 0 0 12 24c6.627 0 12-5.373 12-12S18.627 0 12 0zm0 22a9.944 9.944 0 0 1-5.174-1.448l-.371-.22-3.844 1.005 1.027-3.742-.241-.385A9.944 9.944 0 0 1 2 12C2 6.477 6.477 2 12 2s10 4.477 10 10-4.477 10-10 10z"/></svg>
            <span style="font-size:13px; font-weight:700; color:#1A7A43;">Chat on WhatsApp</span>
            <span style="font-size:12px; color:#9AA5B4; font-family:var(--font-body);"><?= e($phone) ?></span>
          </a>
        </div>
      </div>
      <!-- RIGHT — service cards -->
      <div class="hero-cards-panel" aria-label="Our services">
        <div style="display:grid; grid-template-columns:1fr 1fr; gap:9px;">
          <?php foreach ($hero_services as $svc): ?>
          <a href="<?= $svc[3] ?>" class="h-card" style="display:flex; flex-direction:column; gap:9px; padding:14px 13px; background:#fff; border:1px solid #E0E7EF; border-radius:13px; text-decoration:none; box-shadow:0 2px 8px rgba(30,136,229,0.07);">
            <span style="width:38px; height:38px; background:<?= $svc[5] ?>; border-radius:9px; display:flex; align-items:center; justify-content:center; font-size:18px; flex-shrink:0;"><?= $svc[0] ?></span>
            <div>
              <div style="font-size:13px; font-weight:700; color:#0D1B2A; font-family:var(--font-display); line-height:1.3;"><?= e($svc[1]) ?></div>
              <div style="font-size:11px; color:#9AA5B4; margin-top:2px; font-family:var(--font-body);"><?= e($svc[2]) ?></div>
            </div>
            <span style="font-size:11px; font-weight:700; color:<?= $svc[4] ?>; display:inline-flex; align-items:center; gap:3px;">Learn more
              <svg width="10" height="10" viewBox="0 0 10 10" fill="none"><path d="M2 5h6M6 3l2 2-2 2" stroke="currentColor" stroke-width="1.5" stroke-linecap="round" stroke-linejoin="round"/></svg></span>
          </a>
          <?php endforeach; ?>
        </div>
        <div style="margin-top:10px; padding:12px 18px; background:#F5F7FA; border:1px solid #E0E7EF; border-radius:11px; display:flex; align-items:center;">
          <?php foreach ([['500+','Devices repaired'],['6 Mo','Repair warranty'],['1 Day','Turnaround']] as $i => $st): ?>
          <div style="display:flex; align-items:center; flex:1;">
            <?php if ($i>0): ?><div style="width:1px; height:26px; background:#E0E7EF; margin-right:14px;"></div><?php endif; ?>
            <div style="flex:1; text-align:center;">
              <div style="font-size:19px; font-weight:800; color:#1E88E5; font-family:var(--font-display); line-height:1;"><?= $st[0] ?></div>
              <div style="font-size:10px; color:#9AA5B4; margin-top:3px; font-weight:500; font-family:var(--font-body);"><?= $st[1] ?></div>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>
</section>

<!-- ── Stats bar ── -->
<section class="stats-bar" aria-label="Key statistics">
  <div class="container">
    <div class="stats-grid">
      <?php foreach ([['500+','Devices Repaired'],['100+','CCTV Cameras Installed'],['6 Mo','Repair Warranty'],['1 Day','Same Day Diagnosis']] as $st): ?>
      <div class="stat-item fade-up"><div class="stat-number"><?= $st[0] ?></div><div class="stat-label"><?= e($st[1]) ?></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Services ── -->
<section class="services-section" id="services" aria-label="Our Services">
  <div class="container">
    <div style="text-align:center; margin-bottom:var(--sp-5);" class="fade-up">
      <p class="eyebrow">What We Do</p>
      <h2 class="section-title">Complete IT Solutions in Pune</h2>
      <p class="lead" style="max-width:600px; margin:0 auto;">From device repair to enterprise CCTV, we handle the full range of technology needs for homes and businesses.</p>
    </div>
    <div class="grid-3">
      <?php foreach ($grid_services as $s): ?>
      <article class="service-card fade-up" aria-label="<?= e($s[1]) ?>">
        <div class="service-icon" style="background:<?= $s[5] ?>;"><?= $s[0] ?></div>
        <h3 class="service-title"><?= e($s[1]) ?></h3>
        <p class="service-tagline"><?= e($s[2]) ?></p>
        <ul class="service-bullets" role="list"><?php foreach ($s[4] as $b): ?><li class="service-bullet"><?= e($b) ?></li><?php endforeach; ?></ul>
        <a href="<?= $s[3] ?>" class="service-link">Learn More →</a>
      </article>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Why us ── -->
<section class="why-us" aria-label="Why choose OM Computers">
  <div class="container">
    <div style="text-align:center; margin-bottom:var(--sp-5);" class="fade-up">
      <p class="eyebrow">Why Us</p>
      <h2 class="section-title">Why Choose OM Computers</h2>
    </div>
    <div class="grid-2">
      <?php foreach ($reasons as $r): ?>
      <div class="why-card fade-up"><div class="why-icon"><?= $r[0] ?></div><div><h3 class="why-title"><?= e($r[1]) ?></h3><p class="why-desc"><?= e($r[2]) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── How it works ── -->
<section class="how-it-works" aria-label="How it works">
  <div class="container">
    <div style="text-align:center; margin-bottom:var(--sp-5);" class="fade-up">
      <p class="eyebrow">Simple Process</p>
      <h2 class="section-title">How It Works</h2>
      <p class="lead" style="max-width:500px; margin:0 auto;">Getting your device repaired is simple, transparent, and hassle-free.</p>
    </div>
    <div class="steps-grid">
      <?php foreach ($steps as $st): ?>
      <div class="step-card fade-up"><div class="step-number-wrap"><span class="step-number"><?= $st[0] ?></span></div><div><div class="step-icon"><?= $st[1] ?></div><h3 class="step-title"><?= e($st[2]) ?></h3><p class="step-desc"><?= e($st[3]) ?></p></div></div>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── Testimonials ── -->
<section class="testimonials" aria-label="Customer testimonials">
  <div class="container">
    <div style="text-align:center; margin-bottom:var(--sp-5);" class="fade-up">
      <p class="eyebrow">Happy Customers</p>
      <h2 class="section-title">What Our Customers Say</h2>
    </div>
    <div class="grid-3">
      <?php foreach ($testimonials as $t): ?>
      <blockquote class="testimonial-card fade-up"><div class="stars">★★★★★</div><p class="quote-text"><?= e($t[0]) ?></p><div class="reviewer"><div class="reviewer-avatar" style="background:<?= $t[4] ?>;"><?= e($t[3]) ?></div><div><div class="reviewer-name"><?= e($t[1]) ?></div><div class="reviewer-role"><?= e($t[2]) ?></div></div></div></blockquote>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── FAQ ── -->
<section class="faq-section" aria-label="Frequently asked questions">
  <div class="container">
    <div style="text-align:center; margin-bottom:var(--sp-5);" class="fade-up">
      <p class="eyebrow">FAQ</p>
      <h2 class="section-title">Frequently Asked Questions</h2>
    </div>
    <div class="faq-grid fade-up">
      <?php foreach ($faqs as $f): ?>
      <details><summary><?= e($f[0]) ?></summary><div class="faq-answer"><?= e($f[1]) ?></div></details>
      <?php endforeach; ?>
    </div>
  </div>
</section>

<!-- ── CTA band ── -->
<section class="cta-band" aria-label="Call to action">
  <div class="container">
    <div class="cta-inner fade-up">
      <h2 class="cta-headline">Got a device issue? Let's fix it today.</h2>
      <p class="cta-sub">Walk in or call. Same-day diagnosis, transparent quote, no obligation.</p>
      <div class="cta-actions">
        <a href="/contact" class="btn-white">Contact Us Now</a>
        <a href="tel:<?= e($telnum) ?>" class="btn-white-outline">📞 <?= e($phone) ?></a>
      </div>
    </div>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php';
