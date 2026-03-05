<?php
class Category {

  public static function all($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT * FROM viddra_categories WHERE household_id=? ORDER BY sort_order ASC, id ASC");
    $stmt->execute([(int)$householdId]);
    return $stmt->fetchAll();
  }

  public static function create($householdId, $name, $type){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT INTO viddra_categories (household_id, name, type, sort_order) VALUES (?,?,?,0)");
    $stmt->execute([(int)$householdId, trim($name), $type]);
  }

  public static function delete($householdId, $id){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("DELETE FROM viddra_categories WHERE id=? AND household_id=?");
    $stmt->execute([(int)$id, (int)$householdId]);
  }

  public static function seedDefaults($householdId){
    $pdo = Database::pdo();
    $check = $pdo->prepare("SELECT COUNT(*) FROM viddra_categories WHERE household_id=?");
    $check->execute([(int)$householdId]);
    if ($check->fetchColumn() > 0) return;

    $defaults = [
      ['Rent','fixed'],
      ['Utilities','fixed'],
      ['Groceries','variable'],
      ['Transport','variable'],
      ['Saving','saving']
    ];
    foreach ($defaults as $d){
      self::create($householdId, $d[0], $d[1]);
    }
  }
}
?>
