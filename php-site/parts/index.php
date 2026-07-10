<?php
require_once __DIR__ . '/../inc/helpers.php';
$page_title = 'Parts & Components Price List';
$page_desc  = 'Current prices for processors, laptops, storage, RAM and accessories at OM Computers, Pune. Enquire on WhatsApp.';
$nav_active = 'parts';
$S = settings();
$cats = categories_with_items();
require __DIR__ . '/../inc/header.php';
?>
<section class="pagehero">
  <div class="container" style="text-align:center;">
    <p class="eyebrow">Hardware Store</p>
    <h1 style="font-size:clamp(28px,4vw,42px); font-weight:800;">Parts &amp; Components Price List</h1>
    <p class="lead" style="max-width:560px; margin:var(--sp-2) auto var(--sp-4);">Genuine components at honest prices. Message us on WhatsApp to confirm availability and place an order.</p>
    <a href="<?= e(wa_link('Hi OM Computers! I want to enquire about hardware prices.')) ?>" target="_blank" rel="noopener" class="btn-primary" style="background:#25D366; border-color:#25D366;">💬 Enquire on WhatsApp</a>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$cats): ?>
      <div style="text-align:center; padding:var(--sp-7) 0;"><div style="font-size:64px;">🧰</div><h2>Price list coming soon</h2><p style="color:var(--text-secondary);">Please check back shortly.</p></div>
    <?php else: ?>
      <div class="filter-bar" id="catFilter">
        <button class="filter-pill active" data-cat="">All</button>
        <?php foreach ($cats as $c): ?><button class="filter-pill" data-cat="c<?= (int)$c['id'] ?>"><?= e($c['name']) ?></button><?php endforeach; ?>
      </div>
      <?php foreach ($cats as $c): ?>
      <div class="cat-block" data-cat="c<?= (int)$c['id'] ?>">
        <h2 class="price-cat-title"><?= e($c['name']) ?></h2>
        <?php if (!$c['items']): ?><p style="color:var(--text-muted);">No products in this category yet.</p><?php else: ?>
        <div class="grid-2">
          <?php foreach ($c['items'] as $it): $wa = wa_link('Hi OM Computers! I want to enquire about: '.$it['name']); ?>
          <div class="price-card">
            <?php if (!empty($it['image'])): ?><img src="<?= e($it['image']) ?>" alt="<?= e($it['name']) ?>" /><?php endif; ?>
            <div class="info">
              <div class="brand"><?= e($it['brand']) ?></div>
              <h3><?= e($it['name']) ?></h3>
              <?php if (!empty($it['specs'])): ?><p class="specs"><?= e($it['specs']) ?></p><?php endif; ?>
              <div style="display:flex; align-items:center; justify-content:space-between; gap:var(--sp-2); flex-wrap:wrap; margin-top:6px;">
                <span class="price">₹<?= number_format((float)$it['price']) ?></span>
                <?php if ((int)$it['in_stock']): ?><span class="badge badge-success">In Stock</span><?php else: ?><span class="badge badge-muted">Out of Stock</span><?php endif; ?>
              </div>
              <a href="<?= e($wa) ?>" target="_blank" rel="noopener" class="btn-outline" style="font-size:12px; padding:6px 14px; margin-top:8px;">Enquire →</a>
            </div>
          </div>
          <?php endforeach; ?>
        </div>
        <?php endif; ?>
      </div>
      <?php endforeach; ?>
    <?php endif; ?>
  </div>
</section>

<script>
(function () {
  var pills = document.querySelectorAll('#catFilter .filter-pill');
  var blocks = document.querySelectorAll('.cat-block');
  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      var cat = pill.getAttribute('data-cat');
      blocks.forEach(function (b) { b.style.display = (!cat || b.getAttribute('data-cat') === cat) ? '' : 'none'; });
    });
  });
})();
</script>
<?php require __DIR__ . '/../inc/footer.php';
