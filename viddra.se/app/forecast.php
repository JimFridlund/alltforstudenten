<?php
// app/forecast.php (COMPLETE)

require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Forecast — Viddra";

// ---------- Auth ----------
if (class_exists('Auth')) {
  if (method_exists('Auth','requireLogin')) Auth::requireLogin();
  if (method_exists('Auth','requireVerifiedEmail')) Auth::requireVerifiedEmail();
}

if (class_exists('Billing') && method_exists('Billing','requireActive')) {
  Billing::requireActive();
}

$hid = (class_exists('Household') && method_exists('Household','currentId'))
  ? (int)Household::currentId() : 0;


// ---------- Load Forecast ----------
$forecast = [];

if ($hid > 0 && class_exists('Forecast') && method_exists('Forecast','current')) {
  $tmp = Forecast::current($hid);
  if (is_array($tmp)) $forecast = $tmp;
}

$safe_day = (float)($forecast['safe_per_day'] ?? 0);
$days_left = (int)($forecast['days_to_payday'] ?? 0);

if ($days_left <= 0) $days_left = 14;


// ---------- Generate Forecast ----------
$days = [];

for ($i = 0; $i <= $days_left; $i++) {

  $date = date('Y-m-d', strtotime("+$i days"));

  $factor = $days_left > 0 ? ($days_left - $i) / $days_left : 1;

  $safe = max(0, $safe_day * $factor);

  $days[] = [
    'date' => $date,
    'safe' => $safe
  ];
}

include __DIR__ . '/../includes/header.php';
?>

<style>

.fc-wrap{
display:flex;
flex-direction:column;
gap:18px;
}

.fc-head{
padding:26px;
border-radius:26px;
background:rgba(251,247,239,0.90);
border:1px solid rgba(44,43,39,.12);
box-shadow:0 18px 55px rgba(0,0,0,.06);
}

.fc-head h1{
margin:0;
}

.fc-sub{
margin-top:6px;
font-weight:700;
color:rgba(44,43,39,.70);
}

.fc-panel{
padding:22px;
border-radius:22px;
background:rgba(255,255,255,.55);
border:1px solid rgba(44,43,39,.10);
}

.fc-grid{
display:grid;
grid-template-columns:1fr;
gap:10px;
}

.fc-day{
display:flex;
justify-content:space-between;
align-items:center;
padding:16px;
border-radius:16px;
background:rgba(255,255,255,.55);
border:1px solid rgba(44,43,39,.10);
}

.fc-date{
font-weight:700;
color:rgba(44,43,39,.75);
}

.fc-safe{
font-family:"Fraunces", Georgia, serif;
font-size:22px;
}

</style>


<section class="section">
<div class="container">

<div class="fc-wrap">


<div class="fc-head">

<h1>Spending forecast</h1>

<p class="fc-sub">
See how your safe-to-spend changes each day until payday.
</p>

</div>


<div class="fc-panel">

<canvas id="forecastChart" height="120"></canvas>

</div>


<div class="fc-grid">

<?php foreach ($days as $d): ?>

<div class="fc-day">

<div class="fc-date">

<?php echo htmlspecialchars($d['date']); ?>

</div>

<div class="fc-safe">

£<?php echo number_format($d['safe'],2); ?>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

</div>
</section>



<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>

const labels = [
<?php foreach ($days as $d){ echo "'".$d['date']."',"; } ?>
];

const data = [
<?php foreach ($days as $d){ echo round($d['safe'],2).","; } ?>
];

const ctx = document.getElementById('forecastChart');

new Chart(ctx,{
type:'line',
data:{
labels:labels,
datasets:[{
label:'Safe to spend',
data:data,
tension:0.35,
fill:true,
borderWidth:3,
pointRadius:0
}]
},
options:{
plugins:{
legend:{display:false}
},
scales:{
y:{
ticks:{
callback:(v)=>'£'+v
}
}
}
}
});

</script>


<?php include __DIR__ . '/../includes/footer.php'; ?>