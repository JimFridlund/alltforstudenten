<?php
/**
 * Household (v3 - single membership model)
 * Uses:
 * - viddra_households
 * - viddra_user_households (pivot with role)
 *
 * IMPORTANT: Do not close PHP tag in this file.
 */
class Household {

  public static function currentId(){
    if (isset($_SESSION['viddra_household_id']) && (int)$_SESSION['viddra_household_id'] > 0) {
      return (int)$_SESSION['viddra_household_id'];
    }
    return 0;
  }

  public static function setCurrentId($hid){
    $_SESSION['viddra_household_id'] = (int)$hid;
  }

  /**
   * Ensure the user has at least one household and return the first household_id.
   */
  public static function ensureDefaultForUser($userId){
    $userId = (int)$userId;
    if ($userId <= 0) return 0;

    $pdo = Database::pdo();

    // Already has household?
    $stmt = $pdo->prepare("SELECT household_id FROM viddra_user_households WHERE user_id = ? ORDER BY created_at ASC LIMIT 1");
    $stmt->execute([$userId]);
    $row = $stmt->fetch();
    if ($row && isset($row['household_id'])) {
      return (int)$row['household_id'];
    }

    // Create default household + link as owner
    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("INSERT INTO viddra_households (name, created_at) VALUES (?, NOW())");
      $stmt->execute(["My household"]);
      $hid = (int)$pdo->lastInsertId();

      $stmt = $pdo->prepare("INSERT INTO viddra_user_households (user_id, household_id, role, created_at) VALUES (?, ?, 'owner', NOW())");
      $stmt->execute([$userId, $hid]);

      $pdo->commit();
      return $hid;
    } catch (Exception $e){
      $pdo->rollBack();
      throw $e;
    }
  }

  /**
   * List households a user belongs to.
   */
  public static function userHouseholds($userId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT h.id, h.name, uh.role, uh.created_at
      FROM viddra_user_households uh
      JOIN viddra_households h ON h.id = uh.household_id
      WHERE uh.user_id = ?
      ORDER BY uh.created_at ASC
    ");
    $stmt->execute([(int)$userId]);
    return $stmt->fetchAll();
  }

  /**
   * Check membership.
   */
  public static function userBelongsTo($userId, $householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT 1 FROM viddra_user_households WHERE user_id=? AND household_id=? LIMIT 1");
    $stmt->execute([(int)$userId, (int)$householdId]);
    return (bool)$stmt->fetch();
  }

  /**
   * List members for a household.
   */
  public static function members($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT u.id, u.email, u.email_verified_at, uh.role, uh.created_at
      FROM viddra_user_households uh
      JOIN viddra_users u ON u.id = uh.user_id
      WHERE uh.household_id = ?
      ORDER BY (uh.role='owner') DESC, uh.created_at ASC
    ");
    $stmt->execute([(int)$householdId]);
    return $stmt->fetchAll();
  }

  /**
   * Rename household.
   */
  public static function rename($householdId, $name){
    $name = trim((string)$name);
    if ($name === '') return [false, "Name cannot be empty."];
    if (function_exists('mb_strlen') && mb_strlen($name) > 120) return [false, "Name too long."];
    if (!function_exists('mb_strlen') && strlen($name) > 120) return [false, "Name too long."];

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_households SET name=? WHERE id=?");
    $stmt->execute([$name, (int)$householdId]);
    return [true, null];
  }

  /**
   * Add member (simple)
   * Note: role defaults to 'member'. Use invites to manage properly if you want.
   */
  public static function addMember($householdId, $userId, $role='member'){
    $role = ($role === 'owner') ? 'owner' : 'member';

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT IGNORE INTO viddra_user_households (user_id, household_id, role, created_at)
      VALUES (?, ?, ?, NOW())
    ");
    $stmt->execute([(int)$userId, (int)$householdId, $role]);
  }
}