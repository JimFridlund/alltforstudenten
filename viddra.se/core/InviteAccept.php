<?php
class InviteAccept {

  public static function getInviteByToken($token){
    $token = trim((string)$token);
    if ($token === '') return null;

    $hash = hash('sha256', $token);
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT * FROM viddra_household_invites WHERE token_hash=? LIMIT 1");
    $stmt->execute([$hash]);
    $inv = $stmt->fetch();
    return $inv ?: null;
  }

  public static function accept($token, $userId){
    $inv = self::getInviteByToken($token);
    if (!$inv) return [false, "Invalid invite link."];
    if ($inv['status'] !== 'pending') return [false, "This invite is no longer active."];

    // Expiry check
    if (!empty($inv['expires_at']) && strtotime($inv['expires_at']) < time()) {
      $pdo = Database::pdo();
      $stmt = $pdo->prepare("UPDATE viddra_household_invites SET status='expired', updated_at=NOW() WHERE id=? AND status='pending'");
      $stmt->execute([(int)$inv['id']]);
      return [false, "This invite has expired."];
    }

    if (!empty($inv['revoked_at'])) {
      return [false, "This invite was revoked."];
    }

    // Optional: require verified email
    if (defined('VIDDRA_INVITE_REQUIRES_VERIFIED_EMAIL') && VIDDRA_INVITE_REQUIRES_VERIFIED_EMAIL === true) {
      $pdo = Database::pdo();
      $stmt = $pdo->prepare("SELECT email_verified_at FROM viddra_users WHERE id=? LIMIT 1");
      $stmt->execute([(int)$userId]);
      $u = $stmt->fetch();
      if (!$u || !$u['email_verified_at']) {
        return [false, "Please verify your email before accepting this invite."];
      }
    }

    $householdId = (int)$inv['household_id'];

    $pdo = Database::pdo();
    $pdo->beginTransaction();
    try {
      Household::addMember($householdId, $userId);

      $stmt = $pdo->prepare("UPDATE viddra_household_invites SET status='accepted', accepted_by_user_id=?, accepted_at=NOW() WHERE id=?");
      $stmt->execute([(int)$userId, (int)$inv['id']]);

      // Switch current household
      Household::setCurrentId($householdId);

      $pdo->commit();
      return [true, null];
    } catch (Exception $e){
      $pdo->rollBack();
      throw $e;
    }
  }
}
?>
