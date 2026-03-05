<?php
header("Content-Type: application/json; charset=utf-8");

$allowedOrigins = array("https://www.viddra.se", "https://viddra.se");
$origin = isset($_SERVER["HTTP_ORIGIN"]) ? $_SERVER["HTTP_ORIGIN"] : "";
if ($origin && in_array($origin, $allowedOrigins, true)) {
  header("Access-Control-Allow-Origin: " . $origin);
  header("Vary: Origin");
}
header("Access-Control-Allow-Methods: POST, OPTIONS");
header("Access-Control-Allow-Headers: Content-Type");

if ($_SERVER["REQUEST_METHOD"] === "OPTIONS") { http_response_code(204); exit; }
if ($_SERVER["REQUEST_METHOD"] !== "POST") {
  http_response_code(405);
  echo json_encode(array("ok"=>false,"error"=>"Method not allowed"), JSON_UNESCAPED_UNICODE);
  exit;
}

function json_fail($code, $msg, $extra = array()) {
  http_response_code($code);
  $out = array("ok"=>false,"error"=>$msg);
  foreach ($extra as $k=>$v) { $out[$k] = $v; }
  echo json_encode($out, JSON_UNESCAPED_UNICODE);
  exit;
}

function read_json_body() {
  $raw = file_get_contents("php://input");
  if ($raw === false || trim($raw) === "") return array();
  $data = json_decode($raw, true);
  if (!is_array($data)) json_fail(400, "Invalid JSON");
  return $data;
}

function is_valid_uuid($uuid) {
  return (bool)preg_match(
    "/^[0-9a-fA-F]{8}-[0-9a-fA-F]{4}-[1-5][0-9a-fA-F]{3}-[89abAB][0-9a-fA-F]{3}-[0-9a-fA-F]{12}$/",
    $uuid
  );
}

require __DIR__ . "/config.php";
try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
    PDO::ATTR_TIMEOUT => 5,
  ));
} catch (Exception $e) {
  json_fail(500, "DB connection failed");
}

$body = read_json_body();
$user_uuid = isset($body["user_uuid"]) ? (string)$body["user_uuid"] : "";
$onboarding = isset($body["onboarding"]) ? $body["onboarding"] : null;

if ($user_uuid === "" || !is_valid_uuid($user_uuid)) json_fail(400, "Missing/invalid user_uuid");
if (!is_array($onboarding)) json_fail(400, "Missing/invalid onboarding object");

$payload_json = json_encode($onboarding, JSON_UNESCAPED_UNICODE);
if ($payload_json === false) json_fail(400, "Could not encode onboarding JSON");

try {
  $pdo->beginTransaction();

  // session by token
  $stmt = $pdo->prepare("SELECT id FROM viddra_sessions WHERE token = :t LIMIT 1");
  $stmt->execute(array(":t" => $user_uuid));
  $row = $stmt->fetch();
  $session_id = $row ? (int)$row["id"] : 0;

  if ($session_id <= 0) {
    $stmt = $pdo->prepare("INSERT INTO viddra_sessions (token, created_at) VALUES (:t, NOW())");
    $stmt->execute(array(":t" => $user_uuid));
    $session_id = (int)$pdo->lastInsertId();
  }
  if ($session_id <= 0) { $pdo->rollBack(); json_fail(500, "Could not resolve session_id"); }

  // upsert onboarding (1 row per session)
  $stmt = $pdo->prepare("SELECT id FROM viddra_onboarding WHERE session_id = :sid LIMIT 1");
  $stmt->execute(array(":sid" => $session_id));
  $existing = $stmt->fetch();

  if ($existing && isset($existing["id"])) {
    $stmt = $pdo->prepare("UPDATE viddra_onboarding SET payload_json = :p, updated_at = NOW() WHERE id = :id");
    $stmt->execute(array(":p" => $payload_json, ":id" => (int)$existing["id"]));
  } else {
    $stmt = $pdo->prepare("INSERT INTO viddra_onboarding (session_id, payload_json, created_at, updated_at) VALUES (:sid, :p, NOW(), NOW())");
    $stmt->execute(array(":sid" => $session_id, ":p" => $payload_json));
  }

  $pdo->commit();

  echo json_encode(array("ok"=>true,"session_id"=>$session_id), JSON_UNESCAPED_UNICODE);
} catch (Exception $e) {
  if ($pdo->inTransaction()) $pdo->rollBack();
  json_fail(500, "Save failed", array("detail"=>$e->getMessage()));
}
