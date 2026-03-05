<?php
// app/household.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();
Billing::requireActive();

$page_title = "Household — Viddra";
$uid = Auth::userId();
$hid = Household::currentId();
Invite::markExpired();

$error = null;
$success = null;
$invite_link = null;

if (isset($_GET['err']) && $_GET['err'] === 'not_member') $error = "You are not a member of that household.";
if (isset($_GET['err']) && $_GET['err'] === 'csrf') $error = "Session expired. Please try again.";
if (isset($_GET['ok']) && $_GET['ok'] === 'switch') $success = "Household switched.";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  $action = $_POST['action'] ?? '';
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } elseif ($action === 'invite') {
    $email = $_POST['email'] ?? '';
    [$ok, $msg, $token] = Invite::create($hid, $uid, $email, true);
    if ($ok) {
      $success = "Invite created and email sent (if mail() is configured).";
      $invite_link = "/app/accept_invite.php?t=" . urlencode($token);
    } else {
      $error = $msg;
    }
  } elseif ($action === 'rename') {
    $name = $_POST['household_name'] ?? '';
    [$ok, $msg] = Household::rename($hid, $name);
    if ($ok) $success = "Household renamed.";
    else $error = $msg;
  } elseif ($action === 'cancel_invite') {
    $id = (int)($_POST['invite_id'] ?? 0);
    Invite::cancel($id, $hid);
    $success = "Invite cancelled.";
  } elseif ($action === 'resend_invite') {
    $id = (int)($_POST['invite_id'] ?? 0);
    [$ok, $msg, $token] = Invite::resend($id, $hid);
    if ($ok) {
      $success = "Invite re-sent (new token created).";
      $invite_link = "/app/accept_invite.php?t=" . urlencode($token);
    } else {
      $error = $msg;
    }
  }
}

$households = Household::userHouseholds($uid);
$members = Household::members($hid);
$invites = Invite::listForHousehold($hid);

include __DIR__ . '/../includes/header.php';
?>

<style>
  /* Page polish (self-contained) */
  .page-top{ display:flex; align-items:flex-end; justify-content:space-between; gap:16px; margin-bottom:14px; }
  .lead{ margin:6px 0 0 0; color:rgba(44,43,39,.72); font-weight:650; }
  .kpi-chip{ display:inline-flex; flex-direction:column; gap:2px; padding:10px 12px; border-radius:18px; border:1px solid rgba(44,43,39,.12); background: rgba(255,255,255,.45); min-width: 120px; text-align:right; }
  .kpi-chip-label{ font-weight:800; color: rgba(44,43,39,.60); font-size: 12px; letter-spacing:.02em; text-transform: uppercase; }
  .kpi-chip-value{ font-weight:900; font-family: "Fraunces", Georgia, serif; font-size: 22px; color: rgba(44,43,39,.92); }

  .notice{ margin:12px 0; padding:12px 14px; border-radius:16px; border:1px solid rgba(44,43,39,.14); background:rgba(255,255,255,.55); font-weight:750; }
  .notice--warn{ border-color: rgba(160,80,40,.25); background: rgba(255,240,230,.55); }
  .notice--ok{ border-color: rgba(63,90,60,.22); background: rgba(235,245,238,.55); }

  .two-col{ display:grid; grid-template-columns: repeat(2, minmax(0, 1fr)); gap:14px; }
  @media (max-width: 980px){ .two-col{ grid-template-columns: 1fr; } }

  .mini-callout{
    margin-top: 12px;
    padding: 12px 14px;
    border-radius: 18px;
    border: 1px solid rgba(44,43,39,.12);
    background: rgba(255,255,255,.40);
  }
  .mini-callout code{
    display:block;
    padding: 10px 12px;
    border-radius: 14px;
    border: 1px solid rgba(44,43,39,.10);
    background: rgba(255,255,255,.55);
    overflow:auto;
    margin-top: 8px;
  }

  .list{
    margin-top: 10px;
    border-radius: 18px;
    border: 1px solid rgba(44,43,39,.10);
    background: rgba(255,255,255,.34);
    overflow:hidden;
  }
  .row{
    display:flex;
    align-items:flex-start;
    justify-content: space-between;
    gap: 12px;
    padding: 12px 14px;
    border-bottom: 1px solid rgba(44,43,39,.08);
  }
  .row:last-child{ border-bottom: 0; }
  .row strong{ font-weight:900; }
  .sub{ color: rgba(44,43,39,.62); font-weight:700; font-size: 12px; line-height: 1.35; margin-top: 4px; }
  .hint{ color: rgba(44,43,39,.62); font-weight:650; margin-top: 8px; line-height: 1.45; }

  .inline-actions{ display:flex; gap:10px; flex-wrap:wrap; margin: 10px 0 0 0; }
</style>

<section class="section">
  <div class="container">

    <div class="page-top">
      <div>
        <h1>Household</h1>
        <p class="lead">Switch household, invite members, and manage shared scenarios.</p>
      </div>
      <div class="kpi-chip">
        <div class="kpi-chip-label">Current</div>
        <div class="kpi-chip-value">#<?php echo (int)$hid; ?></div>
      </div>
    </div>

    <?php if ($error): ?>
      <div class="notice notice--warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>
    <?php if ($success): ?>
      <div class="notice notice--ok"><?php echo htmlspecialchars($success, ENT_QUOTES, 'UTF-8'); ?></div>
    <?php endif; ?>

    <?php if ($invite_link): ?>
      <div class="mini-callout">
        <strong>Fallback link (manual)</strong>
        <div class="sub">Use this if email sending isn’t configured yet.</div>
        <code><?php echo htmlspecialchars($invite_link, ENT_QUOTES, 'UTF-8'); ?></code>
      </div>
    <?php endif; ?>

    <div class="two-col" style="margin-top:14px;">
      <div class="card">
        <h2>Switch household</h2>
        <p class="small help">If you belong to multiple households, pick the active one.</p>

        <form method="post" action="/app/switch_household.php">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
          <label for="household_id">Your households</label>
          <select id="household_id" name="household_id">
            <?php foreach ($households as $h): ?>
              <option value="<?php echo (int)$h['id']; ?>" <?php echo ((int)$h['id'] === (int)$hid) ? 'selected' : ''; ?>>
                #<?php echo (int)$h['id']; ?> — <?php echo htmlspecialchars($h['name'], ENT_QUOTES, 'UTF-8'); ?> (<?php echo htmlspecialchars($h['role'], ENT_QUOTES, 'UTF-8'); ?>)
              </option>
            <?php endforeach; ?>
          </select>
          <div class="hint">Forecast + Decisions are shared within the active household.</div>

          <div class="form-actions">
            <button class="btn btn--primary" type="submit">Switch</button>
          </div>
        </form>

        <div style="height:14px;"></div>

        <h3 style="margin-bottom:6px;">Rename household</h3>
        <form method="post" action="/app/household.php">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="rename">

          <label for="household_name">Name</label>
          <input id="household_name" name="household_name" type="text" placeholder="e.g. Jim & Sverker" />

          <div class="form-actions">
            <button class="btn btn--ghost" type="submit">Save name</button>
          </div>
        </form>
      </div>

      <div class="card">
        <h2>Members</h2>

        <div class="list">
          <?php foreach ($members as $m): ?>
            <div class="row">
              <div>
                <div style="font-weight:800;">
                  <?php echo htmlspecialchars($m['email'] ?? ('User #' . $m['id']), ENT_QUOTES, 'UTF-8'); ?>
                </div>
                <div class="sub"><?php echo htmlspecialchars($m['created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
              </div>
              <div style="text-align:right;">
                <strong><?php echo htmlspecialchars($m['role'], ENT_QUOTES, 'UTF-8'); ?></strong>
              </div>
            </div>
          <?php endforeach; ?>
        </div>

        <div class="mini-callout">
          <strong>Sharing</strong>
          <div class="sub">Everyone in this household sees the same Decisions + Forecast.</div>
        </div>
      </div>
    </div>

    <div class="two-col" style="margin-top:14px;">
      <div class="card">
        <h2>Create invite</h2>

        <form method="post" action="/app/household.php">
          <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
          <input type="hidden" name="action" value="invite">

          <label for="email">Invite by email</label>
          <input id="email" name="email" type="email" required placeholder="partner@example.com" />
          <div class="hint">We send an email automatically (uses PHP mail()).</div>

          <div class="form-actions">
            <button class="btn btn--primary" type="submit">Send invite</button>
          </div>
        </form>
      </div>

      <div class="card">
        <h2>Invites</h2>

        <?php if (!$invites): ?>
          <p class="small help">No invites yet.</p>
        <?php else: ?>
          <div class="list">
            <?php foreach ($invites as $inv): ?>
              <div class="row">
                <div>
                  <div style="font-weight:800;"><?php echo htmlspecialchars($inv['email'], ENT_QUOTES, 'UTF-8'); ?></div>
                  <div class="sub"><?php echo htmlspecialchars($inv['created_at'], ENT_QUOTES, 'UTF-8'); ?></div>
                </div>
                <div style="text-align:right;">
                  <strong><?php echo htmlspecialchars($inv['status'], ENT_QUOTES, 'UTF-8'); ?></strong>
                </div>
              </div>

              <?php if (($inv['status'] ?? '') === 'pending'): ?>
                <div class="row" style="border-bottom:0; padding-top: 0;">
                  <form method="post" action="/app/household.php" class="inline-actions">
                    <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
                    <input type="hidden" name="invite_id" value="<?php echo (int)$inv['id']; ?>">
                    <button class="btn btn--ghost" type="submit" name="action" value="resend_invite">Resend</button>
                    <button class="btn btn--ghost" type="submit" name="action" value="cancel_invite">Cancel</button>
                  </form>
                </div>
              <?php endif; ?>

            <?php endforeach; ?>
          </div>
        <?php endif; ?>

        <div class="mini-callout">
          <strong>Note</strong>
          <div class="sub">Resending creates a new token (we store only hashes).</div>
        </div>
      </div>
    </div>

  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>