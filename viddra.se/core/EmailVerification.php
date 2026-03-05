<?php
class EmailVerification {

  public static function isVerified($userRow){
    return isset($userRow['email_verified_at']) && $userRow['email_verified_at'];
  }

  public static function canResend($userId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT email_verify_sent_at FROM viddra_users WHERE id=? LIMIT 1");
    $stmt->execute([(int)$userId]);
    $row = $stmt->fetch();
    if (!$row) return true;
    if (!$row['email_verify_sent_at']) return true;

    $cooldown = defined('VIDDRA_EMAIL_VERIFY_COOLDOWN_SECONDS') ? (int)VIDDRA_EMAIL_VERIFY_COOLDOWN_SECONDS : 120;
    $sent = strtotime($row['email_verify_sent_at']);
    if (!$sent) return true;
    return (time() - $sent) >= $cooldown;
  }

  public static function sendToUser($userId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT id,email,email_verified_at,email_verify_sent_at FROM viddra_users WHERE id=? LIMIT 1");
    $stmt->execute([(int)$userId]);
    $u = $stmt->fetch();
    if (!$u) return [false, "User not found."];

    if ($u['email_verified_at']) return [true, null];

    if (!self::canResend((int)$u['id'])) {
      return [false, "Please wait a moment before resending."];
    }

    $token = bin2hex(random_bytes(20));
    $hash = hash('sha256', $token);

    $stmt = $pdo->prepare("UPDATE viddra_users SET email_verify_token_hash=?, email_verify_sent_at=NOW() WHERE id=?");
    $stmt->execute([$hash, (int)$u['id']]);

    $base = defined('VIDDRA_BASE_URL') ? rtrim(VIDDRA_BASE_URL, '/') : '';
    $link = $base . "/app/verify_email_confirm.php?t=" . urlencode($token);

    $subject = "Verify your email for Viddra";
    $html = "
      <div style='font-family:Arial,Helvetica,sans-serif;line-height:1.5'>
        <h2 style='margin:0 0 8px 0'>Verify your email</h2>
        <p>Click the button below to confirm your email address.</p>
        <p style='margin:16px 0'>
          <a href='{$link}' style='display:inline-block;padding:10px 14px;border-radius:10px;background:#3f5a3c;color:#fff;text-decoration:none;font-weight:bold'>
            Verify email
          </a>
        </p>
        <p style='color:#666;font-size:13px'>If the button doesn’t work, copy this link:</p>
        <p style='font-size:13px;word-break:break-all'>{$link}</p>
      </div>
    ";

    Mailer::send($u['email'], $subject, $html);
    return [true, null];
  }

  public static function confirmByToken($token){
    $token = trim((string)$token);
    if ($token === '') return [false, "Missing token."];

    $hash = hash('sha256', $token);
    $pdo = Database::pdo();

    $stmt = $pdo->prepare("SELECT id,email,email_verified_at FROM viddra_users WHERE email_verify_token_hash=? LIMIT 1");
    $stmt->execute([$hash]);
    $u = $stmt->fetch();
    if (!$u) return [false, "Invalid or expired link."];

    if (!$u['email_verified_at']) {
      $stmt = $pdo->prepare("UPDATE viddra_users SET email_verified_at=NOW(), email_verify_token_hash=NULL WHERE id=?");
      $stmt->execute([(int)$u['id']]);
    }

    return [true, null, $u];
  }
}
?>
