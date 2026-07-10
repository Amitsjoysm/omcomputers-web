<?php
require_once __DIR__ . '/../inc/helpers.php';
header('Content-Type: application/json');
header('Cache-Control: no-store');

$env = [
  'DB_HOST' => DB_HOST, 'DB_PORT' => DB_PORT, 'DB_NAME' => DB_NAME,
  'DB_USER_set' => DB_USER !== '', 'DB_PASSWORD_set' => DB_PASSWORD !== '',
  'DB_SOCKET' => DB_SOCKET ?: '(TCP host/port)',
  'ADMIN_PASSWORD_set' => (ADMIN_PASSWORD !== '' && ADMIN_PASSWORD !== 'change-me-now'),
  'php_version' => PHP_VERSION,
];
$database = ['connected' => false, 'tablesReady' => false, 'tableCount' => 0, 'error' => null, 'hint' => ''];
try {
    $pdo = db();
    $rows = $pdo->query('SHOW TABLES')->fetchAll(PDO::FETCH_COLUMN);
    $database['connected'] = true;
    $database['tableCount'] = count($rows);
    $database['tablesReady'] = count($rows) > 0;
} catch (Throwable $ex) {
    $database['error'] = $ex->getCode() ?: 'error';
    $msg = $ex->getMessage();
    if (stripos($msg, 'Access denied') !== false)      $database['hint'] = 'Wrong DB_USER/DB_PASSWORD, or the user is not attached to DB_NAME.';
    elseif (stripos($msg, 'Unknown database') !== false) $database['hint'] = 'DB_NAME does not exist — check the exact name in hPanel.';
    elseif (stripos($msg, 'refused') !== false || stripos($msg, "can't connect") !== false) $database['hint'] = 'Cannot reach MySQL at DB_HOST:DB_PORT. On shared hosting DB_HOST is usually "localhost".';
    else $database['hint'] = 'Check the DB_* values in config.php (or config.local.php) against hPanel → Databases.';
}
echo json_encode(['ok' => $database['connected'], 'env' => $env, 'database' => $database], JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
