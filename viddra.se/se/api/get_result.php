<?php
// api/get_result.php
require __DIR__ . '/session.php';

$session = ensureSession($pdo);
$month = isset($_GET['month']) ? (string)$_GET['month'] : '';

if ($month !== '') {
  $stmt = $pdo->prepare("SELECT month_key, payload_json FROM viddra_results WHERE session_id=? AND month_key=? LIMIT 1");
  $stmt->execute([$session['session_id'], $month]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
} else {
  $stmt = $pdo->prepare("SELECT month_key, payload_json FROM viddra_results WHERE session_id=? ORDER BY month_key DESC LIMIT 1");
  $stmt->execute([$session['session_id']]);
  $row = $stmt->fetch(PDO::FETCH_ASSOC);
}

echo json_encode([
  'ok' => true,
  'month_key' => $row ? $row['month_key'] : null,
  'data' => $row ? json_decode($row['payload_json'], true) : null
], JSON_UNESCAPED_UNICODE);
