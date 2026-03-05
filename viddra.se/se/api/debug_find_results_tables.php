<?php
header('Content-Type: application/json; charset=utf-8');

function respond($arr, $code = 200) {
  http_response_code($code);
  echo json_encode($arr);
  exit;
}

// Hämta PDO
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
  // 1) hitta tabeller som har en kolumn som heter token (eller liknande)
  $sql = "
    SELECT TABLE_NAME, COLUMN_NAME
    FROM INFORMATION_SCHEMA.COLUMNS
    WHERE TABLE_SCHEMA = DATABASE()
      AND (
        COLUMN_NAME = 'token'
        OR COLUMN_NAME = 'user_token'
        OR COLUMN_NAME = 'session_token'
        OR COLUMN_NAME LIKE '%token%'
      )
    ORDER BY TABLE_NAME, COLUMN_NAME
  ";
  $rows = $pdo->query($sql)->fetchAll(PDO::FETCH_ASSOC);

  // 2) För varje tabell: lista “intressanta” kolumner som ofta finns i results-tabeller
  $tables = array();
  foreach ($rows as $r) $tables[$r['TABLE_NAME']] = true;

  $out = array();
  foreach (array_keys($tables) as $table) {
    $sql2 = "
      SELECT COLUMN_NAME
      FROM INFORMATION_SCHEMA.COLUMNS
      WHERE TABLE_SCHEMA = DATABASE()
        AND TABLE_NAME = :t
        AND (
          COLUMN_NAME IN ('id','token','user_token','session_token','payload','data','json','result','results','created_at','updated_at')
          OR COLUMN_NAME LIKE '%payload%'
          OR COLUMN_NAME LIKE '%json%'
          OR COLUMN_NAME LIKE '%data%'
          OR COLUMN_NAME LIKE '%created%'
        )
      ORDER BY ORDINAL_POSITION
    ";
    $st = $pdo->prepare($sql2);
    $st->execute([':t'=>$table]);
    $cols = $st->fetchAll(PDO::FETCH_COLUMN);

    $out[] = array(
      'table' => $table,
      'token_columns_found' => array_values(array_unique(array_map(function($x){ return $x['COLUMN_NAME']; },
        array_filter($rows, function($x) use ($table){ return $x['TABLE_NAME'] === $table; })
      ))),
      'interesting_columns' => $cols
    );
  }

  respond(['ok'=>true,'candidates'=>$out], 200);
} catch (Exception $e) {
  respond(['ok'=>false,'error'=>'Query failed'], 500);
}
