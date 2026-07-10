<?php
/**
 * Copy this file to  config.local.php  and fill in your real values.
 * config.local.php is git-ignored and takes priority over config.php.
 * (You can also set these as environment variables in hPanel instead.)
 */
define('DB_HOST', 'localhost');   // from hPanel → Databases → MySQL
define('DB_PORT', '3306');
define('DB_NAME', 'your_db_name');
define('DB_USER', 'your_db_user');
define('DB_PASSWORD', 'your_db_password');
// define('DB_SOCKET', '/path/to/mysqld.sock'); // only if hPanel gives a socket

define('ADMIN_PASSWORD', 'choose-a-strong-password'); // your /admin login
define('SITE_URL', 'https://omcomputers.net');
