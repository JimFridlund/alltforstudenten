<?php
/**
 * Auth (stable + verified email gate)
 * IMPORTANT: Do not close PHP tag in this file.
 */
class Auth {

  public static function isLoggedIn(){
    return isset($_SESSION['viddra_user_id']) && (int)$_SESSION['viddra_user_id'] > 0;
  }

  public static function userId(){
    return self::isLoggedIn() ? (int)$_SESSION['viddra_user_id'] : 0;
  }

  public static function requireLogin(){
    if (!self::isLoggedIn()){
      header("Location: /app/login.php");
      exit;
    }
  }

  /**
   * Require verified email (only enforced if VIDDRA_REQUIRE_EMAIL_VERIFICATION === true).
   * If not required, this is a no-op.
   */
  public static function requireVerifiedEmail(){
    self::requireLogin();

    if (!defined('VIDDRA_REQUIRE_EMAIL_VERIFICATION') || VIDDRA_REQUIRE_EMAIL_VERIFICATION !== true) {
      return; // not enforced
    }

    $uid = self::userId();
    $verifiedAt = self::emailVerifiedAt($uid);

    if ($verifiedAt) return;

    // If you have a page for it, use it. Otherwise, send to profile with message.
    if (file_exists(__DIR__ . '/../app/verify_email.php')) {
      header("Location: /app/verify_email.php");
      exit;
    }

    header("Location: /app/profile.php?verify=1");
    exit;
  }

  public static function logout(){
    unset($_SESSION['viddra_user_id']);
  }

  /**
   * Login: returns [bool ok, string message|null]
   */
  public static function login($email, $password){
    $email = trim(mb_strtolower((string)$email));
    $password = (string)$password;

    if ($email === '' || $password === ''){
      return [false, "Please enter email and password."];
    }

    $pdo = Database::pdo();

    $stmt = $pdo->prepare("SELECT id, email, password_hash, email_verified_at FROM viddra_users WHERE email=? LIMIT 1");
    $stmt->execute([$email]);
    $user = $stmt->fetch();

    if (!$user){
      return [false, "Incorrect email or password."];
    }

    $uid = (int)$user['id'];

    if (!password_verify($password, (string)$user['password_hash'])){
      return [false, "Incorrect email or password."];
    }

    $_SESSION['viddra_user_id'] = $uid;

    // Optional: kick off verification email (don’t block login if it fails)
    if (class_exists('EmailVerification')) {
      if (defined('VIDDRA_REQUIRE_EMAIL_VERIFICATION') && VIDDRA_REQUIRE_EMAIL_VERIFICATION) {
        try { EmailVerification::sendToUser($uid); } catch (Throwable $e) {}
      }
    }

    return [true, null];
  }

  /**
   * Helpers
   */
  public static function emailVerifiedAt($userId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT email_verified_at FROM viddra_users WHERE id=? LIMIT 1");
    $stmt->execute([(int)$userId]);
    $row = $stmt->fetch();
    if (!$row) return null;
    $v = $row['email_verified_at'] ?? null;
    return $v ? (string)$v : null;
  }

  public static function userEmail($userId = null){
    $uid = $userId === null ? self::userId() : (int)$userId;
    if ($uid <= 0) return null;

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT email FROM viddra_users WHERE id=? LIMIT 1");
    $stmt->execute([$uid]);
    $row = $stmt->fetch();
    return $row ? (string)$row['email'] : null;
  }
}