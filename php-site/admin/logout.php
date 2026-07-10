<?php
require_once __DIR__ . '/../inc/auth.php';
if ($_SERVER['REQUEST_METHOD'] === 'POST') { logout(); }
header('Location: /admin/login.php');
exit;
