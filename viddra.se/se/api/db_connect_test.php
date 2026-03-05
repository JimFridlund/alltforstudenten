<?php
declare(strict_types=1);
header("Content-Type: text/plain; charset=utf-8");

require __DIR__ . "/config.php";

echo "DSN: " . $DB_DSN . "\n";
echo "USER: " . $DB_USER . "\n";
echo "PASS: " . (strlen($DB_PASS) > 0 ? "SET" : "EMPTY") . "\n\n";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  ]);
  echo "DB CONNECT: OK\n";

  $stmt = $pdo->query("SELECT DATABASE() AS db");
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
  echo "CURRENT DB: " . ($row['db'] ?? '(null)') . "\n";

  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  echo "TABLE COUNT: " . count($tables) . "\n";
  if (count($tables) > 0) {
    echo "FIRST TABLE: " . $tables[0][0] . "\n";
  }
} catch (Throwable $e) {
  echo "DB CONNECT: FAIL\n";
  echo "ERROR: " . $e->getMessage() . "\n";
}
