<?php
require_once __DIR__ . '/helpers.php';
$S = settings();
$page_title = $page_title ?? 'OM Computers';
$page_desc  = $page_desc  ?? 'OM Computers: Computer repair, CCTV, biometric, IT support, and digital services in Pune.';
$canonical  = rtrim(SITE_URL, '/') . ($_SERVER['REQUEST_URI'] ?? '/');
$og_image   = rtrim(SITE_URL, '/') . '/icons/og-image.jpg';
$telnum     = preg_replace('/\s/', '', $S['phone'] ?? '');
$services = [
  ['🖥️', 'Device Repair',           'Laptops · Desktops · Printers', '/services/device-repair'],
  ['📡', 'CCTV & Surveillance',      'HD · IP · DVR/NVR Systems',      '/services/cctv-surveillance'],
  ['🔐', 'Biometric & Access',        'Attendance · Entry Systems',      '/services/biometric-access'],
  ['💻', 'IT Support & AMC',          'For Homes & Businesses',          '/services/it-support-amc'],
  ['🌐', 'Web & Digital Services',   'Websites · SEO · Meta Ads',        '/services/web-digital'],
  ['🔧', 'Hardware Sales',            'RAM · SSD · Accessories',          '/services/hardware-sales'],
];
$nav_links = [ ['Blog','/blog'], ['Parts','/parts'], ['Contact','/contact'] ];
?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <title><?= e($page_title) ?> · OM Computers</title>
  <meta name="description" content="<?= e($page_desc) ?>" />
  <link rel="canonical" href="<?= e($canonical) ?>" />
  <meta property="og:type" content="website" />
  <meta property="og:title" content="<?= e($page_title) ?> · OM Computers" />
  <meta property="og:description" content="<?= e($page_desc) ?>" />
  <meta property="og:image" content="<?= e($og_image) ?>" />
  <meta property="og:url" content="<?= e($canonical) ?>" />
  <meta name="twitter:card" content="summary_large_image" />
  <link rel="icon" href="/icons/favicon-48x48.png" type="image/png" sizes="48x48" />
  <link rel="icon" href="/icons/favicon-32x32.png" type="image/png" sizes="32x32" />
  <link rel="icon" href="/icons/favicon-16x16.png" type="image/png" sizes="16x16" />
  <link rel="icon" href="/favicon.ico" sizes="any" />
  <link rel="apple-touch-icon" href="/icons/apple-touch-icon.png" />
  <link rel="manifest" href="/site.webmanifest" />
  <meta name="theme-color" content="#1E88E5" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/global.css" />
  <link rel="stylesheet" href="/assets/site.css" />
  <meta name="robots" content="index, follow" />
  <script type="application/ld+json">
  <?= json_encode([
    '@context' => 'https://schema.org', '@type' => 'LocalBusiness', 'name' => 'OM Computers',
    'url' => rtrim(SITE_URL,'/'), 'logo' => rtrim(SITE_URL,'/') . '/logo.png',
    'telephone' => $S['phone'], 'email' => $S['email'],
    'address' => ['@type'=>'PostalAddress','streetAddress'=>$S['address'],'addressLocality'=>'Pune','addressRegion'=>'Maharashtra','addressCountry'=>'IN'],
    'openingHoursSpecification' => ['@type'=>'OpeningHoursSpecification','dayOfWeek'=>['Monday','Tuesday','Wednesday','Thursday','Friday','Saturday'],'opens'=>'09:00','closes'=>'19:00'],
  ], JSON_UNESCAPED_SLASHES) ?>
  </script>
</head>
<body>
<a href="#main-content" class="skip-nav">Skip to main content</a>

<nav class="navbar" id="main-nav" aria-label="Main navigation">
  <div class="container">
    <div class="nav-inner">
      <a href="/" class="nav-logo" aria-label="OM Computers - Home">
        <img src="/logo.png" alt="OM Computers" height="62" width="auto" />
      </a>

      <ul class="nav-links" role="list">
        <li class="nav-item">
          <a href="/#services" class="nav-link" aria-haspopup="true">Services
            <svg class="chevron" viewBox="0 0 20 20" fill="currentColor" aria-hidden="true"><path fill-rule="evenodd" d="M5.23 7.21a.75.75 0 011.06.02L10 11.168l3.71-3.938a.75.75 0 111.08 1.04l-4.25 4.5a.75.75 0 01-1.08 0l-4.25-4.5a.75.75 0 01.02-1.06z" clip-rule="evenodd"/></svg>
          </a>
          <div class="dropdown" role="menu">
            <?php foreach ($services as $s): ?>
            <a href="<?= $s[3] ?>" class="dropdown-item" role="menuitem">
              <span class="dropdown-icon"><?= $s[0] ?></span>
              <span><span class="dropdown-label"><?= e($s[1]) ?></span><span class="dropdown-sub"><?= e($s[2]) ?></span></span>
            </a>
            <?php endforeach; ?>
          </div>
        </li>
        <?php foreach ($nav_links as $l): ?>
          <li class="nav-item"><a href="<?= $l[1] ?>" class="nav-link"><?= e($l[0]) ?></a></li>
        <?php endforeach; ?>
      </ul>

      <div class="nav-actions">
        <a href="tel:<?= e($telnum) ?>" class="btn-outline" style="font-size:13px; padding:8px 16px;">📞 Call Us</a>
        <a href="/contact" class="btn-primary" style="font-size:13px; padding:8px 16px;">Get Quote</a>
        <button class="mobile-toggle" id="navToggle" aria-label="Open menu" aria-expanded="false">
          <svg width="24" height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
        </button>
      </div>
    </div>
  </div>
</nav>

<!-- Mobile drawer -->
<div class="mobile-drawer" id="mobileDrawer">
  <div class="backdrop" data-close></div>
  <div class="mobile-panel">
    <div class="mp-head">
      <img src="/logo.png" alt="OM Computers" />
      <button class="mp-close" data-close aria-label="Close menu">×</button>
    </div>
    <a href="/" class="mp-link">Home</a>
    <div class="mp-section-label">Services</div>
    <?php foreach ($services as $s): ?>
      <a href="<?= $s[3] ?>" class="mp-service"><span class="ic"><?= $s[0] ?></span><span><span class="lbl"><?= e($s[1]) ?></span><br><span class="sub"><?= e($s[2]) ?></span></span></a>
    <?php endforeach; ?>
    <a href="/blog" class="mp-link">Blog</a>
    <a href="/parts" class="mp-link">Parts</a>
    <a href="/contact" class="mp-link">Contact</a>
    <div class="mp-cta">
      <a href="tel:<?= e($telnum) ?>" class="btn-outline" style="justify-content:center;">📞 <?= e($S['phone']) ?></a>
      <a href="<?= e(wa_link('Hi OM Computers!')) ?>" target="_blank" rel="noopener" class="btn-primary" style="justify-content:center;">💬 WhatsApp Us</a>
    </div>
  </div>
</div>

<main id="main-content">
