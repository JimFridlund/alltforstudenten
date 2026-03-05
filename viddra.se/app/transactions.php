<?php
// app/transactions.php (COMPLETE)

require_once __DIR__ . '/../includes/bootstrap.php';

$page_title = "Transactions — Viddra";

if (class_exists('Auth')) {
  if (method_exists('Auth','requireLogin')) Auth::requireLogin();
  if (method_exists('Auth','requireVerifiedEmail')) Auth::requireVerifiedEmail();
}

if (class_exists('Billing') && method_exists('Billing','requireActive')) {
  Billing::requireActive();
}

$hid = (class_exists('Household') && method_exists('Household','currentId'))
? (int)Household::currentId() : 0;

$uid = (class_exists('Auth') && method_exists('Auth','userId'))
? (int)Auth::userId() : 0;


// ---------- Forecast ----------
$forecast = [];

if ($hid > 0 && class_exists('Forecast') && method_exists('Forecast','current')) {
  $tmp = Forecast::current($hid);
  if (is_array($tmp)) $forecast = $tmp;
}

$safe_day = (float)($forecast['safe_per_day'] ?? 0);


// ---------- Handle POST ----------
$notice = "";

if ($_SERVER['REQUEST_METHOD'] === 'POST') {

  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {

    $notice = "Session expired.";

  } else {

    $amount = (float)$_POST['amount'];
    $note = trim($_POST['note'] ?? "");
    $date = $_POST['date'] ?? date('Y-m-d');

    if ($amount > 0) $amount = -$amount;

    if (class_exists('Transaction') && method_exists('Transaction','create')) {

      Transaction::create($hid, null, $date, $amount, $note, $uid);

      // ---------- Decision impact text ----------

      $days = $safe_day > 0 ? abs($amount) / $safe_day : 0;

      $days_round = floor($days);

      if ($days < 1) {
        $impact = "mindre än en dags safe-spend";
      }
      else {

        if ($days - $days_round < 0.25) {
          $impact = "ungefär ".$days_round." dag".($days_round>1?"ar":"");
        }
        elseif ($days - $days_round < 0.75) {
          $impact = "lite drygt ".$days_round." dag".($days_round>1?"ar":"");
        }
        else {
          $impact = "nästan ".($days_round+1)." dag".($days_round+1>1?"ar":"");
        }

      }

      $notice = "⚠ Den här utgiften motsvarar ".$impact." av safe-spend.";

    }

  }

}


// ---------- Load transactions ----------

$rows = [];

if ($hid > 0 && class_exists('Transaction') && method_exists('Transaction','latest')) {
  $rows = Transaction::latest($hid,50);
}


include __DIR__ . '/../includes/header.php';
?>


<style>

.tx-wrap{display:flex;flex-direction:column;gap:16px;}

.card{
padding:22px;
border-radius:22px;
background:rgba(255,255,255,.55);
border:1px solid rgba(44,43,39,.10);
}

.tx-row{
display:flex;
justify-content:space-between;
padding:12px 0;
border-bottom:1px solid #eee;
}

.warn{
background:#fff1e8;
border:1px solid #f0c5a8;
}

</style>


<section class="section">
<div class="container">

<div class="tx-wrap">


<h1>Transactions</h1>


<?php if ($notice): ?>

<div class="card warn">

<?php echo htmlspecialchars($notice); ?>

</div>

<?php endif; ?>



<div class="card">

<form method="post">

<input type="hidden" name="csrf" value="<?php echo viddra_csrf_token(); ?>">

<label>Amount (£)</label>
<input name="amount" inputmode="decimal">

<label>Note</label>
<input name="note">

<label>Date</label>
<input name="date" type="date" value="<?php echo date('Y-m-d'); ?>">

<button class="btn btn--primary">Add transaction</button>

</form>

</div>



<div class="card">

<?php foreach ($rows as $r): ?>

<div class="tx-row">

<div>

<?php echo htmlspecialchars($r['note']); ?>

</div>

<div>

£<?php echo number_format(abs($r['amount']),2); ?>

</div>

</div>

<?php endforeach; ?>

</div>


</div>
</div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>