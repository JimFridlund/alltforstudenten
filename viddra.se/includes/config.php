<?php
/**
 * Viddra config
 * IMPORTANT: Do not close PHP tag in this file.
 */

// === Database (MySQL) ===
define('VIDDRA_DB_HOST', 'localhost');
define('VIDDRA_DB_NAME', 'studen36_viddra_uk');      // <-- sätt ditt DB-namn
define('VIDDRA_DB_USER', 'studen36_viddra_user');    // <-- sätt ditt DB-user
define('VIDDRA_DB_PASS', 'FridlundGren1');               // <-- ditt DB-lösen
define('VIDDRA_DB_CHARSET', 'utf8mb4');

// === Base URL (IMPORTANT) ===
define('VIDDRA_BASE_URL', 'https://viddra.se');

// === Scenario store mode ===
define('VIDDRA_SCENARIO_STORE', 'db'); // 'db' or 'session'

// === Security ===
// Change to a long random string in production.
define('VIDDRA_APP_SECRET', 'CHANGE_ME_TO_A_LONG_RANDOM_STRING');

// === Email (invites) ===
define('VIDDRA_MAIL_DRIVER', 'mail'); // 'mail' or 'smtp'
define('VIDDRA_MAIL_FROM', 'no-reply@viddra.se');
define('VIDDRA_MAIL_FROM_NAME', 'Viddra');

// If you later implement SMTP
// define('VIDDRA_SMTP_HOST', 'smtp.example.com');
// define('VIDDRA_SMTP_PORT', 587);
// define('VIDDRA_SMTP_USER', 'user');
// define('VIDDRA_SMTP_PASS', 'pass');
// define('VIDDRA_SMTP_SECURE', 'tls');

// === Billing ===
define('VIDDRA_BILLING_MODE', 'manual'); // 'manual' or 'stripe'
define('VIDDRA_REQUIRE_SUBSCRIPTION', false);

// === Stripe (only used when VIDDRA_BILLING_MODE='stripe') ===
define('VIDDRA_STRIPE_SECRET_KEY', 'CHANGE_ME');
define('VIDDRA_STRIPE_WEBHOOK_SECRET', 'CHANGE_ME');
define('VIDDRA_STRIPE_PRICE_ID_PLUS_MONTHLY', 'price_CHANGE_ME');

define('VIDDRA_STRIPE_SUCCESS_URL', VIDDRA_BASE_URL . '/app/billing.php?stripe=success');
define('VIDDRA_STRIPE_CANCEL_URL',  VIDDRA_BASE_URL . '/app/billing.php?stripe=cancel');
define('VIDDRA_STRIPE_PORTAL_RETURN_URL', VIDDRA_BASE_URL . '/app/billing.php?portal=return');

// === Admin ===
define('VIDDRA_ADMIN_EMAILS', 'jim@example.com');

// === Email verification ===
define('VIDDRA_REQUIRE_EMAIL_VERIFICATION', false);
define('VIDDRA_EMAIL_VERIFY_COOLDOWN_SECONDS', 120);

// === Invites ===
define('VIDDRA_INVITE_REQUIRES_VERIFIED_EMAIL', true);
define('VIDDRA_INVITE_TTL_HOURS', 168);
define('VIDDRA_SHOW_INVITE_COPY_LINK', true);