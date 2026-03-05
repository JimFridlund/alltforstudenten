<?php
class Transaction {

  public static function listRecent($householdId, $limit=50){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT t.*, c.name AS category_name, c.type AS category_type
      FROM viddra_transactions t
      LEFT JOIN viddra_categories c ON c.id = t.category_id
      WHERE t.household_id=?
      ORDER BY t.tx_date DESC, t.id DESC
      LIMIT ?
    ");
    $stmt->bindValue(1, (int)$householdId, PDO::PARAM_INT);
    $stmt->bindValue(2, (int)$limit, PDO::PARAM_INT);
    $stmt->execute();
    return $stmt->fetchAll();
  }

  public static function create($householdId, $categoryId, $date, $amount, $note, $userId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT INTO viddra_transactions (household_id, category_id, tx_date, amount, note, created_by_user_id)
                           VALUES (?,?,?,?,?,?)");
    $stmt->execute([(int)$householdId, $categoryId ? (int)$categoryId : null, $date, self::n($amount), $note, $userId ? (int)$userId : null]);
  }

  public static function delete($householdId, $id){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("DELETE FROM viddra_transactions WHERE id=? AND household_id=?");
    $stmt->execute([(int)$id, (int)$householdId]);
  }

  public static function n($x){
    $x = preg_replace('/[^0-9.\-]/','', (string)$x);
    if ($x === '' || $x === '-' ) return 0.0;
    return (float)$x;
  }
}
?>
