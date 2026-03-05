<?php
header('Content-Type: application/json; charset=utf-8');
header('Cache-Control: no-store, no-cache, must-revalidate, max-age=0');

function respond($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

// --- PDO (db.php sets $pdo) ---
$pdo = null;
$tryFiles = array('db.php','config.php','connect.php','pdo.php');
foreach ($tryFiles as $f) {
  $path = __DIR__ . '/' . $f;
  if (file_exists($path)) {
    require_once $path;
    if (isset($pdo) && $pdo instanceof PDO) break;
  }
}
if (!($pdo instanceof PDO)) respond(array('ok'=>false,'error'=>'DB config missing (no $pdo)'), 500);

// --- inputs ---
$token = isset($_POST['token']) ? trim($_POST['token']) : '';
$month_key = isset($_POST['month_key']) ? trim($_POST['month_key']) : '';
$payload = isset($_POST['payload']) ? $_POST['payload'] : '';

if ($token === '') respond(array('ok'=>false,'error'=>'missing_token'), 400);
if ($month_key === '' || !preg_match('/^\d{4}-\d{2}$/', $month_key)) {
  respond(array('ok'=>false,'error'=>'invalid_month_key'), 400);
}
if ($payload === '') respond(array('ok'=>false,'error'=>'missing_payload'), 400);

// Validate JSON
$payloadArr = json_decode($payload, true);
if (!is_array($payloadArr)) {
  respond(array('ok'=>false,'error'=>'invalid_payload_json'), 400);
}

// Ensure total exists (backward compatible)
if (!isset($payloadArr['total'])) $payloadArr['total'] = 0;

// Re-encode normalized JSON
$payload = json_encode($payloadArr);

try {
  // 1) Find or create session
  $stmt = $pdo->prepare("SELECT id FROM viddra_sessions WHERE token = :token LIMIT 1");
  $stmt->execute(array(':token' => $token));
  $session = $stmt->fetch(PDO::FETCH_ASSOC);

  if (!$session) {
    $stmt = $pdo->prepare("INSERT INTO viddra_sessions (token) VALUES (:token)");
    $stmt->execute(array(':token' => $token));
    $session_id = (int)$pdo->lastInsertId();
  } else {
    $session_id = (int)$session['id'];
  }

  // 2) Upsert results row
  $stmt = $pdo->prepare("
    SELECT id
    FROM viddra_results
    WHERE session_id = :sid AND month_key = :mk
    LIMIT 1
  ");
  $stmt->execute(array(':sid' => $session_id, ':mk' => $month_key));
  $row = $stmt->fetch(PDO::FETCH_ASSOC);

  if ($row) {
    // IMPORTANT: payload_json (NOT payload)
    $stmt = $pdo->prepare("
      UPDATE viddra_results
      SET payload_json = :payload
      WHERE id = :id
      LIMIT 1
    ");
    $stmt->execute(array(
      ':payload' => $payload,
      ':id' => (int)$row['id']
    ));

    respond(array(
      'ok' => true,
      'mode' => 'updated',
      'month_key' => $month_key
    ));
  } else {
    // IMPORTANT: payload_json (NOT payload)
    $stmt = $pdo->prepare("
      INSERT INTO viddra_results (session_id, month_key, payload_json)
      VALUES (:sid, :mk, :payload)
    ");
    $stmt->execute(array(
      ':sid' => $session_id,
      ':mk' => $month_key,
      ':payload' => $payload
    ));

    respond(array(
      'ok' => true,
      'mode' => 'inserted',
      'month_key' => $month_key
    ));
  }

} catch (Exception $e) {
  respond(array(
    'ok' => false,
    'error' => 'exception',
    'message' => $e->getMessage()
  ), 500);
}
