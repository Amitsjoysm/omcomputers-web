<?php
require_once __DIR__ . '/../inc/helpers.php';
$S = settings();

$SERVICES = [
  'device-repair' => [
    'icon' => '🔧', 'eyebrow' => 'Repair Services',
    'title' => 'Device Repair', 'heading' => 'Laptop, Desktop &amp; Printer Repair',
    'sub' => 'Fast, warranty-backed repairs with genuine parts for every major brand.',
    'intro' => 'Cracked screens, dead batteries, slow performance, virus removal, data recovery, printer faults — our technicians diagnose the real problem and fix it right the first time. Most issues are diagnosed the same day.',
    'features' => ['Screen &amp; panel replacement','Battery &amp; charging repair','Virus &amp; malware removal','Data backup &amp; recovery','SSD &amp; RAM upgrades','Printer servicing'],
  ],
  'cctv-surveillance' => [
    'icon' => '📹', 'eyebrow' => 'Security',
    'title' => 'CCTV &amp; Surveillance', 'heading' => 'CCTV &amp; Surveillance Systems',
    'sub' => 'Design, supply and installation of HD &amp; IP camera systems for homes and businesses.',
    'intro' => 'Protect your shop, office or home with a professionally installed camera system — complete with remote mobile viewing, night vision and reliable recording. We handle everything from site survey to installation and maintenance.',
    'features' => ['HD &amp; IP cameras','Remote mobile viewing','Night-vision setup','DVR/NVR &amp; storage','Cabling &amp; installation','Annual maintenance'],
  ],
  'biometric-access' => [
    'icon' => '🔐', 'eyebrow' => 'Access Control',
    'title' => 'Biometric &amp; Access', 'heading' => 'Biometric &amp; Access Control',
    'sub' => 'Attendance and access-control systems that keep your premises secure.',
    'intro' => 'Fingerprint, card and face-recognition systems for attendance tracking and door access. Ideal for offices, factories, schools and clinics that need reliable, tamper-proof entry control and attendance reports.',
    'features' => ['Fingerprint &amp; face readers','Attendance software','Door access control','Multi-location support','Reports &amp; exports','Installation &amp; training'],
  ],
  'it-support-amc' => [
    'icon' => '🛠️', 'eyebrow' => 'Support',
    'title' => 'IT Support &amp; AMC', 'heading' => 'IT Support &amp; Annual Maintenance',
    'sub' => 'Keep your office running with proactive maintenance and on-call support.',
    'intro' => 'Flexible Annual Maintenance Contracts covering preventive maintenance, priority support and on-site visits. We keep your computers, network and peripherals healthy so your team stays productive.',
    'features' => ['Preventive maintenance','Priority on-call support','On-site visits','Network setup &amp; care','Software updates','Asset management'],
  ],
  'web-digital' => [
    'icon' => '🌐', 'eyebrow' => 'Digital',
    'title' => 'Web &amp; Digital', 'heading' => 'Web &amp; Digital Services',
    'sub' => 'Websites, Google listings and digital services to grow your business online.',
    'intro' => 'Get found online with a professional website, Google Business listing and the digital basics every local business needs. We build fast, mobile-friendly sites and help you show up when customers search.',
    'features' => ['Business websites','Google Business setup','Domain &amp; email','SEO basics','Social media setup','Ongoing updates'],
  ],
  'hardware-sales' => [
    'icon' => '💻', 'eyebrow' => 'Store',
    'title' => 'Hardware Sales', 'heading' => 'Hardware Sales &amp; Upgrades',
    'sub' => 'Genuine components, upgrades and complete systems at honest prices.',
    'intro' => 'From SSDs and RAM to processors, laptops and complete custom builds — buy genuine hardware with expert advice on what actually fits your needs and budget. See our live price list for current rates.',
    'features' => ['Processors &amp; motherboards','SSD, HDD &amp; RAM','Laptops &amp; desktops','Custom PC builds','Accessories','Genuine warranty'],
  ],
];

$slug = $_GET['s'] ?? '';
if (!isset($SERVICES[$slug])) { http_response_code(404); require __DIR__ . '/../404.php'; exit; }
$svc = $SERVICES[$slug];
$page_title = $svc['title'] . ' | OM Computers Pune';
$page_desc  = strip_tags($svc['sub']);
$nav_active = 'services';
require __DIR__ . '/../inc/header.php';
?>
<section class="svc-hero section-alt">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a><span>›</span><a href="/services/device-repair">Services</a><span>›</span><span><?= $svc['title'] ?></span></div>
    <p class="eyebrow"><?= $svc['eyebrow'] ?></p>
    <h1 style="font-size:clamp(28px,4vw,44px); font-weight:800; display:flex; align-items:center; gap:12px;"><span style="font-size:.9em;"><?= $svc['icon'] ?></span> <?= $svc['heading'] ?></h1>
    <p class="lead" style="max-width:640px; margin-top:var(--sp-3);"><?= $svc['sub'] ?></p>
    <div style="display:flex; gap:var(--sp-3); flex-wrap:wrap; margin-top:var(--sp-5);">
      <a href="<?= e(wa_link('Hi OM Computers! I want to enquire about '.strip_tags($svc['title']).'.')) ?>" target="_blank" rel="noopener" class="btn-primary">💬 Enquire on WhatsApp</a>
      <a href="<?= e(tel_link()) ?>" class="btn-outline">📞 <?= e($S['phone']) ?></a>
    </div>
  </div>
</section>

<section class="section">
  <div class="container" style="max-width:820px;">
    <p class="lead" style="font-size:17px;"><?= $svc['intro'] ?></p>
    <h2 class="section-title" style="font-size:24px; margin-top:var(--sp-6);">What's included</h2>
    <ul class="feature-list">
      <?php foreach ($svc['features'] as $f): ?><li><span style="color:var(--primary);">✔</span> <span><?= $f ?></span></li><?php endforeach; ?>
    </ul>
    <div class="card" style="margin-top:var(--sp-6); text-align:center;">
      <h3 style="margin-bottom:var(--sp-2);">Ready to get started?</h3>
      <p style="color:var(--text-secondary); margin-bottom:var(--sp-4);">Tell us what you need and we'll help you right away.</p>
      <a href="<?= e(wa_link('Hi OM Computers! I need help with '.strip_tags($svc['title']).'.')) ?>" target="_blank" rel="noopener" class="btn-primary">Chat on WhatsApp</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/../inc/footer.php';
