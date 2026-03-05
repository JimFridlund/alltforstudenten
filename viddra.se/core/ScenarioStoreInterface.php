<?php
interface ScenarioStoreInterface {
  /** @return array<string,int> */
  public function load();
  /** @param array<string,int> $plan */
  public function save(array $plan);
  public function clear();
}
?>
