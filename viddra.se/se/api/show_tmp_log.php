<?php
header("Content-Type: text/plain; charset=utf-8");
$logFile = sys_get_temp_dir() . '/viddra_probe.log';

if (!file_exists($logFile)) {
  echo "No log file found at: " . $logFile . "\n";
  exit;
}

echo file_get_contents($logFile);
