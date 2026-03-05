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
  // 1) Finns tabellen?
  $st = $pdo->query("SHOW TABLES LIKE 'viddra_results'");
  $exists = $st->fetch(PDO::FETCH_NUM) ? true : false;

  if (!$exists) {
    respond([
      'ok'=>false,
      'error'=>'Table viddra_results does not exist',
      'fix_sql'=> "CREATE TABLE IF NOT EXISTS viddra_results (
  id INT UNSIGNED NOT NULL AUTO_INCREMENT,
  token VARCHAR(100) NOT NULL,
  payload MEDIUMTEXT NOT NULL,
  created_at DATETIME NOT NULL,
  PRIMARY KEY (id),
  INDEX idx_token_created (token, created_at)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;"
    ], 200);
  }

  // 2) Visa kolumner (så vi ser att schema stämmer)
  $cols = $pdo->query("SHOW COLUMNS FROM viddra_results")->fetchAll(PDO::FETCH_ASSOC);

  // 3) Test-INSERT
  $token = 'debug_token_' . date('Ymd_His');
  $payload = json_encode(['total'=>123,'debug'=>true]);
  $created = date('Y-m-d H:i:s');

  $st2 = $pdo->prepare("INSERT INTO viddra_results (token, payload, created_at) VALUES (:t,:p,:c)");
  $st2->execute([':t'=>$token, ':p'=>$payload, ':c'=>$created]);

  // 4) Läs tillbaka
  $st3 = $pdo->prepare("SELECT id, token, created_at, payload FROM viddra_results WHERE token = :t ORDER BY id DESC LIMIT 1");
  $st3->execute([':t'=>$token]);
  $row = $st3->fetch(PDO::FETCH_ASSOC);

  respond([
    'ok'=>true,
    'table_exists'=>true,
    'columns'=>$cols,
    'insert_ok'=>true,
    'inserted_row'=>$row
  ], 200);

} catch (Exception $e) {
  // Här får vi ofta "Table doesn't exist" eller "INSERT command denied"
  respond([
    'ok'=>false,
    'error'=>'Exception during test',
    'message'=>$e->getMessage()
  ], 500);
}
