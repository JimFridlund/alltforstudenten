<?php
/**
 * Invite (v3)
 * - Create household invites
 * - List invites per household
 * - Revoke invites
 * - Accept invite by token
 * - Mark expired invites (cleanup)
 *
 * IMPORTANT: Do not close PHP tag in this file.
 */
class Invite {

  private static function normEmail($email){
    $email = trim((string)$email);
    $email = function_exists('mb_strtolower') ? mb_strtolower($email) : strtolower($email);
    return $email;
  }

  private static function tokenHash($token){
    return hash('sha256', (string)$token);
  }

  private static function ttlHours(){
    return defined('VIDDRA_INVITE_TTL_HOURS') ? (int)VIDDRA_INVITE_TTL_HOURS : 168;
  }

  /**
   * Mark pending invites as expired if expires_at has passed.
   * This is safe and idempotent.
   *
   * NOTE: Requires column expires_at. If your schema uses a different name,
   * tell me and I’ll adapt the file.
   */
  public static function markExpired($householdId = null){
    $pdo = Database::pdo();

    if ($householdId !== null) {
      $stmt = $pdo->prepare("
        UPDATE viddra_invites
        SET revoked_at = COALESCE(revoked_at, NOW())
        WHERE household_id = ?
          AND accepted_at IS NULL
          AND revoked_at IS NULL
          AND expires_at IS NOT NULL
          AND expires_at <= NOW()
      ");
      $stmt->execute([(int)$householdId]);
      return;
    }

    // Global cleanup
    $pdo->exec("
      UPDATE viddra_invites
      SET revoked_at = COALESCE(revoked_at, NOW())
      WHERE accepted_at IS NULL
        AND revoked_at IS NULL
        AND expires_at IS NOT NULL
        AND expires_at <= NOW()
    ");
  }

  /**
   * List invites for a household (pending/accepted/revoked/all).
   */
  public static function listForHousehold($householdId, $status='pending'){
    $pdo = Database::pdo();
    $householdId = (int)$householdId;

    $where = "i.household_id = ?";
    if ($status === 'pending') {
      $where .= " AND i.accepted_at IS NULL AND i.revoked_at IS NULL
                  AND (i.expires_at IS NULL OR i.expires_at > NOW())";
    } elseif ($status === 'accepted') {
      $where .= " AND i.accepted_at IS NOT NULL";
    } elseif ($status === 'revoked') {
      $where .= " AND i.revoked_at IS NOT NULL";
    } elseif ($status === 'all') {
      // no extra filters
    } else {
      $where .= " AND i.accepted_at IS NULL AND i.revoked_at IS NULL
                  AND (i.expires_at IS NULL OR i.expires_at > NOW())";
    }

    $stmt = $pdo->prepare("
      SELECT
        i.id,
        i.household_id,
        i.email,
        i.role,
        i.created_at,
        i.expires_at,
        i.accepted_at,
        i.revoked_at,
        i.created_by_user_id
      FROM viddra_invites i
      WHERE {$where}
      ORDER BY i.created_at DESC
    ");
    $stmt->execute([$householdId]);
    return $stmt->fetchAll();
  }

  /**
   * Create invite for email.
   * Returns [true, ['token'=>..., 'invite_id'=>...]] or [false, 'message']
   */
  public static function create($householdId, $inviterUserId, $email, $role='member'){
    $pdo = Database::pdo();
    $householdId = (int)$householdId;
    $inviterUserId = (int)$inviterUserId;

    $email = self::normEmail($email);
    if ($email === '' || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
      return [false, "Invalid email address."];
    }

    $role = ($role === 'owner') ? 'owner' : 'member';

    if (defined('VIDDRA_INVITE_REQUIRES_VERIFIED_EMAIL') && VIDDRA_INVITE_REQUIRES_VERIFIED_EMAIL) {
      if (class_exists('Auth') && method_exists('Auth', 'emailVerifiedAt')) {
        $v = Auth::emailVerifiedAt($inviterUserId);
        if (!$v) return [false, "Please verify your email before inviting others."];
      }
    }

    // Already member?
    $stmt = $pdo->prepare("
      SELECT 1
      FROM viddra_user_households uh
      JOIN viddra_users u ON u.id = uh.user_id
      WHERE uh.household_id=? AND u.email=? LIMIT 1
    ");
    $stmt->execute([$householdId, $email]);
    if ($stmt->fetch()) {
      return [false, "That person is already in this household."];
    }

    // Revoke existing pending invite to same email
    $stmt = $pdo->prepare("
      UPDATE viddra_invites
      SET revoked_at = NOW(), revoked_by_user_id = ?
      WHERE household_id = ? AND email = ?
        AND accepted_at IS NULL AND revoked_at IS NULL
    ");
    $stmt->execute([$inviterUserId, $householdId, $email]);

    $token = bin2hex(random_bytes(16));
    $tokenHash = self::tokenHash($token);

    $ttl = self::ttlHours();
    $expiresSql = ($ttl > 0) ? "DATE_ADD(NOW(), INTERVAL {$ttl} HOUR)" : "NULL";

    $stmt = $pdo->prepare("
      INSERT INTO viddra_invites
        (household_id, email, role, token_hash, created_by_user_id, created_at, expires_at)
      VALUES
        (?, ?, ?, ?, ?, NOW(), {$expiresSql})
    ");
    $stmt->execute([$householdId, $email, $role, $tokenHash, $inviterUserId]);

    $inviteId = (int)$pdo->lastInsertId();

    return [true, ['token' => $token, 'invite_id' => $inviteId]];
  }

  /**
   * Revoke an invite (pending only).
   */
  public static function revoke($inviteId, $householdId, $byUserId){
    $pdo = Database::pdo();
    $inviteId = (int)$inviteId;
    $householdId = (int)$householdId;
    $byUserId = (int)$byUserId;

    $stmt = $pdo->prepare("
      UPDATE viddra_invites
      SET revoked_at = NOW(), revoked_by_user_id = ?
      WHERE id = ? AND household_id = ?
        AND accepted_at IS NULL AND revoked_at IS NULL
    ");
    $stmt->execute([$byUserId, $inviteId, $householdId]);

    return [true, null];
  }

  /**
   * Accept invite by raw token and add current user to household.
   * Returns [true, household_id] or [false, message]
   */
  public static function acceptByToken($token, $userId){
    $pdo = Database::pdo();
    $userId = (int)$userId;
    if ($userId <= 0) return [false, "Not logged in."];

    $token = trim((string)$token);
    if ($token === '') return [false, "Missing token."];

    $tokenHash = self::tokenHash($token);

    $stmt = $pdo->prepare("
      SELECT id, household_id, email, role
      FROM viddra_invites
      WHERE token_hash = ?
        AND accepted_at IS NULL AND revoked_at IS NULL
        AND (expires_at IS NULL OR expires_at > NOW())
      LIMIT 1
    ");
    $stmt->execute([$tokenHash]);
    $inv = $stmt->fetch();
    if (!$inv) return [false, "Invite is invalid or expired."];

    $householdId = (int)$inv['household_id'];

    // Email match check
    $stmt = $pdo->prepare("SELECT email FROM viddra_users WHERE id=? LIMIT 1");
    $stmt->execute([$userId]);
    $u = $stmt->fetch();
    if ($u) {
      $userEmail = self::normEmail($u['email'] ?? '');
      $invEmail  = self::normEmail($inv['email'] ?? '');
      if ($invEmail !== '' && $userEmail !== '' && $invEmail !== $userEmail) {
        return [false, "This invite was sent to a different email address."];
      }
    }

    $role = ($inv['role'] ?? 'member') === 'owner' ? 'owner' : 'member';
    Household::addMember($householdId, $userId, $role);

    $stmt = $pdo->prepare("
      UPDATE viddra_invites
      SET accepted_at = NOW(), accepted_by_user_id = ?
      WHERE id = ? AND accepted_at IS NULL
    ");
    $stmt->execute([$userId, (int)$inv['id']]);

    return [true, $householdId];
  }
}