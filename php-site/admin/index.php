<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

$posts = all_posts();
$cats = categories_with_items();
$published = count(array_filter($posts, fn($p) => (int)$p['published'] === 1));
$drafts = count($posts) - $published;
$itemCount = array_sum(array_map(fn($c) => count($c['items']), $cats));
$unread = unread_count();

admin_head('Dashboard', 'dashboard');
?>
<h1 class="admin-h1">Dashboard</h1>
<div class="grid-3" style="margin-bottom:var(--sp-5);">
  <a href="/admin/posts.php" class="admin-card" style="margin:0; display:block; color:inherit;">
    <div style="font-size:32px; font-weight:800; font-family:var(--font-display); color:var(--primary);"><?= $published ?></div>
    <div style="font-size:14px; color:var(--text-secondary);">Published posts <?php if ($drafts>0): ?><span class="badge badge-warning">+<?= $drafts ?> draft<?= $drafts>1?'s':'' ?></span><?php endif; ?></div>
  </a>
  <a href="/admin/prices.php" class="admin-card" style="margin:0; display:block; color:inherit;">
    <div style="font-size:32px; font-weight:800; font-family:var(--font-display); color:var(--primary);"><?= $itemCount ?></div>
    <div style="font-size:14px; color:var(--text-secondary);">Products in <?= count($cats) ?> categories</div>
  </a>
  <a href="/admin/messages.php" class="admin-card" style="margin:0; display:block; color:inherit;">
    <div style="font-size:32px; font-weight:800; font-family:var(--font-display); color:var(--primary);"><?= $unread ?></div>
    <div style="font-size:14px; color:var(--text-secondary);">Unread enquiries</div>
  </a>
</div>

<div class="admin-card">
  <h2 class="admin-h2">Quick actions</h2>
  <div style="display:flex; gap:var(--sp-3); flex-wrap:wrap;">
    <a href="/admin/post-edit.php" class="btn-primary">✍ Write a new post</a>
    <a href="/admin/prices.php" class="btn-outline">Update prices</a>
    <a href="/admin/settings.php" class="btn-outline">Edit contact details</a>
  </div>
</div>

<div class="admin-card">
  <h2 class="admin-h2">Recent posts</h2>
  <?php if (!$posts): ?>
    <p style="color:var(--text-muted); font-size:14px;">No posts yet. Write your first one!</p>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Title</th><th>Date</th><th>Status</th></tr></thead>
      <tbody>
        <?php foreach (array_slice($posts,0,5) as $p): ?>
        <tr>
          <td><a href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>" style="color:var(--primary); font-weight:600;"><?= e($p['title']) ?></a></td>
          <td style="white-space:nowrap;"><?= e(fmt_date($p['publish_date'])) ?></td>
          <td><?= (int)$p['published'] ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' ?></td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php admin_foot();
