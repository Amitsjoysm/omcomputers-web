<?php
require_once __DIR__ . '/inc/layout.php';
require_admin();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && csrf_check()) {
    $id = (int)($_POST['id'] ?? 0); $action = $_POST['action'] ?? '';
    if ($id > 0) {
        if ($action === 'read')        db()->prepare('UPDATE messages SET is_read=1 WHERE id=?')->execute([$id]);
        elseif ($action === 'unread')  db()->prepare('UPDATE messages SET is_read=0 WHERE id=?')->execute([$id]);
        elseif ($action === 'delete')  db()->prepare('DELETE FROM messages WHERE id=?')->execute([$id]);
    }
    header('Location: /admin/messages.php'); exit;
}

$messages = list_messages();
admin_head('Enquiries', 'messages');
?>
<h1 class="admin-h1">Enquiries</h1>
<p style="color:var(--text-secondary); font-size:14px; margin:-8px 0 var(--sp-4);">Messages sent through the website contact form.</p>
<?php if (!$messages): ?>
  <div class="admin-card"><p style="color:var(--text-muted); font-size:14px;">No enquiries yet.</p></div>
<?php else: foreach ($messages as $m): $read = (int)$m['is_read']; ?>
  <div class="admin-card" style="padding:var(--sp-4); <?= $read ? 'opacity:.72;' : 'border-left:3px solid var(--primary);' ?>">
    <div style="display:flex; justify-content:space-between; gap:var(--sp-3); flex-wrap:wrap; align-items:baseline;">
      <div>
        <strong style="font-family:var(--font-display); font-size:16px;"><?= e($m['name']) ?></strong>
        <?php if (!$read): ?><span class="badge badge-primary" style="margin-left:8px;">New</span><?php endif; ?>
        <?php if (!empty($m['service'])): ?><span class="badge badge-muted" style="margin-left:6px;"><?= e($m['service']) ?></span><?php endif; ?>
      </div>
      <span style="font-size:12px; color:var(--text-muted);"><?= e(date('j M Y, g:i A', strtotime($m['created_at']))) ?></span>
    </div>
    <div style="margin:var(--sp-2) 0; font-size:14px;">📞 <a href="tel:<?= e(preg_replace('/\s/','',$m['phone'])) ?>" style="color:var(--primary); font-weight:600;"><?= e($m['phone']) ?></a></div>
    <?php if (!empty($m['message'])): ?><p style="font-size:14px; color:var(--text-secondary); line-height:1.6; white-space:pre-wrap;"><?= e($m['message']) ?></p><?php endif; ?>
    <div style="display:flex; gap:var(--sp-2); margin-top:var(--sp-3);">
      <form method="POST"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>" /><input type="hidden" name="action" value="<?= $read?'unread':'read' ?>" /><button type="submit" class="btn-outline" style="padding:5px 12px; font-size:13px;"><?= $read?'Mark unread':'Mark read' ?></button></form>
      <form method="POST" onsubmit="return confirm('Delete this enquiry?');"><?= csrf_field() ?><input type="hidden" name="id" value="<?= (int)$m['id'] ?>" /><input type="hidden" name="action" value="delete" /><button type="submit" class="btn-danger" style="padding:5px 12px; font-size:13px;">Delete</button></form>
    </div>
  </div>
<?php endforeach; endif; ?>
<?php admin_foot();
