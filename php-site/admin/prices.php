<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

$err = '';
if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = $_POST['action'] ?? '';
    try {
        if ($action === 'add-category') {
            $name = trim($_POST['name'] ?? '');
            if ($name === '') $err = 'Category name is required.';
            else {
                db()->prepare('INSERT INTO price_categories (name, sort) VALUES (?, (SELECT COALESCE(MAX(s.sort),0)+1 FROM (SELECT sort FROM price_categories) s))')->execute([$name]);
                header('Location: /admin/prices-edit.php?id=' . (int)db()->lastInsertId()); exit;
            }
        } elseif ($action === 'rename-category') {
            $cid = (int)($_POST['id'] ?? 0); $name = trim($_POST['name'] ?? '');
            if ($cid && $name !== '') db()->prepare('UPDATE price_categories SET name=? WHERE id=?')->execute([$name,$cid]);
            header('Location: /admin/prices.php'); exit;
        } elseif ($action === 'delete-category') {
            $cid = (int)($_POST['id'] ?? 0);
            if ($cid) db()->prepare('DELETE FROM price_categories WHERE id=?')->execute([$cid]);
            header('Location: /admin/prices.php?deleted=1'); exit;
        }
    } catch (Throwable $ex) {
        $err = (str_contains($ex->getMessage(), 'Duplicate')) ? 'A category with that name already exists.' : 'Something went wrong — please try again.';
    }
}

$cats = categories_with_items();
$deleted = isset($_GET['deleted']);
admin_head('Price List', 'prices');
?>
<h1 class="admin-h1">Price List</h1>
<?php if ($deleted): ?><p class="flash flash-ok">Category deleted.</p><?php endif; ?>
<?php if ($err): ?><p class="flash flash-err"><?= e($err) ?></p><?php endif; ?>

<div class="admin-card">
  <?php if (!$cats): ?>
    <p style="color:var(--text-muted); font-size:14px;">No categories yet — add one below.</p>
  <?php else: ?>
    <table class="admin-table">
      <thead><tr><th>Category</th><th>Products</th><th style="width:360px;"></th></tr></thead>
      <tbody>
        <?php foreach ($cats as $c): ?>
        <tr>
          <td><a href="/admin/prices-edit.php?id=<?= (int)$c['id'] ?>" style="color:var(--primary); font-weight:600;"><?= e($c['name']) ?></a></td>
          <td><?= count($c['items']) ?></td>
          <td>
            <div style="display:flex; gap:var(--sp-2); justify-content:flex-end; flex-wrap:wrap;">
              <a href="/admin/prices-edit.php?id=<?= (int)$c['id'] ?>" class="btn-outline" style="padding:6px 14px; font-size:13px;">Edit products</a>
              <form method="POST" style="display:inline-flex; gap:6px;">
                <?= csrf_field() ?><input type="hidden" name="action" value="rename-category" /><input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                <input type="text" name="name" value="<?= e($c['name']) ?>" style="width:130px; padding:6px 8px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:13px;" />
                <button type="submit" class="btn-outline" style="padding:6px 10px; font-size:13px;">Rename</button>
              </form>
              <form method="POST" onsubmit="return confirm('Delete this category and all its products?');" style="display:inline;">
                <?= csrf_field() ?><input type="hidden" name="action" value="delete-category" /><input type="hidden" name="id" value="<?= (int)$c['id'] ?>" />
                <button type="submit" class="btn-danger" style="padding:6px 10px; font-size:13px;">Delete</button>
              </form>
            </div>
          </td>
        </tr>
        <?php endforeach; ?>
      </tbody>
    </table>
  <?php endif; ?>
</div>

<div class="admin-card">
  <h2 class="admin-h2">Add a category</h2>
  <form method="POST" style="display:flex; gap:var(--sp-3); flex-wrap:wrap;">
    <?= csrf_field() ?><input type="hidden" name="action" value="add-category" />
    <input type="text" name="name" required placeholder="e.g. RAM & Memory" style="flex:1; min-width:220px; padding:10px 12px; border:1px solid var(--border); border-radius:var(--radius-sm); font-size:14px;" />
    <button type="submit" class="btn-primary">Add category</button>
  </form>
</div>
<?php admin_foot();
