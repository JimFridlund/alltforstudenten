<?php
// public_html/import_student_school_2026.php
// Import av student/bal-datum per skola från CSV (by name) -> school_events (by skolenhetskod).
//
// CSV måste ha rubriker:
// lan_slug,kommun_slug,school_name,student_date,student_time,bal_date,bal_time,source_url,note
//
// Matchning sker mot tabellen schools_gym (kolumn: namn, skolenhetskod).
// Regler:
// - Endast unika matchningar skrivs (för att undvika fel).
// - 0 match eller flera match -> hamnar i rapporten som "unmatched"/"ambiguous".

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(180);
session_start();

// ============================
// SÄTT LÖSENORD HÄR
// ============================
define('ADMIN_PASSWORD', 'FridlundGren1'); // <-- ändra direkt!

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function is_post(){ return isset($_SERVER['REQUEST_METHOD']) && strtoupper($_SERVER['REQUEST_METHOD']) === 'POST'; }
function redirect_self(){ header('Location: ' . $_SERVER['PHP_SELF']); exit; }

// ---- Login ----
if (isset($_GET['logout'])) { unset($_SESSION['schimp']); redirect_self(); }

if (empty($_SESSION['schimp'])) {
  $err = '';
  if (is_post() && isset($_POST['pw'])) {
    $pw = (string)$_POST['pw'];
    if (hash_equals(ADMIN_PASSWORD, $pw)) {
      $_SESSION['schimp'] = 1;
      $_SESSION['schimp_csrf'] = bin2hex(random_bytes(16));
      redirect_self();
    } else {
      $err = 'Fel lösenord.';
    }
  }

  echo '<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
  echo '<title>Import school events 2026</title><meta name="robots" content="noindex,nofollow">';
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

if (empty($_SESSION['schimp_csrf'])) $_SESSION['schimp_csrf'] = bin2hex(random_bytes(16));
$csrf = $_SESSION['schimp_csrf'];

function require_csrf(){
  $t = isset($_POST['csrf']) ? (string)$_POST['csrf'] : '';
  if (!$t || empty($_SESSION['schimp_csrf']) || !hash_equals($_SESSION['schimp_csrf'], $t)) {
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

if (!class_exists('mysqli')) { http_response_code(500); echo "mysqli saknas"; exit; }

$mysqli = @new mysqli($host, $user, $pass, $name);
if ($mysqli->connect_errno) { http_response_code(500); echo "DB connect failed"; exit; }
@$mysqli->set_charset('utf8mb4');

// ---- Ensure school_events table exists (kompatibel med skola.php) ----
$mysqli->query("CREATE TABLE IF NOT EXISTS school_events (
  skolenhetskod VARCHAR(20) NOT NULL,
  lan_slug VARCHAR(80) NULL,
  kommun_slug VARCHAR(120) NULL,
  school_name VARCHAR(255) NULL,
  student_date DATE NULL,
  student_time VARCHAR(10) NULL,
  bal_date DATE NULL,
  bal_time VARCHAR(10) NULL,
  source_url VARCHAR(255) NULL,
  note VARCHAR(255) NULL,
  updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  PRIMARY KEY (skolenhetskod),
  INDEX idx_lan_kommun (lan_slug, kommun_slug)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4");

// ---- Load schools_gym into map (normalized name -> list of {skolenhetskod, namn}) ----
$schoolMap = []; // norm => array of rows
$totalSchools = 0;

$res = $mysqli->query("SELECT skolenhetskod, namn FROM schools_gym");
if ($res) {
  while ($r = $res->fetch_assoc()) {
    $totalSchools++;
    $namn = (string)($r['namn'] ?? '');
    $sk   = (string)($r['skolenhetskod'] ?? '');
    if ($namn === '' || $sk === '') continue;

    $norm = normalize_school_name($namn);
    if ($norm === '') continue;

    if (!isset($schoolMap[$norm])) $schoolMap[$norm] = [];
    $schoolMap[$norm][] = ['skolenhetskod' => $sk, 'namn' => $namn];
  }
  $res->free();
}

// ---- Import handler ----
$report = null;

if (is_post() && isset($_POST['do_import'])) {
  require_csrf();

  if (!isset($_FILES['csv']) || !is_uploaded_file($_FILES['csv']['tmp_name'])) {
    $report = ['error' => 'Ingen fil uppladdad.'];
  } else {
    $tmp = $_FILES['csv']['tmp_name'];
    $fh = fopen($tmp, 'rb');
    if (!$fh) {
      $report = ['error' => 'Kunde inte läsa filen.'];
    } else {
      $header = fgetcsv($fh, 0, ',');
      if (!$header || !is_array($header)) {
        $report = ['error' => 'CSV saknar rubrikrad.'];
      } else {
        $map = [];
        foreach ($header as $i => $col) {
          $col = trim((string)$col);
          $map[$col] = $i;
        }

        $required = ['lan_slug','kommun_slug','school_name','student_date','student_time','bal_date','bal_time','source_url','note'];
        foreach ($required as $r) {
          if (!array_key_exists($r, $map)) {
            $report = ['error' => 'Rubriker måste innehålla: ' . implode(', ', $required)];
            break;
          }
        }

        if (!$report) {
          $sql = "
            INSERT INTO school_events
              (skolenhetskod, lan_slug, kommun_slug, school_name, student_date, student_time, bal_date, bal_time, source_url, note)
            VALUES
              (?, ?, ?, ?, NULLIF(?,''), ?, NULLIF(?,''), ?, ?, ?)
            ON DUPLICATE KEY UPDATE
              lan_slug     = VALUES(lan_slug),
              kommun_slug  = VALUES(kommun_slug),
              school_name  = VALUES(school_name),
              student_date = VALUES(student_date),
              student_time = VALUES(student_time),
              bal_date     = VALUES(bal_date),
              bal_time     = VALUES(bal_time),
              source_url   = VALUES(source_url),
              note         = VALUES(note)
          ";
          $st = $mysqli->prepare($sql);
          if (!$st) {
            $report = ['error' => 'DB prepare misslyckades.'];
          } else {
            $updated = 0;
            $skipped = 0;
            $matched = 0;
            $unmatched = [];
            $ambiguous = [];
            $errors = [];

            $line_no = 1;

            while (($row = fgetcsv($fh, 0, ',')) !== false) {
              $line_no++;

              $lan_slug     = trim((string)($row[$map['lan_slug']] ?? ''));
              $kommun_slug  = trim((string)($row[$map['kommun_slug']] ?? ''));
              $school_name  = trim((string)($row[$map['school_name']] ?? ''));
              $student_date = trim((string)($row[$map['student_date']] ?? ''));
              $student_time = trim((string)($row[$map['student_time']] ?? ''));
              $bal_date     = trim((string)($row[$map['bal_date']] ?? ''));
              $bal_time     = trim((string)($row[$map['bal_time']] ?? ''));
              $source_url   = trim((string)($row[$map['source_url']] ?? ''));
              $note         = trim((string)($row[$map['note']] ?? ''));

              if ($school_name === '') { $skipped++; continue; }

              // Validera datum/tid om angivet
              if ($student_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $student_date)) {
                $errors[] = "Rad $line_no: ogiltigt student_date ($student_date) för $school_name";
                $skipped++; continue;
              }
              if ($bal_date !== '' && !preg_match('/^\d{4}-\d{2}-\d{2}$/', $bal_date)) {
                $errors[] = "Rad $line_no: ogiltigt bal_date ($bal_date) för $school_name";
                $skipped++; continue;
              }
              if ($student_time !== '' && !preg_match('/^\d{1,2}:\d{2}$/', $student_time)) {
                $errors[] = "Rad $line_no: ogiltigt student_time ($student_time) för $school_name";
                $skipped++; continue;
              }
              if ($bal_time !== '' && !preg_match('/^\d{1,2}:\d{2}$/', $bal_time)) {
                $errors[] = "Rad $line_no: ogiltigt bal_time ($bal_time) för $school_name";
                $skipped++; continue;
              }

              $norm = normalize_school_name($school_name);

              // Försök också en variant som tar bort vanliga ord om första missar (säker fallback)
              $cands = $schoolMap[$norm] ?? null;

              if (!$cands) {
                $norm2 = normalize_school_name(loosen_school_name($school_name));
                if ($norm2 !== '' && isset($schoolMap[$norm2])) $cands = $schoolMap[$norm2];
              }

              if (!$cands) {
                $unmatched[] = "Rad $line_no: $school_name";
                $skipped++;
                continue;
              }

              if (count($cands) !== 1) {
                // Flera träffar = risk, hoppa
                $names = [];
                foreach ($cands as $c) $names[] = $c['namn'] . ' (' . $c['skolenhetskod'] . ')';
                $ambiguous[] = "Rad $line_no: $school_name -> " . implode(' | ', $names);
                $skipped++;
                continue;
              }

              $matched++;
              $skolenhetskod = (string)$cands[0]['skolenhetskod'];

              $st->bind_param(
                'ssssssssss',
                $skolenhetskod,
                $lan_slug,
                $kommun_slug,
                $school_name,
                $student_date,
                $student_time,
                $bal_date,
                $bal_time,
                $source_url,
                $note
              );

              if ($st->execute()) $updated++;
              else {
                $errors[] = "Rad $line_no: DB execute-fel för $school_name ($skolenhetskod)";
                $skipped++;
              }
            }

            $st->close();

            $report = [
              'updated'   => $updated,
              'matched'   => $matched,
              'skipped'   => $skipped,
              'unmatched' => $unmatched,
              'ambiguous' => $ambiguous,
              'errors'    => $errors
            ];
          }
        }
      }
      fclose($fh);
    }
  }
}

// ---- Count rows in school_events ----
$countEvents = 0;
$r2 = $mysqli->query("SELECT COUNT(*) AS c FROM school_events");
if ($r2) { $x = $r2->fetch_assoc(); $countEvents = (int)($x['c'] ?? 0); $r2->free(); }

$mysqli->close();

// ---- UI ----
echo '<!doctype html><html lang="sv"><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">';
echo '<title>Import school events 2026</title><meta name="robots" content="noindex,nofollow">';
echo '<style>
  body{margin:0;background:#f4f8ff;font-family:system-ui,-apple-system,Segoe UI,Roboto,Arial,sans-serif;color:#0b1220}
  .wrap{max-width:1100px;margin:0 auto;padding:18px 14px 40px}
  .top{display:flex;justify-content:space-between;align-items:center;gap:10px;margin-bottom:12px}
  .top h1{margin:0;font-size:22px;color:#0b2a4a}
  .top a{color:#0f4c81;font-weight:900;text-decoration:none}
  .card{background:#fff;border:1px solid #e6eaf0;border-radius:16px;box-shadow:0 8px 22px rgba(0,0,0,.05);overflow:hidden;margin-bottom:14px}
  .head{padding:14px 14px 10px;border-bottom:1px solid #f0f2f6;background:linear-gradient(180deg,#eaf2ff 0%,#fff 90%);font-weight:950;color:#0b2a4a}
  .pad{padding:14px}
  label{display:block;font-weight:900;margin:0 0 6px}
  input[type=file]{width:100%;padding:10px 12px;border:1px solid #e6eaf0;border-radius:12px;background:#fff}
  button{margin-top:10px;padding:10px 12px;border-radius:12px;border:1px solid rgba(0,0,0,.06);background:#f5c542;font-weight:950;cursor:pointer}
  .ok{color:#1a6b2d;font-weight:900}
  .err{color:#8a1435;font-weight:900}
  .mono{font-family:ui-monospace,SFMono-Regular,Menlo,Monaco,Consolas,"Liberation Mono",monospace;font-size:12px;color:#5b6472;white-space:pre-wrap}
  .box{border:1px solid #eef1f6;border-radius:12px;padding:10px;background:#fbfcff}
  .grid{display:grid;grid-template-columns:1fr 1fr;gap:14px}
  @media(max-width:980px){.grid{grid-template-columns:1fr}}
</style></head><body><div class="wrap">';

echo '<div class="top"><h1>Import skol-datum (student/bal) → school_events</h1><div><a href="'.h($_SERVER['PHP_SELF']).'?logout=1">Logga ut</a></div></div>';

echo '<div class="card"><div class="head">Status</div><div class="pad">';
echo '<p style="margin:0;color:#5b6472">schools_gym laddade: <strong>'.(int)$totalSchools.'</strong> • school_events rader: <strong>'.(int)$countEvents.'</strong></p>';
echo '</div></div>';

echo '<div class="card"><div class="head">Ladda upp CSV</div><div class="pad">';
echo '<div class="box mono">';
echo "CSV-rubriker (måste vara exakt):\n";
echo "lan_slug,kommun_slug,school_name,student_date,student_time,bal_date,bal_time,source_url,note\n";
echo "Exempel:\n";
echo "skane-lan,lunds-kommun,Polhemskolan,2026-06-09,,,,https://lund.se/... ,\"Kommunen listar datum per skola\"\n";
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
    echo '<p class="ok" style="margin-top:12px;">Klart! Updated: '.(int)$report['updated'].' • Matched: '.(int)$report['matched'].' • Skipped: '.(int)$report['skipped'].'</p>';

    echo '<div class="grid" style="margin-top:12px;">';

    echo '<div class="box"><div style="font-weight:950;margin-bottom:6px;">Unmatched (0 träff)</div>';
    if (!empty($report['unmatched'])) echo '<div class="mono">'.h(implode("\n", array_slice($report['unmatched'], 0, 200))).'</div>';
    else echo '<div class="mono">—</div>';
    echo '</div>';

    echo '<div class="box"><div style="font-weight:950;margin-bottom:6px;">Ambiguous (flera träffar)</div>';
    if (!empty($report['ambiguous'])) echo '<div class="mono">'.h(implode("\n\n", array_slice($report['ambiguous'], 0, 100))).'</div>';
    else echo '<div class="mono">—</div>';
    echo '</div>';

    echo '</div>';

    if (!empty($report['errors'])) {
      echo '<div class="box" style="margin-top:14px;"><div style="font-weight:950;margin-bottom:6px;color:#8a1435;">Errors</div>';
      echo '<div class="mono">'.h(implode("\n", array_slice($report['errors'], 0, 200))).'</div></div>';
    }
  }
}

echo '</div></div>';

echo '</div></body></html>';

// ---- Name normalization helpers ----
function normalize_school_name($name){
  $s = (string)$name;
  $s = trim($s);
  if ($s === '') return '';

  // ta bort parentesinnehåll: "Katedralskolan (Linköping)" -> "Katedralskolan"
  $s = preg_replace('/\s*\([^)]*\)\s*/u', ' ', $s);

  // lowercase (utf8)
  if (function_exists('mb_strtolower')) $s = mb_strtolower($s, 'UTF-8');
  else $s = strtolower($s);

  // ersätt svenska tecken till ASCII-variant för robusthet
  $s = str_replace(['å','ä','ö','é','è','ü','ñ'], ['a','a','o','e','e','u','n'], $s);

  // ersätt & med "och"
  $s = str_replace(['&'], [' och '], $s);

  // ta bort allt utom bokstäver/siffror som mellanslag
  $s = preg_replace('/[^a-z0-9]+/u', ' ', $s);

  // normalisera whitespace
  $s = preg_replace('/\s+/u', ' ', $s);
  $s = trim($s);

  return $s;
}

function loosen_school_name($name){
  // En försiktig fallback som tar bort vissa vanliga suffix.
  // Vi använder den bara om första match misslyckas.
  $s = (string)$name;
  $s = preg_replace('/\s*\([^)]*\)\s*/u', ' ', $s);

  $remove = [
    ' gymnasium', ' gymnasiet', ' gymnasieskola', ' gymnasieskolan',
    ' anpassad', ' ag', ' vux', ' vuxenutbildning'
  ];

  if (function_exists('mb_strtolower')) $low = mb_strtolower($s, 'UTF-8'); else $low = strtolower($s);
  foreach ($remove as $r) {
    // ta bort bara i slutet eller som helord-ish
    $low = preg_replace('/'.preg_quote($r,'/').'\b/u', '', $low);
  }
  return trim($low);
}