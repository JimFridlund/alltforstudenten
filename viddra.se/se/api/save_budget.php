<?php
header('Content-Type: application/json; charset=utf-8');

$raw = file_get_contents('php://input');
if (!$raw) { echo json_encode(array("ok"=>false,"error"=>"No body")); exit; }

$data = json_decode($raw, true);
if (!$data) { echo json_encode(array("ok"=>false,"error"=>"Invalid JSON")); exit; }

if (!isset($data["budget"]) || !is_array($data["budget"])) {
  echo json_encode(array("ok"=>false,"error"=>"Missing budget"));
  exit;
}

// Spara i fil i /api/
$path = __DIR__ . '/budget_store.json';
file_put_contents($path, json_encode(array(
  "saved_at" => time(),
  "budget" => $data["budget"]
)));

echo json_encode(array("ok"=>true));
