<?php
require_once __DIR__ . '/../../inc/helpers.php';
require_once __DIR__ . '/../../inc/auth.php';

function admin_head(string $title, string $active = ''): void {
    $unread = unread_count();
    $nav = [
        ['/admin/', 'Dashboard', 'dashboard'],
        ['/admin/posts.php', 'Blog Posts', 'posts'],
        ['/admin/prices.php', 'Price List', 'prices'],
        ['/admin/messages.php', 'Enquiries', 'messages'],
        ['/admin/settings.php', 'Site Settings', 'settings'],
    ];
    ?><!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1.0" />
  <meta name="robots" content="noindex, nofollow" />
  <title><?= e($title) ?> · OM Computers Admin</title>
  <link rel="icon" href="/favicon.ico" sizes="any" />
  <link rel="preconnect" href="https://fonts.googleapis.com" />
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin />
  <link href="https://fonts.googleapis.com/css2?family=Plus+Jakarta+Sans:wght@400;600;700;800&family=DM+Sans:wght@400;500&family=JetBrains+Mono:wght@400;500&display=swap" rel="stylesheet" />
  <link rel="stylesheet" href="/assets/global.css" />
  <link rel="stylesheet" href="/assets/admin.css" />
</head>
<body class="admin">
  <header class="admin-topbar">
    <div class="admin-topbar-inner">
      <a href="/admin/" class="admin-brand"><img src="/logo.png" alt="" width="34" height="34" /><span>OM Computers <strong>Admin</strong></span></a>
      <nav class="admin-nav">
        <?php foreach ($nav as $item): ?>
          <a href="<?= $item[0] ?>" class="admin-nav-link<?= $active===$item[2]?' active':'' ?>"><?= e($item[1]) ?><?php if ($item[2]==='messages' && $unread>0): ?><span class="admin-badge"><?= $unread ?></span><?php endif; ?></a>
        <?php endforeach; ?>
      </nav>
      <div class="admin-actions">
        <a href="/" target="_blank" rel="noopener" class="admin-nav-link">View site ↗</a>
        <form method="POST" action="/admin/logout.php" style="display:inline;"><?= csrf_field() ?><button type="submit" class="admin-logout">Log out</button></form>
      </div>
    </div>
  </header>
  <main class="admin-main">
<?php
}

function admin_foot(): void { ?>
  </main>
</body>
</html>
<?php
}
