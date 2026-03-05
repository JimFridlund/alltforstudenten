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

  $tables = array("users", "viddra_sessions", "viddra_budget", "viddra_onboarding", "viddra_results");

  $out = array("ok" => true, "tables" => array());

  foreach ($tables as $t) {
    $stmt = $pdo->query("SHOW COLUMNS FROM `" . $t . "`");
    $cols = $stmt->fetchAll();
    $names = array();
    foreach ($cols as $c) { $names[] = $c["Field"]; }
    $out["tables"][$t] = $names;
  }

  echo json_encode($out, JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
  http_response_code(500);
  echo json_encode(array("ok" => false, "error" => $e->getMessage()), JSON_UNESCAPED_UNICODE);
}
