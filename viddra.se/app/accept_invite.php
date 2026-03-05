<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Accept invite — Viddra";
$token = $_GET['t'] ?? ($_POST['t'] ?? '');
$error = null;
$success = null;

$inv = InviteAccept::getInviteByToken($token);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    if (!Auth::isLoggedIn()) {
      // store invite token in session so we can pick it up after login
      $_SESSION['pending_invite_token'] = $token;
      header("Location: /app/login.php"); exit;
    } else {
      [$ok, $msg] = InviteAccept::accept($token, Auth::userId());
      if ($ok) {
        $success = "Invite accepted. Household switched.";
      } else {
        $error = $msg;
      }
    }
  }
}

// If user logs in and has pending invite token, auto accept on this page (GET flow)
if (!$success && !$error && Auth::isLoggedIn() && isset($_SESSION['pending_invite_token']) && $_SESSION['pending_invite_token'] === $token) {
  unset($_SESSION['pending_invite_token']);
  [$ok, $msg] = InviteAccept::accept($token, Auth::userId());
  if ($ok) $success = "Invite accepted. Household switched.";
  else $error = $msg;
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Accept invite</h1>

      <?php if (!$inv): ?>
        <p class="tiny warn">Invalid invite link.</p>
        <div class="form-actions" style="margin-top:16px">
          <a class="btn primary" href="/app/dashboard.php">Go to app</a>
        </div>
      <?php else: ?>
        <p class="lead">You’ve been invited to join a household.</p>

        <div class="signal" style="margin-top:14px">
          <div class="signal-row"><span>Status</span><strong><?php echo htmlspecialchars($inv['status'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div class="signal-row"><span>Invited email</span><strong><?php echo htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8'); ?></strong></div>
          <div class="signal-row"><span>Expires</span><strong><?php echo htmlspecialchars($inv['expires_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
        </div>

        <?php if ($error): ?>
          <p class="tiny warn" style="margin-top:12px"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
          <?php if (strpos($error, 'verify') !== false): ?>
            <div class="mini-callout" style="margin-top:12px">
              <strong>Next step:</strong> verify your email, then come back to this link.
              <div class="form-actions" style="margin-top:10px">
                <a class="btn primary" href="/app/verify_email.php">Verify email</a>
              </div>
            </div>
          <?php endif; ?>
        <?php endif; ?>

        <?php if ($success): ?>
          <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
          <div class="form-actions" style="margin-top:16px">
            <a class="btn primary" href="/app/dashboard.php">Continue</a>
          </div>
        <?php else: ?>
          <form method="post" action="/app/accept_invite.php" style="margin-top:16px">
            <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
            <input type="hidden" name="t" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
            <div class="form-actions">
              <?php if (!Auth::isLoggedIn()): ?>
                <button class="btn primary" type="submit">Sign in to accept</button>
              <?php else: ?>
                <button class="btn primary" type="submit">Accept invite</button>
              <?php endif; ?>
              <a class="btn" href="/app/dashboard.php">Not now</a>
            </div>
          </form>
          <p class="tiny muted" style="margin-top:10px">For security, invites can require verified email.</p>
        <?php endif; ?>

      <?php endif; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
