<?php
// api/session.php (PHP 5-kompatibel)
require __DIR__ . '/db.php';

function viddra_get_cookie_token() {
  return isset($_COOKIE['viddra_token']) ? $_COOKIE['viddra_token'] : null;
}

function viddra_generate_token() {
  // PHP 5 fallback
  if (function_exists('random_bytes')) {
    return bin2hex(random_bytes(32));
  }
  if (function_exists('openssl_random_pseudo_bytes')) {
    return bin2hex(openssl_random_pseudo_bytes(32));
  }
  // sista utväg (inte kryptostarkt men bättre än inget på gammal PHP)
  return sha1(uniqid(mt_rand(), true)) . sha1(uniqid(mt_rand(), true));
}

function ensureSession($pdo) {
  $token = viddra_get_cookie_token();

  if ($token) {
    $stmt = $pdo->prepare("SELECT id, token FROM viddra_sessions WHERE token=? LIMIT 1");
    $stmt->execute(array($token));
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    if ($row) {
      return array('session_id' => (int)$row['id'], 'token' => (string)$row['token']);
    }
  }

  // Skapa ny session
  $token = viddra_generate_token();
  $stmt = $pdo->prepare("INSERT INTO viddra_sessions (token) VALUES (?)");
  $stmt->execute(array($token));
  $sid = (int)$pdo->lastInsertId();

  // PHP 5-kompatibel setcookie (ingen options-array)
  $expires = time() + 60*60*24*180;
  $path = '/';
  $domain = ''; // tomt = current host
  $secure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
  $httponly = true;

  setcookie('viddra_token', $token, $expires, $path, $domain, $secure, $httponly);

  return array('session_id' => $sid, 'token' => $token);
}

function readJsonBody() {
  $raw = file_get_contents('php://input');
  if ($raw === false) $raw = '';
  $data = json_decode($raw, true);
  return is_array($data) ? $data : array();
}
