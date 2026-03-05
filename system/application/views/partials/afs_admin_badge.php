<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<?php
/**
 * partials/afs_admin_badge.php
 * Förväntar sig: $ad (array)
 * Visas bara om admin är inloggad.
 */

if (!isset($this->dx_auth) || !$this->dx_auth->is_logged_in()) {
  return;
}

$orderid = isset($ad['orderid']) ? trim((string)$ad['orderid']) : '';
$seller  = isset($ad['seller']) ? trim((string)$ad['seller']) : '';

// date_expire kan saknas i vissa queries – då visar vi inget datum istället för att strula
$expRaw = '';
if (isset($ad['date_expire'])) {
  $expRaw = trim((string)$ad['date_expire']);
} elseif (isset($ad['date_expire '])) {
  // (super-robust ifall någon råkat få in mellanslag i alias – händer ibland i legacy)
  $expRaw = trim((string)$ad['date_expire ']);
}

$expTxt = '';
if ($expRaw !== '' && $expRaw !== '0000-00-00') {
  $ts = @strtotime($expRaw);
  if ($ts) $expTxt = date('Y-m-d', $ts);
}

if ($orderid === '' && $seller === '' && $expTxt === '') {
  return;
}
?>
<div class="afsAdmin">
  <?php if ($orderid !== ''): ?>
    Fortnox: <?php echo htmlspecialchars($orderid, ENT_QUOTES, 'UTF-8'); ?>
  <?php endif; ?>

  <?php if ($seller !== ''): ?>
    • <?php echo htmlspecialchars($seller, ENT_QUOTES, 'UTF-8'); ?>
  <?php endif; ?>

  <?php if ($expTxt !== ''): ?>
    • Slutar: <?php echo htmlspecialchars($expTxt, ENT_QUOTES, 'UTF-8'); ?>
  <?php endif; ?>
</div>