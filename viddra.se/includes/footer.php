<?php
// includes/footer.php (v2 - strong brand footer)
?>
</main>

<footer class="site-footer">
  <div class="site-footer__inner">

    <div class="footer-brand">
      <a href="/" class="footer-brand__link">
        <span class="footer-brand__mark" aria-hidden="true">
          <svg viewBox="0 0 64 44" width="36" height="36">
            <path d="M6 26 L24 38 L58 6"
              fill="none"
              stroke="currentColor"
              stroke-width="8"
              stroke-linecap="round"
              stroke-linejoin="round"/>
          </svg>
        </span>
        <span class="footer-brand__name">Viddra</span>
      </a>

      <p class="footer-brand__meta">
        Money clarity for couples.<br>
        Not a bank. Not a budgeting spreadsheet.
      </p>
    </div>

    <div class="footer-cols">
      <div class="footer-col">
        <div class="footer-col__title">Product</div>
        <a class="footer-link" href="/#pricing">Pricing</a>
        <a class="footer-link" href="/#how">How it works</a>
        <a class="footer-link" href="/app/register.php">Create account</a>
      </div>

      <div class="footer-col">
        <div class="footer-col__title">Account</div>
        <a class="footer-link" href="/app/login.php">Sign in</a>
        <a class="footer-link" href="/app/forgot.php">Reset password</a>
      </div>

      <div class="footer-col">
        <div class="footer-col__title">Legal</div>
        <a class="footer-link" href="/privacy.php">Privacy</a>
        <a class="footer-link" href="/terms.php">Terms</a>
      </div>
    </div>

  </div>

  <div class="site-footer__bottom">
    <div class="site-footer__bottomInner">
      <span>© <?php echo date('Y'); ?> Viddra</span>
      <span class="dot">•</span>
      <span>Built for calm households</span>
    </div>
  </div>
</footer>

<script>
(function(){
  const btn = document.querySelector('.nav-toggle');
  const panel = document.getElementById('mobileNav');
  const backdrop = document.querySelector('.mobile-nav-backdrop');

  if (!btn || !panel) return;

  const close = () => {
    btn.setAttribute('aria-expanded', 'false');
    panel.hidden = true;
    if (backdrop) backdrop.hidden = true;
    document.body.classList.remove('is-mobile-nav-open');
  };

  const open = () => {
    btn.setAttribute('aria-expanded', 'true');
    panel.hidden = false;
    if (backdrop) backdrop.hidden = false;
    document.body.classList.add('is-mobile-nav-open');
  };

  btn.addEventListener('click', () => {
    const isOpen = btn.getAttribute('aria-expanded') === 'true';
    if (isOpen) close(); else open();
  });

  if (backdrop) backdrop.addEventListener('click', close);
})();
</script>

</body>
</html>