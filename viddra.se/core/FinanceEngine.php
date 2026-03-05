<?php
// core/FinanceEngine.php
// Safe-to-spend v1 engine (UK)
// - Uses household_financials as plan (income/fixed/goal)
// - Uses transactions as reality (spent so far)
// - Auto-detects whether expenses are stored as negative or positive amounts
// - Payday cycle based on payday_day (1..28)

class FinanceEngine
{
  /** @var PDO */
  private $pdo;

  public function __construct(PDO $pdo)
  {
    $this->pdo = $pdo;
  }

  public function getDashboardMetrics(int $householdId): array
  {
    $fin = $this->getHouseholdFinancials($householdId);

    $paydayDay = (int)($fin['payday_day'] ?? 28);
    if ($paydayDay < 1) $paydayDay = 1;
    if ($paydayDay > 28) $paydayDay = 28;

    [$cycleStart, $cycleEnd, $daysToPayday, $daysElapsed] = $this->getPayCycleDates($paydayDay);

    $income = (float)$fin['income_total'];
    $fixed  = (float)$fin['fixed_total'];
    $goalMonthly = (float)$fin['goal_monthly'];

    // Plan for this pay-cycle (approx monthly model)
    $disposablePlanned = $income - $fixed - $goalMonthly;
    if ($disposablePlanned < 0) $disposablePlanned = 0;

    $spentSoFar = $this->getSpentSoFar($householdId, $cycleStart, $this->today());

    $remaining = $disposablePlanned - $spentSoFar;

    // Safe to spend
    $safePerDay = ($daysToPayday > 0) ? ($remaining / $daysToPayday) : 0;
    $safeThisWeek = $safePerDay * min(7, max(0, $daysToPayday));

    // Pace + runway
    $pacePerDay = ($daysElapsed > 0) ? ($spentSoFar / $daysElapsed) : 0;
    $runwayDays = ($pacePerDay > 0) ? ($remaining / $pacePerDay) : 999;

    $status = 'safe';
    $warning = '';
    if ($runwayDays < $daysToPayday) {
      $status = 'risk';
      $warning = "At your current pace, you won't make it to payday.";
    } elseif ($safePerDay < 0) {
      $status = 'risk';
      $warning = "You're over plan for this cycle.";
    }

    // Buffer (optional)
    $bufferCurrent = (float)($fin['buffer_current'] ?? 0);
    $bufferProtected = (float)($fin['buffer_protected'] ?? 0);

    $bufferRiskDays = null;
    if ($bufferCurrent > 0 && $pacePerDay > 0 && $remaining < 0) {
      // if already overspent, how many days until buffer eaten at current overspend pace?
      $overspendPerDay = abs($safePerDay); // negative safe/day means overspend pace vs remaining time
      if ($overspendPerDay > 0) {
        $bufferRiskDays = (int)floor($bufferCurrent / $overspendPerDay);
      }
    }

    return [
      'cycle_start' => $cycleStart,
      'cycle_end' => $cycleEnd,
      'days_to_payday' => $daysToPayday,
      'days_elapsed' => $daysElapsed,

      'income_total' => $income,
      'fixed_total' => $fixed,
      'goal_monthly' => $goalMonthly,
      'planned_disposable' => $disposablePlanned,

      'spent_so_far' => $spentSoFar,
      'remaining' => $remaining,

      'safe_per_day' => $safePerDay,
      'safe_this_week' => $safeThisWeek,

      'pace_per_day' => $pacePerDay,
      'runway_days' => $runwayDays,

      'status' => $status,
      'warning' => $warning,

      'buffer_current' => $bufferCurrent,
      'buffer_protected' => $bufferProtected,
      'buffer_risk_days' => $bufferRiskDays,
    ];
  }

  private function getHouseholdFinancials(int $householdId): array
  {
    $sql = "SELECT
              household_id,
              payday_day,
              income_a, income_b,
              fixed_rent, fixed_utilities, fixed_insurance, fixed_subscriptions,
              goal_key, goal_target, goal_monthly,
              buffer_current, buffer_protected
            FROM viddra_household_financials
            WHERE household_id = :hid
            LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([':hid' => $householdId]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);

    if (!$row) {
      // If no row exists yet, return defaults so dashboard doesn't 500
      $row = [
        'household_id' => $householdId,
        'payday_day' => 28,
        'income_a' => 0, 'income_b' => 0,
        'fixed_rent' => 0, 'fixed_utilities' => 0, 'fixed_insurance' => 0, 'fixed_subscriptions' => 0,
        'goal_key' => 'emergency', 'goal_target' => 0, 'goal_monthly' => 0,
        'buffer_current' => 0, 'buffer_protected' => 0,
      ];
    }

    $incomeTotal = (float)$row['income_a'] + (float)$row['income_b'];
    $fixedTotal  = (float)$row['fixed_rent'] + (float)$row['fixed_utilities'] + (float)$row['fixed_insurance'] + (float)$row['fixed_subscriptions'];

    $row['income_total'] = $incomeTotal;
    $row['fixed_total'] = $fixedTotal;

    return $row;
  }

  private function getSpentSoFar(int $householdId, string $fromDate, string $toDate): float
  {
    // We auto-detect sign:
    // - If there are negative amounts in the period, we treat negatives as spend (abs(sum(negatives))).
    // - Otherwise, we treat positive amounts as spend (sum(positives)).
    $sql = "SELECT
              SUM(CASE WHEN amount < 0 THEN amount ELSE 0 END) AS neg_sum,
              SUM(CASE WHEN amount > 0 THEN amount ELSE 0 END) AS pos_sum,
              SUM(CASE WHEN amount < 0 THEN 1 ELSE 0 END) AS neg_cnt
            FROM viddra_transactions
            WHERE household_id = :hid
              AND tx_date >= :d1
              AND tx_date <= :d2";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([
      ':hid' => $householdId,
      ':d1' => $fromDate,
      ':d2' => $toDate
    ]);
    $r = $stmt->fetch(PDO::FETCH_ASSOC);

    $negCnt = (int)($r['neg_cnt'] ?? 0);
    $negSum = (float)($r['neg_sum'] ?? 0); // negative number
    $posSum = (float)($r['pos_sum'] ?? 0);

    if ($negCnt > 0) {
      return abs($negSum);
    }
    return $posSum;
  }

  private function getPayCycleDates(int $paydayDay): array
  {
    // Cycle: last payday -> next payday
    $today = new DateTimeImmutable('today');

    $year = (int)$today->format('Y');
    $month = (int)$today->format('m');
    $day = (int)$today->format('d');

    // Determine next payday
    $nextPayday = new DateTimeImmutable(sprintf('%04d-%02d-%02d', $year, $month, $paydayDay));
    if ($day > $paydayDay) {
      $nextPayday = $nextPayday->modify('first day of next month')->setDate(
        (int)$nextPayday->modify('first day of next month')->format('Y'),
        (int)$nextPayday->modify('first day of next month')->format('m'),
        $paydayDay
      );
    }

    // Last payday is one month before next payday
    $lastPayday = $nextPayday->modify('-1 month');

    $cycleStart = $lastPayday->format('Y-m-d');
    $cycleEnd   = $nextPayday->format('Y-m-d');

    $daysToPayday = (int)$today->diff($nextPayday)->format('%a');
    if ($daysToPayday < 0) $daysToPayday = 0;

    $daysElapsed = (int)$lastPayday->diff($today)->format('%a');
    if ($daysElapsed < 0) $daysElapsed = 0;
    if ($daysElapsed === 0) $daysElapsed = 1; // avoid divide by 0

    return [$cycleStart, $cycleEnd, $daysToPayday, $daysElapsed];
  }

  private function today(): string
  {
    return (new DateTimeImmutable('today'))->format('Y-m-d');
  }
}