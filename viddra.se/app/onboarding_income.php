<?php
require_once __DIR__ . '/../includes/bootstrap.php';

Auth::requireLogin();
Auth::requireVerifiedEmail();

$page_title = "Income — Onboarding — Viddra";

$hid = Household::currentId();

$error = null;

if($_SERVER['REQUEST_METHOD']==='POST'){

  if(!viddra_csrf_check($_POST['csrf'] ?? '')){
    $error = "Session expired";
  }else{

    $income = (float)($_POST['income'] ?? 0);
    $payday = (int)($_POST['payday'] ?? 1);

    Financials::updateIncome($hid,$income);

    DB::update(
      "viddra_household_financials",
      ['payday_day'=>$payday],
      ['household_id'=>$hid]
    );

    header("Location:/app/onboarding_fixed.php");
    exit;

  }
}

include __DIR__ . '/../includes/header.php';
?>

<section class="section">
<div class="container">

<div class="card big onboarding-card">

<h1>Your income</h1>

<form method="post">

<input type="hidden" name="csrf"
value="<?php echo htmlspecialchars(viddra_csrf_token()); ?>">

<label>Monthly household income (£)</label>
<input name="income" type="number" step="0.01" required>

<label>Payday (day of month)</label>

<select name="payday">
<?php for($i=1;$i<=28;$i++): ?>
<option value="<?php echo $i; ?>"><?php echo $i; ?></option>
<?php endfor; ?>
</select>

<button class="btn primary">Continue</button>

</form>

</div>
</div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>