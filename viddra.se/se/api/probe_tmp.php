<?php
declare(strict_types=1);

ini_set('display_errors', '0');
error_reporting(E_ALL);

$logFile = sys_get_temp_dir() . '/viddra_probe.log';

function w(string $s): void {
  global $logFile;
  @file_put_contents($logFile, $s . "\n", FILE_APPEND);
}

register_shutdown_function(function () {
  $e = error_get_last();
  if ($e) {
    w("SHUTDOWN ERROR: " . json_encode($e));
  }
});

w("\n--- PROBE " . gmdate('c') . " ---");

require __DIR__ . "/config.php";
w("CONFIG loaded");
w("DSN=" . ($DB_DSN ?? 'MISSING'));
w("USER=" . ($DB_USER ?? 'MISSING'));
w("PASSLEN=" . (isset($DB_PASS) ? strlen($DB_PASS) : -1));

try {
  w("About to new PDO()");
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
  ]);
  w("PDO CONNECT OK");
  header("Content-Type: text/plain; charset=utf-8");
  echo "OK\n";
} catch (Throwable $e) {
  w("PDO CONNECT FAIL: " . $e->getMessage());
  http_response_code(500);
  header("Content-Type: text/plain; charset=utf-8");
  echo "FAIL\n";
}
