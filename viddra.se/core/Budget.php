<?php
class Budget {

  public static function getMap($householdId, $ym){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT category_id, budget_amount FROM viddra_budgets WHERE household_id=? AND ym=?");
    $stmt->execute([(int)$householdId, $ym]);
    $map = [];
    foreach ($stmt->fetchAll() as $r){
      $map[(int)$r['category_id']] = (float)$r['budget_amount'];
    }
    return $map;
  }

  public static function set($householdId, $ym, $categoryId, $amount){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT INTO viddra_budgets (household_id, ym, category_id, budget_amount)
                           VALUES (?,?,?,?)
                           ON DUPLICATE KEY UPDATE budget_amount=VALUES(budget_amount)");
    $stmt->execute([(int)$householdId, $ym, (int)$categoryId, self::n($amount)]);
  }

  public static function n($x){
    $x = preg_replace('/[^0-9.\-]/','', (string)$x);
    if ($x === '' || $x === '-') return 0.0;
    return (float)$x;
  }
}
?>