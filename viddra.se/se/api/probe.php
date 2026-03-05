<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

header("Content-Type: text/plain; charset=utf-8");

error_log("VIDDRA PROBE: start");

require __DIR__ . "/config.php";
error_log("VIDDRA PROBE: config loaded DSN=" . ($DB_DSN ?? 'MISSING'));

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
  ]);
  error_log("VIDDRA PROBE: PDO CONNECT OK");
  echo "OK\n";
} catch (Throwable $e) {
  error_log("VIDDRA PROBE: PDO CONNECT FAIL: " . $e->getMessage());
  echo "FAIL\n";
}
