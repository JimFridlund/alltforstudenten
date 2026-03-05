<?php
/**
 * Database helper (PDO).
 * Keep it tiny and predictable.
 */
class Database {
  private static $pdo = null;

  public static function pdo(){
    if (self::$pdo !== null) return self::$pdo;

    $host = VIDDRA_DB_HOST;
    $db   = VIDDRA_DB_NAME;
    $user = VIDDRA_DB_USER;
    $pass = VIDDRA_DB_PASS;
    $charset = VIDDRA_DB_CHARSET;

    $dsn = "mysql:host={$host};dbname={$db};charset={$charset}";
    $opt = [
      PDO::ATTR_ERRMODE            => PDO::ERRMODE_EXCEPTION,
      PDO::ATTR_DEFAULT_FETCH_MODE => PDO::FETCH_ASSOC,
      PDO::ATTR_EMULATE_PREPARES   => false,
    ];
    self::$pdo = new PDO($dsn, $user, $pass, $opt);
    return self::$pdo;
  }
}
?>
