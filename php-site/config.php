<?php
/**
 * OM Computers — configuration.
 *
 * Reads settings from environment variables when present (set these in
 * hPanel → Advanced → PHP variables, or a .env-style include), otherwise
 * falls back to the constants below. Edit the fallbacks with your real
 * database credentials and admin password before uploading, OR set the
 * environment variables — either works.
 */

// Load a local secrets file if present (php-site/config.local.php), so you
// can keep real credentials out of the shared/committed config.
$__local = __DIR__ . '/config.local.php';
if (is_file($__local)) require $__local;

function cfg(string $key, string $default = ''): string {
    $v = getenv($key);
    if ($v !== false && $v !== '') return $v;
    if (defined($key)) return (string) constant($key);
    return $default;
}

// ── Database (MySQL) ────────────────────────────────────────────────
// Set these to the values from hPanel → Databases → MySQL.
if (!defined('DB_HOST'))     define('DB_HOST', 'localhost');
if (!defined('DB_PORT'))     define('DB_PORT', '3306');
if (!defined('DB_NAME'))     define('DB_NAME', 'omcomputers');
if (!defined('DB_USER'))     define('DB_USER', 'root');
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', '');
if (!defined('DB_SOCKET'))   define('DB_SOCKET', '');   // optional unix socket path

// ── Admin panel ─────────────────────────────────────────────────────
// The password you type to log in at /admin/.
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', 'change-me-now');

// ── Site ────────────────────────────────────────────────────────────
if (!defined('SITE_URL'))  define('SITE_URL',  'https://omcomputers.net');
if (!defined('SITE_NAME')) define('SITE_NAME', 'OM Computers');

// Uploads directory (relative to this app root). Must be writable.
if (!defined('UPLOAD_DIR'))  define('UPLOAD_DIR',  __DIR__ . '/uploads');
if (!defined('UPLOAD_URL'))  define('UPLOAD_URL',  '/uploads');

date_default_timezone_set('Asia/Kolkata');

// Show errors only when DEBUG is on.
if (cfg('APP_DEBUG') === '1') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(E_ALL & ~E_DEPRECATED & ~E_NOTICE);
    ini_set('display_errors', '0');
}
