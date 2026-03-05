<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$hid = Household::currentId();
Category::seedDefaults($hid);

$ym = $_GET['ym'] ?? date('Y-m');
list($ym, $start, $end) = Rollup::monthRange($ym);

$cats = Category::all($hid);
$actualRows = Rollup::byCategory($hid, $ym);

// map actual by category_id
$actualMap = [];
foreach ($actualRows as $r){
  $actualMap[(int)$r['category_id']] = (float)$r['total_amount'];
}

if ($_SERVER['REQUEST_METHOD'] === 'POST'){
  if (!viddra_csrf_check($_POST['csrf'] ?? '')) {
    // ignore
  } else {
    foreach ($cats as $c){
      $cid = (int)$c['id'];
      $key = "b_" . $cid;
      if (isset($_POST[$key])){
        Budget::set($hid, $ym, $cid, $_POST[$key]);
      }
    }
  }
  header("Location: /app/budget.php?ym=" . urlencode($ym)); exit;
}

$budgetMap = Budget::getMap($hid, $ym);

function gbp($n){
  return "£" . number_format((float)$n, 2, '.', ',');
}

$page_title = "Budget vs Actual — Viddra";
include __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="card big">
      <h1>Budget vs Actual</h1>
      <p class="muted">Plan your month, then see what happened.</p>

      <form method="post" class="sim-form" style="margin-top:14px">
        <input type="hidden" name="csrf" value="<?php echo htmlspecialchars(viddra_csrf_token(), ENT_QUOTES, 'UTF-8'); ?>">

        <table style="width:100%;margin-top:10px">
          <tr>
            <th align="left">Category</th>
            <th align="left">Type</th>
            <th align="right">Budget</th>
            <th align="right">Actual</th>
            <th align="right">Diff</th>
          </tr>

          <?php
            $sumBudget = 0.0;
            $sumActual = 0.0;
          ?>

          <?php foreach ($cats as $c): ?>
            <?php
              $cid = (int)$c['id'];
              $budget = (float)($budgetMap[$cid] ?? 0);
              $actual = (float)($actualMap[$cid] ?? 0);
              $diff = $actual - $budget;

              $sumBudget += $budget;
              $sumActual += $actual;
            ?>
            <tr>
              <td><?php echo htmlspecialchars($c['name'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td><?php echo htmlspecialchars($c['type'], ENT_QUOTES, 'UTF-8'); ?></td>
              <td align="right">
                <input style="text-align:right" type="text" inputmode="decimal" name="b_<?php echo $cid; ?>"
                       value="<?php echo htmlspecialchars((string)$budget, ENT_QUOTES, 'UTF-8'); ?>"
                       placeholder="0.00">
              </td>
              <td align="right" style="font-weight:900"><?php echo gbp($actual); ?></td>
              <td align="right" style="font-weight:900;opacity:.85"><?php echo gbp($diff); ?></td>
            </tr>
          <?php endforeach; ?>

          <tr>
            <td colspan="2" style="padding-top:12px"><strong>Total</strong></td>
            <td align="right" style="padding-top:12px"><strong><?php echo gbp($sumBudget); ?></strong></td>
            <td align="right" style="padding-top:12px"><strong><?php echo gbp($sumActual); ?></strong></td>
            <td align="right" style="padding-top:12px"><strong><?php echo gbp($sumActual - $sumBudget); ?></strong></td>
          </tr>
        </table>

        <div class="ob-actions" style="margin-top:14px">
          <button class="btn primary" type="submit">Save budgets</button>
          <a class="btn" href="/app/monthly.php?ym=<?php echo urlencode($ym); ?>">View rollup</a>
          <a class="btn" href="/app/transactions.php">Transactions</a>
        </div>
      </form>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
