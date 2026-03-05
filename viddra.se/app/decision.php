<?php
// app/decision.php (COMPLETE)
require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Before you buy — Viddra";

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

$uid = (class_exists('Auth') && method_exists('Auth','userId')) ? (int)Auth::userId() : 0;
$hid = (class_exists('Household') && method_exists('Household','currentId')) ? (int)Household::currentId() : 0;

$forecast = [];
$rollup = [];

if ($hid > 0 && class_exists('Forecast')) {
  $tmp = Forecast::current($hid);
  if (is_array($tmp)) $forecast = $tmp;
}

if ($hid > 0 && class_exists('Rollup')) {
  $tmp = Rollup::current($hid);
  if (is_array($tmp)) $rollup = $tmp;
}

$safe_day_base  = (float)($forecast['safe_per_day'] ?? 0);
$safe_week_base = (float)($forecast['safe_per_week'] ?? 0);
$days_left      = (int)($forecast['days_to_payday'] ?? 0);
$runway_base    = (int)($forecast['runway_days'] ?? 0);

$remaining_base = (float)($rollup['remaining'] ?? 0);

include __DIR__ . '/../includes/header.php';
?>

<style>
.sim-wrap{display:flex;flex-direction:column;gap:16px;}
.sim-head{
padding:22px;border-radius:24px;
background:rgba(251,247,239,0.9);
border:1px solid rgba(44,43,39,.12);
box-shadow:0 18px 55px rgba(0,0,0,.06);
}
.sim-head h1{margin:0;}
.sim-sub{margin-top:6px;font-weight:700;color:rgba(44,43,39,.7);}

.grid2{display:grid;grid-template-columns:1fr 1fr;gap:16px;}
@media(max-width:900px){.grid2{grid-template-columns:1fr;}}

.panel{
padding:22px;border-radius:22px;
background:rgba(255,255,255,.55);
border:1px solid rgba(44,43,39,.1);
}

.kpi{margin-top:14px;font-size:28px;font-family:"Fraunces",Georgia,serif;}
.kpi small{display:block;font-size:12px;text-transform:uppercase;letter-spacing:.05em;}

.badge{display:inline-block;padding:6px 10px;border-radius:999px;font-weight:900;font-size:11px}
.safe{background:#dfe9dc;}
.tighten{background:#f3e4d3;}
.urgent{background:#f5d9d6;}

.impact{margin-top:12px;font-weight:700;color:rgba(44,43,39,.7);}
</style>

<section class="section">
<div class="container">
<div class="sim-wrap">

<div class="sim-head">
<h1>Before you buy</h1>
<p class="sim-sub">Simulate a purchase and see how it affects your safe-to-spend.</p>
</div>

<div class="grid2">

<div class="panel">
<label>What are you buying?</label>
<input id="label" placeholder="e.g. New sofa">

<label>Cost (£)</label>
<input id="amount" inputmode="decimal" placeholder="400">

<label>When?</label>
<input id="date" type="date">

<div style="margin-top:14px;">
<a class="btn btn--ghost" href="/app/dashboard.php">Back</a>
</div>

</div>

<div class="panel">

<div id="stateBadge" class="badge safe">SAFE</div>

<div class="impact" id="impactText">
Enter a purchase amount to simulate.
</div>

<div class="kpi">
<small>Safe per day</small>
<span id="safeDay">£<?php echo number_format($safe_day_base,2); ?></span>
</div>

<div class="kpi">
<small>Runway</small>
<span id="runway"><?php echo $runway_base; ?> days</span>
</div>

<div class="kpi">
<small>Remaining</small>
<span id="remaining">£<?php echo number_format($remaining_base,2); ?></span>
</div>

</div>

</div>
</div>
</div>
</section>

<script>

const safeDayBase = <?php echo $safe_day_base; ?>;
const runwayBase = <?php echo $runway_base; ?>;
const remainingBase = <?php echo $remaining_base; ?>;
const daysLeft = <?php echo $days_left ?: 30; ?>;

const amountInput = document.getElementById("amount");

function calc(){

let amount = parseFloat(amountInput.value || 0);
if(isNaN(amount)) amount = 0;

let remainingAfter = remainingBase - amount;

let perDayImpact = amount / daysLeft;

let safeAfter = Math.max(0, safeDayBase - perDayImpact);

let runwayAfter = runwayBase;
if(safeDayBase>0){
runwayAfter = Math.max(0, Math.round(runwayBase - (amount / safeDayBase)));
}

document.getElementById("safeDay").innerText = "£"+safeAfter.toFixed(2);
document.getElementById("runway").innerText = runwayAfter+" days";
document.getElementById("remaining").innerText = "£"+remainingAfter.toFixed(2);

let badge = document.getElementById("stateBadge");
let text = document.getElementById("impactText");

badge.className="badge";

if(remainingAfter < 0){
badge.classList.add("urgent");
badge.innerText="URGENT";
text.innerText="This purchase pushes your budget negative.";
}
else if(runwayAfter < daysLeft){
badge.classList.add("tighten");
badge.innerText="TIGHTEN";
text.innerText="You can do this, but spending must tighten.";
}
else{
badge.classList.add("safe");
badge.innerText="SAFE";
text.innerText="This purchase fits your current plan.";
}

}

amountInput.addEventListener("input", calc);

</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>