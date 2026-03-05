<?php
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Reset password — Viddra";
$token = $_GET['t'] ?? ($_POST['t'] ?? '');
$error = null;
$success = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $p1 = $_POST['password'] ?? '';
    $p2 = $_POST['password2'] ?? '';
    if ($p1 !== $p2) {
      $error = "Passwords do not match.";
    } else {
      [$ok, $msg] = PasswordReset::setNewPassword($token, $p1);
      if ($ok) {
        $success = "Password updated. You can sign in now.";
      } else {
        $error = $msg;
      }
    }
  }
}

$valid = false;
if (!$success){
  [$ok, $msg, $row] = PasswordReset::validateToken($token);
  $valid = $ok;
  if (!$ok && !$error) $error = $msg;
}

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="card big auth-card">
      <h1>Reset password</h1>
      <p class="lead">Choose a new password.</p>

      <?php if ($error): ?>
        <p class="tiny warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></p>
      <?php endif; ?>
      <?php if ($success): ?>
        <p class="tiny" style="font-weight:900;color:rgba(63,90,60,.95)"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></p>
        <div class="form-actions">
          <a class="btn primary" href="/app/login.php">Sign in</a>
        </div>
      <?php elseif ($valid): ?>
        <form class="sim-form" method="post" action="/app/reset_password.php">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="t" value="<?php echo htmlspecialchars($token, ENT_QUOTES, 'UTF-8'); ?>">
          <div class="field">
            <label for="password">New password</label>
            <input id="password" name="password" type="password" required placeholder="At least 10 characters" />
          </div>
          <div class="field">
            <label for="password2">Repeat password</label>
            <input id="password2" name="password2" type="password" required placeholder="Repeat password" />
          </div>
          <div class="form-actions">
            <button class="btn primary" type="submit">Update password</button>
          </div>
        </form>
      <?php else: ?>
        <div class="form-actions">
          <a class="btn primary" href="/app/forgot.php">Request a new link</a>
        </div>
      <?php endif; ?>

    </div>
  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
