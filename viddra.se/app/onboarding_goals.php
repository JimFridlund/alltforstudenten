<?php
// app/onboarding_goals.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$page_title = "Goals — Onboarding — Viddra";

$hid = Household::currentId();
$fin = Financials::get($hid);

$error = null;

function _vnum($x){
  $x = preg_replace('/[^0-9.]/','', (string)$x);
  if ($x === '') return '';
  return $x;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $goal_key = trim((string)($_POST['goal_key'] ?? ''));
    $target = _vnum($_POST['goal_target'] ?? '');
    $monthly = _vnum($_POST['goal_monthly'] ?? '');

    if ($goal_key === '') $goal_key = 'emergency';

    Financials::updateGoal($hid, $goal_key, $target, $monthly);

    header("Location: /app/onboarding_done.php"); exit;
  }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
/* Matches Step 1–3 visual language (page-only polish) */
.ob-shell{ position: relative; padding: 34px 0 56px; }
.ob-card{
  max-width: 980px;
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
.ob-step{
  font-weight: 900;
  font-size: 12px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(44,43,39,0.55);
}
.ob-title{ margin: 10px 0 0 0; font-size: 44px; line-height: 1.06; }
.ob-lead{
  margin: 10px 0 0 0;
  font-weight: 750;
  color: rgba(44,43,39,0.70);
  max-width: 75ch;
  line-height: 1.55;
}
@media (max-width: 920px){
  .ob-title{ font-size: 38px; }
}

.ob-progress{
  margin-top: 16px;
  height: 10px;
  border-radius: 999px;
  background: rgba(44,43,39,0.10);
  overflow:hidden;
}
.ob-progress > div{
  width: 80%;
  height: 100%;
  border-radius: 999px;
  background: rgba(79,90,65,0.55);
}

.ob-grid{
  margin-top: 18px;
  display:grid;
  grid-template-columns: 1.2fr 0.8fr;
  gap:16px;
}
@media (max-width: 980px){
  .ob-grid{ grid-template-columns: 1fr; }
}

.ob-panel{
  padding: 20px;
  border-radius: 22px;
  background: rgba(255,255,255,0.55);
  border: 1px solid rgba(44,43,39,0.10);
}

.notice-warn{
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid rgba(160,80,50,0.22);
  background: rgba(255,240,232,0.55);
  font-weight: 850;
  color: rgba(44,43,39,0.86);
}

.goal-grid{
  display:grid;
  grid-template-columns: repeat(2, minmax(0,1fr));
  gap:12px;
}
@media (max-width: 640px){
  .goal-grid{ grid-template-columns: 1fr; }
}

.goal-card{
  padding: 14px;
  border-radius: 18px;
  background: rgba(255,255,255,0.45);
  border: 1px solid rgba(44,43,39,0.10);
  cursor:pointer;
  transition: transform .08s ease, background .08s ease, border-color .08s ease;
}
.goal-card:hover{ transform: translateY(-1px); }
.goal-card.selected{
  border-color: rgba(79,90,65,0.28);
  background: rgba(79,90,65,0.10);
}
.goal-top{
  display:flex;
  gap:12px;
  align-items:flex-start;
}
.goal-ico{
  width: 38px;
  height: 38px;
  border-radius: 12px;
  display:flex;
  align-items:center;
  justify-content:center;
  font-size: 18px;
  background: rgba(79,90,65,0.10);
  border: 1px solid rgba(79,90,65,0.18);
}
.goal-title{
  margin: 0;
  font-weight: 950;
}
.goal-sub{
  margin: 6px 0 0 0;
  color: rgba(44,43,39,0.65);
  font-weight: 720;
  line-height: 1.35;
  font-size: 13px;
}

.kpiBox{
  margin-top: 14px;
  padding: 16px;
  border-radius: 20px;
  background: rgba(255,255,255,0.45);
  border: 1px solid rgba(44,43,39,0.10);
}
.kpiLabel{
  font-weight: 900;
  font-size: 12px;
  text-transform: uppercase;
  letter-spacing: .06em;
  color: rgba(44,43,39,0.55);
}
.kpiValue{
  font-family: "Fraunces", Georgia, serif;
  font-size: 40px;
  line-height: 1.05;
  margin-top: 8px;
  color: rgba(44,43,39,0.92);
}
.kpiHint{
  margin-top: 8px;
  font-weight: 720;
  color: rgba(44,43,39,0.62);
  line-height: 1.5;
}

.ring-wrap{
  display:flex;
  gap:14px;
  align-items:center;
  margin-top: 12px;
}
.ring{
  width: 86px;
  height: 86px;
  border-radius: 999px;
  background: conic-gradient(rgba(79,90,65,0.55) 0deg, rgba(44,43,39,0.12) 0deg);
  display:flex;
  align-items:center;
  justify-content:center;
  border: 1px solid rgba(44,43,39,0.12);
}
.ring-inner{
  width: 66px;
  height: 66px;
  border-radius: 999px;
  background: rgba(251,247,239,0.92);
  border: 1px solid rgba(44,43,39,0.10);
  display:flex;
  flex-direction:column;
  align-items:center;
  justify-content:center;
}
.ring-inner strong{
  font-family: "Fraunces", Georgia, serif;
  font-size: 20px;
  line-height: 1;
}
.ring-inner span{
  font-size: 11px;
  font-weight: 850;
  color: rgba(44,43,39,0.60);
  margin-top: 2px;
}

.ob-actions{
  margin-top: 16px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}

.tip{
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid rgba(79,90,65,0.18);
  background: rgba(79,90,65,0.08);
  font-weight: 820;
  color: rgba(44,43,39,0.80);
}
</style>

<section class="section ob-shell">
  <div class="container">

    <div class="ob-card">
      <div class="ob-step">Step 4 of 5</div>
      <h1 class="ob-title">Goals</h1>
      <p class="ob-lead">Start with one goal. This is how we protect the buffer and stop “stealing from future you”.</p>

      <div class="ob-progress" aria-hidden="true"><div></div></div>

      <?php if ($error): ?>
        <div class="notice-warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <form method="post" action="/app/onboarding_goals.php" class="sim-form" style="margin-top:18px" id="obGoalsForm">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">
        <input type="hidden" name="goal_key" id="goal_key" value="<?php echo htmlspecialchars($fin['goal_key'] ?? 'emergency', ENT_QUOTES, 'UTF-8'); ?>">

        <div class="ob-grid">

          <!-- Left: choose goal + inputs -->
          <div class="ob-panel">
            <div class="goal-grid" id="goalGrid">
              <div class="goal-card" data-key="emergency">
                <div class="goal-top">
                  <div class="goal-ico">🛟</div>
                  <div>
                    <p class="goal-title">Emergency fund</p>
                    <p class="goal-sub">3–6 months of safety.</p>
                  </div>
                </div>
              </div>

              <div class="goal-card" data-key="travel">
                <div class="goal-top">
                  <div class="goal-ico">✈️</div>
                  <div>
                    <p class="goal-title">Travel</p>
                    <p class="goal-sub">A trip you’ll remember.</p>
                  </div>
                </div>
              </div>

              <div class="goal-card" data-key="home">
                <div class="goal-top">
                  <div class="goal-ico">🏠</div>
                  <div>
                    <p class="goal-title">Home</p>
                    <p class="goal-sub">Deposit, renovation, buffer.</p>
                  </div>
                </div>
              </div>

              <div class="goal-card" data-key="freedom">
                <div class="goal-top">
                  <div class="goal-ico">🕊️</div>
                  <div>
                    <p class="goal-title">Freedom</p>
                    <p class="goal-sub">Build options over time.</p>
                  </div>
                </div>
              </div>
            </div>

            <div style="height:14px;"></div>

            <label for="goal_target">Goal target</label>
            <input id="goal_target" name="goal_target" inputmode="decimal" placeholder="e.g. 6000"
              value="<?php echo htmlspecialchars($fin['goal_target'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="tiny muted">Total amount you want to reach.</div>

            <div style="height:12px;"></div>

            <label for="goal_monthly">Monthly saving</label>
            <input id="goal_monthly" name="goal_monthly" inputmode="decimal" placeholder="e.g. 250"
              value="<?php echo htmlspecialchars($fin['goal_monthly'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
            <div class="tiny muted">A realistic monthly amount.</div>

            <div class="tip">
              Tip: set a monthly amount you can keep even on a “messy” month.
            </div>

            <div class="ob-actions">
              <button class="btn btn--primary" type="submit">Save & continue</button>
              <a class="btn btn--ghost" href="/app/onboarding_fixed.php">Back</a>
            </div>
          </div>

          <!-- Right: ETA / meaning -->
          <div class="ob-panel">
            <div class="kpiBox">
              <div class="kpiLabel">Estimated completion</div>
              <div class="ring-wrap">
                <div class="ring" id="ring">
                  <div class="ring-inner">
                    <strong id="months">—</strong>
                    <span>months</span>
                  </div>
                </div>
                <div>
                  <div class="tiny muted">ETA</div>
                  <div class="kpiValue" style="font-size:28px; margin-top:6px;" id="eta">—</div>
                  <div class="kpiHint">You can adjust later — this is just to make the trade-off visible.</div>
                </div>
              </div>
            </div>

            <div class="kpiBox" style="margin-top:12px;">
              <div class="kpiLabel">Why this is worth paying for</div>
              <div class="kpiHint" style="margin-top:10px;">
                Once goals exist, Viddra can tell you if a decision steals from future-you — and exactly what you’d need to cut to stay safe.
              </div>
            </div>

          </div>

        </div>
      </form>

    </div>

  </div>
</section>

<script>
(function(){
  const grid = document.getElementById('goalGrid');
  const keyInput = document.getElementById('goal_key');
  const target = document.getElementById('goal_target');
  const monthly = document.getElementById('goal_monthly');
  const monthsEl = document.getElementById('months');
  const etaEl = document.getElementById('eta');
  const ring = document.getElementById('ring');

  function parseVal(x){
    if(!x) return 0;
    x = (''+x).replace(/[^0-9.]/g,'');
    const n = parseFloat(x);
    return isNaN(n) ? 0 : n;
  }

  function setSelected(key){
    keyInput.value = key;
    [...grid.querySelectorAll('.goal-card')].forEach(c=>{
      c.classList.toggle('selected', c.getAttribute('data-key')===key);
    });
    try{ localStorage.setItem('viddra_onb_goal_key', key); }catch(e){}
  }

  function addMonths(date, m){
    const d = new Date(date.getTime());
    const day = d.getDate();
    d.setMonth(d.getMonth()+m);
    if (d.getDate() < day) d.setDate(0);
    return d;
  }

  function fmtMonthYear(d){
    try{
      return d.toLocaleString('en-GB', { month: 'long', year: 'numeric' });
    }catch(e){
      return d.getFullYear() + '-' + (d.getMonth()+1);
    }
  }

  function recalc(){
    const t = parseVal(target.value);
    const m = parseVal(monthly.value);

    let months = 0;
    if (t > 0 && m > 0) months = Math.ceil(t / m);

    if (!months){
      monthsEl.textContent = '—';
      etaEl.textContent = '—';
      ring.style.background = 'conic-gradient(rgba(79,90,65,0.55) 0deg, rgba(44,43,39,0.12) 0deg)';
    } else {
      monthsEl.textContent = String(months);
      const eta = addMonths(new Date(), months);
      etaEl.textContent = fmtMonthYear(eta);

      // visual ring: cap at 24 months for display
      const capped = Math.min(months, 24);
      const pct = Math.max(6, Math.round((24 - capped) / 24 * 100));
      const deg = Math.round(pct * 3.6);
      ring.style.background = `conic-gradient(rgba(79,90,65,0.62) ${deg}deg, rgba(44,43,39,0.12) 0deg)`;
    }

    try{
      localStorage.setItem('viddra_onb_goal_target', target.value||'');
      localStorage.setItem('viddra_onb_goal_monthly', monthly.value||'');
    }catch(e){}
  }

  grid.addEventListener('click', (e)=>{
    const card = e.target.closest('.goal-card');
    if (!card) return;
    setSelected(card.getAttribute('data-key'));
  });

  [target, monthly].forEach(el => el.addEventListener('input', recalc));

  // restore
  try{
    const k = localStorage.getItem('viddra_onb_goal_key');
    if (k) setSelected(k);
    else setSelected(keyInput.value || 'emergency');

    const t = localStorage.getItem('viddra_onb_goal_target');
    const m = localStorage.getItem('viddra_onb_goal_monthly');
    if (t && !target.value) target.value = t;
    if (m && !monthly.value) monthly.value = m;
  }catch(e){
    setSelected(keyInput.value || 'emergency');
  }

  recalc();
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>