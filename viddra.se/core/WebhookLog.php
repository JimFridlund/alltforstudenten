<?php
class WebhookLog {

  public static function isAdmin($email){
    $email = strtolower(trim((string)$email));
    if ($email === '') return false;
    $list = defined('VIDDRA_ADMIN_EMAILS') ? VIDDRA_ADMIN_EMAILS : '';
    $arr = array_filter(array_map('trim', explode(',', strtolower($list))));
    return in_array($email, $arr, true);
  }

  public static function write($provider, $eventId, $eventType, $statusCode, $signatureOk, $errorMessage, $payload){
    $pdo = Database::pdo();
    $payloadJson = null;
    if ($payload !== null) {
      if (is_string($payload)) $payloadJson = $payload;
      else $payloadJson = json_encode($payload, JSON_UNESCAPED_UNICODE);
    }

    $stmt = $pdo->prepare("
      INSERT INTO viddra_webhook_events
      (provider, event_id, event_type, status_code, signature_ok, error_message, payload_json, created_at)
      VALUES (?, ?, ?, ?, ?, ?, ?, NOW())
      ON DUPLICATE KEY UPDATE
        status_code=VALUES(status_code),
        signature_ok=VALUES(signature_ok),
        error_message=VALUES(error_message),
        payload_json=VALUES(payload_json)
    ");
    $stmt->execute([
      (string)$provider,
      $eventId ? (string)$eventId : null,
      $eventType ? (string)$eventType : null,
      (int)$statusCode,
      $signatureOk ? 1 : 0,
      $errorMessage ? (string)$errorMessage : null,
      $payloadJson
    ]);
  }

  public static function latest($limit=50){
    $pdo = Database::pdo();
    $limit = max(1, min(200, (int)$limit));
    $stmt = $pdo->query("SELECT id,provider,event_id,event_type,status_code,signature_ok,error_message,created_at FROM viddra_webhook_events ORDER BY created_at DESC LIMIT " . $limit);
    return $stmt->fetchAll();
  }

  public static function read($id){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT * FROM viddra_webhook_events WHERE id=? LIMIT 1");
    $stmt->execute([(int)$id]);
    return $stmt->fetch();
  }
}
?>
