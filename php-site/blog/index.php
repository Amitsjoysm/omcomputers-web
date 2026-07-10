<?php
require_once __DIR__ . '/../inc/helpers.php';
$page_title = 'Blog & Project Updates';
$page_desc  = 'Tips, guides and updates from OM Computers: computer repair advice, CCTV guides, IT security tips and more.';
$nav_active = 'blog';
$posts = published_posts();
$tags = [];
foreach ($posts as $p) foreach ($p['tags'] as $t) $tags[$t] = true;
$tags = array_keys($tags); sort($tags);
require __DIR__ . '/../inc/header.php';
?>
<section class="pagehero">
  <div class="container" style="text-align:center;">
    <p class="eyebrow">Knowledge Base</p>
    <h1 style="font-size:clamp(28px,4vw,42px); font-weight:800;">Blog &amp; Project Updates</h1>
    <p class="lead" style="max-width:520px; margin:var(--sp-2) auto 0;">Tips, guides and updates from the OM Computers team.</p>
  </div>
</section>

<section class="section">
  <div class="container">
    <?php if (!$posts): ?>
      <div style="text-align:center; padding:var(--sp-7) 0;">
        <div style="font-size:64px;">📝</div>
        <h2 style="font-family:var(--font-display);">No posts yet</h2>
        <p style="color:var(--text-secondary);">Check back soon — we're working on useful content.</p>
      </div>
    <?php else: ?>
      <div class="filter-bar" id="tagFilter">
        <button class="filter-pill active" data-tag="">All Posts (<?= count($posts) ?>)</button>
        <?php foreach ($tags as $t): ?><button class="filter-pill" data-tag="<?= e($t) ?>"><?= e($t) ?></button><?php endforeach; ?>
      </div>
      <div class="grid-3" id="postGrid">
        <?php foreach ($posts as $p): ?>
        <a href="/blog/<?= e($p['slug']) ?>" class="post-card" data-tags="<?= e(strtolower(implode(',', $p['tags']))) ?>">
          <div class="cover">
            <?php if (!empty($p['cover_image'])): ?><img src="<?= e($p['cover_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy" />
            <?php else: ?><span class="ph">📝</span><?php endif; ?>
          </div>
          <div class="body">
            <div class="tag-row"><?php foreach (array_slice($p['tags'],0,3) as $t): ?><span class="badge badge-primary"><?= e($t) ?></span><?php endforeach; ?></div>
            <h3><?= e($p['title']) ?></h3>
            <p class="ex"><?= e($p['excerpt']) ?></p>
            <div class="meta"><span>📅 <?= e(fmt_date($p['publish_date'])) ?></span><span>⏱ <?= read_time($p['body']) ?> min read</span></div>
          </div>
        </a>
        <?php endforeach; ?>
      </div>
      <div id="noResults" style="display:none; text-align:center; padding:var(--sp-7) 0; color:var(--text-secondary);">
        <div style="font-size:48px;">📝</div><p>No posts found for that tag.</p>
      </div>
    <?php endif; ?>
  </div>
</section>

<script>
(function () {
  var pills = document.querySelectorAll('#tagFilter .filter-pill');
  var cards = document.querySelectorAll('#postGrid .post-card');
  var none = document.getElementById('noResults');
  pills.forEach(function (pill) {
    pill.addEventListener('click', function () {
      pills.forEach(function (p) { p.classList.remove('active'); });
      pill.classList.add('active');
      var tag = pill.getAttribute('data-tag').toLowerCase();
      var shown = 0;
      cards.forEach(function (c) {
        var match = !tag || (',' + c.getAttribute('data-tags') + ',').indexOf(',' + tag + ',') !== -1;
        c.style.display = match ? '' : 'none';
        if (match) shown++;
      });
      if (none) none.style.display = shown ? 'none' : 'block';
    });
  });
})();
</script>
<?php require __DIR__ . '/../inc/footer.php';
