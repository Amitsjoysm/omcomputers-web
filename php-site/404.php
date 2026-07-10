<?php
require_once __DIR__ . '/inc/helpers.php';
http_response_code(404);
$page_title = 'Page Not Found';
$nav_active = '';
require __DIR__ . '/inc/header.php';
?>
<section class="section" style="text-align:center; padding:var(--sp-9) 0;">
  <div class="container">
    <div style="font-size:72px;">🔍</div>
    <h1 style="font-size:clamp(28px,5vw,44px); font-weight:800; margin:var(--sp-3) 0;">404 — Page not found</h1>
    <p class="lead" style="max-width:460px; margin:0 auto var(--sp-5);">The page you're looking for doesn't exist or has moved.</p>
    <div style="display:flex; gap:var(--sp-3); justify-content:center; flex-wrap:wrap;">
      <a href="/" class="btn-primary">← Back to Home</a>
      <a href="/contact" class="btn-outline">Contact Us</a>
    </div>
  </div>
</section>
<?php require __DIR__ . '/inc/footer.php';
