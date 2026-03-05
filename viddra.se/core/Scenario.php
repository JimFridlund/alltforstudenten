<?php
/**
 * Scenario (demo): holds adjustable category plan values.
 * Later: persists per user/household in DB.
 */
class Scenario {
  public static function defaults(){
    return [
      'groceries' => 520,
      'subscriptions' => 60,
      'fuel' => 240,
      'eating_out' => 160,
    ];
  }

  public static function clampPlan(array $plan){
    $plan['groceries'] = self::clampInt($plan['groceries'] ?? 0, 0, 1200);
    $plan['subscriptions'] = self::clampInt($plan['subscriptions'] ?? 0, 0, 500);
    $plan['fuel'] = self::clampInt($plan['fuel'] ?? 0, 0, 800);
    $plan['eating_out'] = self::clampInt($plan['eating_out'] ?? 0, 0, 800);
    return $plan;
  }

  public static function fromArray(array $src, array $fallback){
    $out = $fallback;
    foreach ($fallback as $k=>$v){
      if (isset($src[$k]) && preg_match('/^-?\d+$/', trim((string)$src[$k]))) {
        $out[$k] = (int)$src[$k];
      }
    }
    return self::clampPlan($out);
  }

  public static function deltaVsDefaults(array $plan){
    $defaults = self::defaults();
    $d = 0;
    foreach ($defaults as $k=>$v){
      $pv = isset($plan[$k]) ? (int)$plan[$k] : $v;
      $d += ($v - $pv); // lowering spend increases margin
    }
    return $d;
  }

  private static function clampInt($v, $min, $max){
    $v = (int)$v;
    return max($min, min($max, $v));
  }
}
?>
