<?php
/**
 * Stripe API helper (no SDK) using cURL.
 * This is intentionally small for shared hosting.
 */
class StripeApi {

  private static function key(){
    return defined('VIDDRA_STRIPE_SECRET_KEY') ? VIDDRA_STRIPE_SECRET_KEY : '';
  }

  public static function post($path, $params){
    $key = self::key();
    if ($key === '' || strpos($key, 'CHANGE_ME') !== false) {
      throw new Exception("Stripe secret key not configured.");
    }

    $url = "https://api.stripe.com/v1" . $path;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query($params));
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Authorization: Bearer " . $key,
      "Content-Type: application/x-www-form-urlencoded"
    ]);

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) throw new Exception("Stripe request failed: " . $err);

    $data = json_decode($resp, true);
    if ($code >= 400) {
      $msg = isset($data['error']['message']) ? $data['error']['message'] : ("HTTP " . $code);
      throw new Exception("Stripe error: " . $msg);
    }
    return $data;
  }

  public static function get($path){
    $key = self::key();
    if ($key === '' || strpos($key, 'CHANGE_ME') !== false) {
      throw new Exception("Stripe secret key not configured.");
    }

    $url = "https://api.stripe.com/v1" . $path;

    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_HTTPHEADER, [
      "Authorization: Bearer " . $key
    ]);

    $resp = curl_exec($ch);
    $code = curl_getinfo($ch, CURLINFO_HTTP_CODE);
    $err = curl_error($ch);
    curl_close($ch);

    if ($resp === false) throw new Exception("Stripe request failed: " . $err);

    $data = json_decode($resp, true);
    if ($code >= 400) {
      $msg = isset($data['error']['message']) ? $data['error']['message'] : ("HTTP " . $code);
      throw new Exception("Stripe error: " . $msg);
    }
    return $data;
  }

  public static function verifyWebhook($payload, $sigHeader, $secret){
    // Minimal Stripe signature verification (v1)
    // Stripe-Signature: t=timestamp,v1=signature,...
    if (!$secret || strpos($secret, 'CHANGE_ME') !== false) return [false, "Webhook secret not configured."];
    if (!$sigHeader) return [false, "Missing Stripe-Signature header."];

    $parts = explode(',', $sigHeader);
    $t = null; $v1 = null;
    foreach ($parts as $p){
      $kv = explode('=', trim($p), 2);
      if (count($kv) !== 2) continue;
      if ($kv[0] === 't') $t = $kv[1];
      if ($kv[0] === 'v1') $v1 = $kv[1];
    }
    if (!$t || !$v1) return [false, "Invalid signature header."];

    // Tolerance 5 minutes
    if (abs(time() - (int)$t) > 300) return [false, "Webhook timestamp outside tolerance."];

    $signed = $t . "." . $payload;
    $expected = hash_hmac('sha256', $signed, $secret);
    if (!hash_equals($expected, $v1)) return [false, "Signature mismatch."];

    return [true, null];
  }
}
?>
