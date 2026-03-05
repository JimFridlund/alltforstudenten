<?php
/**
 * DB-backed store (Step 12)
 * Scenarios are stored per household (shared).
 * created_by_user_id is recorded for auditing.
 */
class DatabaseScenarioStore implements ScenarioStoreInterface {
  private $pdo;
  private $householdId;
  private $createdByUserId;

  public function __construct(PDO $pdo, $householdId, $createdByUserId){
    $this->pdo = $pdo;
    $this->householdId = (int)$householdId;
    $this->createdByUserId = (int)$createdByUserId;
  }

  public function load(){
    $defaults = Scenario::defaults();
    $sql = "SELECT payload_json FROM viddra_scenarios WHERE household_id = ? ORDER BY updated_at DESC LIMIT 1";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$this->householdId]);
    $row = $stmt->fetch();
    if (!$row) return $defaults;

    $payload = json_decode($row['payload_json'], true);
    if (!is_array($payload)) return $defaults;

    return Scenario::fromArray($payload, $defaults);
  }

  public function save(array $plan){
    $plan = Scenario::clampPlan($plan);
    $json = json_encode($plan, JSON_UNESCAPED_UNICODE);

    $sql = "INSERT INTO viddra_scenarios (household_id, created_by_user_id, payload_json, created_at, updated_at)
            VALUES (?, ?, ?, NOW(), NOW())";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$this->householdId, $this->createdByUserId, $json]);
  }

  public function clear(){
    $sql = "DELETE FROM viddra_scenarios WHERE household_id = ?";
    $stmt = $this->pdo->prepare($sql);
    $stmt->execute([$this->householdId]);
  }
}
?>
