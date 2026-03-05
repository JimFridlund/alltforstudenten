<?php
// public_html/export_studentdates_csv.php
// Exporterar CSV för alla skolor + studentdatum/tid (för valt studentår)
// PHP 5.6, mysqli, bind_result (ingen get_result)

error_reporting(0);
ini_set('display_errors', '0');

// Skydda sidan (samma auth som admin_studentdates.php)
$auth = __DIR__ . '/admin_studentdates_auth.php';
if (file_exists($auth)) {
  require $auth;
} else {
  header("HTTP/1.0 500 Internal Server Error");
  echo "500";
  exit;
}

// DB-config (CI 1.x i /system/application)
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { header("HTTP/1.0 500 Internal Server Error"); echo "500"; exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/system/');
$db = null;
require $ci_db_config;

if (!isset($db) || !isset($db['default'])) { header("HTTP/1.0 500 Internal Server Error"); echo "500"; exit; }

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) { header("HTTP/1.0 500 Internal Server Error"); echo "500"; exit; }
$mysqli->set_charset('utf8');

// Studentår: ?year=2026 (default = dynamiskt)
$student_year = 0;
if (isset($_GET['year']) && ctype_digit((string)$_GET['year'])) {
  $student_year = (int)$_GET['year'];
}
if ($student_year < 2011 || $student_year > 2100) {
  $y = (int)date('Y');
  $m = (int)date('n');
  $d = (int)date('j');
  $student_year = ($m > 6 || ($m == 7 && $d >= 1)) ? ($y + 1) : $y;
}

// Kolla att tabellen finns
$has_dates = false;
$rt = $mysqli->query("SHOW TABLES LIKE 'school_student_dates'");
if ($rt) { $has_dates = ($rt->num_rows > 0); $rt->free(); }

// Bygg query: alla skolor + left join datum för valt år
$sql = "
SELECT
  s.skolenhetskod,
  s.namn,
  s.lan_slug,
  s.kommun_slug,
  s.kommun_namn,
  d.student_year,
  d.student_date,
  d.student_time,
  d.source_url
FROM schools_gym s
";

if ($has_dates) {
  $sql .= "
LEFT JOIN school_student_dates d
  ON d.skolenhetskod = s.skolenhetskod
  AND d.student_year = ?
";
} else {
  // Ingen tabell => returnera utan datum
  $sql .= "LEFT JOIN (SELECT NULL AS student_year, NULL AS student_date, NULL AS student_time, NULL AS source_url, NULL AS skolenhetskod) d
           ON d.skolenhetskod = s.skolenhetskod";
}

$sql .= "
WHERE s.status IN ('AKTIV','PLANERAD')
ORDER BY s.lan_slug ASC, s.kommun_slug ASC, s.namn ASC
";

$stmt = $mysqli->prepare($sql);
if (!$stmt) { header("HTTP/1.0 500 Internal Server Error"); echo "500"; exit; }

if ($has_dates) {
  $stmt->bind_param('i', $student_year);
}

if (!$stmt->execute()) { header("HTTP/1.0 500 Internal Server Error"); echo "500"; exit; }

// Bind result
$skolenhetskod = $namn = $lan_slug = $kommun_slug = $kommun_namn = '';
$d_year = null; $student_date = null; $student_time = null; $source_url = null;

$stmt->bind_result(
  $skolenhetskod,
  $namn,
  $lan_slug,
  $kommun_slug,
  $kommun_namn,
  $d_year,
  $student_date,
  $student_time,
  $source_url
);

// Output headers
$filename = "studentdates_export_" . $student_year . ".csv";
header('Content-Type: text/csv; charset=utf-8');
header('Content-Disposition: attachment; filename="'.$filename.'"');

// CSV stream
$out = fopen('php://output', 'w');

// UTF-8 BOM (så Excel i Windows öppnar rätt)
fwrite($out, "\xEF\xBB\xBF");

// Header row
fputcsv($out, array(
  'skolenhetskod',
  'namn',
  'lan_slug',
  'kommun_slug',
  'kommun_namn',
  'student_year',
  'student_date',
  'student_time',
  'source_url'
), ';');

// Rows
while ($stmt->fetch()) {
  // Normalisera null -> ''
  $row = array(
    (string)$skolenhetskod,
    (string)$namn,
    (string)$lan_slug,
    (string)$kommun_slug,
    (string)$kommun_namn,
    $d_year !== null ? (string)$d_year : (string)$student_year,
    ($student_date !== null && $student_date !== '0000-00-00') ? (string)$student_date : '',
    ($student_time !== null) ? (string)$student_time : '',
    ($source_url !== null) ? (string)$source_url : ''
  );
  fputcsv($out, $row, ';');
}

fclose($out);
$stmt->close();
$mysqli->close();
exit;