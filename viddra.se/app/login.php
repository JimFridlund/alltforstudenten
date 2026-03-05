<?php
// app/login.php (COMPLETE)
ob_start();

require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Sign in — Viddra";
$error = null;

// If already logged in, go straight to dashboard
if (Auth::isLoggedIn()) {
  session_write_close();
  header("Location: /app/dashboard.php");
  exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  // CSRF
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {

    $email = $_POST['email'] ?? '';
    $pass  = $_POST['password'] ?? '';

    try {
      [$ok, $msg] = Auth::login($email, $pass);

      if ($ok) {

        // Ensure household context is set immediately after login
        $uid = (int)Auth::userId();
        $hid = (int)Household::ensureDefaultForUser($uid);
        Household::setCurrentId($hid);

        // If onboarding has not created a financials row yet, send user to onboarding
        $has_financials = false;
        try {
          $pdo = Database::pdo();
          $stmt = $pdo->prepare("SELECT 1 FROM viddra_household_financials WHERE household_id=? LIMIT 1");
          $stmt->execute([$hid]);
          $has_financials = (bool)$stmt->fetch();
        } catch (Throwable $e) {
          // If DB check fails, still allow login and fall back to dashboard
          $has_financials = true;
        }

        session_write_close();

        if (!$has_financials && file_exists(__DIR__ . "/onboarding.php")) {
          header("Location: /app/onboarding.php");
          exit;
        }

        header("Location: /app/dashboard.php");
        exit;

      } else {
        $error = $msg ?: "Login failed.";
      }

    } catch (Throwable $e) {
      $error = "Login error. Please try again.";
    }
  }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="auth-shell">
  <div class="auth-shell__inner">

    <div class="auth-card">
      <div class="auth-card__top">
        <h1 class="auth-title">Sign in</h1>
        <p class="auth-subtitle">Money clarity, not banking.</p>
      </div>

      <?php if ($error): ?>
        <div class="auth-alert" role="alert">
          <?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <form class="auth-form" method="post" action="/app/login.php" autocomplete="on">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <div class="auth-field">
          <label class="auth-label" for="email">Email</label>
          <input class="auth-input" id="email" name="email" type="email" required placeholder="you@example.com" autocomplete="email" />
        </div>

        <div class="auth-field">
          <label class="auth-label" for="password">Password</label>
          <input class="auth-input" id="password" name="password" type="password" required placeholder="Your password" autocomplete="current-password" />
        </div>

        <button class="btn btn--primary btn--full" type="submit">Sign in</button>

        <div class="auth-secondary">
          <a class="btn btn--ghost btn--full" href="/app/register.php">Create account</a>
        </div>

        <div class="auth-foot">
          <a class="auth-link" href="/app/forgot.php">Forgot password?</a>
        </div>
      </form>

      <div class="auth-meta">
        <div class="auth-meta__line">Not a bank. Not a budgeting app. Not a spreadsheet.</div>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>