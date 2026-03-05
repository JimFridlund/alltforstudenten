<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();

$u = Auth::user();
if (!WebhookLog::isAdmin($u['email'] ?? '')) {
  http_response_code(403);
  echo "Forbidden";
  exit;
}

$page_title = "Webhook logs — Viddra";
$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

include __DIR__ . '/../includes/header.php';
?>
<section class="section">
  <div class="container">
    <div class="page-top">
      <div>
        <h1>Webhook logs</h1>
        <p class="lead">Stripe events received by this server.</p>
      </div>
    </div>

    <?php if ($id > 0): ?>
      <?php $row = WebhookLog::read($id); ?>
      <?php if (!$row): ?>
        <p class="warn tiny">Not found.</p>
      <?php else: ?>
        <div class="card big">
          <h2><?php echo htmlspecialchars($row['event_type'] ?? 'event', ENT_QUOTES, 'UTF-8'); ?></h2>
          <div class="signal">
            <div class="signal-row"><span>ID</span><strong><?php echo (int)$row['id']; ?></strong></div>
            <div class="signal-row"><span>Event ID</span><strong><?php echo htmlspecialchars($row['event_id'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Status code</span><strong><?php echo htmlspecialchars($row['status_code'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Signature ok</span><strong><?php echo ($row['signature_ok'] ? 'yes' : 'no'); ?></strong></div>
            <div class="signal-row"><span>Error</span><strong><?php echo htmlspecialchars($row['error_message'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
            <div class="signal-row"><span>Time</span><strong><?php echo htmlspecialchars($row['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></strong></div>
          </div>
          <h3 style="margin-top:18px">Payload</h3>
          <pre style="white-space:pre-wrap;word-break:break-word;background:rgba(255,255,255,.55);border:1px solid rgba(31,31,26,.14);padding:14px;border-radius:18px"><?php echo htmlspecialchars($row['payload_json'] ?? '', ENT_QUOTES, 'UTF-8'); ?></pre>
          <div class="form-actions" style="margin-top:12px">
            <a class="btn" href="/app/admin_webhooks.php">Back to list</a>
          </div>
        </div>
      <?php endif; ?>
    <?php else: ?>
      <?php $rows = WebhookLog::latest(80); ?>
      <div class="card big">
        <h2>Latest events</h2>
        <div class="signal">
          <?php foreach ($rows as $r): ?>
            <div class="signal-row">
              <span>
                <a href="/app/admin_webhooks.php?id=<?php echo (int)$r['id']; ?>"><?php echo htmlspecialchars($r['event_type'] ?? '-', ENT_QUOTES, 'UTF-8'); ?></a><br>
                <span class="tiny muted"><?php echo htmlspecialchars($r['created_at'] ?? '', ENT_QUOTES, 'UTF-8'); ?></span>
              </span>
              <strong><?php echo htmlspecialchars((string)$r['status_code'], ENT_QUOTES, 'UTF-8'); ?></strong>
            </div>
          <?php endforeach; ?>
        </div>
        <div class="mini-callout">
          <strong>Tip:</strong> open a row to see full payload and errors.
        </div>
      </div>
    <?php endif; ?>

  </div>
</section>
<?php include __DIR__ . '/../includes/footer.php'; ?>
