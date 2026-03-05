<?php
// app/onboarding.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';

Auth::requireLogin();
if (method_exists('Auth', 'requireVerifiedEmail')) {
  Auth::requireVerifiedEmail();
}

$page_title = "Setup — Onboarding — Viddra";

$uid = (int)Auth::userId();
$hid = (int)Household::currentId();
if ($hid <= 0 && method_exists('Household', 'ensureDefaultForUser')) {
  $hid = (int)Household::ensureDefaultForUser($uid);
  if (method_exists('Household', 'setCurrentId')) Household::setCurrentId($hid);
}

$pdo = null;
try { $pdo = Database::pdo(); } catch (Throwable $e) { $pdo = null; }

// Helpers
function viddra_db_has_column($pdo, $table, $col){
  if (!$pdo) return false;
  try{
    $stmt = $pdo->query("SHOW COLUMNS FROM `$table`");
    $cols = $stmt ? $stmt->fetchAll(PDO::FETCH_ASSOC) : [];
    foreach ($cols as $c){
      if (isset($c['Field']) && strtolower($c['Field']) === strtolower($col)) return true;
    }
  } catch (Throwable $e) {}
  return false;
}
function viddra_meta_set($pdo, $hid, $key, $val){
  if (!$pdo) return;
  try{
    $pdo->exec("CREATE TABLE IF NOT EXISTS viddra_household_meta (
      household_id INT NOT NULL,
      meta_key VARCHAR(64) NOT NULL,
      meta_value TEXT NULL,
      updated_at DATETIME NOT NULL,
      PRIMARY KEY (household_id, meta_key)
    ) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");
    $stmt = $pdo->prepare("INSERT INTO viddra_household_meta (household_id, meta_key, meta_value, updated_at)
      VALUES (?,?,?,NOW())
      ON DUPLICATE KEY UPDATE meta_value=VALUES(meta_value), updated_at=NOW()");
    $stmt->execute([(int)$hid, (string)$key, (string)$val]);
  } catch (Throwable $e) {}
}
function viddra_meta_get($pdo, $hid, $key){
  if (!$pdo) return '';
  try{
    $stmt = $pdo->prepare("SELECT meta_value FROM viddra_household_meta WHERE household_id=? AND meta_key=? LIMIT 1");
    $stmt->execute([(int)$hid, (string)$key]);
    $row = $stmt->fetch(PDO::FETCH_ASSOC);
    return $row ? (string)$row['meta_value'] : '';
  } catch (Throwable $e) {}
  return '';
}

// Prefill
$household_name = '';
try{
  if (method_exists('Household', 'current')) {
    $h = Household::current();
    if (is_array($h) && isset($h['name'])) $household_name = (string)$h['name'];
  }
} catch (Throwable $e) {}

$payday_day  = '';
$payday_date = '';

$fin = null;
try{
  if (class_exists('Financials') && method_exists('Financials', 'get')) {
    $fin = Financials::get($hid);
  }
} catch (Throwable $e) { $fin = null; }

if (is_array($fin)) {
  if (isset($fin['payday_day']))  $payday_day  = (string)$fin['payday_day'];
  if (isset($fin['payday_date'])) $payday_date = (string)$fin['payday_date'];
}
if ($pdo) {
  if ($payday_day === '')  $payday_day  = viddra_meta_get($pdo, $hid, 'payday_day');
  if ($payday_date === '') $payday_date = viddra_meta_get($pdo, $hid, 'payday_date');
}

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {

    $name = trim((string)($_POST['household_name'] ?? ''));
    if ($name === '') $name = 'My household';

    $mode = (string)($_POST['payday_mode'] ?? 'day'); // day | date
    $day_in  = trim((string)($_POST['payday_day'] ?? ''));
    $date_in = trim((string)($_POST['payday_date'] ?? ''));

    $day = (int)preg_replace('/[^0-9]/', '', $day_in);
    if ($day < 1 || $day > 31) $day = 0;

    $date = '';
    if (preg_match('/^\d{4}-\d{2}-\d{2}$/', $date_in)) {
      $date = $date_in;
    }

    // normalize based on mode
    if ($mode === 'date') {
      $day = 0;
    } else {
      $date = '';
    }

    // Rename household
    try{
      if (method_exists('Household', 'rename')) {
        Household::rename($hid, $name);
      }
    } catch (Throwable $e) {}

    // Save payday best-effort
    $saved = false;

    try{
      if (class_exists('Financials') && method_exists('Financials', 'updatePayday')) {
        Financials::updatePayday($hid, ($day ?: null), ($date ?: null));
        $saved = true;
      }
    } catch (Throwable $e) {}

    if (!$saved && $pdo) {
      try{
        $hasDay  = viddra_db_has_column($pdo, 'viddra_household_financials', 'payday_day');
        $hasDate = viddra_db_has_column($pdo, 'viddra_household_financials', 'payday_date');

        if ($hasDay || $hasDate) {
          $sqlParts = [];
          $vals = [];
          if ($hasDay)  { $sqlParts[] = "payday_day=?";  $vals[] = ($day ?: null); }
          if ($hasDate) { $sqlParts[] = "payday_date=?"; $vals[] = ($date ?: null); }
          $vals[] = $hid;
          $sql = "UPDATE viddra_household_financials SET " . implode(',', $sqlParts) . ", updated_at=NOW() WHERE household_id=?";
          $stmt = $pdo->prepare($sql);
          $stmt->execute($vals);
          $saved = true;
        }
      } catch (Throwable $e) {}
    }

    if ($pdo) {
      if ($day)  viddra_meta_set($pdo, $hid, 'payday_day', (string)$day);
      if ($date) viddra_meta_set($pdo, $hid, 'payday_date', (string)$date);
      viddra_meta_set($pdo, $hid, 'household_name', $name);
    }

    header("Location: /app/onboarding_income.php");
    exit;
  }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
/* Page-only polish */
.ob-shell{
  position: relative;
  padding: 34px 0 56px;
}
.ob-card{
  max-width: 920px;
  margin: 0 auto;
  padding: 26px;
  border-radius: 26px;
  background: rgba(251,247,239,0.90);
  border: 1px solid rgba(44,43,39,0.12);
  box-shadow: 0 18px 55px rgba(0,0,0,0.06);
}
.ob-card::before{
  content:"";
  display:block;
  height:4px;
  width:140px;
  border-radius:999px;
  background: rgba(79,90,65,0.32);
  margin-bottom:14px;
}
.ob-top{
  display:flex;
  justify-content:space-between;
  align-items:flex-start;
  gap:14px;
  flex-wrap:wrap;
}
.ob-step{
  font-weight: 900;
  font-size: 12px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(44,43,39,0.55);
}
.ob-title{
  margin: 10px 0 0 0;
  font-size: 44px;
  line-height: 1.06;
}
.ob-lead{
  margin: 10px 0 0 0;
  font-weight: 750;
  color: rgba(44,43,39,0.70);
  max-width: 70ch;
  line-height: 1.55;
}

.ob-progress{
  margin-top: 16px;
  height: 10px;
  border-radius: 999px;
  background: rgba(44,43,39,0.10);
  overflow:hidden;
}
.ob-progress > div{
  width: 20%;
  height: 100%;
  border-radius: 999px;
  background: rgba(79,90,65,0.55);
}

.ob-grid{
  margin-top: 18px;
  display:grid;
  grid-template-columns: 1.1fr 0.9fr;
  gap:16px;
}
@media (max-width: 920px){
  .ob-grid{ grid-template-columns: 1fr; }
  .ob-title{ font-size: 38px; }
}

.ob-panel{
  padding: 20px;
  border-radius: 22px;
  background: rgba(255,255,255,0.55);
  border: 1px solid rgba(44,43,39,0.10);
}

.ob-help{
  font-weight: 800;
  color: rgba(44,43,39,0.78);
  line-height: 1.5;
}
.ob-help small{
  display:block;
  margin-top:8px;
  font-weight: 720;
  color: rgba(44,43,39,0.62);
}

.mode{
  display:flex;
  gap:10px;
  flex-wrap:wrap;
  margin-top: 10px;
}
.pill{
  display:inline-flex;
  align-items:center;
  gap:10px;
  padding: 10px 12px;
  border-radius: 999px;
  border: 1px solid rgba(44,43,39,0.14);
  background: rgba(255,255,255,0.60);
  font-weight: 900;
  cursor:pointer;
  user-select:none;
}
.pill input{ display:none; }
.pill.active{
  border-color: rgba(79,90,65,0.30);
  background: rgba(79,90,65,0.12);
  color: rgba(63,74,52,0.98);
}

.ob-actions{
  margin-top: 16px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.warn{
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid rgba(160,80,50,0.22);
  background: rgba(255,240,232,0.55);
  font-weight: 850;
  color: rgba(44,43,39,0.86);
}
</style>

<section class="section ob-shell">
  <div class="container">

    <div class="ob-card">
      <div class="ob-top">
        <div>
          <div class="ob-step">Step 1 of 5</div>
          <h1 class="ob-title">Set the basics</h1>
          <p class="ob-lead">We’ll set a calm structure in minutes. Payday unlocks runway + pacing — it’s the key to “safe to spend”.</p>
        </div>
      </div>

      <div class="ob-progress" aria-hidden="true"><div></div></div>

      <?php if ($error): ?>
        <div class="warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <form method="post" action="/app/onboarding.php" class="sim-form" style="margin-top:18px" id="obStep1">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <div class="ob-grid">

          <!-- Left: Inputs -->
          <div class="ob-panel">
            <label for="household_name">Household name</label>
            <input id="household_name" name="household_name" placeholder="e.g. Jim & Linnea"
              value="<?php echo htmlspecialchars($household_name, ENT_QUOTES, 'UTF-8'); ?>">

            <div style="height:12px;"></div>

            <div class="ob-help">
              Payday
              <small>Pick the simplest option. You can change it later.</small>
            </div>

            <div class="mode" role="radiogroup" aria-label="Payday mode">
              <label class="pill" id="pill-day">
                <input type="radio" name="payday_mode" value="day" checked>
                Day-of-month (e.g. 25th)
              </label>
              <label class="pill" id="pill-date">
                <input type="radio" name="payday_mode" value="date">
                Next payday date
              </label>
            </div>

            <div style="height:12px;"></div>

            <div id="mode-day">
              <label for="payday_day">Payday day-of-month</label>
              <input id="payday_day" name="payday_day" inputmode="numeric" placeholder="25"
                value="<?php echo htmlspecialchars($payday_day, ENT_QUOTES, 'UTF-8'); ?>">
              <div class="tiny muted">Best for most UK payroll schedules.</div>
            </div>

            <div id="mode-date" style="display:none;">
              <label for="payday_date">Next payday date</label>
              <input id="payday_date" name="payday_date" type="date"
                value="<?php echo htmlspecialchars($payday_date, ENT_QUOTES, 'UTF-8'); ?>">
              <div class="tiny muted">Best if pay varies or you want precision today.</div>
            </div>

            <div class="ob-actions">
              <button class="btn btn--primary" type="submit">Save & continue</button>
              <a class="btn btn--ghost" href="/app/dashboard.php">Skip for now</a>
            </div>
          </div>

          <!-- Right: What you unlock -->
          <div class="ob-panel">
            <div class="ob-help">What you unlock</div>
            <div style="height:10px;"></div>

            <div class="kpi-inline" style="margin:0; padding:14px; border-radius:18px; background: rgba(255,255,255,0.45); border:1px solid rgba(44,43,39,0.10);">
              <div>
                <div class="tiny muted">Dashboard</div>
                <strong>Safe to spend</strong>
              </div>
              <div class="muted">One number you can trust.</div>
            </div>

            <div style="height:10px;"></div>

            <div class="kpi-inline" style="margin:0; padding:14px; border-radius:18px; background: rgba(255,255,255,0.45); border:1px solid rgba(44,43,39,0.10);">
              <div>
                <div class="tiny muted">Pacing</div>
                <strong>Days to payday</strong>
              </div>
              <div class="muted">No more “month left”.</div>
            </div>

            <div style="height:10px;"></div>

            <div class="kpi-inline" style="margin:0; padding:14px; border-radius:18px; background: rgba(255,255,255,0.45); border:1px solid rgba(44,43,39,0.10);">
              <div>
                <div class="tiny muted">Protection</div>
                <strong>Buffer guard</strong>
              </div>
              <div class="muted">Don’t steal from future-you.</div>
            </div>

            <div class="small-note" style="margin-top:12px;">
              You’re not opening a bank account. Viddra is a planning layer: clarity, pacing, and decisions.
            </div>
          </div>

        </div>
      </form>
    </div>

  </div>
</section>

<script>
(function(){
  const pillDay  = document.getElementById('pill-day');
  const pillDate = document.getElementById('pill-date');
  const modeDay  = document.getElementById('mode-day');
  const modeDate = document.getElementById('mode-date');
  const inputDay = document.getElementById('payday_day');
  const inputDate= document.getElementById('payday_date');

  function setMode(mode){
    if(mode === 'date'){
      pillDate.classList.add('active');
      pillDay.classList.remove('active');
      modeDate.style.display = '';
      modeDay.style.display = 'none';
      try{ pillDate.querySelector('input').checked = true; }catch(e){}
      if(inputDate.value){ inputDay.value=''; }
    }else{
      pillDay.classList.add('active');
      pillDate.classList.remove('active');
      modeDay.style.display = '';
      modeDate.style.display = 'none';
      try{ pillDay.querySelector('input').checked = true; }catch(e){}
      if(inputDay.value){ inputDate.value=''; }
    }
  }

  pillDay.addEventListener('click', ()=>setMode('day'));
  pillDate.addEventListener('click', ()=>setMode('date'));

  inputDay.addEventListener('input', ()=>{
    inputDay.value = (inputDay.value||'').replace(/[^0-9]/g,'');
    if(inputDay.value){ inputDate.value=''; setMode('day'); }
    try{ localStorage.setItem('viddra_onb_payday_day', inputDay.value||''); }catch(e){}
  });

  inputDate.addEventListener('change', ()=>{
    if(inputDate.value){ inputDay.value=''; setMode('date'); }
    try{ localStorage.setItem('viddra_onb_payday_date', inputDate.value||''); }catch(e){}
  });

  // Prefill mode based on values
  try{
    const dd = localStorage.getItem('viddra_onb_payday_day');
    const dt = localStorage.getItem('viddra_onb_payday_date');
    if(dd && !inputDay.value) inputDay.value = dd;
    if(dt && !inputDate.value) inputDate.value = dt;
  }catch(e){}

  if(inputDate.value && !inputDay.value) setMode('date');
  else setMode('day');
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>