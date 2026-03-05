<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Email verified — Viddra";
$token = $_GET['t'] ?? '';
$success = null;
$error = null;

if ($token === '') {
  $error = "Missing token.";
} else {
  [$ok, $msg, $u] = EmailVerification::confirmByToken($token);
  if ($ok) $success = "Email verified. You can continue.";
  else $error = $msg;
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Email verification</h1>
      <?php if ($error): ?>
        <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="form-actions" style="margin-top:16px">
          <a class="btn primary" href="/app/verify_email.php">Back</a>
        </div>
      <?php else: ?>
        <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="form-actions" style="margin-top:16px">
          <?php if (Auth::isLoggedIn()): ?>
            <a class="btn primary" href="/app/dashboard.php">Continue</a>
          <?php else: ?>
            <a class="btn primary" href="/app/login.php">Sign in</a>
          <?php endif; ?>
        </div>
      <?php endif; ?>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
