<?php
header("Content-Type: text/plain; charset=utf-8");

echo "Before require\n";
require __DIR__ . "/config.php";
echo "After require\n";
echo "OK\n";
