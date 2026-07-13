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

// Read a setting from (1) a constant already defined in config.local.php,
// (2) an environment variable (getenv / $_SERVER / $_ENV — covers the ways
// different hosts expose them), else (3) the default. So BOTH config.local.php
// and hPanel environment variables work; config.local.php wins if both set.
function cfg(string $key, string $default = ''): string {
    if (defined($key)) return (string) constant($key);
    $v = getenv($key);
    if ($v === false || $v === '') $v = $_SERVER[$key] ?? ($_ENV[$key] ?? false);
    if ($v !== false && $v !== '') return (string) $v;
    return $default;
}

// ── Database (MySQL) ────────────────────────────────────────────────
// Set these via config.local.php OR as hPanel environment variables.
if (!defined('DB_HOST'))     define('DB_HOST',     cfg('DB_HOST', 'localhost'));
if (!defined('DB_PORT'))     define('DB_PORT',     cfg('DB_PORT', '3306'));
if (!defined('DB_NAME'))     define('DB_NAME',     cfg('DB_NAME', 'omcomputers'));
if (!defined('DB_USER'))     define('DB_USER',     cfg('DB_USER', 'root'));
if (!defined('DB_PASSWORD')) define('DB_PASSWORD', cfg('DB_PASSWORD', ''));
if (!defined('DB_SOCKET'))   define('DB_SOCKET',   cfg('DB_SOCKET', ''));   // optional unix socket path

// ── Admin panel ─────────────────────────────────────────────────────
// The password you type to log in at /admin/.
if (!defined('ADMIN_PASSWORD')) define('ADMIN_PASSWORD', cfg('ADMIN_PASSWORD', 'change-me-now'));

// ── Site ────────────────────────────────────────────────────────────
if (!defined('SITE_URL'))  define('SITE_URL',  cfg('SITE_URL',  'https://omcomputers.net'));
if (!defined('SITE_NAME')) define('SITE_NAME', cfg('SITE_NAME', 'OM Computers'));

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

// ── Global safety net ───────────────────────────────────────────────
// Guarantees a friendly page (never a blank white screen) if any uncaught
// exception or fatal error slips through. The real error is written to the
// server log; visitors never see a stack trace in production.
function oms_error_page(): void {
    if (!headers_sent()) http_response_code(500);
    echo '<!doctype html><html lang="en"><head><meta charset="utf-8">'
       . '<meta name="viewport" content="width=device-width, initial-scale=1">'
       . '<title>Temporarily unavailable · OM Computers</title>'
       . '<style>body{font-family:"Segoe UI",system-ui,sans-serif;background:#F5F7FA;color:#0D1B2A;'
       . 'display:flex;min-height:100vh;align-items:center;justify-content:center;margin:0;padding:24px}'
       . '.b{max-width:440px;text-align:center;background:#fff;border:1px solid #E0E7EF;border-radius:16px;'
       . 'padding:40px 32px;box-shadow:0 2px 8px rgba(30,136,229,.10)}h1{font-size:22px;margin:0 0 10px}'
       . 'p{color:#4A5568;line-height:1.6;margin:0 0 8px}a{color:#1E88E5;font-weight:600}</style></head>'
       . '<body><div class="b"><div style="font-size:46px">🛠️</div>'
       . '<h1>We\'ll be right back</h1>'
       . '<p>The site hit a temporary problem. Please try again in a moment.</p>'
       . '<p><a href="/">Reload homepage</a></p></div></body></html>';
}
set_exception_handler(function (Throwable $e) {
    error_log('[OMS] Uncaught: ' . $e->getMessage() . ' @ ' . $e->getFile() . ':' . $e->getLine());
    if (cfg('APP_DEBUG') === '1') {
        if (!headers_sent()) http_response_code(500);
        echo '<pre>' . htmlspecialchars((string) $e, ENT_QUOTES) . '</pre>';
        return;
    }
    oms_error_page();
});
register_shutdown_function(function () {
    $err = error_get_last();
    if ($err && in_array($err['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR], true)) {
        error_log('[OMS] Fatal: ' . $err['message'] . ' @ ' . $err['file'] . ':' . $err['line']);
        if (cfg('APP_DEBUG') !== '1' && !headers_sent()) oms_error_page();
    }
});
