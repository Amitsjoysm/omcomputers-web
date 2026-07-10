<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check() && ($_POST['action'] ?? '') === 'delete') {
    $id = (int) ($_POST['id'] ?? 0);
    if ($id > 0) db()->prepare('DELETE FROM posts WHERE id=?')->execute([$id]);
    header('Location: /admin/posts.php?deleted=1'); exit;
}

$posts = all_posts();
$deleted = isset($_GET['deleted']);
admin_head('Blog Posts', 'posts');
?>
<div style="display:flex; align-items:center; justify-content:space-between; gap:var(--sp-4); flex-wrap:wrap; margin-bottom:var(--sp-4);">
  <h1 class="admin-h1" style="margin:0;">Blog Posts</h1>
  <a href="/admin/post-edit.php" class="btn-primary">✍ New post</a>
</div>
<?php if ($deleted): ?><p class="flash flash-ok">Post deleted.</p><?php endif; ?>
<div class="admin-card">
  <?php if (!$posts): ?>
    <p style="color:var(--text-muted); font-size:14px;">No posts yet.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Title</th><th>Tags</th><th>Date</th><th>Status</th><th></th></tr></thead>
      <tbody>
        <?php foreach ($posts as $p): ?>
        <tr>
          <td>
            <a href="/admin/post-edit.php?id=<?= (int)$p['id'] ?>" style="color:var(--primary); font-weight:600;"><?= e($p['title']) ?></a>
            <?php if ((int)$p['published']): ?><a href="/blog/<?= e($p['slug']) ?>" target="_blank" rel="noopener" style="font-size:12px; color:var(--text-muted); margin-left:8px;">view ↗</a><?php endif; ?>
          </td>
          <td><?php foreach (array_slice($p['tags'],0,3) as $t): ?><span class="badge badge-muted" style="margin-right:4px;"><?= e($t) ?></span><?php endforeach; ?></td>
          <td style="white-space:nowrap;"><?= e(fmt_date($p['publish_date'])) ?></td>
          <td><?= (int)$p['published'] ? '<span class="badge badge-success">Published</span>' : '<span class="badge badge-warning">Draft</span>' ?></td>
          <td style="text-align:right;">
            <form method="POST" onsubmit="return confirm('Delete this post permanently?');" style="display:inline;">
              <?= csrf_field() ?><input type="hidden" name="action" value="delete" /><input type="hidden" name="id" value="<?= (int)$p['id'] ?>" />
              <button type="submit" class="btn-danger" style="padding:4px 10px; font-size:12px;">Delete</button>
            </form>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>
<?php admin_foot();
