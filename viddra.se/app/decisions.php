<?php
// app/decisions.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Decisions — Viddra";

// ---------- Guarded auth ----------
if (class_exists('Auth')) {
  if (method_exists('Auth', 'requireLogin')) {
    Auth::requireLogin();
  } elseif (method_exists('Auth', 'isLoggedIn') && !Auth::isLoggedIn()) {
    header("Location: /app/login.php");
    exit;
  }
  if (method_exists('Auth', 'requireVerifiedEmail')) {
    Auth::requireVerifiedEmail();
  }
}
if (class_exists('Billing') && method_exists('Billing', 'requireActive')) {
  Billing::requireActive();
}

$uid = (class_exists('Auth') && method_exists('Auth', 'userId')) ? (int)Auth::userId() : 0;
$hid = (class_exists('Household') && method_exists('Household', 'currentId')) ? (int)Household::currentId() : 0;

// ---------- Session store ----------
if (!isset($_SESSION['viddra_decisions']) || !is_array($_SESSION['viddra_decisions'])) {
  $_SESSION['viddra_decisions'] = [];
}
$decisions = &$_SESSION['viddra_decisions'];

// Migrate single decision store if present
if (isset($_SESSION['viddra_decision']) && is_array($_SESSION['viddra_decision'])) {
  // Avoid duplicates by simple compare
  $single = $_SESSION['viddra_decision'];
  $found = false;
  foreach ($decisions as $d) {
    if (($d['label'] ?? '') === ($single['label'] ?? '') &&
        (string)($d['amount'] ?? '') === (string)($single['amount'] ?? '') &&
        ($d['date'] ?? '') === ($single['date'] ?? '')) {
      $found = true;
      break;
    }
  }
  if (!$found) {
    $decisions[] = $single;
  }
}

// ---------- Category list (optional, for later; apply uses null category by default) ----------
try {
  if ($hid > 0 && class_exists('Category') && method_exists('Category', 'seedDefaults')) {
    Category::seedDefaults($hid);
  }
} catch (Throwable $e) {}

// ---------- Forecast/Rollup for state ----------
$forecast = [];
$rollup   = [];

try {
  if ($hid > 0 && class_exists('Forecast') && method_exists('Forecast', 'current')) {
    $tmp = Forecast::current($hid);
    if (is_array($tmp)) $forecast = $tmp;
  }
} catch (Throwable $e) {}

try {
  if ($hid > 0 && class_exists('Rollup') && method_exists('Rollup', 'current')) {
    $tmp = Rollup::current($hid);
    if (is_array($tmp)) $rollup = $tmp;
  }
} catch (Throwable $e) {}

$remaining_base = (float)($rollup['remaining'] ?? 0);
$days_left      = (int)  ($forecast['days_to_payday'] ?? 0);
$safe_day_base  = (float)($forecast['safe_per_day'] ?? 0);
$runway_base    = (int)  ($forecast['runway_days'] ?? 0);

$notice = null;
$notice_type = 'ok'; // ok | warn

// ---------- Helpers ----------
function viddra_norm_amount_to_float($raw){
  $s = (string)$raw;
  $s = str_replace(['£',' '], '', $s);
  $s = str_replace(',', '.', $s);
  $f = (float)$s;
  return $f;
}
function viddra_is_valid_date($s){
  return is_string($s) && preg_match('/^\d{4}-\d{2}-\d{2}$/', $s);
}

// ---------- Actions ----------
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $notice = "Session expired. Please try again.";
    $notice_type = 'warn';
  } else {
    $action = (string)($_POST['action'] ?? '');
    $index  = (int)($_POST['index'] ?? -1);

    if (!isset($decisions[$index]) || !is_array($decisions[$index])) {
      $notice = "That decision no longer exists.";
      $notice_type = 'warn';
    } else {

      if ($action === 'delete') {
        array_splice($decisions, $index, 1);
        $notice = "Decision deleted.";
        $notice_type = 'ok';
      }

      if ($action === 'duplicate') {
        $decisions[] = $decisions[$index];
        $notice = "Decision duplicated.";
        $notice_type = 'ok';
      }

      if ($action === 'open') {
        // Load into single editor session and jump to decision.php
        $_SESSION['viddra_decision'] = $decisions[$index];
        header("Location: /app/decision.php");
        exit;
      }

      if ($action === 'apply') {
        // Create a transaction (future-dated) from the decision
        $d = $decisions[$index];

        $label = trim((string)($d['label'] ?? 'Decision'));
        $notes = trim((string)($d['notes'] ?? ''));
        $date  = (string)($d['date'] ?? date('Y-m-d'));
        $amt   = viddra_norm_amount_to_float($d['amount'] ?? 0);

        if ($amt < 0) $amt = abs($amt); // decision cost
        if (!viddra_is_valid_date($date)) $date = date('Y-m-d');

        // Transaction::create expects negative for spend (based on your transactions page usage)
        $tx_amount = -1 * $amt;

        $note = "[Decision] " . ($label !== '' ? $label : "Planned spend");
        if ($notes !== '') $note .= " — " . $notes;

        $created = false;

        try {
          if ($hid > 0 && class_exists('Transaction') && method_exists('Transaction', 'create')) {
            // category_id: null for now (you can add category picking later)
            Transaction::create($hid, null, $date, $tx_amount, $note, $uid);
            $created = true;
          }
        } catch (Throwable $e) {
          $created = false;
        }

        if ($created) {
          $notice = "Applied: created a transaction for £" . number_format($amt, 2, '.', ',') . " on " . $date . ".";
          $notice_type = 'ok';
        } else {
          $notice = "Could not apply (Transaction::create not available or failed).";
          $notice_type = 'warn';
        }
      }

    }
  }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
.dec-shell{ display:flex; flex-direction:column; gap:16px; }
.dec-head{
  padding: 22px;
  border-radius: 24px;
  background: rgba(251,247,239,0.90);
  border: 1px solid rgba(44,43,39,.12);
  box-shadow: 0 18px 55px rgba(0,0,0,.06);
}
.dec-head::before{
  content:"";
  display:block;
  height:4px;
  width:120px;
  border-radius:999px;
  background: rgba(79,90,65,.32);
  margin-bottom:14px;
}
.dec-sub{ margin: 8px 0 0 0; font-weight: 750; color: rgba(44,43,39,.70); }

.notice{
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid rgba(79,90,65,0.22);
  background: rgba(79,90,65,0.10);
  color: rgba(44,43,39,0.86);
  font-weight: 850;
}
.notice.warn{
  border-color: rgba(160,80,50,0.22);
  background: rgba(255,240,232,0.55);
}

.dec-grid{ display:grid; grid-template-columns: 1fr; gap:14px; }

.dec-card{
  padding: 22px;
  border-radius: 22px;
  background: rgba(255,255,255,.55);
  border: 1px solid rgba(44,43,39,.10);
}

.dec-top{
  display:flex;
  align-items:flex-start;
  justify-content: space-between;
  gap: 12px;
  flex-wrap: wrap;
}
.dec-title{ font-weight: 950; font-size: 18px; margin: 0; }
.dec-meta{ margin-top: 6px; font-size: 13px; color: rgba(44,43,39,.65); font-weight: 720; line-height: 1.4; }
.dec-amount{
  font-family:"Fraunces", Georgia, serif;
  font-size: 28px;
  color: rgba(44,43,39,.92);
  white-space: nowrap;
}

.badge{
  display:inline-flex;
  align-items:center;
  padding: 7px 11px;
  border-radius: 999px;
  font-weight: 950;
  letter-spacing: .05em;
  font-size: 11px;
  text-transform: uppercase;
  border: 1px solid rgba(44,43,39,.12);
  background: rgba(255,255,255,.60);
  color: rgba(44,43,39,.78);
}
.badge.safe{ border-color: rgba(79,90,65,.22); background: rgba(79,90,65,.10); color: rgba(63,74,52,.95); }
.badge.tighten{ border-color: rgba(170,120,60,.22); background: rgba(170,120,60,.10); color: rgba(120,80,40,.95); }
.badge.urgent{ border-color: rgba(160,80,50,.22); background: rgba(160,80,50,.10); color: rgba(120,55,35,.95); }
.badge.setup{ border-color: rgba(44,43,39,.16); background: rgba(44,43,39,.07); color: rgba(44,43,39,.80); }

.dec-actions{ display:flex; gap:10px; flex-wrap:wrap; margin-top: 14px; }
.dec-actions form{ display:inline; }

.small-note{ margin-top: 10px; font-weight: 720; color: rgba(44,43,39,.62); line-height: 1.45; }
</style>

<section class="section">
  <div class="container">
    <div class="dec-shell">

      <div class="dec-head">
        <h1 style="margin:0;">Decisions</h1>
        <p class="dec-sub">Your decision history. Apply a decision to create a future-dated transaction.</p>
        <div style="margin-top:12px; display:flex; gap:10px; flex-wrap:wrap;">
          <a class="btn btn--primary" href="/app/decision.php">New decision</a>
          <a class="btn btn--ghost" href="/app/dashboard.php">Back to dashboard</a>
        </div>
      </div>

      <?php if ($notice): ?>
        <div class="notice <?php echo $notice_type === 'warn' ? 'warn' : ''; ?>">
          <?php echo htmlspecialchars($notice, ENT_QUOTES, 'UTF-8'); ?>
        </div>
      <?php endif; ?>

      <?php if (empty($decisions)): ?>
        <div class="dec-card">
          <p style="margin:0;">No decisions yet. Create one in <a href="/app/decision.php">Decision mode</a>.</p>
        </div>
      <?php else: ?>

        <div class="dec-grid">
          <?php foreach ($decisions as $i => $d): ?>
            <?php
              $label = trim((string)($d['label'] ?? 'Decision'));
              if ($label === '') $label = 'Decision';

              $notes = trim((string)($d['notes'] ?? ''));
              $date  = (string)($d['date'] ?? '');
              $amount = viddra_norm_amount_to_float($d['amount'] ?? 0);
              if ($amount < 0) $amount = abs($amount);

              // Simple state logic (approx)
              $state = 'safe';
              $tag   = 'SAFE';

              if ($days_left <= 0 || $safe_day_base <= 0) {
                $state = 'setup';
                $tag = 'SETUP';
              } else {
                $remaining_after = $remaining_base - $amount;
                if ($remaining_after < 0) {
                  $state = 'urgent'; $tag = 'URGENT';
                } else {
                  $runway_after = $runway_base;
                  if ($safe_day_base > 0) {
                    $runway_after = (int)max(0, round($runway_base - ($amount / max(1, $safe_day_base))));
                  }
                  if ($runway_after > 0 && $runway_after < $days_left) {
                    $state = 'tighten'; $tag = 'TIGHTEN';
                  }
                }
              }

              $meta = "Planned for " . ($date !== '' ? $date : "—");
              if ($notes !== '') $meta .= " · " . $notes;
            ?>

            <div class="dec-card">
              <div class="dec-top">
                <div>
                  <p class="dec-title"><?php echo htmlspecialchars($label, ENT_QUOTES, 'UTF-8'); ?></p>
                  <div class="dec-meta"><?php echo htmlspecialchars($meta, ENT_QUOTES, 'UTF-8'); ?></div>
                </div>

                <div style="text-align:right;">
                  <div class="dec-amount">£<?php echo number_format($amount, 2, '.', ','); ?></div>
                  <div style="margin-top:8px;">
                    <span class="badge <?php echo htmlspecialchars($state, ENT_QUOTES, 'UTF-8'); ?>">
                      <?php echo htmlspecialchars($tag, ENT_QUOTES, 'UTF-8'); ?>
                    </span>
                  </div>
                </div>
              </div>

              <div class="dec-actions">
                <!-- Open -->
                <form method="post">
                  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                  <input type="hidden" name="action" value="open">
                  <button class="btn btn--ghost" type="submit">Open</button>
                </form>

                <!-- Apply -->
                <form method="post">
                  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                  <input type="hidden" name="action" value="apply">
                  <button class="btn btn--primary" type="submit">Apply</button>
                </form>

                <!-- Duplicate -->
                <form method="post">
                  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                  <input type="hidden" name="action" value="duplicate">
                  <button class="btn btn--ghost" type="submit">Duplicate</button>
                </form>

                <!-- Delete -->
                <form method="post">
                  <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                  <input type="hidden" name="index" value="<?php echo (int)$i; ?>">
                  <input type="hidden" name="action" value="delete">
                  <button class="btn btn--ghost" type="submit">Delete</button>
                </form>
              </div>

              <div class="small-note">
                Apply creates a future-dated transaction so it shows up in your cycle without manual typing.
              </div>
            </div>

          <?php endforeach; ?>
        </div>

      <?php endif; ?>

    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>