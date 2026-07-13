<?php
require_once __DIR__ . '/../inc/helpers.php';
$page_title = 'Parts & Components Price List';
$page_desc  = 'Transparent price list for processors, laptops, storage, RAM, and accessories at OM Computers, Pune. Request a quote via WhatsApp.';
$S = settings();
$cats = categories_with_items();
require __DIR__ . '/../inc/header.php';
?>
<!-- Page Hero -->
<section style="background:var(--surface-2); padding:var(--sp-6) 0; border-bottom:1px solid var(--border);">
  <div class="container" style="text-align:center;">
    <p class="eyebrow">Hardware Sales</p>
    <h1 style="font-size:clamp(28px,4vw,42px); font-weight:800; font-family:var(--font-display); margin:var(--sp-2) 0 var(--sp-3);">Parts &amp; Components</h1>
    <p class="lead" style="max-width:540px; margin:0 auto;">Transparent pricing on all components. Prices updated regularly. Call or WhatsApp to confirm current availability and stock.</p>
    <div style="margin-top:var(--sp-4); display:flex; gap:var(--sp-3); justify-content:center; flex-wrap:wrap;">
      <a href="<?= e(wa_link('Hi OM Computers! I want to enquire about parts and component prices.')) ?>" target="_blank" rel="noopener" class="btn-primary">WhatsApp for Latest Prices</a>
      <a href="/contact" class="btn-outline">Get Custom Quote</a>
    </div>
  </div>
</section>

<!-- Parts list -->
<section style="padding:var(--sp-5) 0;">
  <div class="container">
    <?php if (!$cats): ?>
      <div style="text-align:center; padding:var(--sp-7) 0;"><div style="font-size:64px;">🧰</div><h2 style="font-family:var(--font-display);">Price list coming soon</h2><p style="color:var(--text-secondary);">Please check back shortly, or WhatsApp us for current prices.</p></div>
    <?php else: ?>
      <div class="filter-bar" id="catFilter">
        <button class="filter-pill active" data-cat="">All</button>
        <?php foreach ($cats as $c): ?><button class="filter-pill" data-cat="c<?= (int)$c['id'] ?>"><?= e($c['name']) ?></button><?php endforeach; ?>
      </div>
      <?php foreach ($cats as $c): ?>
      <div class="cat-block" data-cat="c<?= (int)$c['id'] ?>">
        <h2 class="price-cat-title"><?= e($c['name']) ?></h2>
        <?php if (!$c['items']): ?><p style="color:var(--text-muted);">No products in this category yet.</p><?php else: ?>
        <div class="grid-3">
          <?php foreach ($c['items'] as $it):
            $price = '₹' . number_format((float)$it['price']);
            $wa = wa_link("Hi OM Computers! I'm interested in: {$it['name']} ({$it['brand']}) - {$price}. Please confirm availability.");
          ?>
          <article class="price-card" aria-label="<?= e($it['name']) ?>">
            <?php if (!empty($it['image'])): ?>
              <img class="product-image" src="<?= e($it['image']) ?>" alt="<?= e($it['name']) ?>" width="150" height="150" loading="lazy" />
            <?php else: ?>
              <div class="product-image-placeholder">🔧</div>
            <?php endif; ?>
            <div>
              <span class="badge badge-primary" style="margin-bottom:8px;"><?= e($it['brand']) ?></span>
              <h3 class="product-name"><?= e($it['name']) ?></h3>
            </div>
            <p class="product-specs"><?= e($it['specs']) ?></p>
            <div class="card-footer">
              <div><div class="product-price"><?= $price ?></div></div>
              <div style="display:flex; flex-direction:column; align-items:flex-end; gap:8px;">
                <span class="badge <?= (int)$it['in_stock'] ? 'badge-success' : 'badge-muted' ?>"><?= (int)$it['in_stock'] ? '✓ In Stock' : 'Out of Stock' ?></span>
                <a href="<?= e($wa) ?>" target="_blank" rel="noopener" class="wa-btn">
                  <svg width="14" height="14" viewBox="0 0 24 24" fill="currentColor"><path d="M17.472 14.382c-.297-.149-1.758-.867-2.03-.967-.273-.099-.471-.148-.67.15-.197.297-.767.966-.94 1.164-.173.199-.347.223-.644.075-.297-.15-1.255-.463-2.39-1.475-.883-.788-1.48-1.761-1.653-2.059-.173-.297-.018-.458.13-.606.134-.133.298-.347.446-.52.149-.174.198-.298.298-.497.099-.198.05-.371-.025-.52-.075-.149-.669-1.612-.916-2.207-.242-.579-.487-.5-.669-.51-.173-.008-.371-.01-.57-.01-.198 0-.52.074-.792.372-.272.297-1.04 1.016-1.04 2.479 0 1.462 1.065 2.875 1.213 3.074.149.198 2.096 3.2 5.077 4.487.709.306 1.262.489 1.694.625.712.227 1.36.195 1.871.118.571-.085 1.758-.719 2.006-1.413.248-.694.248-1.289.173-1.413-.074-.124-.272-.198-.57-.347z"/></svg>
                  Request Quote
                </a>
              </div>
            </div>
          </article>
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
