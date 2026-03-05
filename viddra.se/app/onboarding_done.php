<?php
// app/onboarding_done.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$page_title = "You’re live — Onboarding — Viddra";

$hid = Household::currentId();
$fin = Financials::get($hid);
$snap = Financials::snapshot($hid);

function _gbp($n){
  return "£" . number_format((float)$n, 2, '.', ',');
}

$income = $snap ? $snap['income'] : 0;
$fixed = $snap ? $snap['fixed'] : 0;
$available = $snap ? $snap['available'] : 0;
$goal_monthly = $snap ? $snap['goal_monthly'] : 0;
$free = $snap ? $snap['free'] : 0;

$goal_key = $fin ? (string)$fin['goal_key'] : 'emergency';
$goal_title = [
  'emergency' => 'Emergency fund',
  'travel' => 'Travel',
  'home' => 'Home',
  'freedom' => 'Freedom'
][$goal_key] ?? 'Goal';

include __DIR__ . '/../includes/header.php';
?>

<style>
/* Matches Step 1–4 visual language (page-only polish) */
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
  max-width: 78ch;
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
  width: 100%;
  height: 100%;
  border-radius: 999px;
  background: rgba(79,90,65,0.55);
}

.ob-grid{
  margin-top: 18px;
  display:grid;
  grid-template-columns: 1.05fr 0.95fr;
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

.kpiBig{
  padding: 18px;
  border-radius: 22px;
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
  font-size: 56px;
  line-height: 1.00;
  margin-top: 10px;
  color: rgba(44,43,39,0.92);
}
@media (max-width: 520px){
  .kpiValue{ font-size: 46px; }
}
.kpiHint{
  margin-top: 10px;
  font-weight: 760;
  color: rgba(44,43,39,0.66);
  line-height: 1.55;
}

.miniGrid{
  margin-top: 14px;
  display:grid;
  grid-template-columns: repeat(3, minmax(0,1fr));
  gap:12px;
}
@media (max-width: 760px){
  .miniGrid{ grid-template-columns: 1fr; }
}

.miniCard{
  padding: 14px;
  border-radius: 18px;
  background: rgba(255,255,255,0.45);
  border: 1px solid rgba(44,43,39,0.10);
}
.miniCard .t{
  font-weight: 900;
  font-size: 12px;
  letter-spacing: .06em;
  text-transform: uppercase;
  color: rgba(44,43,39,0.55);
}
.miniCard .v{
  margin-top: 8px;
  font-family: "Fraunces", Georgia, serif;
  font-size: 26px;
  color: rgba(44,43,39,0.92);
}

.breakdown{
  display:flex;
  flex-direction:column;
  gap:10px;
}
.row{
  display:flex;
  align-items:center;
  justify-content:space-between;
  gap:12px;
  padding: 12px 14px;
  border-radius: 16px;
  background: rgba(255,255,255,0.40);
  border: 1px solid rgba(44,43,39,0.10);
}
.row span{
  color: rgba(44,43,39,0.72);
  font-weight: 800;
}
.row strong{
  font-family: "Fraunces", Georgia, serif;
  font-size: 20px;
}

.divider{
  height: 1px;
  background: rgba(44,43,39,0.12);
  margin: 4px 0;
  border-radius: 999px;
}

.callout{
  margin-top: 12px;
  padding: 12px 14px;
  border-radius: 16px;
  border: 1px solid rgba(79,90,65,0.18);
  background: rgba(79,90,65,0.08);
  font-weight: 820;
  color: rgba(44,43,39,0.82);
  line-height: 1.55;
}

.ob-actions{
  margin-top: 16px;
  display:flex;
  gap:10px;
  flex-wrap:wrap;
}
</style>

<section class="section ob-shell">
  <div class="container">

    <div class="ob-card">
      <div class="ob-step">Step 5 of 5</div>
      <h1 class="ob-title">You’re live</h1>
      <p class="ob-lead">This is your first calm snapshot. The number below is what you can spend without breaking the plan.</p>

      <div class="ob-progress" aria-hidden="true"><div></div></div>

      <div class="ob-grid">

        <!-- Left: Big payoff -->
        <div class="ob-panel">
          <div class="kpiBig">
            <div class="kpiLabel">Real free cash</div>
            <div class="kpiValue"><?php echo _gbp($free); ?></div>
            <div class="kpiHint">Money you can spend without stealing from future-you.</div>
          </div>

          <div class="miniGrid" aria-label="Snapshot summary">
            <div class="miniCard">
              <div class="t">Income</div>
              <div class="v"><?php echo _gbp($income); ?></div>
            </div>
            <div class="miniCard">
              <div class="t">Fixed</div>
              <div class="v"><?php echo _gbp($fixed); ?></div>
            </div>
            <div class="miniCard">
              <div class="t">Goal</div>
              <div class="v"><?php echo _gbp($goal_monthly); ?></div>
            </div>
          </div>

          <div class="callout">
            Next: go to the dashboard to see <strong>safe-to-spend</strong> and pacing in your current cycle.
          </div>

          <div class="ob-actions">
            <a class="btn btn--primary" href="/app/dashboard.php">Go to dashboard</a>
            <a class="btn btn--ghost" href="/app/onboarding_goals.php">Edit setup</a>
          </div>
        </div>

        <!-- Right: Breakdown -->
        <div class="ob-panel">
          <div class="kpiLabel" style="margin-bottom:10px;">How we got there</div>

          <div class="breakdown">
            <div class="row"><span>Monthly income</span><strong><?php echo _gbp($income); ?></strong></div>
            <div class="row"><span>Fixed costs</span><strong><?php echo _gbp($fixed); ?></strong></div>
            <div class="row"><span>Available after fixed</span><strong><?php echo _gbp($available); ?></strong></div>
            <div class="divider"></div>
            <div class="row"><span>Saving toward <?php echo htmlspecialchars($goal_title, ENT_QUOTES, 'UTF-8'); ?></span><strong><?php echo _gbp($goal_monthly); ?></strong></div>
            <div class="row"><span><strong>Real free cash</strong></span><strong><?php echo _gbp($free); ?></strong></div>
          </div>

          <div class="callout" style="margin-top:14px;">
            Viddra is not a bank. It’s <strong>money clarity</strong>: one plan, one number, less stress.
          </div>
        </div>

      </div>

    </div>

  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>