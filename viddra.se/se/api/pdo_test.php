<?php
header("Content-Type: text/plain; charset=utf-8");

echo "PDO loaded: " . (class_exists("PDO") ? "YES" : "NO") . "\n";
echo "pdo_mysql loaded: " . (extension_loaded("pdo_mysql") ? "YES" : "NO") . "\n";
