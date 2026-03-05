<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();

$page_title = "Verify email — Viddra";
$u = Auth::user();

$success = null;
$error = null;

if ($u && ($u['email_verified_at'] ?? null)) {
  $success = "Your email is verified.";
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    [$ok, $msg] = EmailVerification::sendToUser(Auth::userId());
    if ($ok) $success = "Verification email sent. Please check your inbox.";
    else $error = $msg;
    $u = Auth::user();
  }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Verify your email</h1>
      <p class="lead">We use this to protect your household invites and billing.</p>

      <?php if ($error): ?>
        <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>
      <?php if ($success): ?>
        <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>

      <div class="signal" style="margin-top:14px">
        <div class="signal-row"><span>Email</span><strong><?php echo htmlspecialchars($u['email'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
        <div class="signal-row"><span>Status</span><strong><?php echo ($u && ($u['email_verified_at'] ?? null)) ? 'Verified' : 'Not verified'; ?></strong></div>
      </div>

      <?php if (!$u || !($u['email_verified_at'] ?? null)): ?>
        <form class="sim-form" method="post" action="/app/verify_email.php" style="margin-top:16px">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
          <div class="form-actions">
            <button class="btn primary" type="submit">Resend verification email</button>
            <a class="btn" href="/app/dashboard.php">Skip for now</a>
          </div>
        </form>
        <p class="tiny muted" style="margin-top:10px">Tip: check spam/junk. Cooldown applies to resends.</p>
      <?php else: ?>
        <div class="form-actions" style="margin-top:16px">
          <a class="btn primary" href="/app/dashboard.php">Continue</a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
