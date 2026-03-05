<?php
header("Content-Type: text/plain; charset=utf-8");

$path = __DIR__ . "/config.php";
echo "config exists: " . (file_exists($path) ? "YES" : "NO") . "\n";

if (file_exists($path)) {
  require $path;
  echo "DB_DSN set: " . (isset($DB_DSN) ? "YES" : "NO") . "\n";
  echo "DB_USER set: " . (isset($DB_USER) ? "YES" : "NO") . "\n";
  echo "DB_PASS set: " . (isset($DB_PASS) ? "YES" : "NO") . "\n";
}
