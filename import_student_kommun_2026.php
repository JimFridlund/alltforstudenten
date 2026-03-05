<?php
// public_html/import_student_kommun_2026.php
// Importerar kommunvisa studentdatum från en CSV till tabellen kommun_events.
// CSV-format (rubrikrad krävs):
// lan_slug,kommun_slug,student_date,student_time,bal_date,bal_time,source_url
//
// Exempelrad:
// vastra-gotalands-lan,boras-stad,2026-06-05,13:00,,,https://boras.se/...

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(120);
session_start();

// ============================
// SÄTT LÖSENORD HÄR
// ============================
define('ADMIN_PASSWORD', 'FridlundGren1'); // <-- ändra direkt!

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_post(){ return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'; }
function redirect_self(){ header('Location: ' . $_SERVER['PHP_SELF']); exit; }

// ---- Login ----
if (isset($_GET['logout'])) { unset($_SESSION['komimp']); redirect_self(); }

if (empty($_SESSION['komimp'])) {
  $err = '';
  if (is_post() && isset($_POST['pw'])) {
    $pw = (string)$_POST['pw'];
    if (hash_equals(ADMIN_PASSWORD, $pw)) {
      $_SESSION['komimp'] = 1;
      $_SESSION['komimp_csrf'] = bin2hex(random_bytes(16));
      redirect_self();
    } else {
      $err = 'Fel lösenord.';
    }
  }

  echo '<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
  echo '<title>Import kommun student 2026</title><meta name="robots" content="noindex,nofollow">';
  echo '<style>
    body{margin:0;background:#f4f8ff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0b1220}
    .wrap{max-width:560px;margin:0 auto;padding:18px 14px}
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

if (empty($_SESSION['komimp_csrf'])) $_SESSION['komimp_csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['komimp_csrf'];

function require_csrf(){
  $t = isset($_POST['csrf']) ? (string)$_POST['csrf'] : '';
  if (!$t || empty($_SESSION['komimp_csrf']) || !hash_equals($_SESSION['komimp_csrf'], $t)) {
    http_response_code(400);
    echo "Bad Request";
    exit;
  }
}

// ---- DB-config (CI 1.x) ----
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { http_response_code(500); echo "Hittar inte database.php"; exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/system/');
require $ci_db_config;

if (!isset($db) || !isset($db['default'])) { http_response_code(500); echo "DB-config saknas"; exit; }
$dbc = $db['default'];

$host = (string)($dbc['hostname'] ?? '');
$user = (string)($dbc['username'] ?? '');
$pass = (string)($dbc['password'] ?? '');
$name = (string)($dbc['database'] ?? '');

$mysqli = @new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) { http_response_code(500); echo "DB connect failed"; exit; }
@$mysqli->set_charset('utf8mb4');

// ---- Ensure table exists (skydd) ----
$mysqli->query("CREATE TABLE IF NOT EXISTS kommun_events (
  lan_slug VARCHAR(80) NOT NULL,
  kommun_slug VARCHAR(120) NOT NULL,
  student_date DATE NULL,
  student_time VARCHAR(10) NULL,
  bal_date DATE NULL,
  bal_time VARCHAR(10) NULL,
  source_url VARCHAR(255) NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (lan_slug, kommun_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ---- Import handler ----
$report = null;

if (is_post() && isset($_POST['do_import'])) {
  require_csrf();

  if (!isset($_FILES['csv']) || !is_uploaded_file($_FILES['csv']['tmp_name'])) {
    $report = array('error' => 'Ingen fil uppladdad.');
  } else {
    $tmp = $_FILES['csv']['tmp_name'];
    $fh = fopen($tmp, 'rb');
    if (!$fh) {
      $report = array('error' => 'Kunde inte läsa filen.');
    } else {
      // Läs första raden (rubriker)
      $header = fgetcsv($fh, 0, ',');
      if (!$header || !is_array($header)) {
        $report = array('error' => 'CSV saknar rubrikrad.');
      } else {
        $map = array();
        foreach ($header as $i => $col) {
          $col = trim((string)$col);
          $map[$col] = $i;
        }

        $required = array('lan_slug','kommun_slug','student_date','student_time','bal_date','bal_time','source_url');
        foreach ($required as $r) {
          if (!array_key_exists($r, $map)) {
            $report = array('error' => 'Rubriker måste innehålla: ' . implode(', ', $required));
            break;
          }
        }

        if (!$report) {
          $sql = "
            INSERT INTO kommun_events
              (lan_slug, kommun_slug, student_date, student_time, bal_date, bal_time, source_url)
            VALUES
              (?, ?, NULLIF(?,''), ?, NULLIF(?,''), ?, ?)
            ON DUPLICATE KEY UPDATE
              student_date = VALUES(student_date),
              student_time = VALUES(student_time),
              bal_date     = VALUES(bal_date),
              bal_time     = VALUES(bal_time),
              source_url   = VALUES(source_url)
          ";
          $st = $mysqli->prepare($sql);
          if (!$st) {
            $report = array('error' => 'DB prepare misslyckades.');
          } else {

            $updated = 0;
            $skipped = 0;
            $errors  = array();
            $line_no = 1; // rubrikrad = 1

            $mysqli->begin_transaction();

            while (($row = fgetcsv($fh, 0, ',')) !== false) {
              $line_no++;

              $lan_slug    = trim((string)($row[$map['lan_slug']] ?? ''));
              $kommun_slug = trim((string)($row[$map['kommun_slug']] ?? ''));
              $student_date= trim((string)($row[$map['student_date']] ?? ''));
              $student_time= trim((string)($row[$map['student_time']] ?? ''));
              $bal_date    = trim((string)($row[$map['bal_date']] ?? ''));
              $bal_time    = trim((string)($row[$map['bal_time']] ?? ''));
              $source_url  = trim((string)($row[$map['source_url']] ?? ''));

              // Grundvalidering
              if ($lan_slug === '' || $kommun_slug === '') { $skipped++; continue; }
              if ($student_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $student_date)) {
                $errors[] = "Rad $line_no: ogiltigt student_date ($student_date)";
                $skipped++; continue;
              }
              if ($bal_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $bal_date)) {
                $errors[] = "Rad $line_no: ogiltigt bal_date ($bal_date)";
                $skipped++; continue;
              }
              if ($student_time !== '' && !preg_match('/^\d{1,2}:\d{2}$/', $student_time)) {
                $errors[] = "Rad $line_no: ogiltigt student_time ($student_time)";
                $skipped++; continue;
              }
              if ($bal_time !== '' && !preg_match('/^\d{1,2}:\d{2}$/', $bal_time)) {
                $errors[] = "Rad $line_no: ogiltigt bal_time ($bal_time)";
                $skipped++; continue;
              }

              $st->bind_param(
                'sssssss',
                $lan_slug,
                $kommun_slug,
                $student_date,
                $student_time,
                $bal_date,
                $bal_time,
                $source_url
              );

              if ($st->execute()) $updated++;
              else {
                $errors[] = "Rad $line_no: DB execute-fel";
                $skipped++;
              }
            }

            if (count($errors) === 0) {
              $mysqli->commit();
            } else {
              // Vi vill ändå inte rulla tillbaka allt om några rader failar.
              // Men vi kör safe och committar de som gått igenom.
              $mysqli->commit();
            }

            $st->close();
            $report = array(
              'updated' => $updated,
              'skipped' => $skipped,
              'errors'  => $errors
            );
          }
        }
      }
      fclose($fh);
    }
  }
}

// ---- Fetch count ----
$count = 0;
$r = $mysqli->query("SELECT COUNT(*) AS c FROM kommun_events");
if ($r) { $x = $r->fetch_assoc(); $count = (int)($x['c'] ?? 0); $r->free(); }
$mysqli->close();

// ---- UI ----
echo '<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
echo '<title>Import kommun student 2026</title><meta name="robots" content="noindex,nofollow">';
echo '<style>
  body{margin:0;background:#f4f8ff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0b1220}
  .wrap{max-width:980px;margin:0 auto;padding:18px 14px 40px}
  .top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px}
  .top h1{margin:0;font-size:22px;color:#0b2a4a}
  .top a{color:#0f4c81;font-weight:900;text-decoration:none}
  .card{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 8px 22px rgba(0,0,0,.05);overflow:hidden}
  .head{padding:14px 14px 10px;border-bottom:1px solid #f0f2f6;background:linear-gradient(180deg,#eaf2ff 0%,#fff 90%);font-weight:950;color:#0b2a4a}
  .pad{padding:14px}
  label{display:block;font-weight:900;margin:0 0 6px}
  input[type=file]{width:100%;padding:10px 12px;border:1px solid #e6eaf0;border-radius:12px;background:#fff}
  button{margin-top:10px;padding:10px 12px;border-radius:12px;border:1px solid rgba(0,0,0,.06);background:#f5c542;font-weight:950;cursor:pointer}
  .ok{color:#1a6b2d;font-weight:900}
  .err{color:#8a1435;font-weight:900}
  .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;font-size:12px;color:#5b6472}
  .box{border:1px solid #eef1f6;border-radius:12px;padding:10px;background:#fbfcff}
</style></head><body><div class="wrap">';

echo '<div class="top"><h1>Import kommun-datum (student/bal)</h1><div><a href="'.h($_SERVER['PHP_SELF']).'?logout=1">Logga ut</a></div></div>';

echo '<div class="card"><div class="head">Ladda upp CSV</div><div class="pad">';
echo '<p style="margin:0 0 10px;color:#5b6472">I databasen finns nu <strong>'.(int)$count.'</strong> rader i kommun_events.</p>';

echo '<div class="box mono">';
echo "CSV-rubriker (måste vara exakt):\n";
echo "lan_slug,kommun_slug,student_date,student_time,bal_date,bal_time,source_url\n";
echo "Exempel:\n";
echo "vastra-gotalands-lan,boras-stad,2026-06-05,13:00,,,https://boras.se/...\n";
echo '</div>';

echo '<form method="post" enctype="multipart/form-data" style="margin-top:12px;">';
echo '<input type="hidden" name="csrf" value="'.h($csrf).'">';
echo '<input type="hidden" name="do_import" value="1">';
echo '<label>CSV-fil</label>';
echo '<input type="file" name="csv" accept=".csv,text/csv" required>';
echo '<button type="submit">Importera</button>';
echo '</form>';

if ($report) {
  if (!empty($report['error'])) {
    echo '<p class="err" style="margin-top:12px;">'.h($report['error']).'</p>';
  } else {
    echo '<p class="ok" style="margin-top:12px;">Klart! Updated: '.(int)$report['updated'].' • Skipped: '.(int)$report['skipped'].'</p>';
    if (!empty($report['errors'])) {
      echo '<p class="err">Varningar:</p><div class="box mono">';
      foreach ($report['errors'] as $e) echo h($e)."\n";
      echo '</div>';
    }
  }
}

echo '</div></div></div></body></html>';