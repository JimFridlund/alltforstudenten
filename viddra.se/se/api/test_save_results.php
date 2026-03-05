<?php
header('Content-Type: application/json; charset=utf-8');

$token = isset($_GET['token']) ? trim($_GET['token']) : '';
if ($token === '') {
  http_response_code(400);
  echo json_encode(array("ok"=>false, "error"=>"Missing token"));
  exit;
}

require __DIR__ . '/config.php';

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ));

  // 1) Ensure session row exists for token
  $pdo->beginTransaction();

  $stmt = $pdo->prepare("SELECT id FROM viddra_sessions WHERE token = ? LIMIT 1");
  $stmt->execute(array($token));
  $row = $stmt->fetch();

  if ($row) {
    $session_id = (int)$row['id'];
  } else {
    $ins = $pdo->prepare("INSERT INTO viddra_sessions (token, created_at) VALUES (?, NOW())");
    $ins->execute(array($token));
    $session_id = (int)$pdo->lastInsertId();
  }

  // 2) Upsert results for month
  $month_key = "2026-02";
  $payload = array(
    "incomes_total" => 45000,
    "expenses_total" => 40000,
    "savings_total" => 5000,
    "note" => "test_save_results"
  );
  $json = json_encode($payload);

  $chk = $pdo->prepare("SELECT id FROM viddra_results WHERE session_id = ? AND month_key = ? LIMIT 1");
  $chk->execute(array($session_id, $month_key));
  $existing = $chk->fetch();

  if ($existing) {
    $upd = $pdo->prepare("UPDATE viddra_results SET payload_json = ?, updated_at = NOW() WHERE id = ?");
    $upd->execute(array($json, (int)$existing['id']));
    $action = "updated";
    $id = (int)$existing['id'];
  } else {
    $ins2 = $pdo->prepare("INSERT INTO viddra_results (session_id, month_key, payload_json, created_at, updated_at) VALUES (?, ?, ?, NOW(), NOW())");
    $ins2->execute(array($session_id, $month_key, $json));
    $action = "inserted";
    $id = (int)$pdo->lastInsertId();
  }

  $pdo->commit();

  echo json_encode(array(
    "ok" => true,
    "action" => $action,
    "session_id" => $session_id,
    "month_key" => $month_key,
    "payload" => $payload
  ));
} catch (Exception $e) {
  if (isset($pdo) && $pdo->inTransaction()) $pdo->rollBack();
  http_response_code(500);
  echo json_encode(array("ok"=>false, "error"=>$e->getMessage()));
}
