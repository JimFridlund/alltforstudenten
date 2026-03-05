<?php
// api/db.php (PHP 5-kompatibel)
$cfg = __DIR__ . '/config.php';
if (file_exists($cfg)) {
  require $cfg; // ska sätta $DB_DSN, $DB_USER, $DB_PASS
}

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ));
} catch (Exception $e) {
  http_response_code(500);
  header('Content-Type: application/json; charset=utf-8');
  echo json_encode(array('ok' => false, 'error' => 'DB connect failed'));
  exit;
}
