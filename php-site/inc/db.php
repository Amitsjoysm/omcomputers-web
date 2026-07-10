<?php
require_once __DIR__ . '/../config.php';

/** Returns a shared PDO connection, creating tables + seed data on first use. */
function db(): PDO {
    static $pdo = null;
    static $inited = false;
    if ($pdo === null) {
        $socket = DB_SOCKET;
        if ($socket !== '') {
            $dsn = 'mysql:unix_socket=' . $socket . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        } else {
            $dsn = 'mysql:host=' . DB_HOST . ';port=' . DB_PORT . ';dbname=' . DB_NAME . ';charset=utf8mb4';
        }
        $pdo = new PDO($dsn, DB_USER, DB_PASSWORD, [
            PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
            PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
            PDO::ATTR_EMULATE_PREPARES   => false,
        ]);
    }
    if (!$inited) {
        $inited = true;
        init_db($pdo);
    }
    return $pdo;
}

/** Creates any missing tables and seeds starter content once. */
function init_db(PDO $pdo): void {
    $schema = [
        'settings' => "CREATE TABLE IF NOT EXISTS settings (
            id TINYINT UNSIGNED PRIMARY KEY, data TEXT NOT NULL
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        'posts' => "CREATE TABLE IF NOT EXISTS posts (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            slug VARCHAR(191) NOT NULL UNIQUE,
            title VARCHAR(255) NOT NULL,
            excerpt VARCHAR(500) NOT NULL DEFAULT '',
            tags TEXT, cover_image VARCHAR(255) DEFAULT NULL, body MEDIUMTEXT,
            published TINYINT(1) NOT NULL DEFAULT 0, publish_date DATE NOT NULL,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP,
            updated_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        'price_categories' => "CREATE TABLE IF NOT EXISTS price_categories (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(191) NOT NULL UNIQUE, sort INT NOT NULL DEFAULT 0
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        'price_items' => "CREATE TABLE IF NOT EXISTS price_items (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            category_id INT UNSIGNED NOT NULL,
            name VARCHAR(255) NOT NULL, brand VARCHAR(191) NOT NULL DEFAULT '',
            specs TEXT, price DECIMAL(10,2) NOT NULL DEFAULT 0,
            in_stock TINYINT(1) NOT NULL DEFAULT 1, image VARCHAR(255) DEFAULT NULL,
            sort INT NOT NULL DEFAULT 0,
            CONSTRAINT fk_item_category FOREIGN KEY (category_id)
              REFERENCES price_categories(id) ON DELETE CASCADE
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        'images' => "CREATE TABLE IF NOT EXISTS images (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            filename VARCHAR(255) NOT NULL DEFAULT '', mime VARCHAR(100) NOT NULL,
            data MEDIUMBLOB NOT NULL, created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
        'messages' => "CREATE TABLE IF NOT EXISTS messages (
            id INT UNSIGNED AUTO_INCREMENT PRIMARY KEY,
            name VARCHAR(191) NOT NULL, phone VARCHAR(60) NOT NULL DEFAULT '',
            service VARCHAR(120) NOT NULL DEFAULT '', message TEXT,
            is_read TINYINT(1) NOT NULL DEFAULT 0,
            created_at TIMESTAMP NOT NULL DEFAULT CURRENT_TIMESTAMP
        ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4",
    ];

    // Only CREATE tables that don't exist (works even without DDL rights if
    // the tables were created manually from schema.sql).
    $existing = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    foreach ($schema as $table => $sql) {
        if (!in_array($table, $existing, true)) $pdo->exec($sql);
    }

    seed_if_empty($pdo);
}

function seed_if_empty(PDO $pdo): void {
    // Settings
    $n = (int) $pdo->query('SELECT COUNT(*) FROM settings WHERE id=1')->fetchColumn();
    if ($n === 0) {
        $s = @json_decode((string) @file_get_contents(__DIR__ . '/../seed/settings.json'), true) ?: [];
        $pdo->prepare('INSERT INTO settings (id, data) VALUES (1, ?)')
            ->execute([json_encode($s ?: default_settings())]);
    }
    // Prices
    $n = (int) $pdo->query('SELECT COUNT(*) FROM price_categories')->fetchColumn();
    if ($n === 0) {
        $sort = 0;
        foreach (glob(__DIR__ . '/../seed/prices/*.json') as $f) {
            $cat = json_decode((string) file_get_contents($f), true);
            if (!$cat) continue;
            $pdo->prepare('INSERT INTO price_categories (name, sort) VALUES (?, ?)')
                ->execute([$cat['category'] ?? 'Category', $sort++]);
            $cid = (int) $pdo->lastInsertId();
            $isort = 0;
            foreach (($cat['items'] ?? []) as $it) {
                $pdo->prepare('INSERT INTO price_items (category_id, name, brand, specs, price, in_stock, sort) VALUES (?,?,?,?,?,?,?)')
                    ->execute([$cid, $it['name'] ?? '', $it['brand'] ?? '', $it['specs'] ?? '',
                        (float)($it['price'] ?? 0), (($it['inStock'] ?? true) ? 1 : 0), $isort++]);
            }
        }
    }
    // Blog posts (from seed/blog/*.md with frontmatter)
    $n = (int) $pdo->query('SELECT COUNT(*) FROM posts')->fetchColumn();
    if ($n === 0) {
        foreach (glob(__DIR__ . '/../seed/blog/*.md') as $f) {
            $raw = (string) file_get_contents($f);
            [$meta, $body] = parse_frontmatter($raw);
            $slug = basename($f, '.md');
            $pdo->prepare('INSERT INTO posts (slug, title, excerpt, tags, body, published, publish_date) VALUES (?,?,?,?,?,?,?)')
                ->execute([
                    $slug, $meta['title'] ?? $slug, $meta['excerpt'] ?? '',
                    json_encode($meta['tags'] ?? []), trim($body),
                    (($meta['published'] ?? false) ? 1 : 0),
                    $meta['publishDate'] ?? date('Y-m-d'),
                ]);
        }
    }
}

function default_settings(): array {
    return [
        'phone' => '+91 98765 43210', 'whatsapp' => '919876543210',
        'email' => 'info@omcomputers.net', 'address' => 'Pune, Maharashtra',
        'googleMaps' => '', 'openHours' => 'Monday to Saturday, 9 AM – 7 PM',
    ];
}

/** Minimal YAML-ish frontmatter parser for the seed markdown files. */
function parse_frontmatter(string $raw): array {
    if (!preg_match('/^---\r?\n(.*?)\r?\n---\r?\n?/s', $raw, $m)) return [[], $raw];
    $meta = [];
    foreach (preg_split('/\r?\n/', $m[1]) as $line) {
        $p = strpos($line, ':');
        if ($p === false) continue;
        $k = trim(substr($line, 0, $p));
        $v = trim(substr($line, $p + 1));
        if (strlen($v) >= 2 && $v[0] === '[' && substr($v, -1) === ']') {
            $v = array_values(array_filter(array_map(
                fn($x) => trim($x, " \"'"), explode(',', substr($v, 1, -1)))));
        } elseif ($v === 'true' || $v === 'false') {
            $v = $v === 'true';
        } else {
            $v = trim($v, "\"'");
        }
        $meta[$k] = $v;
    }
    return [$meta, substr($raw, strlen($m[0]))];
}
