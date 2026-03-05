<?php
header('Content-Type: application/json; charset=utf-8');

function respond($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

$token = '';
if (!empty($_GET['token'])) $token = trim($_GET['token']);
if ($token === '') respond(['ok'=>false,'error'=>'Missing token'], 400);

// Försök återanvända samma $pdo som du ev har i api
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

// Prova vanliga tabell/kolumn-kombos
$tries = array(
  array('results','token','payload','created_at'),
  array('results','token','data','created_at'),
  array('results','token','json','created_at'),
  array('viddra_results','token','payload','created_at'),
  array('viddra_results','token','data','created_at'),
  array('viddra_results','token','json','created_at'),
);

foreach ($tries as $t) {
  list($table,$tcol,$pcol,$ocol) = $t;
  try {
    $sql = "SELECT {$pcol} AS payload, {$ocol} AS created_at FROM {$table} WHERE {$tcol} = :token ORDER BY {$ocol} DESC LIMIT 1";
    $st = $pdo->prepare($sql);
    $st->execute([':token'=>$token]);
    $row = $st->fetch(PDO::FETCH_ASSOC);
    if ($row && isset($row['payload'])) {
      respond([
        'ok'=>true,
        'found'=>true,
        'table'=>$table,
        'payload_col'=>$pcol,
        'created_at_col'=>$ocol,
        'created_at'=>$row['created_at'],
        'payload_raw'=>$row['payload'],
        'payload_json'=>json_decode($row['payload'], true)
      ], 200);
    }
  } catch (Exception $e) {
    // prova nästa
  }
}

respond(['ok'=>true,'found'=>false,'note'=>'no rows for token in known tables'], 200);
