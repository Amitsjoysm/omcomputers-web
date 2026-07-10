<?php
require_once __DIR__ . '/../inc/helpers.php';
$id = (int) ($_GET['id'] ?? 0);
if ($id <= 0) { http_response_code(404); echo 'Not found'; exit; }
$img = get_image($id);
if (!$img) { http_response_code(404); echo 'Not found'; exit; }
header('Content-Type: ' . $img['mime']);
header('Cache-Control: public, max-age=31536000, immutable');
echo $img['data'];
