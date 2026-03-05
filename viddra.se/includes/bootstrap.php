<?php
// Viddra bootstrap (NO AUTOLOAD — avoids double-includes)
// IMPORTANT: Do not close PHP tag in this file.

session_start();

require_once __DIR__ . '/config.php';

define('VIDDRA_ROOT', dirname(__DIR__));
define('VIDDRA_CORE', VIDDRA_ROOT . '/core');

// Load core files explicitly ONCE (absolute paths)
require_once VIDDRA_CORE . '/Database.php';
require_once VIDDRA_CORE . '/Auth.php';
require_once VIDDRA_CORE . '/Household.php';
require_once VIDDRA_CORE . '/Financials.php';
require_once VIDDRA_CORE . '/Category.php';
require_once VIDDRA_CORE . '/Transaction.php';
require_once VIDDRA_CORE . '/Rollup.php';
require_once VIDDRA_CORE . '/Budget.php';
require_once VIDDRA_CORE . '/Forecast.php';
require_once VIDDRA_CORE . '/EmailVerification.php';
require_once VIDDRA_CORE . '/Invite.php';
require_once VIDDRA_CORE . '/Billing.php';

// CSRF helpers
function viddra_csrf_token(){
  if (!isset($_SESSION['viddra_csrf'])) {
    $_SESSION['viddra_csrf'] = bin2hex(random_bytes(16));
  }
  return $_SESSION['viddra_csrf'];
}
function viddra_csrf_check($token){
  return isset($_SESSION['viddra_csrf']) && hash_equals($_SESSION['viddra_csrf'], (string)$token);
}

// Scenario store helper
if (!defined('VIDDRA_SCENARIO_KEY')) define('VIDDRA_SCENARIO_KEY', 'viddra_scenario');

function viddra_scenario_store(){
  $mode = defined('VIDDRA_SCENARIO_STORE') ? VIDDRA_SCENARIO_STORE : 'session';
  if ($mode === 'db') {
    $uid = (int)Auth::userId();
    if ($uid <= 0) return new SessionScenarioStore();

    $hid = (int)Household::currentId();
    if ($hid <= 0) {
      $hid = (int)Household::ensureDefaultForUser($uid);
      Household::setCurrentId($hid);
    }
    return new DatabaseScenarioStore(Database::pdo(), $hid, $uid);
  }
  return new SessionScenarioStore();
}