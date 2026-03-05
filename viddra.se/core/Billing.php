<?php
/**
 * Billing (Stripe-stable)
 * - household-level subscription
 * - Stripe mapping: household can be derived from customer id (reliable)
 * - upsert updates existing rows instead of creating duplicates
 */
class Billing {

  public static function activeSubscription($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT s.*, p.name AS plan_name, p.price_gbp_pence, p.interval_unit
      FROM viddra_subscriptions s
      JOIN viddra_plans p ON p.id = s.plan_id
      WHERE s.household_id = ? AND s.status IN ('active','trialing')
      ORDER BY s.updated_at DESC
      LIMIT 1
    ");
    $stmt->execute([(int)$householdId]);
    return $stmt->fetch();
  }

  public static function latestSubscription($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      SELECT s.*, p.name AS plan_name, p.price_gbp_pence, p.interval_unit
      FROM viddra_subscriptions s
      JOIN viddra_plans p ON p.id = s.plan_id
      WHERE s.household_id = ?
      ORDER BY s.updated_at DESC
      LIMIT 1
    ");
    $stmt->execute([(int)$householdId]);
    return $stmt->fetch();
  }

  public static function formatGBP($pence){
    $pence = (int)$pence;
    $pounds = floor($pence / 100);
    $rest = $pence % 100;
    return "£" . $pounds . "." . str_pad((string)$rest, 2, "0", STR_PAD_LEFT);
  }

  public static function requireActive(){
    if (!defined('VIDDRA_REQUIRE_SUBSCRIPTION') || VIDDRA_REQUIRE_SUBSCRIPTION !== true) return;
    $hid = Household::currentId();
    $sub = self::activeSubscription($hid);
    if (!$sub){
      header("Location: /app/billing.php"); exit;
    }
  }

  // ---- Manual (testing) ----
  public static function manualActivate($householdId, $userId, $planId=1){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT INTO viddra_subscriptions
      (household_id, plan_id, status, current_period_start, current_period_end, cancel_at_period_end, created_by_user_id, created_at, updated_at)
      VALUES (?, ?, 'active', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 0, ?, NOW(), NOW())
    ");
    $stmt->execute([(int)$householdId, (int)$planId, (int)$userId]);
  }

  public static function manualCancel($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_subscriptions SET status='canceled', updated_at=NOW() WHERE household_id=? AND status IN ('active','trialing')");
    $stmt->execute([(int)$householdId]);
  }

  // ---- Stripe mapping ----
  public static function stripeCustomerId($householdId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT stripe_customer_id FROM viddra_stripe_customers WHERE household_id=? LIMIT 1");
    $stmt->execute([(int)$householdId]);
    $row = $stmt->fetch();
    return $row ? $row['stripe_customer_id'] : null;
  }

  public static function householdIdFromStripeCustomer($stripeCustomerId){
    if (!$stripeCustomerId) return 0;
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("SELECT household_id FROM viddra_stripe_customers WHERE stripe_customer_id=? LIMIT 1");
    $stmt->execute([(string)$stripeCustomerId]);
    $row = $stmt->fetch();
    return $row ? (int)$row['household_id'] : 0;
  }

  public static function ensureStripeCustomer($householdId, $email){
    $existing = self::stripeCustomerId($householdId);
    if ($existing) return $existing;

    $data = StripeApi::post("/customers", [
      "email" => (string)$email,
      "metadata[household_id]" => (int)$householdId
    ]);

    $cid = $data['id'];

    $pdo = Database::pdo();
    $stmt = $pdo->prepare("INSERT INTO viddra_stripe_customers (household_id, stripe_customer_id, created_at, updated_at)
                           VALUES (?, ?, NOW(), NOW())
                           ON DUPLICATE KEY UPDATE stripe_customer_id=VALUES(stripe_customer_id), updated_at=NOW()");
    $stmt->execute([(int)$householdId, $cid]);

    return $cid;
  }

  public static function createCheckoutSession($householdId, $userId, $email){
    $price = defined('VIDDRA_STRIPE_PRICE_ID_PLUS_MONTHLY') ? VIDDRA_STRIPE_PRICE_ID_PLUS_MONTHLY : '';
    if ($price === '' || strpos($price, 'CHANGE_ME') !== false) throw new Exception("Stripe price id not configured.");

    $customerId = self::ensureStripeCustomer($householdId, $email);

    $success = defined('VIDDRA_STRIPE_SUCCESS_URL') ? VIDDRA_STRIPE_SUCCESS_URL : '';
    $cancel  = defined('VIDDRA_STRIPE_CANCEL_URL') ? VIDDRA_STRIPE_CANCEL_URL : '';

    $session = StripeApi::post("/checkout/sessions", [
      "mode" => "subscription",
      "customer" => $customerId,
      "line_items[0][price]" => $price,
      "line_items[0][quantity]" => 1,
      "success_url" => $success,
      "cancel_url" => $cancel,
      "client_reference_id" => (string)$householdId,
      "metadata[household_id]" => (int)$householdId,
      "metadata[user_id]" => (int)$userId
    ]);

    // Local placeholder until webhook confirms
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("
      INSERT INTO viddra_subscriptions
      (household_id, plan_id, status, current_period_start, current_period_end, cancel_at_period_end,
       created_by_user_id, created_at, updated_at, stripe_checkout_session_id, stripe_customer_id)
      VALUES (?, 1, 'trialing', NOW(), DATE_ADD(NOW(), INTERVAL 1 MONTH), 0, ?, NOW(), NOW(), ?, ?)
    ");
    $stmt->execute([(int)$householdId, (int)$userId, (string)$session['id'], (string)$customerId]);

    return $session;
  }

  private static function mapStripeStatus($status){
    $map = [
      'trialing' => 'trialing',
      'active' => 'active',
      'past_due' => 'past_due',
      'canceled' => 'canceled',
      'unpaid' => 'past_due',
      'incomplete' => 'inactive',
      'incomplete_expired' => 'inactive'
    ];
    return isset($map[$status]) ? $map[$status] : 'inactive';
  }

  public static function upsertFromStripeSubscription($householdId, $userId, $stripeSub){
    $status = isset($stripeSub['status']) ? $stripeSub['status'] : 'incomplete';
    $localStatus = self::mapStripeStatus($status);

    $periodStart = isset($stripeSub['current_period_start']) ? date('Y-m-d H:i:s', (int)$stripeSub['current_period_start']) : null;
    $periodEnd   = isset($stripeSub['current_period_end']) ? date('Y-m-d H:i:s', (int)$stripeSub['current_period_end']) : null;
    $cancelAtEnd = (isset($stripeSub['cancel_at_period_end']) && $stripeSub['cancel_at_period_end']) ? 1 : 0;

    $stripeSubId = $stripeSub['id'];
    $stripeCustomerId = $stripeSub['customer'] ?? null;

    $pdo = Database::pdo();

    // Update existing by stripe_subscription_id if present
    $stmt = $pdo->prepare("SELECT id FROM viddra_subscriptions WHERE stripe_subscription_id=? LIMIT 1");
    $stmt->execute([(string)$stripeSubId]);
    $existing = $stmt->fetch();

    if ($existing) {
      $stmt = $pdo->prepare("
        UPDATE viddra_subscriptions
        SET status=?, current_period_start=?, current_period_end=?, cancel_at_period_end=?, updated_at=NOW(),
            stripe_customer_id=COALESCE(stripe_customer_id, ?)
        WHERE id=?
      ");
      $stmt->execute([$localStatus, $periodStart, $periodEnd, (int)$cancelAtEnd, $stripeCustomerId, (int)$existing['id']]);
      return;
    }

    // Otherwise insert new
    $stmt = $pdo->prepare("
      INSERT INTO viddra_subscriptions
      (household_id, plan_id, status, current_period_start, current_period_end, cancel_at_period_end,
       created_by_user_id, created_at, updated_at, stripe_subscription_id, stripe_customer_id)
      VALUES (?, 1, ?, ?, ?, ?, ?, NOW(), NOW(), ?, ?)
    ");
    $stmt->execute([(int)$householdId, $localStatus, $periodStart, $periodEnd, (int)$cancelAtEnd, (int)$userId, (string)$stripeSubId, $stripeCustomerId]);
  }

  public static function attachStripeSubToCheckoutSession($checkoutSessionId, $stripeSubId, $stripeCustomerId){
    $pdo = Database::pdo();
    // Prefer update placeholder row created when checkout started
    $stmt = $pdo->prepare("
      UPDATE viddra_subscriptions
      SET stripe_subscription_id=?, stripe_customer_id=COALESCE(stripe_customer_id, ?), updated_at=NOW()
      WHERE stripe_checkout_session_id=? 
      ORDER BY id DESC
      LIMIT 1
    ");
    $stmt->execute([(string)$stripeSubId, $stripeCustomerId, (string)$checkoutSessionId]);
  }

  public static function cancelByStripeSubscriptionId($stripeSubId){
    $pdo = Database::pdo();
    $stmt = $pdo->prepare("UPDATE viddra_subscriptions SET status='canceled', updated_at=NOW() WHERE stripe_subscription_id=?");
    $stmt->execute([(string)$stripeSubId]);
  }

  public static function createPortalSession($householdId){
    $customerId = self::stripeCustomerId($householdId);
    if (!$customerId) throw new Exception("No Stripe customer found for this household yet.");

    $returnUrl = defined('VIDDRA_STRIPE_PORTAL_RETURN_URL') ? VIDDRA_STRIPE_PORTAL_RETURN_URL : '';
    if ($returnUrl === '') $returnUrl = (defined('VIDDRA_BASE_URL') ? rtrim(VIDDRA_BASE_URL,'/') : '') . "/app/billing.php";

    $portal = StripeApi::post("/billing_portal/sessions", [
      "customer" => $customerId,
      "return_url" => $returnUrl
    ]);

    return $portal;
  }

  public static function statusLabel($status){
    $status = (string)$status;
    $map = [
      'active' => 'Active',
      'trialing' => 'Trial',
      'past_due' => 'Past due',
      'canceled' => 'Canceled',
      'inactive' => 'Inactive'
    ];
    return $map[$status] ?? ucfirst($status);
  }

  public static function statusTone($status){
    $status = (string)$status;
    if ($status === 'active') return 'good';
    if ($status === 'trialing') return 'info';
    if ($status === 'past_due') return 'warn';
    if ($status === 'canceled') return 'muted';
    return 'muted';
  }

}
?>
