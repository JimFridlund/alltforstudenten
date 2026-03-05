<?php
/**
 * public_html/admin/student_dates_import.php
 * Admin-light: import av studentdatum för skolsidor.
 *
 * Krav: PHP 5.6, mysqli (INTE PDO), mysqlnd saknas -> ingen get_result.
 * Vi använder bind_result för SELECT där det behövs.
 *
 * CSV-kolumner (med header):
 *  skolenhetskod,student_date,student_time,bal_date,bal_time,source_url
 *
 * Date-format: YYYY-MM-DD (t.ex. 2026-06-12)
 * Time-format: HH:MM (t.ex. 13:00)
 */

error_reporting(E_ALL & ~E_NOTICE & ~E_WARNING);
ini_set('display_errors', '0');
header('Content-Type: text/html; charset=utf-8');

define('BASEPATH', __DIR__ . '/../system/');

// ====== ENKELT LÖSENORD (byt!) ======
define('ADMIN_IMPORT_PASSWORD', 'BYT_MIG_TILL_NAGOT_STARKT');

// ====== Auth (basic via query/post) ======
$pw = '';
if (isset($_POST['pw'])) $pw = (string)$_POST['pw'];
elseif (isset($_GET['pw'])) $pw = (string)$_GET['pw'];

$authed = ($pw !== '' && hash_equals(ADMIN_IMPORT_PASSWORD, $pw));

// ====== DB-config (CI 1.x) ======
$ci_db_config = __DIR__ . '/../system/application/config/database.php';
if (!file_exists($ci_db_config)) {
    http_response_code(500);
    echo "500";
    exit;
}
require $ci_db_config;

if (!isset($db) || !isset($db['default'])) {
    http_response_code(500);
    echo "500";
    exit;
}

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) {
    http_response_code(500);
    echo "500";
    exit;
}
@mysqli_set_charset($mysqli, 'utf8');

// ====== Helpers ======
function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }

function normalize_date($s) {
    $s = trim((string)$s);
    if ($s === '') return '';
    // Accept YYYY-MM-DD only
    if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $s)) return '';
    $ts = strtotime($s);
    if (!$ts) return '';
    return date('Y-m-d', $ts);
}

function normalize_time($s) {
    $s = trim((string)$s);
    if ($s === '') return '';
    // Accept H:MM or HH:MM, normalize to HH:MM
    if (!preg_match('/^\d{1,2}:\d{2}$/', $s)) return '';
    list($hh, $mm) = explode(':', $s, 2);
    $hh = (int)$hh;
    $mm = (int)$mm;
    if ($hh < 0 || $hh > 23) return '';
    if ($mm < 0 || $mm > 59) return '';
    return sprintf('%02d:%02d', $hh, $mm);
}

function year_from_date($ymd) {
    if (!$ymd) return 0;
    if (!preg_match('/^(\d{4})-/', $ymd, $m)) return 0;
    return (int)$m[1];
}

function detect_delimiter($firstLine) {
    // Very simple: choose the delimiter that appears most.
    $c = substr_count($firstLine, ',');
    $s = substr_count($firstLine, ';');
    $t = substr_count($firstLine, "\t");
    if ($s >= $c && $s >= $t) return ';';
    if ($t >= $c && $t >= $s) return "\t";
    return ',';
}

function table_exists($mysqli, $table) {
    $table = preg_replace('/[^a-zA-Z0-9_]/', '', $table);
    $sql = "SHOW TABLES LIKE ?";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param('s', $table);
    $stmt->execute();
    $stmt->store_result();
    $ok = ($stmt->num_rows > 0);
    $stmt->close();
    return $ok;
}

// ====== Basic UI ======
if (!$authed) {
    echo "<!doctype html><html><head><meta charset='utf-8'><title>Import studentdatum</title>
    <style>body{font-family:Arial,Helvetica,sans-serif;margin:20px} .box{max-width:720px} input{padding:8px} .btn{padding:10px 12px;background:#111;color:#fff;border:0;border-radius:6px;cursor:pointer}</style>
    </head><body><div class='box'>
    <h1>Import studentdatum</h1>
    <p>Logga in med lösenord.</p>
    <form method='post'>
      <label>Lösenord</label><br>
      <input type='password' name='pw' style='width:320px' autofocus>
      <div style='height:12px'></div>
      <button class='btn' type='submit'>Fortsätt</button>
    </form>
    </div></body></html>";
    $mysqli->close();
    exit;
}

if (!table_exists($mysqli, 'school_student_dates')) {
    http_response_code(500);
    echo "<p><strong>Tabellen school_student_dates finns inte.</strong> Skapa den via SQL-schemat först.</p>";
    $mysqli->close();
    exit;
}

$action = isset($_POST['action']) ? (string)$_POST['action'] : '';

// ====== PREVIEW/IMPORT WORKFLOW ======
$errors = array();
$warnings = array();
$rowsPreview = array();
$importResult = array(
    'inserted' => 0,
    'updated' => 0,
    'skipped' => 0,
    'invalid' => 0
);

function load_csv_rows($tmpPath, &$errors, &$warnings) {
    $rows = array();
    if (!file_exists($tmpPath)) {
        $errors[] = "Filen saknas (upload-fel).";
        return $rows;
    }

    $fh = fopen($tmpPath, 'r');
    if (!$fh) {
        $errors[] = "Kunde inte läsa filen.";
        return $rows;
    }

    $firstLine = fgets($fh);
    if ($firstLine === false) {
        $errors[] = "CSV är tom.";
        fclose($fh);
        return $rows;
    }

    $delimiter = detect_delimiter($firstLine);
    // Rewind and parse via fgetcsv
    rewind($fh);

    $header = fgetcsv($fh, 0, $delimiter);
    if (!$header || count($header) < 2) {
        $errors[] = "Kunde inte läsa header-raden. Kontrollera delimiter (, ; eller tab).";
        fclose($fh);
        return $rows;
    }

    // Normalize headers
    for ($i=0; $i<count($header); $i++) {
        $header[$i] = trim(mb_strtolower($header[$i], 'UTF-8'));
    }

    $required = array('skolenhetskod','student_date','student_time','bal_date','bal_time','source_url');
    $map = array();
    for ($i=0; $i<count($header); $i++) {
        $map[$header[$i]] = $i;
    }

    for ($r=0; $r<count($required); $r++) {
        if (!isset($map[$required[$r]])) {
            $errors[] = "Saknar kolumn: " . $required[$r];
        }
    }
    if (count($errors) > 0) {
        fclose($fh);
        return $rows;
    }

    $lineNo = 1;
    while (($cols = fgetcsv($fh, 0, $delimiter)) !== false) {
        $lineNo++;
        // Skip empty lines
        $allEmpty = true;
        for ($k=0; $k<count($cols); $k++) {
            if (trim((string)$cols[$k]) !== '') { $allEmpty = false; break; }
        }
        if ($allEmpty) continue;

        $row = array(
            'line' => $lineNo,
            'skolenhetskod' => trim((string)$cols[$map['skolenhetskod']]),
            'student_date'  => trim((string)$cols[$map['student_date']]),
            'student_time'  => trim((string)$cols[$map['student_time']]),
            'bal_date'      => trim((string)$cols[$map['bal_date']]),
            'bal_time'      => trim((string)$cols[$map['bal_time']]),
            'source_url'    => trim((string)$cols[$map['source_url']])
        );
        $rows[] = $row;
        if (count($rows) >= 5000) {
            $warnings[] = "CSV är stor (>=5000 rader). Import går, men kan ta tid.";
            break;
        }
    }

    fclose($fh);
    return $rows;
}

function school_exists($mysqli, $skolenhetskod) {
    $sql = "SELECT skolenhetskod FROM schools_gym WHERE skolenhetskod = ? LIMIT 1";
    $stmt = $mysqli->prepare($sql);
    if (!$stmt) return false;
    $stmt->bind_param('s', $skolenhetskod);
    $stmt->execute();
    $stmt->store_result();
    $exists = ($stmt->num_rows > 0);
    // bind_result required by policy? Here we only need num_rows; keep it safe anyway.
    $tmp = '';
    if ($exists) {
        $stmt->bind_result($tmp);
        $stmt->fetch();
    }
    $stmt->close();
    return $exists;
}

function upsert_date_row($mysqli, $skolenhetskod, $student_year, $student_date, $student_time, $bal_date, $bal_time, $source_url, &$didUpdate) {
    $didUpdate = false;

    // Check existing
    $sqlSel = "SELECT id FROM school_student_dates WHERE skolenhetskod = ? AND student_year = ? LIMIT 1";
    $stmtSel = $mysqli->prepare($sqlSel);
    if (!$stmtSel) return false;

    $stmtSel->bind_param('si', $skolenhetskod, $student_year);
    $stmtSel->execute();
    $stmtSel->store_result();

    $existingId = 0;
    if ($stmtSel->num_rows > 0) {
        $stmtSel->bind_result($existingId);
        $stmtSel->fetch();
    }
    $stmtSel->close();

    $now = date('Y-m-d H:i:s');

    if ($existingId > 0) {
        $sqlUpd = "UPDATE school_student_dates
                   SET student_date = ?, student_time = ?, bal_date = ?, bal_time = ?, source_url = ?, updated_at = ?
                   WHERE id = ?
                   LIMIT 1";
        $stmtUpd = $mysqli->prepare($sqlUpd);
        if (!$stmtUpd) return false;
        $stmtUpd->bind_param('ssssssi', $student_date, $student_time, $bal_date, $bal_time, $source_url, $now, $existingId);
        $ok = $stmtUpd->execute();
        $stmtUpd->close();
        $didUpdate = true;
        return $ok;
    } else {
        $sqlIns = "INSERT INTO school_student_dates
                   (skolenhetskod, student_year, student_date, student_time, bal_date, bal_time, source_url, created_at, updated_at)
                   VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)";
        $stmtIns = $mysqli->prepare($sqlIns);
        if (!$stmtIns) return false;
        $stmtIns->bind_param('sisssssss', $skolenhetskod, $student_year, $student_date, $student_time, $bal_date, $bal_time, $source_url, $now, $now);
        $ok = $stmtIns->execute();
        $stmtIns->close();
        return $ok;
    }
}

// ====== Handle upload -> preview ======
if ($action === 'preview' || $action === 'import') {
    if (!isset($_FILES['csv']) || !is_uploaded_file($_FILES['csv']['tmp_name'])) {
        $errors[] = "Ingen fil uppladdad.";
    } else {
        $rows = load_csv_rows($_FILES['csv']['tmp_name'], $errors, $warnings);

        // Validate rows + build preview
        if (count($errors) === 0) {
            $maxPreview = 30;
            $validCount = 0;
            $invalidCount = 0;

            for ($i=0; $i<count($rows); $i++) {
                $r = $rows[$i];

                $code = preg_replace('/[^0-9]/', '', $r['skolenhetskod']);
                $sd = normalize_date($r['student_date']);
                $st = normalize_time($r['student_time']);
                $bd = normalize_date($r['bal_date']);
                $bt = normalize_time($r['bal_time']);
                $su = trim((string)$r['source_url']);

                $rowErrors = array();

                if ($code === '') $rowErrors[] = "Ogiltig skolenhetskod";
                if ($sd === '') $rowErrors[] = "student_date måste vara YYYY-MM-DD";
                $year = year_from_date($sd);
                if ($year <= 2000) $rowErrors[] = "Kunde inte läsa år från student_date";

                // Time fields are optional, but if present must be valid; normalize_time returns '' if invalid.
                if (trim((string)$r['student_time']) !== '' && $st === '') $rowErrors[] = "student_time ogiltig (HH:MM)";
                if (trim((string)$r['bal_time']) !== '' && $bt === '') $rowErrors[] = "bal_time ogiltig (HH:MM)";

                // If bal_date present must be valid
                if (trim((string)$r['bal_date']) !== '' && $bd === '') $rowErrors[] = "bal_date ogiltig (YYYY-MM-DD)";

                // Check school exists (only if basic validation ok)
                if (count($rowErrors) === 0) {
                    if (!school_exists($mysqli, $code)) {
                        $rowErrors[] = "Skolenhetskod finns inte i schools_gym";
                    }
                }

                if (count($rowErrors) > 0) {
                    $invalidCount++;
                } else {
                    $validCount++;
                }

                if (count($rowsPreview) < $maxPreview) {
                    $rowsPreview[] = array(
                        'line' => $r['line'],
                        'skolenhetskod' => $code,
                        'student_year' => $year,
                        'student_date' => $sd,
                        'student_time' => $st,
                        'bal_date' => $bd,
                        'bal_time' => $bt,
                        'source_url' => $su,
                        'status' => (count($rowErrors) > 0 ? ('FEL: ' . implode(', ', $rowErrors)) : 'OK')
                    );
                }
            }

            if ($action === 'import' && count($errors) === 0) {
                // Import all valid rows in a transaction
                $mysqli->begin_transaction();

                $okAll = true;
                for ($i=0; $i<count($rows); $i++) {
                    $r = $rows[$i];

                    $code = preg_replace('/[^0-9]/', '', $r['skolenhetskod']);
                    $sd = normalize_date($r['student_date']);
                    $st = normalize_time($r['student_time']);
                    $bd = normalize_date($r['bal_date']);
                    $bt = normalize_time($r['bal_time']);
                    $su = trim((string)$r['source_url']);

                    $rowErrors = array();
                    if ($code === '') $rowErrors[] = "bad code";
                    if ($sd === '') $rowErrors[] = "bad student_date";
                    $year = year_from_date($sd);
                    if ($year <= 2000) $rowErrors[] = "bad year";
                    if (trim((string)$r['student_time']) !== '' && $st === '') $rowErrors[] = "bad student_time";
                    if (trim((string)$r['bal_time']) !== '' && $bt === '') $rowErrors[] = "bad bal_time";
                    if (trim((string)$r['bal_date']) !== '' && $bd === '') $rowErrors[] = "bad bal_date";
                    if (count($rowErrors) === 0 && !school_exists($mysqli, $code)) $rowErrors[] = "school missing";

                    if (count($rowErrors) > 0) {
                        $importResult['invalid']++;
                        continue;
                    }

                    $didUpdate = false;
                    $ok = upsert_date_row($mysqli, $code, $year, $sd, $st, $bd, $bt, $su, $didUpdate);
                    if (!$ok) {
                        $okAll = false;
                        $errors[] = "DB-fel vid rad " . $r['line'] . " (skolenhetskod " . h($code) . ")";
                        break;
                    } else {
                        if ($didUpdate) $importResult['updated']++;
                        else $importResult['inserted']++;
                    }
                }

                if ($okAll) {
                    $mysqli->commit();
                } else {
                    $mysqli->rollback();
                }
            }
        }
    }
}

// ====== Render page ======
?>
<!doctype html>
<html lang="sv">
<head>
    <meta charset="utf-8">
    <title>Import studentdatum</title>
    <style>
        body{font-family:Arial,Helvetica,sans-serif;margin:20px;color:#111}
        .box{max-width:980px}
        .card{border:1px solid #e6e6e6;border-radius:10px;padding:14px;margin:12px 0;background:#fafafa}
        .btn{padding:10px 12px;background:#111;color:#fff;border:0;border-radius:6px;cursor:pointer}
        .muted{color:#666}
        table{border-collapse:collapse;width:100%}
        th,td{border-bottom:1px solid #e6e6e6;padding:8px;text-align:left;font-size:13px;vertical-align:top}
        th{background:#fff}
        .ok{color:#0a7a0a;font-weight:bold}
        .bad{color:#b00020;font-weight:bold}
        code{background:#fff;border:1px solid #eee;border-radius:6px;padding:2px 6px}
        input[type=file]{padding:8px;background:#fff;border:1px solid #ddd;border-radius:6px}
        input[type=password]{padding:8px;border:1px solid #ddd;border-radius:6px}
    </style>
</head>
<body>
<div class="box">
    <h1>Import studentdatum (skolsidor)</h1>
    <p class="muted">
        CSV med header: <code>skolenhetskod,student_date,student_time,bal_date,bal_time,source_url</code><br>
        Datum: <code>YYYY-MM-DD</code> • Tid: <code>HH:MM</code>
    </p>

    <div class="card">
        <form method="post" enctype="multipart/form-data">
            <input type="hidden" name="pw" value="<?php echo h($pw); ?>">
            <div style="margin:8px 0">
                <label><strong>CSV-fil</strong></label><br>
                <input type="file" name="csv" accept=".csv,text/csv">
            </div>
            <div style="display:flex;gap:10px;flex-wrap:wrap">
                <button class="btn" type="submit" name="action" value="preview">Förhandsgranska</button>
                <button class="btn" type="submit" name="action" value="import">Importera</button>
            </div>
        </form>
    </div>

    <?php if (count($errors) > 0) { ?>
        <div class="card" style="background:#fff5f5">
            <h2>Fel</h2>
            <ul>
                <?php for ($i=0; $i<count($errors); $i++) { ?>
                    <li class="bad"><?php echo h($errors[$i]); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if (count($warnings) > 0) { ?>
        <div class="card" style="background:#fffbea">
            <h2>Notiser</h2>
            <ul>
                <?php for ($i=0; $i<count($warnings); $i++) { ?>
                    <li><?php echo h($warnings[$i]); ?></li>
                <?php } ?>
            </ul>
        </div>
    <?php } ?>

    <?php if ($action === 'import' && count($errors) === 0) { ?>
        <div class="card">
            <h2>Importresultat</h2>
            <p>
                Inserted: <strong><?php echo (int)$importResult['inserted']; ?></strong><br>
                Updated: <strong><?php echo (int)$importResult['updated']; ?></strong><br>
                Invalid/skipped: <strong><?php echo (int)$importResult['invalid']; ?></strong>
            </p>
            <p class="muted">Tips: Besök valfri skolsida efter import och verifiera att datum syns.</p>
        </div>
    <?php } ?>

    <?php if (count($rowsPreview) > 0) { ?>
        <div class="card">
            <h2>Förhandsgranskning (max 30 rader)</h2>
            <table>
                <thead>
                <tr>
                    <th>Rad</th>
                    <th>Skolenhetskod</th>
                    <th>År</th>
                    <th>Student</th>
                    <th>Tid</th>
                    <th>Bal</th>
                    <th>Tid</th>
                    <th>Källa</th>
                    <th>Status</th>
                </tr>
                </thead>
                <tbody>
                <?php for ($i=0; $i<count($rowsPreview); $i++) {
                    $r = $rowsPreview[$i];
                    $isOk = ($r['status'] === 'OK');
                ?>
                    <tr>
                        <td><?php echo (int)$r['line']; ?></td>
                        <td><?php echo h($r['skolenhetskod']); ?></td>
                        <td><?php echo (int)$r['student_year']; ?></td>
                        <td><?php echo h($r['student_date']); ?></td>
                        <td><?php echo h($r['student_time']); ?></td>
                        <td><?php echo h($r['bal_date']); ?></td>
                        <td><?php echo h($r['bal_time']); ?></td>
                        <td><?php echo h($r['source_url']); ?></td>
                        <td class="<?php echo $isOk ? 'ok' : 'bad'; ?>"><?php echo h($r['status']); ?></td>
                    </tr>
                <?php } ?>
                </tbody>
            </table>
        </div>
    <?php } ?>

    <div class="card">
        <h2>Exempel-CSV</h2>
<pre>skolenhetskod,student_date,student_time,bal_date,bal_time,source_url
123456,2026-06-12,13:00,2026-05-28,18:30,https://exempel.se/studentinfo
987654,2026-06-13,,2026-05-29,,https://exempel.se</pre>
        <p class="muted">Du kan använda semikolon eller tab i stället för komma – importen försöker auto-detektera.</p>
    </div>
</div>
</body>
</html>
<?php
$mysqli->close();