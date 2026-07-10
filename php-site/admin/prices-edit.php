<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

$cid = (int) ($_GET['id'] ?? 0);
$cat = $cid > 0 ? category_by_id($cid) : null;
if (!$cat) { header('Location: /admin/prices.php'); exit; }

$err = '';
function item_from_post(int $catId, ?array $existing, ?string &$err): ?array {
    $name = trim($_POST['name'] ?? '');
    if ($name === '') { $err = 'Product name is required.'; return null; }
    $image = $existing['image'] ?? null;
    if (isset($_POST['removeImage'])) $image = null;
    $up = store_upload($_FILES['image'] ?? null, $err);
    if ($err) return null;
    if ($up) $image = $up;
    return [
        'name' => $name, 'brand' => trim($_POST['brand'] ?? ''), 'specs' => trim($_POST['specs'] ?? ''),
        'price' => max(0, (float)($_POST['price'] ?? 0)), 'in_stock' => isset($_POST['inStock']) ? 1 : 0, 'image' => $image,
    ];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $action = $_POST['action'] ?? '';
    if ($action === 'add-item') {
        $it = item_from_post($cid, null, $err);
        if ($it) {
            db()->prepare('INSERT INTO price_items (category_id,name,brand,specs,price,in_stock,image,sort) VALUES (?,?,?,?,?,?,?, (SELECT COALESCE(MAX(s.sort),0)+1 FROM (SELECT sort FROM price_items WHERE category_id=?) s))')
                ->execute([$cid,$it['name'],$it['brand'],$it['specs'],$it['price'],$it['in_stock'],$it['image'],$cid]);
            header('Location: /admin/prices-edit.php?id='.$cid.'&saved=1'); exit;
        }
    } elseif ($action === 'save-item') {
        $iid = (int)($_POST['item_id'] ?? 0); $ex = item_by_id($iid);
        if (!$ex || (int)$ex['category_id'] !== $cid) $err = 'Product not found.';
        else {
            $it = item_from_post($cid, $ex, $err);
            if ($it) {
                db()->prepare('UPDATE price_items SET name=?, brand=?, specs=?, price=?, in_stock=?, image=? WHERE id=?')
                    ->execute([$it['name'],$it['brand'],$it['specs'],$it['price'],$it['in_stock'],$it['image'],$iid]);
                header('Location: /admin/prices-edit.php?id='.$cid.'&saved=1'); exit;
            }
        }
    } elseif ($action === 'delete-item') {
        $iid = (int)($_POST['item_id'] ?? 0); $ex = item_by_id($iid);
        if ($ex && (int)$ex['category_id'] === $cid) db()->prepare('DELETE FROM price_items WHERE id=?')->execute([$iid]);
        header('Location: /admin/prices-edit.php?id='.$cid.'&saved=1'); exit;
    }
}

$items = items_for_category($cid);
$saved = isset($_GET['saved']);
admin_head('Prices: '.$cat['name'], 'prices');
?>
<div style="display:flex; align-items:center; gap:var(--sp-3); margin-bottom:var(--sp-4); flex-wrap:wrap;">
  <a href="/admin/prices.php" style="color:var(--text-muted); font-size:14px;">← Categories</a>
  <h1 class="admin-h1" style="margin:0;"><?= e($cat['name']) ?></h1>
</div>
<?php if ($saved): ?><p class="flash flash-ok">Saved.</p><?php endif; ?>
<?php if ($err): ?><p class="flash flash-err"><?= e($err) ?></p><?php endif; ?>

<?php foreach ($items as $it): ?>
<form method="POST" enctype="multipart/form-data" class="admin-card" style="padding:var(--sp-4);">
  <?= csrf_field() ?><input type="hidden" name="action" value="save-item" /><input type="hidden" name="item_id" value="<?= (int)$it['id'] ?>" />
  <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:var(--sp-3); margin-bottom:var(--sp-3);">
    <div class="field" style="margin:0;"><label>Product name</label><input type="text" name="name" required value="<?= e($it['name']) ?>" /></div>
    <div class="field" style="margin:0;"><label>Brand</label><input type="text" name="brand" value="<?= e($it['brand']) ?>" /></div>
    <div class="field" style="margin:0;"><label>Price (INR)</label><input type="number" name="price" min="0" step="1" value="<?= (int)$it['price'] ?>" /></div>
  </div>
  <div class="field" style="margin-bottom:var(--sp-3);"><label>Specs</label><input type="text" name="specs" value="<?= e($it['specs']) ?>" placeholder="e.g. 10-Core · 2.5 GHz · LGA1700" /></div>
  <div style="display:flex; align-items:center; gap:var(--sp-4); flex-wrap:wrap;">
    <label class="check"><input type="checkbox" name="inStock" <?= (int)$it['in_stock'] ? 'checked' : '' ?> /> In stock</label>
    <?php if (!empty($it['image'])): ?><span style="display:inline-flex; align-items:center; gap:var(--sp-2);"><img src="<?= e($it['image']) ?>" alt="" style="height:36px; width:36px; object-fit:cover; border-radius:6px; border:1px solid var(--border);" /><label class="check" style="font-size:13px;"><input type="checkbox" name="removeImage" /> remove</label></span><?php endif; ?>
    <input type="file" name="image" accept="image/*" style="font-size:13px;" />
    <span style="flex:1;"></span>
    <button type="submit" class="btn-primary" style="padding:8px 18px; font-size:13px;">Save</button>
    <button type="submit" formnovalidate name="action" value="delete-item" class="btn-danger" style="padding:8px 14px; font-size:13px;" onclick="return confirm('Delete this product?');">Delete</button>
  </div>
</form>
<?php endforeach; ?>

<form method="POST" enctype="multipart/form-data" class="admin-card" style="border-style:dashed;">
  <?= csrf_field() ?><input type="hidden" name="action" value="add-item" />
  <h2 class="admin-h2">Add a product to <?= e($cat['name']) ?></h2>
  <div style="display:grid; grid-template-columns:2fr 1fr 1fr; gap:var(--sp-3); margin-bottom:var(--sp-3);">
    <div class="field" style="margin:0;"><label>Product name *</label><input type="text" name="name" required placeholder="e.g. Intel Core i5-13400F" /></div>
    <div class="field" style="margin:0;"><label>Brand</label><input type="text" name="brand" placeholder="e.g. Intel" /></div>
    <div class="field" style="margin:0;"><label>Price (INR)</label><input type="number" name="price" min="0" step="1" value="0" /></div>
  </div>
  <div class="field" style="margin-bottom:var(--sp-3);"><label>Specs</label><input type="text" name="specs" placeholder="Short spec line" /></div>
  <div style="display:flex; align-items:center; gap:var(--sp-4); flex-wrap:wrap;">
    <label class="check"><input type="checkbox" name="inStock" checked /> In stock</label>
    <input type="file" name="image" accept="image/*" style="font-size:13px;" />
    <span style="flex:1;"></span>
    <button type="submit" class="btn-primary">Add product</button>
  </div>
</form>
<?php admin_foot();
