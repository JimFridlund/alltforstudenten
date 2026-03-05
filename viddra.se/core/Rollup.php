<?php
class Rollup {

  public static function monthRange($ym){
    // ym: YYYY-MM
    $y = (int)substr($ym, 0, 4);
    $m = (int)substr($ym, 5, 2);
    if ($y < 2000 || $y > 2100 || $m < 1 || $m > 12) {
      $y = (int)date('Y'); $m = (int)date('m');
      $ym = sprintf('%04d-%02d', $y, $m);
    }
    $start = sprintf('%04d-%02d-01', $y, $m);
    $end = date('Y-m-d', strtotime($start . ' +1 month'));
    return [$ym, $start, $end];
  }

  public static function byCategory($householdId, $ym){
    list($ym, $start, $end) = self::monthRange($ym);

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT
        c.id AS category_id,
        c.name AS category_name,
        c.type AS category_type,
        COALESCE(SUM(t.amount), 0) AS total_amount,
        COUNT(t.id) AS tx_count
      FROM viddra_categories c
      LEFT JOIN viddra_transactions t
        ON t.category_id = c.id
       AND t.household_id = c.household_id
       AND t.tx_date >= ? AND t.tx_date < ?
      WHERE c.household_id = ?
      GROUP BY c.id, c.name, c.type
      ORDER BY
        FIELD(c.type,'fixed','variable','saving'),
        c.sort_order ASC, c.id ASC
    ");
    $stmt->execute([$start, $end, (int)$householdId]);
    return $stmt->fetchAll();
  }

  public static function totals($rows){
    $income = 0.0;
    $spend_fixed = 0.0;
    $spend_variable = 0.0;
    $saving = 0.0;

    foreach ($rows as $r){
      $amt = (float)$r['total_amount'];
      // Convention: negative = spend, positive = income/refunds
      if ($r['category_type'] === 'saving'){
        // saving usually negative (money set aside). We'll treat negative as saving.
        $saving += (-1 * min(0, $amt));
      } elseif ($r['category_type'] === 'fixed'){
        $spend_fixed += (-1 * min(0, $amt));
        $income += max(0, $amt);
      } else {
        $spend_variable += (-1 * min(0, $amt));
        $income += max(0, $amt);
      }
    }

    $spend_total = $spend_fixed + $spend_variable;
    return [
      'income' => $income,
      'spend_fixed' => $spend_fixed,
      'spend_variable' => $spend_variable,
      'spend_total' => $spend_total,
      'saving' => $saving,
    ];
  }
}
?>