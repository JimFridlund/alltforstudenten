<?php
declare(strict_types=1);

ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Content-Type: text/plain; charset=utf-8");

echo "1) Start\n";

require __DIR__ . "/config.php";
echo "2) Config loaded\n";

echo "DSN: " . $DB_DSN . "\n";
echo "USER: " . $DB_USER . "\n";
echo "PASS: " . (strlen($DB_PASS) ? "SET" : "EMPTY") . "\n";

echo "3) About to connect...\n";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
  ]);
  echo "4) CONNECT OK\n";

  $row = $pdo->query("SELECT DATABASE() AS db")->fetch(PDO::FETCH_ASSOC);
  echo "5) CURRENT DB: " . ($row['db'] ?? '(null)') . "\n";

  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  echo "6) TABLE COUNT: " . count($tables) . "\n";

} catch (Throwable $e) {
  echo "4) CONNECT FAIL\n";
  echo "ERROR: " . $e->getMessage() . "\n";
}
