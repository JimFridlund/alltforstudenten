<?php
header('Content-Type: application/json; charset=utf-8');

$path = __DIR__ . '/budget_store.json';

if (!file_exists($path)) {
  echo json_encode(array("ok"=>true,"budget"=>null));
  exit;
}

$raw = file_get_contents($path);
$data = json_decode($raw, true);

$budget = null;
if ($data && isset($data["budget"]) && is_array($data["budget"])) {
  $budget = $data["budget"];
}

echo json_encode(array("ok"=>true,"budget"=>$budget));
