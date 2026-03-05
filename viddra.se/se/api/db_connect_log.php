<?php
declare(strict_types=1);

// Skriv ALLT till fil även om sidan dör
ini_set('display_errors', '0');
ini_set('log_errors', '1');
ini_set('error_log', __DIR__ . '/_php_error.log');
error_reporting(E_ALL);

register_shutdown_function(function () {
  $e = error_get_last();
  if ($e) {
    file_put_contents(__DIR__ . '/_php_error.log',
      "SHUTDOWN ERROR: " . print_r($e, true) . "\n",
      FILE_APPEND
    );
  }
});

file_put_contents(__DIR__ . '/_php_error.log', "\n--- DB CONNECT TEST " . gmdate('c') . " ---\n", FILE_APPEND);

require __DIR__ . "/config.php";

file_put_contents(__DIR__ . '/_php_error.log', "DSN={$DB_DSN}\nUSER={$DB_USER}\nPASSLEN=" . strlen($DB_PASS) . "\n", FILE_APPEND);

try {
  $pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, [
    PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
    PDO::ATTR_TIMEOUT => 3, // så den inte hänger länge
  ]);

  file_put_contents(__DIR__ . '/_php_error.log', "CONNECT: OK\n", FILE_APPEND);

  $row = $pdo->query("SELECT DATABASE() AS db")->fetch(PDO::FETCH_ASSOC);
  file_put_contents(__DIR__ . '/_php_error.log', "CURRENT_DB=" . ($row['db'] ?? '(null)') . "\n", FILE_APPEND);

  $tables = $pdo->query("SHOW TABLES")->fetchAll(PDO::FETCH_NUM);
  file_put_contents(__DIR__ . '/_php_error.log', "TABLES=" . count($tables) . "\n", FILE_APPEND);

  header("Content-Type: text/plain; charset=utf-8");
  echo "OK\n";

} catch (Throwable $e) {
  file_put_contents(__DIR__ . '/_php_error.log', "CONNECT: FAIL\n" . $e->getMessage() . "\n", FILE_APPEND);
  http_response_code(500);
  header("Content-Type: text/plain; charset=utf-8");
  echo "FAIL\n";
}
