<?php
require_once __DIR__ . '/../config.php';

function is_https(): bool {
    if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') return true;
    // Behind Hostinger's / a CDN's proxy, HTTPS is signalled via a header.
    if (strtolower($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https') return true;
    if (($_SERVER['SERVER_PORT'] ?? '') == 443) return true;
    return false;
}

function admin_session_start(): void {
    if (session_status() === PHP_SESSION_NONE) {
        session_set_cookie_params([
            'lifetime' => 0,
            'path' => '/',
            'httponly' => true,
            'samesite' => 'Lax',
            'secure' => is_https(),
        ]);
        session_name('oms_admin');
        session_start();
    }
}

function is_logged_in(): bool {
    admin_session_start();
    return !empty($_SESSION['admin_ok']);
}

function check_password(string $input): bool {
    $expected = ADMIN_PASSWORD;
    if ($expected === '' || $input === '') return false;
    return hash_equals(hash('sha256', $expected), hash('sha256', $input));
}

function login(): void { admin_session_start(); session_regenerate_id(true); $_SESSION['admin_ok'] = true; }
function logout(): void { admin_session_start(); $_SESSION = []; session_destroy(); }

/** Redirect to login unless authenticated. Call at the top of every admin page. */
function require_admin(): void {
    if (!is_logged_in()) { header('Location: /admin/login.php'); exit; }
}

// ── Simple in-memory-ish login rate limit (per session + IP file) ──
function login_allowed(): bool {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'x';
    $f = sys_get_temp_dir() . '/oms_login_' . md5($ip);
    $now = time();
    $data = @json_decode((string)@file_get_contents($f), true) ?: ['c' => 0, 'r' => $now + 900];
    if ($data['r'] < $now) return true;
    return $data['c'] < 8;
}
function record_login_fail(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'x';
    $f = sys_get_temp_dir() . '/oms_login_' . md5($ip);
    $now = time();
    $data = @json_decode((string)@file_get_contents($f), true) ?: ['c' => 0, 'r' => $now + 900];
    if ($data['r'] < $now) $data = ['c' => 1, 'r' => $now + 900];
    else $data['c']++;
    @file_put_contents($f, json_encode($data));
}
function clear_login_fails(): void {
    $ip = $_SERVER['REMOTE_ADDR'] ?? 'x';
    @unlink(sys_get_temp_dir() . '/oms_login_' . md5($ip));
}

/** CSRF token helpers for admin forms. */
function csrf_token(): string {
    admin_session_start();
    if (empty($_SESSION['csrf'])) $_SESSION['csrf'] = bin2hex(random_bytes(16));
    return $_SESSION['csrf'];
}
function csrf_check(): bool {
    admin_session_start();
    return !empty($_POST['csrf']) && !empty($_SESSION['csrf']) && hash_equals($_SESSION['csrf'], $_POST['csrf']);
}
function csrf_field(): string { return '<input type="hidden" name="csrf" value="' . csrf_token() . '">'; }
