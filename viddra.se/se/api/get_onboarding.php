<?php
header("Content-Type: application/json; charset=utf-8");

if ($_SERVER["REQUEST_METHOD"] !== "GET") {
  http_response_code(405);
  echo json_encode(array("ok"=>false,"error"=>"Method not allowed"), JSON_UNESCAPED_UNICODE);
  exit;
}

function json_fail($code, $msg) {
  http_response_code($code);
  echo json_encode(array("ok"=>false,"error"=>$msg), JSON_UNESCAPED_UNICODE);
  exit;
}

function is_valid_uuid($uuid) {
  return (bool)preg_match(
    "/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/",
    $uuid
  );
}

$user_uuid = isset($_GET["user_uuid"]) ? (string)$_GET["user_uuid"] : "";
if ($user_uuid === "" || !is_valid_uuid($user_uuid)) json_fail(400, "Missing/invalid user_uuid");

require __DIR__ . "/config.php";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 5,
  ));

  $stmt = $pdo->prepare("
    SELECT o.payload_json
    FROM viddra_sessions s
    JOIN viddra_onboarding o ON o.session_id = s.id
    WHERE s.token = :t
    LIMIT 1
  ");
  $stmt->execute(array(":t" => $user_uuid));
  $row = $stmt->fetch();

  if (!$row) {
    echo json_encode(array("ok"=>true,"onboarding"=>null), JSON_UNESCAPED_UNICODE);
    exit;
  }

  $payload = json_decode($row["payload_json"], true);
  echo json_encode(array("ok"=>true,"onboarding"=>$payload), JSON_UNESCAPED_UNICODE);

} catch (Exception $e) {
  json_fail(500, $e->getMessage());
}
