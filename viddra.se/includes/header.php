<?php
// includes/header.php (COMPLETE)
// NOTE: Each page should require ../includes/bootstrap.php BEFORE including this header.

$isLoggedIn = false;
if (class_exists('Auth')) {
  if (method_exists('Auth', 'isLoggedIn')) {
    $isLoggedIn = (bool)Auth::isLoggedIn();
  } elseif (method_exists('Auth', 'userId')) {
    $isLoggedIn = ((int)Auth::userId() > 0);
  }
}

$title = isset($page_title) && trim((string)$page_title) !== '' ? (string)$page_title : 'Viddra';

// Auto cache-bust for CSS (no manual ?v= needed)
$cssRel = '/assets/css/app.css';
$cssAbs = (defined('VIDDRA_ROOT') ? VIDDRA_ROOT : dirname(__DIR__)) . $cssRel;
$cssVer = file_exists($cssAbs) ? (string)filemtime($cssAbs) : (string)time();
?><!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo htmlspecialchars($title, ENT_QUOTES, 'UTF-8'); ?></title>

  <!-- Fonts -->
  <link rel="preconnect" href="https://fonts.googleapis.com">
  <link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>
  <link href="https://fonts.googleapis.com/css2?family=Fraunces:opsz,wght@9..144,500;9..144,600;9..144,700&family=Inter:wght@400;500;600;700;800&display=swap" rel="stylesheet">

  <!-- App CSS (auto-versioned) -->
  <link rel="stylesheet" href="<?php echo htmlspecialchars($cssRel . '?v=' . $cssVer, ENT_QUOTES, 'UTF-8'); ?>" />
</head>

<body>

<header class="site-header">
  <div class="site-header__inner">

    <div class="brand">
      <a class="brand__link" href="/" aria-label="Viddra home">
        <span class="brand__mark" aria-hidden="true">
          <svg viewBox="0 0 64 44" width="44" height="44" role="img" focusable="false" aria-hidden="true">
            <path d="M6 26 L24 38 L58 6" fill="none" stroke="currentColor" stroke-width="8" stroke-linecap="round" stroke-linejoin="round"/>
          </svg>
        </span>
        <span class="brand__name">Viddra</span>
      </a>
    </div>

    <?php if ($isLoggedIn): ?>

      <nav class="nav nav--desktop" aria-label="Primary">
        <a class="nav__link" href="/app/dashboard.php">Home</a>
        <a class="nav__link" href="/app/transactions.php">Transactions</a>
        <a class="nav__link" href="/app/budget.php">Budget</a>
        <a class="nav__link" href="/app/forecast.php">Forecast</a>
        <a class="nav__link" href="/app/household.php">Household</a>

        <details class="nav__dropdown">
          <summary class="nav__link nav__link--button">Account</summary>
          <div class="nav__menu">
            <a class="nav__menuLink" href="/app/profile.php">Profile</a>
            <a class="nav__menuLink" href="/app/billing.php">Billing</a>
            <div class="nav__menuDivider"></div>
            <a class="nav__menuLink" href="/app/logout.php">Log out</a>
          </div>
        </details>
      </nav>

      <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
        <span class="nav-toggle__text">Menu</span>
        <span class="nav-toggle__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" role="img" focusable="false" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
          </svg>
        </span>
      </button>

      <div class="mobile-nav-backdrop" hidden></div>

      <nav id="mobileNav" class="mobile-nav" aria-label="Mobile menu" hidden>
        <a class="mobile-nav__link" href="/app/dashboard.php">Home</a>
        <a class="mobile-nav__link" href="/app/transactions.php">Transactions</a>
        <a class="mobile-nav__link" href="/app/budget.php">Budget</a>
        <a class="mobile-nav__link" href="/app/forecast.php">Forecast</a>
        <a class="mobile-nav__link" href="/app/household.php">Household</a>

        <div class="mobile-nav__divider"></div>

        <a class="mobile-nav__link" href="/app/profile.php">Profile</a>
        <a class="mobile-nav__link" href="/app/billing.php">Billing</a>
        <a class="mobile-nav__link" href="/app/logout.php">Log out</a>
      </nav>

    <?php else: ?>

      <nav class="nav nav--desktop" aria-label="Primary">
        <a class="nav__link" href="/forecast.php">Forecast</a>
        <a class="nav__link" href="/privacy.php">Privacy</a>
        <a class="nav__link" href="/app/login.php">Log in</a>
        <a class="btn btn--primary" href="/app/register.php">Get early access</a>
      </nav>

      <button class="nav-toggle" type="button" aria-label="Open menu" aria-expanded="false" aria-controls="mobileNav">
        <span class="nav-toggle__text">Menu</span>
        <span class="nav-toggle__icon" aria-hidden="true">
          <svg viewBox="0 0 24 24" width="18" height="18" role="img" focusable="false" aria-hidden="true">
            <path d="M4 7h16M4 12h16M4 17h16" fill="none" stroke="currentColor" stroke-width="2.2" stroke-linecap="round"/>
          </svg>
        </span>
      </button>

      <div class="mobile-nav-backdrop" hidden></div>

      <nav id="mobileNav" class="mobile-nav" aria-label="Mobile menu" hidden>
        <a class="mobile-nav__link" href="/forecast.php">Forecast</a>
        <a class="mobile-nav__link" href="/privacy.php">Privacy</a>
        <a class="mobile-nav__link" href="/app/login.php">Log in</a>
        <div class="mobile-nav__divider"></div>
        <a class="mobile-nav__link" href="/app/register.php">Get early access</a>
      </nav>

    <?php endif; ?>

  </div>
</header>

<main class="site-main">

<script>
(function(){
  const btn = document.querySelector('.nav-toggle');
  const panel = document.getElementById('mobileNav');
  const backdrop = document.querySelector('.mobile-nav-backdrop');
  if (!btn || !panel) return;

  const close = () => {
    btn.setAttribute('aria-expanded', 'false');
    btn.setAttribute('aria-label', 'Open menu');
    panel.hidden = true;
    if (backdrop) backdrop.hidden = true;
    document.body.classList.remove('is-mobile-nav-open');
  };

  const open = () => {
    btn.setAttribute('aria-expanded', 'true');
    btn.setAttribute('aria-label', 'Close menu');
    panel.hidden = false;
    if (backdrop) backdrop.hidden = false;
    document.body.classList.add('is-mobile-nav-open');
  };

  btn.addEventListener('click', () => {
    const isOpen = btn.getAttribute('aria-expanded') === 'true';
    if (isOpen) close(); else open();
  });

  if (backdrop) backdrop.addEventListener('click', close);

  panel.addEventListener('click', (e) => {
    const link = e.target.closest('a');
    if (link) close();
  });

  document.addEventListener('keydown', (e) => {
    if (e.key === 'Escape') close();
  });

  window.addEventListener('resize', () => {
    if (window.innerWidth > 980) close();
  });

  close();
})();
</script>