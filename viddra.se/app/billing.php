<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$page_title = "Billing — Viddra";
$uid = Auth::userId();
$hid = Household::currentId();
$u = Auth::user();

$sub = Billing::latestSubscription($hid);
$mode = defined('VIDDRA_BILLING_MODE') ? VIDDRA_BILLING_MODE : 'manual';
$require = defined('VIDDRA_REQUIRE_SUBSCRIPTION') ? (VIDDRA_REQUIRE_SUBSCRIPTION ? 'true' : 'false') : 'false';

$success = null;
$error = null;

if (isset($_GET['stripe']) && $_GET['stripe'] === 'success') $success = "Checkout completed. Subscription will confirm in a moment.";
if (isset($_GET['stripe']) && $_GET['stripe'] === 'cancel') $error = "Checkout cancelled.";

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $action = $_POST['action'] ?? '';
    try {
      if ($mode === 'manual') {
        if ($action === 'activate') {
          Billing::manualActivate($hid, $uid, 1);
          $success = "Activated (manual mode).";
        } else if ($action === 'cancel') {
          Billing::manualCancel($hid);
          $success = "Cancelled (manual mode).";
        }
      } else if ($mode === 'stripe') {
        if ($action === 'stripe_checkout') {
          $session = Billing::createCheckoutSession($hid, $uid, $u['email'] ?? '');
          header("Location: " . $session['url']); exit;
        }
        if ($action === 'stripe_portal') {
          $portal = Billing::createPortalSession($hid);
          header("Location: " . $portal['url']); exit;
        }
      }
    } catch (Exception $e){
      $error = $e->getMessage();
    }
    $sub = Billing::latestSubscription($hid);
  }
}

$active = $sub && in_array($sub['status'], ['active','trialing'], true);
$customerId = ($mode === 'stripe') ? Billing::stripeCustomerId($hid) : null;

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="page-top">
      <div>
        <h1>Billing</h1>
        <p class="lead">Subscription is shared within your household.</p>
      </div>
      <div class="kpi-chip">
        <div class="kpi-chip-label">Mode</div>
        <div class="kpi-chip-value"><?php echo htmlspecialchars($mode, ENT_QUOTES, 'UTF-8'); ?></div>
      </div>
    </div>

    <?php if ($error): ?>
      <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>
    <?php if ($success): ?>
      <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
    <?php endif; ?>

    <div class="cards two">
      <div class="card big">
        <div style="display:flex;align-items:flex-start;justify-content:space-between;gap:14px">
          <div>
            <h2 style="margin-bottom:6px">Current plan</h2>
            <p class="muted" style="margin-top:0">One plan per household.</p>
          </div>
          <?php if ($sub): ?>
            <div class="sub-badge <?php echo Billing::statusTone($sub['status']); ?>">
              <?php echo Billing::statusLabel($sub['status']); ?>
            </div>
          <?php endif; ?>
        </div>

        <?php if (!$sub): ?>
          <p class="muted">No subscription yet for this household.</p>
        <?php else: ?>
          <div class="signal">
            <div class="signal-row"><span>Plan</span><strong><?php echo htmlspecialchars($sub['plan_name'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Price</span><strong><?php echo Billing::formatGBP($sub['price_gbp_pence']); ?> / <?php echo htmlspecialchars($sub['interval_unit'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Period end</span><strong><?php echo htmlspecialchars($sub['current_period_end'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Cancel at period end</span><strong><?php echo ((int)$sub['cancel_at_period_end'] === 1) ? 'Yes' : 'No'; ?></strong></div>
          </div>
        <?php endif; ?>

        <div class="mini-callout">
          <strong>Gate:</strong> VIDDRA_REQUIRE_SUBSCRIPTION = <?php echo htmlspecialchars($require, ENT_QUOTES, 'UTF-8'); ?>.
          <div class="tiny muted">Switch it on when Stripe webhooks are stable.</div>
        </div>
      </div>

      <div class="card big">
        <h2>Actions</h2>
        <?php if ($mode === 'stripe'): ?>
          <?php if ($active): ?>
            <p class="muted">Manage payment details, invoices, and cancellation in Stripe Portal.</p>
          <?php else: ?>
            <p class="muted">Start subscription with Stripe Checkout.</p>
          <?php endif; ?>

          <form method="post" action="/app/billing.php" style="display:flex;gap:12px;flex-wrap:wrap">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <?php if (!$active): ?>
              <button class="btn primary" type="submit" name="action" value="stripe_checkout">Subscribe (£11/mo)</button>
            <?php endif; ?>
            <?php if ($customerId): ?>
              <button class="btn" type="submit" name="action" value="stripe_portal">Manage billing</button>
            <?php endif; ?>
          </form>

          <?php if (!$customerId): ?>
            <div class="mini-callout" style="margin-top:14px">
              <strong>Portal note:</strong> customer is created when you start checkout the first time.
            </div>
          <?php endif; ?>

        <?php else: ?>
          <p class="muted">Manual activation for testing.</p>
          <form method="post" action="/app/billing.php" style="display:flex;gap:12px;flex-wrap:wrap">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <button class="btn primary" type="submit" name="action" value="activate">Activate Plus (£11/mo)</button>
            <button class="btn" type="submit" name="action" value="cancel">Cancel</button>
          </form>
        <?php endif; ?>
      </div>
    </div>

  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
