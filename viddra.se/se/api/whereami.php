<?php
header("Content-Type: text/plain; charset=utf-8");

echo "URL host: " . ($_SERVER['HTTP_HOST'] ?? '-') . "\n";
echo "DOCUMENT_ROOT: " . ($_SERVER['DOCUMENT_ROOT'] ?? '-') . "\n";
echo "__DIR__: " . __DIR__ . "\n";
echo "__FILE__: " . __FILE__ . "\n";
echo "SCRIPT_FILENAME: " . ($_SERVER['SCRIPT_FILENAME'] ?? '-') . "\n\n";

echo "sys_get_temp_dir(): " . sys_get_temp_dir() . "\n";
echo "open_basedir: " . (ini_get('open_basedir') ?: '(none)') . "\n";
echo "error_log: " . (ini_get('error_log') ?: '(default)') . "\n\n";

$testPath = __DIR__ . "/_write_test.txt";
$ok = @file_put_contents($testPath, "write test " . date('c') . "\n", FILE_APPEND);
echo "Write to " . $testPath . ": " . ($ok ? "OK" : "FAIL") . "\n";
