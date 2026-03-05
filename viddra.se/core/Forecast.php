<?php
class Forecast {

  public static function daysInMonth($ym){
    $y = (int)substr($ym,0,4);
    $m = (int)substr($ym,5,2);
    return cal_days_in_month(CAL_GREGORIAN, $m, $y);
  }

  public static function dayOfMonth($dateYmd){
    return (int)substr($dateYmd, 8, 2);
  }

  public static function projectMonth($householdId, $ym){
    list($ym, $start, $end) = Rollup::monthRange($ym);

    $today = date('Y-m-d');
    $isCurrent = (substr($today,0,7) === $ym);
    $dayNow = $isCurrent ? self::dayOfMonth($today) : self::daysInMonth($ym);
    $daysTotal = self::daysInMonth($ym);
    $daysElapsed = max(1, min($dayNow, $daysTotal));

    // actuals so far
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT
        c.id AS category_id,
        c.name AS category_name,
        c.type AS category_type,
        COALESCE(SUM(t.amount), 0) AS actual_to_date
      FROM viddra_categories c
      LEFT JOIN viddra_transactions t
        ON t.category_id = c.id
       AND t.household_id = c.household_id
       AND t.tx_date >= ? AND t.tx_date < ?
      WHERE c.household_id = ?
      GROUP BY c.id, c.name, c.type
      ORDER BY FIELD(c.type,'fixed','variable','saving'), c.sort_order ASC, c.id ASC
    ");
    $stmt->execute([$start, $today < $end ? $today : $end, (int)$householdId]); // to-date
    $rows = $stmt->fetchAll();

    $budgetMap = Budget::getMap($householdId, $ym);

    $out = [];
    foreach ($rows as $r){
      $cid = (int)$r['category_id'];
      $actual = (float)$r['actual_to_date'];
      $budget = (float)($budgetMap[$cid] ?? 0);

      // projection based on run-rate (linear)
      $proj = $actual;
      if ($isCurrent){
        $proj = ($actual / $daysElapsed) * $daysTotal;
      }

      $out[] = [
        'category_id' => $cid,
        'category_name' => $r['category_name'],
        'category_type' => $r['category_type'],
        'budget' => $budget,
        'actual_to_date' => $actual,
        'projected' => $proj,
        'diff_projected_vs_budget' => ($proj - $budget),
      ];
    }

    // totals
    $totBudget = 0.0;
    $totActual = 0.0;
    $totProj = 0.0;
    foreach ($out as $r){
      $totBudget += (float)$r['budget'];
      $totActual += (float)$r['actual_to_date'];
      $totProj += (float)$r['projected'];
    }

    return [
      'ym' => $ym,
      'days_elapsed' => $daysElapsed,
      'days_total' => $daysTotal,
      'rows' => $out,
      'totals' => [
        'budget' => $totBudget,
        'actual_to_date' => $totActual,
        'projected' => $totProj,
        'diff_projected_vs_budget' => ($totProj - $totBudget),
      ],
    ];
  }
}
?>