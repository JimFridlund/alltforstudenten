<?php
header('Content-Type: text/plain; charset=utf-8');

echo "PHP: " . PHP_VERSION . "\n";
echo "SAPI: " . php_sapi_name() . "\n";
echo "File: " . __FILE__ . "\n";
echo "Dir: " . __DIR__ . "\n";

echo "PDO loaded: " . (extension_loaded('PDO') ? "YES" : "NO") . "\n";
echo "pdo_mysql loaded: " . (extension_loaded('pdo_mysql') ? "YES" : "NO") . "\n";

echo "\nTrying config...\n";

$cfg = __DIR__ . '/config.php';
if (!file_exists($cfg)) {
  echo "Config MISSING: $cfg\n";
  exit;
}

require $cfg;

echo "Config OK\n";
echo "DSN: " . (isset($DB_DSN) ? $DB_DSN : "(missing)") . "\n";
echo "USER: " . (isset($DB_USER) ? $DB_USER : "(missing)") . "\n";
echo "PASS set: " . (isset($DB_PASS) && $DB_PASS !== '' ? "YES" : "NO") . "\n";

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC
  ));
  $db = $pdo->query("SELECT DATABASE() AS db")->fetch();
  echo "\nPDO OK\n";
  echo "DB=" . $db['db'] . "\n";

  $rows = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  echo "tables=" . count($rows) . "\n";
  echo "DONE\n";
} catch (Exception $e) {
  echo "\nPDO FAIL\n";
  echo $e->getMessage() . "\n";
}
