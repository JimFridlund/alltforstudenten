<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require __DIR__ . "/config.php";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ]);

  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  $names = array_map(fn($r) => $r[0], $tables);

  echo json_encode([
    "ok" => true,
    "php" => PHP_VERSION,
    "db" => $pdo->query("SELECT DATABASE() AS db")->fetch()["db"] ?? null,
    "tables" => [
      "users" => in_array("users", $names, true),
      "onboarding" => in_array("onboarding", $names, true),
      "budgets" => in_array("budgets", $names, true),
      "results" => in_array("results", $names, true),
    ],
    "table_count" => count($names),
  ], JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode([
    "ok" => false,
    "error" => $e->getMessage(),
  ], JSON_UNESCAPED_UNICODE);
}
