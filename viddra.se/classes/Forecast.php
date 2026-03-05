<?php

class Forecast {

  public static function current($hid){

    $f = DB::fetch(
      "SELECT * FROM viddra_household_financials
       WHERE household_id=?",
       [$hid]
    );

    if(!$f){
      return [];
    }

    $income = (float)$f['income_monthly'];
    $fixed = (float)$f['fixed_monthly'];
    $goal = (float)$f['goal_monthly'];
    $payday = (int)$f['payday_day'];

    $available = $income - $fixed;
    $free = $available - $goal;

    $today = new DateTime();

    $year = $today->format("Y");
    $month = $today->format("m");

    $nextPayday = new DateTime("$year-$month-$payday");

    if($today > $nextPayday){
      $nextPayday->modify("+1 month");
    }

    $days_to_payday = $today->diff($nextPayday)->days;

    if($days_to_payday <= 0){
      $days_to_payday = 30;
    }

    $spent = DB::fetchValue(
      "SELECT COALESCE(SUM(amount),0)
       FROM viddra_transactions
       WHERE household_id=?
       AND amount < 0
       AND date >= DATE_SUB(CURDATE(), INTERVAL 30 DAY)",
       [$hid]
    );

    $spent = abs((float)$spent);

    $remaining = $free - $spent;

    $safe_per_day = $remaining / $days_to_payday;
    $safe_per_week = $safe_per_day * 7;

    $safe_tomorrow = $remaining / max(1, $days_to_payday - 1);

    if($safe_per_day > 0){
      $runway = floor($remaining / $safe_per_day);
    } else {
      $runway = 0;
    }

    return [

      'safe_per_day' => $safe_per_day,
      'safe_per_week' => $safe_per_week,
      'safe_tomorrow' => $safe_tomorrow,
      'days_to_payday' => $days_to_payday,
      'runway_days' => $runway,
      'remaining' => $remaining,
      'spent' => $spent,
      'free' => $free

    ];

  }

}