<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Create account — Viddra";
$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';
    [$ok, $msg] = Auth::register($email, $pass);
    if ($ok){
      session_write_close();
      header("Location: /app/onboarding.php"); exit;
    } else {
      $error = $msg;
    }
  }
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Create account</h1>
      <p class="lead">Start with a clean household money system.</p>

      <?php if ($error): ?>
        <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>

      <form class="sim-form" method="post" action="/app/register.php">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" name="email" type="email" required placeholder="you@example.com" />
        </div>
        <div class="field">
          <label for="password">Password</label>
          <input id="password" name="password" type="password" required placeholder="At least 10 characters" />
          <div class="hint">We’ll add password reset + verification later.</div>
        </div>
        <div class="form-actions">
          <button class="btn primary" type="submit">Create account</button>
          <a class="btn" href="/app/login.php">I already have an account</a>
        </div>
      </form>

      <p class="tiny muted">We keep this minimal while we build household + billing.</p>
    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
