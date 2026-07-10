<?php
require_once __DIR__ . '/db.php';

/** HTML-escape. */
function e($v): string { return htmlspecialchars((string)$v, ENT_QUOTES, 'UTF-8'); }

/** Site settings (cached per request). */
function settings(): array {
    static $s = null;
    if ($s !== null) return $s;
    try {
        $row = db()->query('SELECT data FROM settings WHERE id=1')->fetchColumn();
        $s = $row ? array_merge(default_settings(), json_decode($row, true) ?: []) : default_settings();
    } catch (Throwable $ex) {
        $s = default_settings();
    }
    return $s;
}

function save_settings(array $data): void {
    db()->prepare('INSERT INTO settings (id, data) VALUES (1, ?) ON DUPLICATE KEY UPDATE data = VALUES(data)')
        ->execute([json_encode($data)]);
}

/** WhatsApp link with optional prefilled text. */
function wa_link(string $text = ''): string {
    $wa = preg_replace('/\D/', '', settings()['whatsapp'] ?? '');
    return 'https://wa.me/' . $wa . ($text ? '?text=' . rawurlencode($text) : '');
}

function tel_link(): string { return 'tel:' . preg_replace('/\s/', '', settings()['phone'] ?? ''); }

// ── Posts ───────────────────────────────────────────────────────────
function decode_tags($t): array { $a = json_decode((string)$t, true); return is_array($a) ? $a : []; }

function published_posts(): array {
    $rows = db()->query('SELECT id, slug, title, excerpt, tags, cover_image, body, publish_date
                         FROM posts WHERE published=1 ORDER BY publish_date DESC, id DESC')->fetchAll();
    foreach ($rows as &$r) $r['tags'] = decode_tags($r['tags']);
    return $rows;
}
function all_posts(): array {
    $rows = db()->query('SELECT id, slug, title, excerpt, tags, cover_image, body, published, publish_date
                         FROM posts ORDER BY publish_date DESC, id DESC')->fetchAll();
    foreach ($rows as &$r) $r['tags'] = decode_tags($r['tags']);
    return $rows;
}
function post_by_slug(string $slug): ?array {
    $st = db()->prepare('SELECT * FROM posts WHERE slug=? AND published=1 LIMIT 1');
    $st->execute([$slug]);
    $r = $st->fetch();
    if (!$r) return null;
    $r['tags'] = decode_tags($r['tags']);
    return $r;
}
function post_by_id(int $id): ?array {
    $st = db()->prepare('SELECT * FROM posts WHERE id=? LIMIT 1');
    $st->execute([$id]);
    $r = $st->fetch();
    if (!$r) return null;
    $r['tags'] = decode_tags($r['tags']);
    return $r;
}
function slug_exists(string $slug, int $exclude = 0): bool {
    $st = db()->prepare('SELECT id FROM posts WHERE slug=? AND id<>? LIMIT 1');
    $st->execute([$slug, $exclude]);
    return (bool)$st->fetch();
}
function slugify(string $s): string {
    $s = strtolower(trim($s));
    $s = preg_replace('/[\'’]/u', '', $s);
    $s = preg_replace('/[^a-z0-9]+/', '-', $s);
    $s = trim($s, '-');
    return substr($s ?: 'post', 0, 120);
}

// ── Prices ──────────────────────────────────────────────────────────
function categories_with_items(): array {
    $cats = db()->query('SELECT id, name FROM price_categories ORDER BY sort, name')->fetchAll();
    $items = db()->query('SELECT id, category_id, name, brand, specs, price, in_stock, image
                          FROM price_items ORDER BY sort, id')->fetchAll();
    foreach ($cats as &$c) {
        $c['items'] = array_values(array_filter($items, fn($i) => (int)$i['category_id'] === (int)$c['id']));
    }
    return $cats;
}
function category_by_id(int $id): ?array {
    $st = db()->prepare('SELECT id, name FROM price_categories WHERE id=? LIMIT 1');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}
function items_for_category(int $id): array {
    $st = db()->prepare('SELECT id, name, brand, specs, price, in_stock, image FROM price_items WHERE category_id=? ORDER BY sort, id');
    $st->execute([$id]);
    return $st->fetchAll();
}
function item_by_id(int $id): ?array {
    $st = db()->prepare('SELECT * FROM price_items WHERE id=? LIMIT 1');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

// ── Messages ────────────────────────────────────────────────────────
function create_message(string $name, string $phone, string $service, string $message): void {
    db()->prepare('INSERT INTO messages (name, phone, service, message) VALUES (?,?,?,?)')
        ->execute([mb_substr($name,0,191), mb_substr($phone,0,60), mb_substr($service,0,120), mb_substr($message,0,5000)]);
}
function list_messages(): array {
    return db()->query('SELECT id, name, phone, service, message, is_read, created_at
                        FROM messages ORDER BY created_at DESC, id DESC LIMIT 500')->fetchAll();
}
function unread_count(): int {
    try { return (int) db()->query('SELECT COUNT(*) FROM messages WHERE is_read=0')->fetchColumn(); }
    catch (Throwable $e) { return 0; }
}

// ── Images (stored in DB) ───────────────────────────────────────────
function save_image(string $filename, string $mime, string $data): int {
    $st = db()->prepare('INSERT INTO images (filename, mime, data) VALUES (?,?,?)');
    $st->bindValue(1, $filename);
    $st->bindValue(2, $mime);
    $st->bindValue(3, $data, PDO::PARAM_LOB);
    $st->execute();
    return (int) db()->lastInsertId();
}
function get_image(int $id): ?array {
    $st = db()->prepare('SELECT mime, data FROM images WHERE id=? LIMIT 1');
    $st->execute([$id]);
    return $st->fetch() ?: null;
}

/** Handle an uploaded image ($_FILES entry) → returns public URL or null. */
function store_upload(?array $file, ?string &$err = null): ?string {
    if (!$file || ($file['error'] ?? UPLOAD_ERR_NO_FILE) === UPLOAD_ERR_NO_FILE) return null;
    if (($file['error'] ?? 1) !== UPLOAD_ERR_OK) { $err = 'Upload failed. Try a smaller file.'; return null; }
    if (($file['size'] ?? 0) > 5 * 1024 * 1024) { $err = 'Image too large — maximum 5 MB.'; return null; }
    $mime = mime_content_type($file['tmp_name']) ?: ($file['type'] ?? '');
    $ok = ['image/jpeg','image/png','image/webp','image/gif','image/svg+xml','image/avif'];
    if (!in_array($mime, $ok, true)) { $err = 'Unsupported type — use JPG, PNG, WebP or GIF.'; return null; }
    $data = file_get_contents($file['tmp_name']);
    $id = save_image($file['name'] ?? 'upload', $mime, $data);
    return '/api/image.php?id=' . $id;
}

function read_time(string $body): int {
    $words = str_word_count(strip_tags($body));
    return max(1, (int) ceil($words / 200));
}
function fmt_date(string $d): string { return date('j M Y', strtotime($d)); }
function fmt_date_long(string $d): string { return date('j F Y', strtotime($d)); }
