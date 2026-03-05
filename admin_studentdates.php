<?php
// public_html/admin_studentdates.php
// Admin-light för studentdatum (PHP 5.6, mysqli, bind_result).
// 1) CSV-import per skola
// 2) Kommun-bulk: sätt student_date + source_url för alla skolor i en kommun (ingen bal auto)
// Auth: Basic Auth i PHP (admin_studentdates_auth.php).

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(120);
error_reporting(0);
ini_set('display_errors', '0');

define('BASEPATH', __DIR__ . '/system/');

// ---- Auth (Basic) ----
require __DIR__ . '/admin_studentdates_auth.php';
auth_require_basic($ADMIN_USER, $ADMIN_PASS);

// ---- Helpers ----
function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function base_url_guess(){
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  $scheme = $https ? 'https' : 'http';
  $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
  return $scheme . '://' . $host;
}

function current_student_year(){
  $oldTz = @date_default_timezone_get();
  @date_default_timezone_set('Europe/Stockholm');

  $y = (int)date('Y');
  $m = (int)date('n');
  $d = (int)date('j');
  // Byter 1 juli
  if ($m > 7 || ($m == 7 && $d >= 1)) $y = $y + 1;

  @date_default_timezone_set($oldTz);
  return $y;
}

function normalize_date($s){
  $s = trim((string)$s);
  if ($s === '') return '';
  $s = str_replace('/', '-', $s);
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return '__INVALID__';
  $ts = strtotime($s);
  if (!$ts) return '__INVALID__';
  return date('Y-m-d', $ts);
}

function normalize_time($s){
  $s = trim((string)$s);
  if ($s === '') return '';
  if (!preg_match('/^\d{1,2}:\d{2}$/', $s)) return '__INVALID__';
  $parts = explode(':', $s);
  $h = (int)$parts[0];
  $m = (int)$parts[1];
  if ($h < 0 || $h > 23) return '__INVALID__';
  if ($m < 0 || $m > 59) return '__INVALID__';
  return sprintf('%02d:%02d', $h, $m);
}

function table_exists($mysqli, $table){
  $table = (string)$table;
  if (!preg_match('/^[a-zA-Z0-9_]+$/', $table)) return false;
  $sql = "SHOW TABLES LIKE '" . $mysqli->real_escape_string($table) . "'";
  $res = $mysqli->query($sql);
  if (!$res) return false;
  $ok = ($res->num_rows > 0);
  $res->free();
  return $ok;
}

// ---- Load CI db config ----
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { http_response_code(500); echo "500"; exit; }
require $ci_db_config;
if (!isset($db) || !isset($db['default'])) { http_response_code(500); echo "500"; exit; }

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) { http_response_code(500); echo "DB error"; exit; }
@mysqli_set_charset($mysqli, 'utf8mb4');

// Ensure table exists
if (!table_exists($mysqli, 'school_student_dates')) {
  $mysqli->close();
  echo "<h1>Tabell saknas</h1>";
  echo "<p>Skapa tabellen <code>school_student_dates</code> i databasen <code>".h($dbc['database'])."</code>.</p>";
  exit;
}

// ---- UI state ----
$defaultYear = current_student_year();
$student_year = isset($_POST['student_year']) ? (int)$_POST['student_year'] : $defaultYear;
if ($student_year < 2020 || $student_year > 2100) $student_year = $defaultYear;

// ---- CSV states ----
$dry_run = (isset($_POST['dry_run']) && $_POST['dry_run'] === '1') ? 1 : 0;
$do_import = (isset($_POST['do_import']) && $_POST['do_import'] === '1') ? 1 : 0;

// ---- Kommun-bulk states ----
$dry_bulk = (isset($_POST['dry_bulk']) && $_POST['dry_bulk'] === '1') ? 1 : 0;
$do_bulk  = (isset($_POST['do_bulk']) && $_POST['do_bulk'] === '1') ? 1 : 0;

$bulk_lan_slug   = isset($_POST['bulk_lan_slug']) ? trim((string)$_POST['bulk_lan_slug']) : '';
$bulk_kommun_slug= isset($_POST['bulk_kommun_slug']) ? trim((string)$_POST['bulk_kommun_slug']) : '';
$bulk_student_date = isset($_POST['bulk_student_date']) ? trim((string)$_POST['bulk_student_date']) : '';
$bulk_source_url   = isset($_POST['bulk_source_url']) ? trim((string)$_POST['bulk_source_url']) : '';

// ---- CSV expected columns ----
$expected = array('skolenhetskod','student_date','student_time','bal_date','bal_time','source_url');

// ---- Reports ----
$report = array(
  'rows_total' => 0,
  'rows_ok' => 0,
  'inserted_or_updated' => 0,
  'skipped_missing_school' => 0,
  'skipped_invalid' => 0,
  'errors' => array(),
  'preview' => array(),
);

$bulk_report = array(
  'schools_found' => 0,
  'schools_updated' => 0,
  'errors' => array(),
  'preview' => array(),
);

function school_exists($mysqli, $code){
  $sql = "SELECT skolenhetskod FROM schools_gym WHERE skolenhetskod = ? LIMIT 1";
  $stmt = $mysqli->prepare($sql);
  if (!$stmt) return false;
  $stmt->bind_param('s', $code);
  $stmt->execute();
  $stmt->store_result();
  $ok = ($stmt->num_rows > 0);
  $stmt->close();
  return $ok;
}

// ================================
// 1) CSV import
// ================================
if (($dry_run || $do_import) && isset($_FILES['csv_file']) && is_uploaded_file($_FILES['csv_file']['tmp_name'])) {
  $tmp = $_FILES['csv_file']['tmp_name'];

  $fh = fopen($tmp, 'r');
  if ($fh === false) {
    $report['errors'][] = "Kunde inte läsa CSV-filen.";
  } else {
    $header = fgetcsv($fh, 0, ',');
    if ($header === false) {
      $report['errors'][] = "CSV saknar header-rad.";
    } else {
      $map = array();
      for ($i=0; $i<count($header); $i++) {
        $key = strtolower(trim((string)$header[$i]));
        $map[$key] = $i;
      }

      for ($i=0; $i<count($expected); $i++) {
        if (!isset($map[$expected[$i]])) {
          $report['errors'][] = "Saknar kolumn i CSV: " . $expected[$i];
        }
      }

      if (count($report['errors']) === 0) {

        $sqlUp = "INSERT INTO school_student_dates
          (skolenhetskod, student_year, student_date, student_time, bal_date, bal_time, source_url)
          VALUES (?, ?, ?, ?, ?, ?, ?)
          ON DUPLICATE KEY UPDATE
            student_date = VALUES(student_date),
            student_time = VALUES(student_time),
            bal_date = VALUES(bal_date),
            bal_time = VALUES(bal_time),
            source_url = VALUES(source_url),
            updated_at = CURRENT_TIMESTAMP";

        $stmtUp = $mysqli->prepare($sqlUp);
        if (!$stmtUp) {
          $report['errors'][] = "DB prepare failed: ".$mysqli->error;
        } else {

          while (($row = fgetcsv($fh, 0, ',')) !== false) {
            $report['rows_total']++;

            $code = preg_replace('/[^0-9]/', '', trim((string)$row[$map['skolenhetskod']]));

            $sd = normalize_date($row[$map['student_date']]);
            $st = normalize_time($row[$map['student_time']]);
            $bd = normalize_date($row[$map['bal_date']]);
            $bt = normalize_time($row[$map['bal_time']]);
            $su = trim((string)$row[$map['source_url']]);

            $invalid = false;
            if ($code === '') $invalid = true;
            if ($sd === '__INVALID__') $invalid = true;
            if ($st === '__INVALID__') $invalid = true;
            if ($bd === '__INVALID__') $invalid = true;
            if ($bt === '__INVALID__') $invalid = true;

            if ($invalid) {
              $report['skipped_invalid']++;
              if (count($report['preview']) < 25) {
                $report['preview'][] = array('code'=>$code,'student_date'=>$sd,'student_time'=>$st,'bal_date'=>$bd,'bal_time'=>$bt,'source_url'=>$su,'status'=>'SKIPPED_INVALID');
              }
              continue;
            }

            if (!school_exists($mysqli, $code)) {
              $report['skipped_missing_school']++;
              if (count($report['preview']) < 25) {
                $report['preview'][] = array('code'=>$code,'student_date'=>$sd,'student_time'=>$st,'bal_date'=>$bd,'bal_time'=>$bt,'source_url'=>$su,'status'=>'SKIPPED_MISSING_SCHOOL');
              }
              continue;
            }

            $sdParam = ($sd === '') ? NULL : $sd;
            $stParam = ($st === '') ? NULL : $st;
            $bdParam = ($bd === '') ? NULL : $bd;
            $btParam = ($bt === '') ? NULL : $bt;
            $suParam = ($su === '') ? NULL : $su;

            $report['rows_ok']++;

            if ($do_import) {
              $y = $student_year;
              $c = $code;
              $sdv = $sdParam;
              $stv = $stParam;
              $bdv = $bdParam;
              $btv = $btParam;
              $suv = $suParam;

              $stmtUp->bind_param('sisssss', $c, $y, $sdv, $stv, $bdv, $btv, $suv);
              $ok = $stmtUp->execute();
              if ($ok) {
                $report['inserted_or_updated']++;
                if (count($report['preview']) < 25) {
                  $report['preview'][] = array('code'=>$code,'student_date'=>$sd,'student_time'=>$st,'bal_date'=>$bd,'bal_time'=>$bt,'source_url'=>$su,'status'=>'IMPORTED');
                }
              } else {
                $report['errors'][] = "DB execute error (code {$code}): ".$stmtUp->error;
              }
            } else {
              if (count($report['preview']) < 25) {
                $report['preview'][] = array('code'=>$code,'student_date'=>$sd,'student_time'=>$st,'bal_date'=>$bd,'bal_time'=>$bt,'source_url'=>$su,'status'=>'OK (DRY RUN)');
              }
            }
          }

          $stmtUp->close();
        }
      }
    }

    fclose($fh);
  }
}

// ================================
// 2) Kommun-bulk: student_date + source_url
// ================================
if ($dry_bulk || $do_bulk) {
  // Validera slugs enkelt
  if ($bulk_lan_slug === '' || !preg_match('/^[a-z0-9\-]+$/', $bulk_lan_slug)) {
    $bulk_report['errors'][] = 'Ogiltig lan_slug.';
  }
  if ($bulk_kommun_slug === '' || !preg_match('/^[a-z0-9\-]+$/', $bulk_kommun_slug)) {
    $bulk_report['errors'][] = 'Ogiltig kommun_slug.';
  }

  $sd = normalize_date($bulk_student_date);
  if ($sd === '' || $sd === '__INVALID__') {
    $bulk_report['errors'][] = 'Ogiltigt studentdatum. Använd YYYY-MM-DD.';
  }

  // source_url får vara tom (men vi vill oftast ha den)
  if ($bulk_source_url !== '' && !preg_match('#^https?://#i', $bulk_source_url)) {
    $bulk_report['errors'][] = 'source_url måste börja med http:// eller https:// (eller vara tom).';
  }

  if (count($bulk_report['errors']) === 0) {

    // Hämta alla skolenhetskod i kommunen
    $sqlS = "SELECT skolenhetskod, namn FROM schools_gym WHERE lan_slug = ? AND kommun_slug = ? ORDER BY namn ASC";
    $stS = $mysqli->prepare($sqlS);
    if (!$stS) {
      $bulk_report['errors'][] = 'DB prepare failed (schools): '.$mysqli->error;
    } else {
      $stS->bind_param('ss', $bulk_lan_slug, $bulk_kommun_slug);

      if (!$stS->execute()) {
        $bulk_report['errors'][] = 'DB execute failed (schools): '.$stS->error;
      } else {
        $sc = '';
        $sn = '';
        $stS->bind_result($sc, $sn);

        // Prepare upsert
        $sqlUp2 = "INSERT INTO school_student_dates
          (skolenhetskod, student_year, student_date, student_time, bal_date, bal_time, source_url)
          VALUES (?, ?, ?, NULL, NULL, NULL, ?)
          ON DUPLICATE KEY UPDATE
            student_date = VALUES(student_date),
            source_url = VALUES(source_url),
            updated_at = CURRENT_TIMESTAMP";

        $stUp2 = $mysqli->prepare($sqlUp2);
        if (!$stUp2) {
          $bulk_report['errors'][] = 'DB prepare failed (upsert): '.$mysqli->error;
        } else {

          while ($stS->fetch()) {
            $sc = trim((string)$sc);
            $sn = trim((string)$sn);
            if ($sc === '') continue;

            $bulk_report['schools_found']++;

            if (count($bulk_report['preview']) < 25) {
              $bulk_report['preview'][] = array('code'=>$sc,'name'=>$sn,'date'=>$sd,'source'=>$bulk_source_url,'status'=>($do_bulk ? 'WILL_IMPORT' : 'OK (DRY RUN)'));
            }

            if ($do_bulk) {
              $y = $student_year;
              $code2 = $sc;
              $date2 = $sd;
              $src2 = ($bulk_source_url === '') ? NULL : $bulk_source_url;

              $stUp2->bind_param('siss', $code2, $y, $date2, $src2);
              $ok = $stUp2->execute();
              if ($ok) {
                $bulk_report['schools_updated']++;
              } else {
                // samla inte för mycket spam
                if (count($bulk_report['errors']) < 10) {
                  $bulk_report['errors'][] = 'DB execute error ('.$code2.'): '.$stUp2->error;
                }
              }
            }
          }

          $stUp2->close();
        }
      }

      $stS->close();
    }
  }
}

$base = rtrim(base_url_guess(), '/');
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Studentdatum</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#fff;color:#111}
    .wrap{max-width:1100px;margin:0 auto;padding:18px}
    .card{border:1px solid #e6e6e6;border-radius:12px;padding:14px;margin:12px 0;background:#fafafa}
    .btn{display:inline-block;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;border:none;cursor:pointer}
    .btn2{display:inline-block;padding:10px 12px;border-radius:8px;background:#1a5fb4;color:#fff;text-decoration:none;border:none;cursor:pointer}
    .muted{color:#666}
    table{border-collapse:collapse;width:100%;background:#fff}
    th,td{border:1px solid #eee;padding:8px;text-align:left;font-size:14px}
    th{background:#f7f7f7}
    code{background:#fff;padding:2px 5px;border-radius:6px;border:1px solid #eee}
    .err{background:#fff2f2;border:1px solid #ffd4d4;color:#7a0000}
    .ok{background:#e9f7ec;border:1px solid #ccebd3;color:#0a7a0a}
    .warn{background:#fffbea;border:1px solid #ffe69c;color:#8a5a00}
    input[type="number"], input[type="text"]{padding:8px;border:1px solid #ddd;border-radius:8px;min-width:240px}
    input[type="file"]{padding:6px}
    label{display:inline-block;margin-right:12px;margin-bottom:6px}
    .row{display:flex;gap:10px;flex-wrap:wrap;align-items:center}
    .small{font-size:12px}
  </style>
</head>
<body>
<div class="wrap">

  <h1 style="margin:0">Admin – Studentdatum</h1>
  <p class="muted" style="margin:8px 0 0">CSV-import per skola + kommun-bulk för att fylla studentdatum snabbt. (Bal sätts <strong>inte</strong> automatiskt.)</p>

  <div class="card">
    <h2 style="margin-top:0">Kommun-bulk (rekommenderat)</h2>
    <p class="muted">Sätter <code>student_date</code> + <code>source_url</code> för <strong>alla skolor</strong> i vald kommun (lan_slug + kommun_slug), för valt studentår. Lämnar tider + bal tomt.</p>

    <form method="post">
      <p>
        <label>Studentår
          <input type="number" name="student_year" value="<?php echo (int)$student_year; ?>" min="2020" max="2100">
        </label>
      </p>

      <div class="row">
        <label>lan_slug<br>
          <input type="text" name="bulk_lan_slug" value="<?php echo h($bulk_lan_slug); ?>" placeholder="t.ex. blekinge-lan">
        </label>
        <label>kommun_slug<br>
          <input type="text" name="bulk_kommun_slug" value="<?php echo h($bulk_kommun_slug); ?>" placeholder="t.ex. karlshamn">
        </label>
        <label>student_date<br>
          <input type="text" name="bulk_student_date" value="<?php echo h($bulk_student_date); ?>" placeholder="YYYY-MM-DD">
        </label>
        <label>source_url (valfritt men bra)<br>
          <input type="text" name="bulk_source_url" value="<?php echo h($bulk_source_url); ?>" placeholder="https://...">
        </label>
      </div>

      <p class="muted small">Tips: använd kommunens läsårstider som källa. Exempel: Borlänge anger "Årskurs 3 studenten: 12 juni".</p>

      <p>
        <button class="btn2" type="submit" name="dry_bulk" value="1">Kör dry-run (kommun)</button>
        <button class="btn" type="submit" name="do_bulk" value="1" onclick="return confirm('Sätta studentdatum för ALLA skolor i kommunen för valt år?');">Sätt datum för kommunen</button>
      </p>
    </form>

    <?php if (($dry_bulk || $do_bulk) && count($bulk_report['errors']) > 0) { ?>
      <div class="card err" style="margin:10px 0 0">
        <h3 style="margin-top:0">Fel (kommun-bulk)</h3>
        <ul>
          <?php for ($i=0; $i<count($bulk_report['errors']); $i++) { ?>
            <li><?php echo h($bulk_report['errors'][$i]); ?></li>
          <?php } ?>
        </ul>
      </div>
    <?php } ?>

    <?php if (($dry_bulk || $do_bulk) && count($bulk_report['errors']) === 0) { ?>
      <div class="card <?php echo $do_bulk ? 'ok' : 'warn'; ?>" style="margin:10px 0 0">
        <h3 style="margin-top:0"><?php echo $do_bulk ? 'Kommun-uppdatering klar' : 'Kommun dry-run klar'; ?></h3>
        <p style="margin:0">
          Skolor hittade: <strong><?php echo (int)$bulk_report['schools_found']; ?></strong>
          <?php if ($do_bulk) { ?> • Uppdaterade: <strong><?php echo (int)$bulk_report['schools_updated']; ?></strong><?php } ?>
        </p>
      </div>

      <?php if (count($bulk_report['preview']) > 0) { ?>
        <div class="card" style="margin:10px 0 0">
          <h3 style="margin-top:0">Förhandsvisning (max 25)</h3>
          <table>
            <thead>
              <tr>
                <th>skolenhetskod</th>
                <th>skola</th>
                <th>student_date</th>
                <th>source_url</th>
                <th>status</th>
              </tr>
            </thead>
            <tbody>
              <?php for ($i=0; $i<count($bulk_report['preview']); $i++) { $p = $bulk_report['preview'][$i]; ?>
                <tr>
                  <td><?php echo h($p['code']); ?></td>
                  <td><?php echo h($p['name']); ?></td>
                  <td><?php echo h($p['date']); ?></td>
                  <td><?php echo h($p['source']); ?></td>
                  <td><?php echo h($p['status']); ?></td>
                </tr>
              <?php } ?>
            </tbody>
          </table>
        </div>
      <?php } ?>

    <?php } ?>

  </div>

  <div class="card">
    <h2 style="margin-top:0">CSV-format</h2>
    <p class="muted">CSV måste ha header med exakt dessa kolumner (kommaseparerad):</p>
    <p><code>skolenhetskod,student_date,student_time,bal_date,bal_time,source_url</code></p>
    <p class="muted">Datum: <code>YYYY-MM-DD</code> (eller <code>YYYY/MM/DD</code>) • Tid: <code>HH:MM</code> • Tomma fält är ok.</p>
  </div>

  <div class="card">
    <h2 style="margin-top:0">CSV-import</h2>
    <form method="post" enctype="multipart/form-data">
      <p>
        <label>Studentår
          <input type="number" name="student_year" value="<?php echo (int)$student_year; ?>" min="2020" max="2100">
        </label>
      </p>

      <p>
        <label><input type="checkbox" name="dry_run" value="1" <?php echo $dry_run ? 'checked' : ''; ?>> Dry-run (förhandsgranska, ingen import)</label>
      </p>

      <p><input type="file" name="csv_file" accept=".csv,text/csv"></p>

      <p>
        <button class="btn2" type="submit" name="dry_run" value="1">Kör dry-run</button>
        <button class="btn" type="submit" name="do_import" value="1" onclick="return confirm('Importera nu? Detta skriver/uppdaterar rader för valt år.');">Importera</button>
      </p>
    </form>
  </div>

  <?php if (count($report['errors']) > 0) { ?>
    <div class="card err">
      <h3 style="margin-top:0">Fel</h3>
      <ul>
        <?php for ($i=0; $i<count($report['errors']); $i++) { ?>
          <li><?php echo h($report['errors'][$i]); ?></li>
        <?php } ?>
      </ul>
    </div>
  <?php } ?>

  <?php if (($dry_run || $do_import) && count($report['errors']) === 0 && $report['rows_total'] > 0) { ?>
    <div class="card <?php echo $do_import ? 'ok' : 'warn'; ?>">
      <h3 style="margin-top:0"><?php echo $do_import ? 'Import klar' : 'Dry-run klar'; ?></h3>
      <p style="margin:0">
        Rader totalt: <strong><?php echo (int)$report['rows_total']; ?></strong> •
        OK: <strong><?php echo (int)$report['rows_ok']; ?></strong> •
        Skippade (saknas i schools_gym): <strong><?php echo (int)$report['skipped_missing_school']; ?></strong> •
        Skippade (ogiltiga värden): <strong><?php echo (int)$report['skipped_invalid']; ?></strong>
        <?php if ($do_import) { ?>
          • Importerade/uppdaterade: <strong><?php echo (int)$report['inserted_or_updated']; ?></strong>
        <?php } ?>
      </p>
    </div>

    <div class="card">
      <h3 style="margin-top:0">Förhandsvisning (max 25 rader)</h3>
      <table>
        <thead>
          <tr>
            <th>skolenhetskod</th>
            <th>student_date</th>
            <th>student_time</th>
            <th>bal_date</th>
            <th>bal_time</th>
            <th>source_url</th>
            <th>status</th>
          </tr>
        </thead>
        <tbody>
          <?php for ($i=0; $i<count($report['preview']); $i++) { $p = $report['preview'][$i]; ?>
            <tr>
              <td><?php echo h($p['code']); ?></td>
              <td><?php echo h($p['student_date']); ?></td>
              <td><?php echo h($p['student_time']); ?></td>
              <td><?php echo h($p['bal_date']); ?></td>
              <td><?php echo h($p['bal_time']); ?></td>
              <td><?php echo h($p['source_url']); ?></td>
              <td><?php echo h($p['status']); ?></td>
            </tr>
          <?php } ?>
        </tbody>
      </table>
      <p class="muted" style="margin-bottom:0">När <code>source_url</code> finns kan skolsidan visa “baserat på skolans/kommunens datum” och länka källan.</p>
    </div>
  <?php } ?>

</div>
</body>
</html>
<?php $mysqli->close(); ?>
