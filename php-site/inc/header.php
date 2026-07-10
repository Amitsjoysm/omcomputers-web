<?php
require_once __DIR__ . '/helpers.php';
$S = settings();
$page_title = $page_title ?? 'OM Computers';
$page_desc  = $page_desc  ?? 'OM Computers: Computer repair, CCTV, biometric, IT support, and digital services in Pune.';
$canonical  = rtrim(SITE_URL, '/') . ($_SERVER['REQUEST_URI'] ?? '/');
$og_image   = rtrim(SITE_URL, '/') . '/icons/og-image.jpg';
$nav_active = $nav_active ?? '';
$services_menu = [
  ['/services/device-repair', 'Device Repair'],
  ['/services/cctv-surveillance', 'CCTV & Surveillance'],
  ['/services/biometric-access', 'Biometric & Access'],
  ['/services/it-support-amc', 'IT Support & AMC'],
  ['/services/web-digital', 'Web & Digital'],
  ['/services/hardware-sales', 'Hardware Sales'],
];
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

<header class="navbar">
  <div class="container nav-inner">
    <a href="/" class="nav-logo" aria-label="OM Computers home">
      <img src="/logo.png" alt="OM Computers" width="52" height="52" />
    </a>
    <nav class="nav-links" aria-label="Primary">
      <a href="/"        class="nav-link<?= $nav_active==='home'?' active':'' ?>">Home</a>
      <div class="nav-dropdown">
        <a href="/services/device-repair" class="nav-link<?= $nav_active==='services'?' active':'' ?>">Services ▾</a>
        <div class="nav-dropdown-menu">
          <?php foreach ($services_menu as $s): ?>
            <a href="<?= $s[0] ?>"><?= e($s[1]) ?></a>
          <?php endforeach; ?>
        </div>
      </div>
      <a href="/blog"    class="nav-link<?= $nav_active==='blog'?' active':'' ?>">Blog</a>
      <a href="/parts"   class="nav-link<?= $nav_active==='parts'?' active':'' ?>">Parts</a>
      <a href="/contact" class="nav-link<?= $nav_active==='contact'?' active':'' ?>">Contact</a>
    </nav>
    <div class="nav-cta">
      <a href="<?= e(tel_link()) ?>" class="btn-outline nav-phone">📞 <?= e($S['phone']) ?></a>
      <a href="/contact" class="btn-primary nav-quote">Get Quote</a>
      <button class="nav-hamburger" id="navToggle" aria-label="Open menu" aria-expanded="false">
        <svg width="26" height="26" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"><line x1="3" y1="6" x2="21" y2="6"/><line x1="3" y1="12" x2="21" y2="12"/><line x1="3" y1="18" x2="21" y2="18"/></svg>
      </button>
    </div>
  </div>
  <div class="mobile-menu" id="mobileMenu" hidden>
    <a href="/">Home</a>
    <a href="/services/device-repair">Services</a>
    <a href="/blog">Blog</a>
    <a href="/parts">Parts</a>
    <a href="/contact">Contact</a>
    <a href="<?= e(wa_link('Hi OM Computers!')) ?>" class="btn-primary" style="justify-content:center;">💬 WhatsApp Us</a>
  </div>
</header>
<main id="main-content">
