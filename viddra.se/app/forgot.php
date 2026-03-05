<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Forgot password — Viddra";
$message = null;
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $email = $_POST['email'] ?? '';
    PasswordReset::request($email);
    $message = "If an account exists for that email, we’ve sent a reset link.";
  }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Forgot password</h1>
      <p class="lead">We’ll email you a reset link.</p>

      <?php if ($error): ?>
        <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>
      <?php if ($message): ?>
        <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($message, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>

      <form class="sim-form" method="post" action="/app/forgot.php">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required placeholder="you@example.com" />
        </div>
        <div class="form-actions">
          <button class="btn primary" type="submit">Send reset link</button>
          <a class="btn" href="/app/login.php">Back</a>
        </div>
      </form>

      <p class="tiny muted">Make sure VIDDRA_BASE_URL is set correctly in config.</p>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
