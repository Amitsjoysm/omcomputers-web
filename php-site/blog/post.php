<?php
require_once __DIR__ . '/../inc/helpers.php';
require_once __DIR__ . '/../inc/markdown.php';

$slug = $_GET['slug'] ?? '';
$post = $slug ? post_by_slug($slug) : null;
if (!$post) { header('Location: /blog'); exit; }

$page_title = $post['title'];
$page_desc  = $post['excerpt'];
$nav_active = 'blog';
$html = markdown($post['body']);
$rt = read_time($post['body']);

// related (shared tags, fallback recent)
$all = published_posts();
$related = array_filter($all, fn($p) => $p['slug'] !== $post['slug'] && array_intersect($p['tags'], $post['tags']));
if (count($related) < 3) $related = array_filter($all, fn($p) => $p['slug'] !== $post['slug']);
$related = array_slice(array_values($related), 0, 3);

$share = rtrim(SITE_URL,'/') . '/blog/' . rawurlencode($post['slug']);
require __DIR__ . '/../inc/header.php';
?>
<nav class="pagehero">
  <div class="container">
    <div class="breadcrumb"><a href="/">Home</a><span>›</span><a href="/blog">Blog</a><span>›</span><span><?= e($post['title']) ?></span></div>
    <div style="display:flex; gap:var(--sp-4); flex-wrap:wrap; font-size:13px; color:var(--text-secondary); margin-bottom:var(--sp-3);">
      <span>📅 <?= e(fmt_date_long($post['publish_date'])) ?></span>
      <span>⏱ <?= $rt ?> min read</span>
      <?php foreach ($post['tags'] as $t): ?><span class="badge badge-primary"><?= e($t) ?></span><?php endforeach; ?>
    </div>
    <h1 style="font-size:clamp(24px,4vw,40px); font-weight:800; max-width:800px;"><?= e($post['title']) ?></h1>
    <div style="display:flex; align-items:center; gap:var(--sp-2); font-size:14px; color:var(--text-secondary); margin-top:var(--sp-4);">
      <img src="/logo.png" alt="" width="24" height="24" style="border-radius:50%;" /><span>OM Computers Team</span>
    </div>
  </div>
</nav>

<article class="section">
  <div class="container">
    <?php if (!empty($post['cover_image'])): ?>
      <img class="cover-img" src="<?= e($post['cover_image']) ?>" alt="<?= e($post['title']) ?>" />
    <?php endif; ?>
    <div style="max-width:72ch; margin:0 auto;">
      <div class="prose-om"><?= $html ?></div>
      <div style="display:flex; gap:var(--sp-3); align-items:center; flex-wrap:wrap; margin:var(--sp-7) 0 var(--sp-5); padding-top:var(--sp-5); border-top:1px solid var(--border);">
        <span style="font-weight:600; font-size:14px; color:var(--text-secondary);">Share this post:</span>
        <a href="https://wa.me/?text=<?= rawurlencode($post['title'].': '.$share) ?>" target="_blank" rel="noopener" class="btn-primary" style="background:#25D366; border-color:#25D366; font-size:13px; padding:8px 16px;">WhatsApp</a>
        <button class="btn-outline" style="font-size:13px; padding:8px 16px;" onclick="navigator.clipboard.writeText('<?= e($share) ?>');this.textContent='Copied!';">Copy Link</button>
      </div>
    </div>
  </div>
</article>

<?php if ($related): ?>
<section class="section-alt section">
  <div class="container">
    <p class="eyebrow">More Articles</p>
    <h2 class="section-title" style="font-size:28px;">Related Posts</h2>
    <div class="grid-3">
      <?php foreach ($related as $p): ?>
      <a href="/blog/<?= e($p['slug']) ?>" class="post-card">
        <div class="cover"><?php if(!empty($p['cover_image'])): ?><img src="<?= e($p['cover_image']) ?>" alt="<?= e($p['title']) ?>" loading="lazy" /><?php else: ?><span class="ph">📝</span><?php endif; ?></div>
        <div class="body">
          <div class="tag-row"><?php foreach (array_slice($p['tags'],0,3) as $t): ?><span class="badge badge-primary"><?= e($t) ?></span><?php endforeach; ?></div>
          <h3><?= e($p['title']) ?></h3>
          <p class="ex"><?= e($p['excerpt']) ?></p>
          <div class="meta"><span>📅 <?= e(fmt_date($p['publish_date'])) ?></span><span>⏱ <?= read_time($p['body']) ?> min read</span></div>
        </div>
      </a>
      <?php endforeach; ?>
    </div>
  </div>
</section>
<?php endif; ?>
<?php require __DIR__ . '/../inc/footer.php';
