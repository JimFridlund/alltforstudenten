<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();
Billing::requireActive();
$page_title = "Profile — Viddra";
$u = Auth::user();
include __DIR__ . '/../includes/header.php';
?>
<section class="section"><div class="container">
  <h1>Profile</h1>
  <p class="lead">Account basics.</p>
  <div class="card big">
    <div class="row"><span>Email</span><strong><?php echo htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
    <div class="row"><span>Household</span><strong>#<?php echo (int)Household::currentId(); ?></strong></div>
    <a class="btn" href="/app/household.php" style="margin-top:14px">Manage household</a>
    <div class="row"><span>Member since</span><strong><?php echo htmlspecialchars($u['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
  </div>
</div></section>

    <?php if (WebhookLog::isAdmin($u['email'] ?? '')): ?>
      <div class="spacer"></div>
      <h3>Admin</h3>
      <a class="btn" href="/app/admin_webhooks.php">Webhook logs</a>
    <?php endif; ?>

<?php include __DIR__ . '/../includes/footer.php'; ?>
