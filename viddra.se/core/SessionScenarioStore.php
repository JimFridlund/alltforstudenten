<?php
/**
 * Session store (fallback)
 */
class SessionScenarioStore implements ScenarioStoreInterface {

  public function load(){
    $defaults = Scenario::defaults();
    if (isset($_SESSION[VIDDRA_SCENARIO_KEY]) && is_array($_SESSION[VIDDRA_SCENARIO_KEY])) {
      return Scenario::fromArray($_SESSION[VIDDRA_SCENARIO_KEY], $defaults);
    }
    return $defaults;
  }

  public function save(array $plan){
    $_SESSION[VIDDRA_SCENARIO_KEY] = Scenario::clampPlan($plan);
  }

  public function clear(){
    unset($_SESSION[VIDDRA_SCENARIO_KEY]);
  }
}
?>
