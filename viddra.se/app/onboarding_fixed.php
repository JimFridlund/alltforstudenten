<?php
// app/onboarding_fixed.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';

Auth::requireLogin();
Auth::requireVerifiedEmail();

$page_title = "Fixed costs — Onboarding — Viddra";

$hid = Household::currentId();
$fin = Financials::get($hid);

$error = null;

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    $error = "Session expired. Please try again.";
  } else {
    $rent = preg_replace('/[^0-9.]/','', (string)($_POST['rent'] ?? ''));
    $utilities = preg_replace('/[^0-9.]/','', (string)($_POST['utilities'] ?? ''));
    $insurance = preg_replace('/[^0-9.]/','', (string)($_POST['insurance'] ?? ''));
    $subscriptions = preg_replace('/[^0-9.]/','', (string)($_POST['subscriptions'] ?? ''));

    Financials::updateFixed($hid, $rent, $utilities, $insurance, $subscriptions);

    header("Location: /app/onboarding_goals.php"); exit;
  }
}

include __DIR__ . '/../includes/header.php';
?>

<style>
/* Matches Step 1–2 visual language (page-only polish) */
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
  width: 60%;
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
@media (max-width: 920px){
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

.kpiBox{
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

.grid2{
  display:grid;
  grid-template-columns: 1fr 1fr;
  gap:12px;
}
@media (max-width: 640px){
  .grid2{ grid-template-columns: 1fr; }
}

.miniCard{
  margin-top: 12px;
  padding: 14px;
  border-radius: 18px;
  background: rgba(255,255,255,0.45);
  border: 1px solid rgba(44,43,39,0.10);
}
.miniCard strong{ display:block; }
.miniCard .muted{ color: rgba(44,43,39,0.62); font-weight: 720; line-height: 1.45; margin-top: 6px; }

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
      <div class="ob-step">Step 3 of 5</div>
      <h1 class="ob-title">Fixed costs</h1>
      <p class="ob-lead">Add the bills that happen every month. Keep it simple — the goal is clarity, not accounting.</p>

      <div class="ob-progress" aria-hidden="true"><div></div></div>

      <?php if ($error): ?>
        <div class="notice-warn"><?php echo htmlspecialchars($error, ENT_QUOTES, 'UTF-8'); ?></div>
      <?php endif; ?>

      <form method="post" action="/app/onboarding_fixed.php" class="sim-form" style="margin-top:18px" id="obFixedForm">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <div class="ob-grid">

          <!-- Left: inputs -->
          <div class="ob-panel">
            <div class="grid2">
              <div>
                <label for="rent">Rent / Mortgage</label>
                <input id="rent" name="rent" inputmode="decimal" placeholder="e.g. 1150"
                  value="<?php echo htmlspecialchars($fin['fixed_rent'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="tiny muted">Housing cost.</div>
              </div>

              <div>
                <label for="utilities">Utilities</label>
                <input id="utilities" name="utilities" inputmode="decimal" placeholder="e.g. 190"
                  value="<?php echo htmlspecialchars($fin['fixed_utilities'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="tiny muted">Energy, internet, council tax, etc.</div>
              </div>

              <div>
                <label for="insurance">Insurance</label>
                <input id="insurance" name="insurance" inputmode="decimal" placeholder="e.g. 75"
                  value="<?php echo htmlspecialchars($fin['fixed_insurance'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="tiny muted">Home, car, pet, etc.</div>
              </div>

              <div>
                <label for="subscriptions">Subscriptions</label>
                <input id="subscriptions" name="subscriptions" inputmode="decimal" placeholder="e.g. 48"
                  value="<?php echo htmlspecialchars($fin['fixed_subscriptions'] ?? '', ENT_QUOTES, 'UTF-8'); ?>">
                <div class="tiny muted">Streaming, gym, apps, etc.</div>
              </div>
            </div>

            <div class="tip">
              Tip: round up. It’s better to be slightly conservative.
            </div>

            <div class="ob-actions">
              <button class="btn btn--primary" type="submit">Save & continue</button>
              <a class="btn btn--ghost" href="/app/onboarding_income.php">Back</a>
            </div>
          </div>

          <!-- Right: KPI + meaning -->
          <div class="ob-panel">
            <div class="kpiBox" aria-live="polite">
              <div class="kpiLabel">Monthly fixed total</div>
              <div class="kpiValue" id="fixedTotal">£0.00</div>
              <div class="kpiHint">This is the “non-negotiable” part. Next we set buffer + goals.</div>
            </div>

            <div class="miniCard">
              <strong>Why this matters</strong>
              <div class="muted">Safe-to-spend is only credible if fixed costs are real. This is what prevents “mystery money leaks”.</div>
            </div>

            <div class="miniCard">
              <strong>Keep it minimal</strong>
              <div class="muted">If you’re unsure, estimate. You can refine later — Viddra is about calm clarity.</div>
            </div>
          </div>

        </div>
      </form>

    </div>

  </div>
</section>

<script>
(function(){
  const ids = ['rent','utilities','insurance','subscriptions'];
  const els = ids.map(id => document.getElementById(id));
  const total = document.getElementById('fixedTotal');

  function parseVal(x){
    if(!x) return 0;
    x = (''+x).replace(/[^0-9.]/g,'');
    const n = parseFloat(x);
    return isNaN(n) ? 0 : n;
  }
  function fmtGBP(n){
    try{
      return new Intl.NumberFormat('en-GB',{style:'currency',currency:'GBP'}).format(n);
    }catch(e){
      return '£' + (Math.round(n*100)/100).toFixed(2);
    }
  }
  function recalc(){
    let sum = 0;
    els.forEach(el => sum += parseVal(el.value));
    total.textContent = fmtGBP(sum);
    try{
      ids.forEach(id => localStorage.setItem('viddra_onb_fixed_'+id, document.getElementById(id).value || ''));
    }catch(e){}
  }

  els.forEach(el => el.addEventListener('input', recalc));

  try{
    ids.forEach(id => {
      const v = localStorage.getItem('viddra_onb_fixed_'+id);
      const el = document.getElementById(id);
      if(v && el && !el.value) el.value = v;
    });
  }catch(e){}

  recalc();
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>