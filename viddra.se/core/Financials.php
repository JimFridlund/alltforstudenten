<?php
class Financials {

  private static function ensureRow($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT IGNORE INTO viddra_household_financials (household_id) VALUES (?)");
    $stmt->execute([(int)$householdId]);
  }

  public static function get($householdId){
    self::ensureRow($householdId);
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT * FROM viddra_household_financials WHERE household_id=? LIMIT 1");
    $stmt->execute([(int)$householdId]);
    $row = $stmt->fetch();
    return $row ?: null;
  }

  public static function updateIncome($householdId, $incomeA, $incomeB){
    self::ensureRow($householdId);
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_household_financials SET income_a=?, income_b=? WHERE household_id=?");
    $stmt->execute([self::n($incomeA), self::n($incomeB), (int)$householdId]);
  }

  public static function updateFixed($householdId, $rent, $utilities, $insurance, $subs){
    self::ensureRow($householdId);
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_household_financials SET fixed_rent=?, fixed_utilities=?, fixed_insurance=?, fixed_subscriptions=? WHERE household_id=?");
    $stmt->execute([self::n($rent), self::n($utilities), self::n($insurance), self::n($subs), (int)$householdId]);
  }

  public static function updateGoal($householdId, $goalKey, $goalTarget, $goalMonthly){
    self::ensureRow($householdId);
    $goalKey = trim((string)$goalKey);
    if ($goalKey === '') $goalKey = 'emergency';
    $allow = ['emergency','travel','home','freedom'];
    if (!in_array($goalKey, $allow, true)) $goalKey = 'emergency';

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_household_financials SET goal_key=?, goal_target=?, goal_monthly=? WHERE household_id=?");
    $stmt->execute([$goalKey, self::n($goalTarget), self::n($goalMonthly), (int)$householdId]);
  }

  public static function snapshot($householdId){
    $f = self::get($householdId);
    if (!$f) return null;

    $income = (float)$f['income_a'] + (float)$f['income_b'];
    $fixed = (float)$f['fixed_rent'] + (float)$f['fixed_utilities'] + (float)$f['fixed_insurance'] + (float)$f['fixed_subscriptions'];
    $available = $income - $fixed;
    $free = $available - (float)$f['goal_monthly'];

    return [
      'income' => $income,
      'fixed' => $fixed,
      'available' => $available,
      'goal_monthly' => (float)$f['goal_monthly'],
      'free' => $free,
      'goal_key' => (string)$f['goal_key'],
      'goal_target' => (float)$f['goal_target'],
    ];
  }

  public static function n($x){
    $x = preg_replace('/[^0-9.]/','', (string)$x);
    if ($x === '') return 0.0;
    return (float)$x;
  }
}
?>
