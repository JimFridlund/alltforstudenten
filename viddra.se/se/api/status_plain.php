<?php
ini_set('display_errors', '1');
ini_set('display_startup_errors', '1');
error_reporting(E_ALL);

header("Content-Type: text/plain; charset=utf-8");

echo "S0 start\n";

require __DIR__ . "/config.php";
echo "S1 config ok\n";

echo "S2 about to PDO\n";
$pdo = new PDO($DB_DSN, $DB_USER, $DB_PASS, array(
  PDO::ATTR_ERRMODE => PDO::ERRMODE_EXCEPTION,
  PDO::ATTR_TIMEOUT => 3,
));
echo "S3 PDO ok\n";

$db = $pdo->query("SELECT DATABASE()")->fetchColumn();
echo "S4 DB=" . $db . "\n";

$stmt = $pdo->query("SHOW TABLES");
$tables = $stmt->fetchAll(PDO::FETCH_NUM);
echo "S5 tables=" . count($tables) . "\n";

echo "DONE\n";
