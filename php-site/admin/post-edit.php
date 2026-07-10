<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

$id = (int) ($_GET['id'] ?? 0);
$post = $id > 0 ? post_by_id($id) : null;
if ($id > 0 && !$post) { header('Location: /admin/posts.php'); exit; }

$err = ''; $uploaded = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (!csrf_check()) {
        $err = 'Session expired — please try again.';
    } else {
        $action = $_POST['action'] ?? 'save';
        if ($action === 'upload-image') {
            $uploaded = store_upload($_FILES['inlineImage'] ?? null, $err);
        } else {
            $title = trim($_POST['title'] ?? '');
            if ($title === '') {
                $err = 'Title is required.';
            } else {
                $slug = slugify(($_POST['slug'] ?? '') !== '' ? $_POST['slug'] : $title);
                if (slug_exists($slug, $id)) {
                    $err = 'A post with the slug "' . e($slug) . '" already exists — choose a different slug.';
                } else {
                    $excerpt = mb_substr(trim($_POST['excerpt'] ?? ''), 0, 500);
                    $tags = array_values(array_filter(array_map('trim', explode(',', $_POST['tags'] ?? ''))));
                    $pubdate = preg_match('/^\d{4}-\d{2}-\d{2}$/', $_POST['publishDate'] ?? '') ? $_POST['publishDate'] : date('Y-m-d');
                    $published = isset($_POST['published']) ? 1 : 0;
                    $body = $_POST['body'] ?? '';
                    // cover image
                    $cover = $post['cover_image'] ?? null;
                    if (isset($_POST['removeCover'])) $cover = null;
                    $up = store_upload($_FILES['cover'] ?? null, $err);
                    if ($err) { /* stop */ }
                    elseif ($up) $cover = $up;

                    if (!$err) {
                        if ($id > 0) {
                            db()->prepare('UPDATE posts SET slug=?, title=?, excerpt=?, tags=?, cover_image=?, body=?, published=?, publish_date=? WHERE id=?')
                                ->execute([$slug,$title,$excerpt,json_encode($tags),$cover,$body,$published,$pubdate,$id]);
                        } else {
                            db()->prepare('INSERT INTO posts (slug,title,excerpt,tags,cover_image,body,published,publish_date) VALUES (?,?,?,?,?,?,?,?)')
                                ->execute([$slug,$title,$excerpt,json_encode($tags),$cover,$body,$published,$pubdate]);
                            $id = (int) db()->lastInsertId();
                        }
                        header('Location: /admin/post-edit.php?id=' . $id . '&saved=1'); exit;
                    }
                }
            }
        }
        $post = $id > 0 ? post_by_id($id) : $post;
    }
}

$saved = isset($_GET['saved']);
$dateVal = $post ? date('Y-m-d', strtotime($post['publish_date'])) : date('Y-m-d');
admin_head($post ? 'Edit: '.$post['title'] : 'New Post', 'posts');
?>
<h1 class="admin-h1"><?= $post ? 'Edit post' : 'Write a new post' ?></h1>
<?php if ($saved): ?><p class="flash flash-ok">Saved. <?php if ($post && (int)$post['published']): ?><a href="/blog/<?= e($post['slug']) ?>" target="_blank" rel="noopener">View the post ↗</a><?php else: ?>Still a draft — tick "Published" when ready.<?php endif; ?></p><?php endif; ?>
<?php if ($err): ?><p class="flash flash-err"><?= $err ?></p><?php endif; ?>

<form method="POST" enctype="multipart/form-data" class="admin-card">
  <?= csrf_field() ?><input type="hidden" name="action" value="save" />
  <div class="field"><label for="title">Title *</label><input type="text" id="title" name="title" required maxlength="255" value="<?= e($post['title'] ?? '') ?>" /></div>
  <div class="field-row">
    <div class="field"><label for="slug">URL slug</label><input type="text" id="slug" name="slug" value="<?= e($post['slug'] ?? '') ?>" placeholder="leave empty to auto-generate" /><p class="hint">Address: /blog/<strong>your-slug</strong></p></div>
    <div class="field"><label for="publishDate">Publish date</label><input type="date" id="publishDate" name="publishDate" value="<?= e($dateVal) ?>" /></div>
  </div>
  <div class="field"><label for="excerpt">Excerpt</label><textarea id="excerpt" name="excerpt" rows="2" maxlength="160" placeholder="One or two sentences (max 160 chars)"><?= e($post['excerpt'] ?? '') ?></textarea></div>
  <div class="field"><label for="tags">Tags</label><input type="text" id="tags" name="tags" value="<?= e($post ? implode(', ', $post['tags']) : '') ?>" placeholder="CCTV, Repair, Tips" /><p class="hint">Separate with commas.</p></div>
  <div class="field">
    <label for="cover">Cover image <?= !empty($post['cover_image']) ? '(replace current)' : '' ?></label>
    <?php if (!empty($post['cover_image'])): ?>
      <div style="margin-bottom:var(--sp-2);"><img src="<?= e($post['cover_image']) ?>" alt="Current cover" style="max-height:120px; border-radius:var(--radius-sm); border:1px solid var(--border);" />
        <label class="check" style="margin-top:var(--sp-2);"><input type="checkbox" name="removeCover" /> Remove cover image</label></div>
    <?php endif; ?>
    <input type="file" id="cover" name="cover" accept="image/*" /><p class="hint">16:9 recommended. Max 5 MB.</p>
  </div>
  <div class="field"><label for="body">Post body (Markdown)</label><textarea id="body" name="body" rows="18" style="font-family:var(--font-mono); font-size:13px;"><?= e($post['body'] ?? '') ?></textarea>
    <p class="hint">Formatting: <code>**bold**</code> · <code>## Heading</code> · <code>- bullet</code> · <code>[link](https://url)</code> · <code>![photo](/api/image.php?id=1)</code></p></div>
  <div class="field"><label class="check"><input type="checkbox" name="published" <?= ($post && (int)$post['published']) ? 'checked' : '' ?> /> Published (unticked = hidden draft)</label></div>
  <div style="display:flex; gap:var(--sp-3); align-items:center;"><button type="submit" class="btn-primary">Save post</button><a href="/admin/posts.php" style="font-size:14px; color:var(--text-secondary);">Cancel</a></div>
</form>

<?php if ($post): ?>
<form method="POST" enctype="multipart/form-data" class="admin-card">
  <?= csrf_field() ?><input type="hidden" name="action" value="upload-image" />
  <h2 class="admin-h2">Insert an image into the post</h2>
  <?php if ($uploaded): ?><p class="flash flash-ok" style="word-break:break-all;">Uploaded! Copy this into the body where you want the image:<br /><code>![describe the image](<?= e($uploaded) ?>)</code></p><?php endif; ?>
  <div class="field"><input type="file" name="inlineImage" accept="image/*" required /><p class="hint">Upload, then copy the snippet into the body above. Max 5 MB.</p></div>
  <button type="submit" class="btn-outline">Upload image</button>
</form>
<?php endif; ?>
<?php admin_foot();
