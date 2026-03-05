<?php
// public_html/school_events_admin.php
// Enkel admin för att lägga in student-/bal-datum per skolenhetskod.
// Skyddas med ett lösenord du sätter här nedan.

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(30);
session_start();

// ============================
// 1) SÄTT DITT LÖSENORD HÄR
// ============================
define('ADMIN_PASSWORD', 'FridlundGren1'); // <-- ändra!

// ---- Minimal helpers ----
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function now(){ return date('Y-m-d H:i:s'); }
function is_post(){ return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'; }
function redirect_self($qs=''){
  $url = $_SERVER['PHP_SELF'] . ($qs ? ('?' . $qs) : '');
  header('Location: ' . $url);
  exit;
}

// ---- Login ----
if (isset($_GET['logout'])) {
  unset($_SESSION['se_admin']);
  redirect_self();
}

$logged_in = !empty($_SESSION['se_admin']);

if (!$logged_in) {
  $err = '';
  if (is_post() && isset($_POST['pw'])) {
    $pw = (string)$_POST['pw'];
    if (hash_equals(ADMIN_PASSWORD, $pw)) {
      $_SESSION['se_admin'] = 1;
      // CSRF token
      if (empty($_SESSION['se_csrf'])) {
        $_SESSION['se_csrf'] = bin2hex(random_bytes(16));
      }
      redirect_self();
    } else {
      $err = 'Fel lösenord.';
    }
  }

  echo '<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
  echo '<title>School Events Admin</title>';
  echo '<style>
    body{margin:0;background:#f4f8ff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0b1220}
    .wrap{max-width:520px;margin:0 auto;padding:18px 14px}
    .card{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 8px 22px rgba(0,0,0,.05);overflow:hidden}
    .head{padding:14px 14px 10px;border-bottom:1px solid #f0f2f6;background:linear-gradient(180deg,#eaf2ff 0%,#fff 90%);font-weight:950;color:#0b2a4a}
    .pad{padding:14px}
    label{display:block;font-weight:900;margin:0 0 6px}
    input{width:100%;padding:10px 12px;border:1px solid #e6eaf0;border-radius:12px;font-size:14px}
    button{margin-top:10px;width:100%;padding:10px 12px;border-radius:12px;border:1px solid rgba(0,0,0,.06);background:#f5c542;font-weight:950;cursor:pointer}
    .err{margin-top:10px;color:#8a1435;font-weight:900}
  </style></head><body><div class="wrap">';
  echo '<div class="card"><div class="head">Logga in</div><div class="pad">';
  echo '<form method="post"><label>Lösenord</label><input type="password" name="pw" autocomplete="current-password" required>';
  echo '<button type="submit">Logga in</button></form>';
  if ($err) echo '<div class="err">'.h($err).'</div>';
  echo '</div></div></div></body></html>';
  exit;
}

// ---- CSRF ----
if (empty($_SESSION['se_csrf'])) {
  $_SESSION['se_csrf'] = bin2hex(random_bytes(16));
}
$csrf = $_SESSION['se_csrf'];

function require_csrf(){
  if (!is_post()) return;
  $t = isset($_POST['csrf']) ? (string)$_POST['csrf'] : '';
  if (!$t || empty($_SESSION['se_csrf']) || !hash_equals($_SESSION['se_csrf'], $t)) {
    http_response_code(400);
    echo "Bad Request";
    exit;
  }
}

// ---- DB-config (CI 1.x) ----
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) {
  http_response_code(500);
  echo "Hittar inte database.php: " . h($ci_db_config);
  exit;
}
if (!defined('BASEPATH')) {
  define('BASEPATH', __DIR__ . '/system/');
}
require $ci_db_config;

if (!isset($db) || !isset($db['default'])) {
  http_response_code(500);
  echo "DB-config saknas (\$db['default']).";
  exit;
}
$dbc = $db['default'];

$host = (string)$dbc['hostname'];
$user = (string)$dbc['username'];
$pass = (string)$dbc['password'];
$name = (string)$dbc['database'];

$mysqli = @new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) {
  http_response_code(500);
  echo "DB connect failed.";
  exit;
}
@$mysqli->set_charset('utf8mb4');

// ---- Handle actions ----
$notice = '';
$error = '';

if (is_post() && isset($_POST['action'])) {
  require_csrf();
  $action = (string)$_POST['action'];

  if ($action === 'save') {
    $sk = trim((string)($_POST['skolenhetskod'] ?? ''));
    $student_date = trim((string)($_POST['student_date'] ?? ''));
    $student_time = trim((string)($_POST['student_time'] ?? ''));
    $bal_date     = trim((string)($_POST['bal_date'] ?? ''));
    $bal_time     = trim((string)($_POST['bal_time'] ?? ''));
    $source_url   = trim((string)($_POST['source_url'] ?? ''));

    if ($sk === '' || !preg_match('/^[0-9]{6,12}$/', $sk)) {
      $error = 'Ogiltig skolenhetskod (måste vara 6–12 siffror).';
    } else {
      // tom sträng => NULL för date-fälten
      $sd = ($student_date !== '') ? $student_date : null;
      $bd = ($bal_date !== '') ? $bal_date : null;

      // validera datumformat om angivet
      if ($sd !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $sd)) $error = 'Student-datum måste vara YYYY-MM-DD.';
      if ($bd !== null && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $bd)) $error = 'Bal-datum måste vara YYYY-MM-DD.';
    }

    if (!$error) {
      // UPSERT
      $sql = "
        INSERT INTO school_events
          (skolenhetskod, student_date, student_time, bal_date, bal_time, source_url)
        VALUES
          (?, ?, ?, ?, ?, ?)
        ON DUPLICATE KEY UPDATE
          student_date = VALUES(student_date),
          student_time = VALUES(student_time),
          bal_date     = VALUES(bal_date),
          bal_time     = VALUES(bal_time),
          source_url   = VALUES(source_url)
      ";
      $st = $mysqli->prepare($sql);
      if (!$st) {
        $error = 'DB-fel (prepare).';
      } else {
        // bind_param kräver strängar – vi skickar null som NULL via "s" funkar ej -> workaround:
        // Vi skickar '' och låter MySQL tolka '' för DATE? bättre att skicka null via set_null.
        // Enklast: använd SET ? = NULL vid tomt => vi gör om genom att använda IFNULL/NULLIF.
        $st->close();

        $sql2 = "
          INSERT INTO school_events
            (skolenhetskod, student_date, student_time, bal_date, bal_time, source_url)
          VALUES
            (?, NULLIF(?,''), ?, NULLIF(?,''), ?, ?)
          ON DUPLICATE KEY UPDATE
            student_date = VALUES(student_date),
            student_time = VALUES(student_time),
            bal_date     = VALUES(bal_date),
            bal_time     = VALUES(bal_time),
            source_url   = VALUES(source_url)
        ";
        $st2 = $mysqli->prepare($sql2);
        if (!$st2) {
          $error = 'DB-fel (prepare 2).';
        } else {
          $st2->bind_param(
            'ssssss',
            $sk,
            $student_date,
            $student_time,
            $bal_date,
            $bal_time,
            $source_url
          );
          if ($st2->execute()) {
            $notice = 'Sparat ' . h($sk) . ' (' . now() . ')';
          } else {
            $error = 'DB-fel (execute).';
          }
          $st2->close();
        }
      }
    }
  }

  if ($action === 'delete') {
    $sk = trim((string)($_POST['skolenhetskod'] ?? ''));
    if ($sk === '' || !preg_match('/^[0-9]{6,12}$/', $sk)) {
      $error = 'Ogiltig skolenhetskod.';
    } else {
      $st = $mysqli->prepare("DELETE FROM school_events WHERE skolenhetskod = ? LIMIT 1");
      if ($st) {
        $st->bind_param('s', $sk);
        if ($st->execute()) {
          $notice = 'Raderat ' . h($sk) . ' (' . now() . ')';
        } else {
          $error = 'DB-fel (delete).';
        }
        $st->close();
      } else {
        $error = 'DB-fel (prepare delete).';
      }
    }
  }
}

// ---- Load current record (if search) ----
$search_code = trim((string)($_GET['code'] ?? ''));
$current = array(
  'skolenhetskod' => '',
  'student_date' => '',
  'student_time' => '',
  'bal_date' => '',
  'bal_time' => '',
  'source_url' => '',
  'updated_at' => '',
);

if ($search_code !== '' && preg_match('/^[0-9]{6,12}$/', $search_code)) {
  $st = $mysqli->prepare("SELECT skolenhetskod, student_date, student_time, bal_date, bal_time, source_url, updated_at
                          FROM school_events WHERE skolenhetskod = ? LIMIT 1");
  if ($st) {
    $st->bind_param('s', $search_code);
    if ($st->execute()) {
      $st->bind_result($sk, $sd, $stt, $bd, $bt, $su, $ua);
      if ($st->fetch()) {
        $current['skolenhetskod'] = (string)$sk;
        $current['student_date'] = (string)$sd;
        $current['student_time'] = (string)$stt;
        $current['bal_date']     = (string)$bd;
        $current['bal_time']     = (string)$bt;
        $current['source_url']   = (string)$su;
        $current['updated_at']   = (string)$ua;
      } else {
        $current['skolenhetskod'] = $search_code; // förifyll kod även om den inte finns än
      }
    }
    $st->close();
  }
}

// ---- Recent list ----
$recent = array();
$r = $mysqli->query("SELECT skolenhetskod, student_date, bal_date, updated_at FROM school_events ORDER BY updated_at DESC LIMIT 20");
if ($r) {
  while ($row = $r->fetch_assoc()) $recent[] = $row;
  $r->free();
}

$mysqli->close();

// ---- UI ----
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title>School Events Admin</title>
  <meta name="robots" content="noindex,nofollow">
  <style>
    :root{ --bg:#f4f8ff; --card:#fff; --text:#0b1220; --muted:#5b6472; --line:#e6eaf0; --blue:#0b2a4a; --sky:#eaf2ff; --yellow:#f5c542; --r16:16px; }
    body{margin:0;background:var(--bg);font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:var(--text)}
    .wrap{max-width:980px;margin:0 auto;padding:18px 14px 40px}
    .top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px}
    .top h1{margin:0;font-size:22px;color:var(--blue)}
    .top a{color:#0f4c81;font-weight:900;text-decoration:none}
    .grid{display:grid;grid-template-columns:1fr 360px;gap:14px;align-items:start}
    @media(max-width:980px){.grid{grid-template-columns:1fr}}
    .card{background:var(--card);border:1px solid var(--line);border-radius:var(--r16);box-shadow:0 8px 22px rgba(0,0,0,.05);overflow:hidden}
    .head{padding:14px 14px 10px;border-bottom:1px solid #f0f2f6;background:linear-gradient(180deg,var(--sky) 0%,#fff 90%);font-weight:950;color:var(--blue)}
    .pad{padding:14px}
    label{display:block;font-weight:900;margin:10px 0 6px}
    input{width:100%;padding:10px 12px;border:1px solid var(--line);border-radius:12px;font-size:14px}
    .row2{display:grid;grid-template-columns:1fr 1fr;gap:10px}
    .btnRow{display:flex;gap:10px;flex-wrap:wrap;margin-top:12px}
    .btn{display:inline-flex;align-items:center;justify-content:center;padding:10px 12px;border-radius:12px;border:1px solid var(--line);font-weight:950;background:#fff;cursor:pointer;text-decoration:none;color:var(--blue)}
    .btnPrimary{background:var(--yellow);border-color:rgba(0,0,0,.06);color:#132033}
    .btnDanger{background:#ffe3ea;border-color:#ffc2d3;color:#7a0e2f}
    .msg{margin:0 0 10px;font-weight:900}
    .ok{color:#1a6b2d}
    .err{color:#8a1435}
    table{width:100%;border-collapse:collapse;font-size:13px}
    th,td{padding:8px;border-bottom:1px solid #f0f2f6;text-align:left;color:var(--muted)}
    th{color:var(--text)}
    .small{font-size:12px;color:var(--muted);margin:6px 0 0}
    .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace}
  </style>
</head>
<body>
  <div class="wrap">
    <div class="top">
      <h1>Datum-admin (student & bal)</h1>
      <div>
        <a href="<?php echo h($_SERVER['PHP_SELF']); ?>?logout=1">Logga ut</a>
      </div>
    </div>

    <div class="grid">

      <main class="card">
        <div class="head">Sök / redigera</div>
        <div class="pad">

          <?php if ($notice): ?><p class="msg ok"><?php echo $notice; ?></p><?php endif; ?>
          <?php if ($error): ?><p class="msg err"><?php echo h($error); ?></p><?php endif; ?>

          <form method="get" action="<?php echo h($_SERVER['PHP_SELF']); ?>">
            <label>Skolenhetskod</label>
            <input type="text" name="code" value="<?php echo h($search_code); ?>" placeholder="t.ex. 77364720" class="mono">
            <div class="btnRow">
              <button class="btn btnPrimary" type="submit">Sök</button>
              <a class="btn" href="<?php echo h($_SERVER['PHP_SELF']); ?>">Rensa</a>
            </div>
          </form>

          <hr style="border:none;border-top:1px solid #eef1f6;margin:14px 0;">

          <form method="post" action="<?php echo h($_SERVER['PHP_SELF']); ?>?<?php echo $search_code ? ('code=' . urlencode($search_code)) : ''; ?>">
            <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
            <input type="hidden" name="action" value="save">

            <label>Skolenhetskod</label>
            <input type="text" name="skolenhetskod" value="<?php echo h($current['skolenhetskod']); ?>" required class="mono">

            <div class="row2">
              <div>
                <label>Student/utspring datum (YYYY-MM-DD)</label>
                <input type="text" name="student_date" value="<?php echo h($current['student_date']); ?>" placeholder="2026-06-12" class="mono">
              </div>
              <div>
                <label>Student tid (HH:MM)</label>
                <input type="text" name="student_time" value="<?php echo h($current['student_time']); ?>" placeholder="12:00" class="mono">
              </div>
            </div>

            <div class="row2">
              <div>
                <label>Studentbal datum (YYYY-MM-DD)</label>
                <input type="text" name="bal_date" value="<?php echo h($current['bal_date']); ?>" placeholder="2026-05-30" class="mono">
              </div>
              <div>
                <label>Bal tid (HH:MM)</label>
                <input type="text" name="bal_time" value="<?php echo h($current['bal_time']); ?>" placeholder="18:00" class="mono">
              </div>
            </div>

            <label>Källa (URL)</label>
            <input type="text" name="source_url" value="<?php echo h($current['source_url']); ?>" placeholder="https://kommun.se/...">

            <?php if (!empty($current['updated_at'])): ?>
              <p class="small">Senast uppdaterad: <span class="mono"><?php echo h($current['updated_at']); ?></span></p>
            <?php endif; ?>

            <div class="btnRow">
              <button class="btn btnPrimary" type="submit">Spara</button>
            </div>
          </form>

          <form method="post" action="<?php echo h($_SERVER['PHP_SELF']); ?>?<?php echo $search_code ? ('code=' . urlencode($search_code)) : ''; ?>" onsubmit="return confirm('Radera datum för denna skola?');" style="margin-top:10px;">
            <input type="hidden" name="csrf" value="<?php echo h($csrf); ?>">
            <input type="hidden" name="action" value="delete">
            <input type="hidden" name="skolenhetskod" value="<?php echo h($current['skolenhetskod']); ?>">
            <button class="btn btnDanger" type="submit">Radera</button>
          </form>

        </div>
      </main>

      <aside class="card">
        <div class="head">Senast uppdaterade</div>
        <div class="pad">
          <?php if (empty($recent)): ?>
            <p class="small">Inga rader ännu.</p>
          <?php else: ?>
            <table>
              <thead>
                <tr>
                  <th>Skolenhetskod</th>
                  <th>Student</th>
                  <th>Bal</th>
                </tr>
              </thead>
              <tbody>
              <?php foreach ($recent as $r): ?>
                <tr>
                  <td class="mono">
                    <a href="<?php echo h($_SERVER['PHP_SELF']); ?>?code=<?php echo h($r['skolenhetskod']); ?>" style="font-weight:900;color:#0f4c81;text-decoration:none;">
                      <?php echo h($r['skolenhetskod']); ?>
                    </a>
                  </td>
                  <td class="mono"><?php echo h($r['student_date']); ?></td>
                  <td class="mono"><?php echo h($r['bal_date']); ?></td>
                </tr>
              <?php endforeach; ?>
              </tbody>
            </table>
          <?php endif; ?>
          <p class="small">Tips: skriv in skolenhetskod och spara. Skolsidan visar datum automatiskt om det finns.</p>
        </div>
      </aside>

    </div>
  </div>
</body>
</html>