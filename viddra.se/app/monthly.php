<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();

$hid = Household::currentId();
Category::seedDefaults($hid);

$ym = $_GET['ym'] ?? date('Y-m');
list($ym, $start, $end) = Rollup::monthRange($ym);

$rows = Rollup::byCategory($hid, $ym);
$tot = Rollup::totals($rows);

function gbp($n){
  return "£" . number_format((float)$n, 2, '.', ',');
}

$page_title = "Monthly — Viddra";
include __DIR__ . '/../includes/header.php';
?>

<section class="section">
  <div class="container">
    <div class="card big">
      <h1>Monthly</h1>
      <p class="muted">A clean rollup of what happened in <strong><?php echo htmlspecialchars($ym, ENT_QUOTES, 'UTF-8'); ?></strong>.</p>

      <div class="snap-grid" style="margin-top:14px">
        <div class="snap-card"><h3>Income</h3><strong><?php echo gbp($tot['income']); ?></strong></div>
        <div class="snap-card"><h3>Fixed spend</h3><strong><?php echo gbp($tot['spend_fixed']); ?></strong></div>
        <div class="snap-card"><h3>Variable spend</h3><strong><?php echo gbp($tot['spend_variable']); ?></strong></div>
      </div>

      <div class="snap-grid" style="margin-top:12px">
        <div class="snap-card"><h3>Total spend</h3><strong><?php echo gbp($tot['spend_total']); ?></strong></div>
        <div class="snap-card"><h3>Saving</h3><strong><?php echo gbp($tot['saving']); ?></strong></div>
        <div class="snap-card"><h3>Net (income - spend - saving)</h3><strong><?php echo gbp($tot['income'] - $tot['spend_total'] - $tot['saving']); ?></strong></div>
      </div>

      <div class="form-actions" style="margin-top:12px">
        <a class="btn" href="/app/transactions.php">Add transactions</a>
        <a class="btn" href="/app/categories.php">Categories</a>
      </div>

      <hr style="margin:18px 0">

      <h2 style="margin-top:0">By category</h2>
      <table style="width:100%;margin-top:10px">
        <tr>
          <th align="left">Category</th>
          <th align="left">Type</th>
          <th align="right">Total</th>
          <th align="right">Tx</th>
        </tr>
        <?php foreach ($rows as $r): ?>
          <tr>
            <td><?php echo htmlspecialchars($r['category_name'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td><?php echo htmlspecialchars($r['category_type'], ENT_QUOTES, 'UTF-8'); ?></td>
            <td align="right" style="font-weight:900"><?php echo gbp($r['total_amount']); ?></td>
            <td align="right"><?php echo (int)$r['tx_count']; ?></td>
          </tr>
        <?php endforeach; ?>
      </table>
    </div>
  </div>
</section>

<?php include __DIR__ . '/../includes/footer.php'; ?>
