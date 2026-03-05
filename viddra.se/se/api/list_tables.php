<?php
ini_set('display_errors', '1');
error_reporting(E_ALL);

header("Content-Type: application/json; charset=utf-8");

require __DIR__ . "/config.php";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
  ));

  $stmt = $pdo->query("SHOW TABLES");
  $rows = $stmt->fetchAll(PDO::FETCH_NUM);

  $tables = array();
  foreach ($rows as $r) { $tables[] = $r[0]; }

  echo json_encode(array("ok" => true, "db" => $pdo->query("SELECT DATABASE()")->fetchColumn(), "tables" => $tables), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(array("ok" => false, "error" => $e->getMessage()), JSON_UNESCAPED_UNICODE);
}
