<?php
header("Content-Type: text/plain; charset=utf-8");
echo "X1\n";

require __DIR__ . "/config.php";
echo "X2\n";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
  ]);
  echo "X3 CONNECT OK\n";
} catch (Throwable $e) {
  echo "X3 CONNECT FAIL\n";
  echo $e->getMessage() . "\n";
}
