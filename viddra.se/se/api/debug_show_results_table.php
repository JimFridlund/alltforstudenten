<?php
header('Content-Type: application/json; charset=utf-8');

function respond($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

// PDO
$pdo = null;
$tryFiles = array('db.php','config.php','connect.php','pdo.php');
foreach ($tryFiles as $f) {
  $path = __DIR__ . '/' . $f;
  if (file_exists($path)) {
    require_once $path;
    if (isset($pdo) && $pdo instanceof PDO) break;
  }
}
if (!($pdo instanceof PDO)) respond(['ok'=>false,'error'=>'DB config missing (no $pdo)'], 500);

try {
  $exists = $pdo->query("SHOW TABLES LIKE 'viddra_results'")->fetch(PDO::FETCH_NUM) ? true : false;
  if (!$exists) respond(['ok'=>false,'error'=>'Table viddra_results does not exist'], 200);

  $cols = $pdo->query("SHOW COLUMNS FROM viddra_results")->fetchAll(PDO::FETCH_ASSOC);

  respond([
    'ok'=>true,
    'table'=>'viddra_results',
    'columns'=>$cols
  ], 200);

} catch (Exception $e) {
  respond(['ok'=>false,'error'=>'Exception','message'=>$e->getMessage()], 500);
}
