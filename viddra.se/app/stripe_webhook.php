<?php
require_once __DIR__ . '/../includes/bootstrap.php';

// Stripe webhook endpoint (Step 21: stable household mapping)

$payload = file_get_contents('php://input');
$sig = $_SERVER['HTTP_STRIPE_SIGNATURE'] ?? '';
$secret = defined('VIDDRA_STRIPE_WEBHOOK_SECRET') ? VIDDRA_STRIPE_WEBHOOK_SECRET : '';

[$okSig, $sigMsg] = StripeApi::verifyWebhook($payload, $sig, $secret);
if (!$okSig){
  WebhookLog::write('stripe', null, null, 400, false, $sigMsg, $payload);
  http_response_code(400);
  echo "Webhook error: " . $sigMsg;
  exit;
}

$event = json_decode($payload, true);
if (!is_array($event) || !isset($event['type'])){
  WebhookLog::write('stripe', null, null, 400, true, "Invalid payload", $payload);
  http_response_code(400);
  echo "Invalid payload";
  exit;
}

$eventId = $event['id'] ?? null;
$type = $event['type'];
$data = $event['data']['object'] ?? null;

try {
  WebhookLog::write('stripe', $eventId, $type, 0, true, null, $event);

  if ($type === 'checkout.session.completed') {
    $householdId = 0;
    if (isset($data['metadata']['household_id'])) $householdId = (int)$data['metadata']['household_id'];
    if ($householdId <= 0 && isset($data['client_reference_id'])) $householdId = (int)$data['client_reference_id'];

    $userId = 1;
    if (isset($data['metadata']['user_id'])) $userId = (int)$data['metadata']['user_id'];

    $checkoutSessionId = $data['id'] ?? null;

    if (isset($data['subscription'])) {
      $stripeSubId = $data['subscription'];
      $sub = StripeApi::get("/subscriptions/" . urlencode($stripeSubId));

      // Prefer mapping by customer id if household id missing
      $stripeCustomerId = $sub['customer'] ?? ($data['customer'] ?? null);
      if ($householdId <= 0 && $stripeCustomerId) {
        $householdId = Billing::householdIdFromStripeCustomer($stripeCustomerId);
      }

      if ($checkoutSessionId) {
        Billing::attachStripeSubToCheckoutSession($checkoutSessionId, $stripeSubId, $stripeCustomerId);
      }

      if ($householdId > 0) {
        Billing::upsertFromStripeSubscription($householdId, $userId, $sub);
      }
    }
  }

  if ($type === 'customer.subscription.updated' || $type === 'customer.subscription.created') {
    $stripeSub = $data;
    $stripeCustomerId = $stripeSub['customer'] ?? null;

    $householdId = 0;
    if (isset($stripeSub['metadata']['household_id'])) $householdId = (int)$stripeSub['metadata']['household_id'];
    if ($householdId <= 0 && $stripeCustomerId) {
      $householdId = Billing::householdIdFromStripeCustomer($stripeCustomerId);
    }

    if ($householdId > 0) {
      Billing::upsertFromStripeSubscription($householdId, 1, $stripeSub);
    }
  }

  if ($type === 'customer.subscription.deleted') {
    $stripeSub = $data;
    if (isset($stripeSub['id'])) Billing::cancelByStripeSubscriptionId($stripeSub['id']);
  }

  if ($type === 'invoice.payment_failed') {
    $stripeSubId = $data['subscription'] ?? null;
    if ($stripeSubId) {
      $pdo = Database::pdo();
      $stmt = $pdo->prepare("UPDATE viddra_subscriptions SET status='past_due', updated_at=NOW() WHERE stripe_subscription_id=?");
      $stmt->execute([(string)$stripeSubId]);
    }
  }

  WebhookLog::write('stripe', $eventId, $type, 200, true, null, $event);
  http_response_code(200);
  echo "ok";
} catch (Exception $e){
  WebhookLog::write('stripe', $eventId, $type, 500, true, $e->getMessage(), $event);
  http_response_code(500);
  echo "handler error: " . $e->getMessage();
}
