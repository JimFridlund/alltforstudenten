<?php
declare(strict_types=1);

header("Content-Type: application/json; charset=utf-8");

require __DIR__ . "/config.php";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3,
  ));

  $stmt = $pdo->query("SHOW TABLES");
  $tables = $stmt->fetchAll(PDO::FETCH_NUM);

  $names = array();
  foreach ($tables as $row) {
    $names[] = $row[0];
  }

  $dbRow = $pdo->query("SELECT DATABASE() AS db")->fetch(PDO::FETCH_ASSOC);
  $dbName = isset($dbRow['db']) ? $dbRow['db'] : null;

  echo json_encode(array(
    "ok" => true,
    "php" => PHP_VERSION,
    "db" => $dbName,
    "table_count" => count($names),
    "has_users" => in_array("users", $names, true),
    "has_budgets" => in_array("budgets", $names, true),
    "has_onboarding" => in_array("onboarding", $names, true),
    "has_results" => in_array("results", $names, true),
  ), JSON_UNESCAPED_UNICODE);

} catch (Throwable $e) {
  http_response_code(500);
  echo json_encode(array(
    "ok" => false,
    "error" => $e->getMessage(),
  ), JSON_UNESCAPED_UNICODE);
}
