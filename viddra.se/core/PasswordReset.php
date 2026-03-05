<?php
/**
 * Password reset (v1)
 * - request reset by email
 * - email link with token
 * - set new password via token
 *
 * Notes:
 * - We store only token hash in DB.
 * - We do not reveal whether an email exists (basic privacy).
 */
class PasswordReset {

  public static function request($email){
    $email = strtolower(trim((string)$email));
    if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
      // still return success-like message to avoid enumeration
      return [true, null];
    }

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT id,email FROM viddra_users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $u = $stmt->fetch();
    if (!$u) {
      return [true, null];
    }

    $token = bin2hex(random_bytes(20));
    $tokenHash = hash('sha256', $token);

    // expire previous pending tokens for this user
    $stmt = $pdo->prepare("UPDATE viddra_password_resets SET status='expired' WHERE user_id=? AND status='pending'");
    $stmt->execute([(int)$u['id']]);

    $stmt = $pdo->prepare("INSERT INTO viddra_password_resets (user_id, token_hash, status, created_at) VALUES (?, ?, 'pending', NOW())");
    $stmt->execute([(int)$u['id'], $tokenHash]);

    self::sendEmail($u['email'], $token);

    return [true, null];
  }

  private static function sendEmail($email, $token){
    $base = defined('VIDDRA_BASE_URL') ? rtrim(VIDDRA_BASE_URL, '/') : '';
    $link = $base . "/app/reset_password.php?t=" . urlencode($token);

    $subject = "Reset your Viddra password";
    $html = "
      <div style='font-family:Arial,Helvetica,sans-serif;line-height:1.5'>
        <h2 style='margin:0 0 8px 0'>Reset your password</h2>
        <p>Click the button below to choose a new password.</p>
        <p style='margin:16px 0'>
          <a href='{$link}' style='display:inline-block;padding:10px 14px;border-radius:10px;background:#3f5a3c;color:#fff;text-decoration:none;font-weight:bold'>
            Set new password
          </a>
        </p>
        <p style='color:#666;font-size:13px'>If the button doesn’t work, copy this link:</p>
        <p style='font-size:13px;word-break:break-all'>{$link}</p>
        <p style='color:#666;font-size:13px;margin-top:16px'>If you didn’t request this, you can ignore this email.</p>
      </div>
    ";
    Mailer::send($email, $subject, $html);
  }

  public static function validateToken($token){
    $token = trim((string)$token);
    if ($token === '') return [false, "Missing token.", null];

    $pdo = Database::pdo();
    $tokenHash = hash('sha256', $token);

    $stmt = $pdo->prepare("SELECT id,user_id,status,created_at FROM viddra_password_resets WHERE token_hash=? LIMIT 1");
    $stmt->execute([$tokenHash]);
    $row = $stmt->fetch();
    if (!$row) return [false, "Invalid or expired link.", null];
    if ($row['status'] !== 'pending') return [false, "This link has already been used or expired.", null];

    // Optional expiry window: 2 hours
    $created = strtotime($row['created_at']);
    if ($created && (time() - $created) > 2*60*60) {
      $stmt = $pdo->prepare("UPDATE viddra_password_resets SET status='expired' WHERE id=?");
      $stmt->execute([(int)$row['id']]);
      return [false, "This link expired. Please request a new one.", null];
    }

    return [true, null, $row];
  }

  public static function setNewPassword($token, $newPassword){
    if (strlen((string)$newPassword) < 10) return [false, "Password must be at least 10 characters."];

    [$ok, $msg, $row] = self::validateToken($token);
    if (!$ok) return [false, $msg];

    $pdo = Database::pdo();
    $hash = password_hash($newPassword, PASSWORD_DEFAULT);

    $pdo->beginTransaction();
    try {
      $stmt = $pdo->prepare("UPDATE viddra_users SET password_hash=? WHERE id=?");
      $stmt->execute([$hash, (int)$row['user_id']]);

      $stmt = $pdo->prepare("UPDATE viddra_password_resets SET status='used', used_at=NOW() WHERE id=?");
      $stmt->execute([(int)$row['id']]);

      $pdo->commit();
      return [true, null];
    } catch (Exception $e){
      $pdo->rollBack();
      throw $e;
    }
  }
}
?>
