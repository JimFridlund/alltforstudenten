<?php
// app/_panic.php (COMPLETE) — temporary debug tool
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

require_once __DIR__ . '/../includes/bootstrap.php';

// Catch fatal errors too
register_shutdown_function(function(){
  $e = error_get_last();
  if ($e && in_array($e['type'], [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR])) {
    header('Content-Type: text/plain; charset=utf-8');
    echo "FATAL:\n";
    echo $e['message'] . "\n";
    echo "File: " . $e['file'] . "\n";
    echo "Line: " . $e['line'] . "\n";
  }
});

// Only allow specific pages to avoid abuse
$map = [
  'household'    => __DIR__ . '/household.php',
  'forecast'     => __DIR__ . '/forecast.php',
  'budget'       => __DIR__ . '/budget.php',
  'transactions' => __DIR__ . '/transactions.php',
  'decisions'    => __DIR__ . '/decisions.php',
  'structure'    => __DIR__ . '/structure.php',
  'profile'      => __DIR__ . '/profile.php',
  'billing'      => __DIR__ . '/billing.php',
];

$p = $_GET['p'] ?? '';
if (!isset($map[$p])) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "OK. Use:\n";
  foreach ($map as $k => $v) echo "  /app/_panic.php?p={$k}\n";
  exit;
}

Auth::requireLogin();

try {
  include $map[$p];
  echo "\n\nOK: included {$p}";
} catch (Throwable $e) {
  header('Content-Type: text/plain; charset=utf-8');
  echo "THROWABLE:\n" . $e;
}