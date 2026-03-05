<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();
Auth::requireVerifiedEmail();
Billing::requireActive();
$page_title = "Structure — Viddra";
include __DIR__ . '/../includes/header.php';
?>
<section class="section"><div class="container">
  <h1>Structure</h1>
  <p class="lead">Next step: make this editable and saved per household.</p>
</div></section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
