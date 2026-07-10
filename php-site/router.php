<?php
/**
 * Router for PHP's built-in dev server ONLY (php -S). It reproduces the
 * .htaccess rewrites so you can test locally without Apache. On Hostinger
 * (real Apache) this file is not used — .htaccess handles routing.
 *
 * Usage:  php -S 0.0.0.0:8000 router.php
 */
$uri = urldecode(parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH));

// Serve existing static files directly.
$full = __DIR__ . $uri;
if ($uri !== '/' && is_file($full)) return false;

// Clean-URL rewrites (mirror .htaccess)
if (preg_match('#^/blog/([A-Za-z0-9_-]+)/?$#', $uri, $m)) { $_GET['slug'] = $m[1]; require __DIR__ . '/blog/post.php'; return true; }
if (preg_match('#^/services/([A-Za-z0-9_-]+)/?$#', $uri, $m)) { $_GET['s'] = $m[1]; require __DIR__ . '/services/index.php'; return true; }
if (preg_match('#^/prices/?$#', $uri)) { header('Location: /parts/', true, 301); return true; }

// Directory index
if (is_dir($full)) {
    $idx = rtrim($full, '/') . '/index.php';
    if (is_file($idx)) { require $idx; return true; }
}
if ($uri === '/') { require __DIR__ . '/index.php'; return true; }

// 404
http_response_code(404);
require __DIR__ . '/404.php';
return true;
