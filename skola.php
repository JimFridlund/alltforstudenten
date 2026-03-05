<?php
// public_html/skola.php (PHP 5.6-kompatibel)
// SEO-landningssida för skola. Inga annonser här. Kommun-sidan har intäkten.

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(30);

$debug = (isset($_GET['debug']) && $_GET['debug'] == '1');
if ($debug) {
  error_reporting(E_ALL);
  ini_set('display_errors', '1');
} else {
  error_reporting(0);
  ini_set('display_errors', '0');
}

function h($s){ return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function die404(){ header("HTTP/1.0 404 Not Found"); echo "404"; exit; }
function die500($msg, $debug){
  header("HTTP/1.0 500 Internal Server Error");
  if ($debug) {
    echo "<pre style='white-space:pre-wrap;font-family:Consolas,monospace'>";
    echo "500\n".$msg."\n";
    echo "</pre>";
  } else {
    echo "500";
  }
  exit;
}
function base_url_guess(){
  $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
  $scheme = $https ? 'https' : 'http';
  $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
  return $scheme . '://' . $host;
}
function student_year_guess(){
  $y = (int)date('Y');
  $m = (int)date('n');
  $d = (int)date('j');
  if ($m > 6 || ($m == 7 && $d >= 1)) return $y + 1;
  return $y;
}
function slugify_simple($s){
  $s = trim((string)$s);
  $map = array('å'=>'a','ä'=>'a','ö'=>'o','Å'=>'a','Ä'=>'a','Ö'=>'o');
  $s = strtr($s, $map);
  $s = strtolower($s);
  $s = preg_replace('/[^a-z0-9]+/', '-', $s);
  $s = preg_replace('/-+/', '-', $s);
  return trim($s, '-');
}
function json_escape($s){
  // Säker nog för JSON-LD
  return str_replace(array("\\","\"","\n","\r","\t"), array("\\\\","\\\"","\\n","\\r","\\t"), (string)$s);
}
function format_sv_date($ymd){
  // Tar YYYY-MM-DD och returnerar "11 juni 2026" om möjligt, annars original.
  $ymd = trim((string)$ymd);
  if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $ymd)) return $ymd;
  $ts = strtotime($ymd);
  if (!$ts) return $ymd;
  $months = array(
    1=>'januari',2=>'februari',3=>'mars',4=>'april',5=>'maj',6=>'juni',
    7=>'juli',8=>'augusti',9=>'september',10=>'oktober',11=>'november',12=>'december'
  );
  $d = (int)date('j', $ts);
  $m = (int)date('n', $ts);
  $y = (int)date('Y', $ts);
  $mn = isset($months[$m]) ? $months[$m] : date('m', $ts);
  return $d.' '.$mn.' '.$y;
}

// ---- ENHANCED: gruppbas + enhetssuffix ----
function afs_school_group_base($name){
  $name = trim((string)$name);
  if ($name === '') return '';
  $name = preg_replace('/\s+/', ' ', $name);

  // Skala bort vanliga "enhets"-suffix på slutet
  $patterns = array(
    '/\s+Enhet\s*\d+\s*$/i',    // "Enhet 3"
    '/\s+RO\s*\d+\s*$/i',       // "RO4" / "RO 4"
    '/\s+\d+[a-z]\s*$/i',       // "1a"
    '/\s+\d+\s*$/i',            // "2"
    '/\s+[A-ZÅÄÖ]\s*$/u'        // "A"
  );

  $base = $name;
  foreach ($patterns as $p){
    $base2 = preg_replace($p, '', $base);
    $base2 = trim(preg_replace('/\s+/', ' ', $base2));
    if ($base2 !== '' && $base2 !== $base) {
      $base = $base2;
      break;
    }
  }
  return $base;
}
function afs_unit_suffix_from_names($full, $base){
  $full = trim((string)$full);
  $base = trim((string)$base);
  if ($full === '' || $base === '') return '';
  if ($full === $base) return '';
  // Om full börjar med base + mellanslag: suffix = resten
  $pfx = $base.' ';
  if (strpos($full, $pfx) === 0) {
    return trim(substr($full, strlen($pfx)));
  }
  // annars: inget suffix (matchar ändå på bas via strip)
  return '';
}
function afs_unit_label($suffix){
  $suffix = trim((string)$suffix);
  if ($suffix === '') return 'Enhet';

  $s = preg_replace('/\s+/', ' ', $suffix);

  // "Enhet 3"
  if (preg_match('/^enhet\s*(\d+)$/i', $s, $m)) {
    return 'Enhet '.$m[1];
  }
  // "RO4" / "RO 4" / "ro 4"
  if (preg_match('/^ro\s*(\d+)$/i', $s, $m)) {
    return 'RO '.$m[1];
  }
  // "1a" / "1b" / "2"
  if (preg_match('/^(\d+)([a-z])$/i', $s, $m)) {
    return 'Enhet '.$m[1].strtolower($m[2]);
  }
  if (preg_match('/^(\d+)$/', $s, $m)) {
    return 'Enhet '.$m[1];
  }
  // "A" / "B"
  if (preg_match('/^[A-ZÅÄÖ]$/u', $s)) {
    return 'Enhet '.$s;
  }

  // Fallback: visa som det är, men prefixa
  return 'Enhet '.$s;
}
function afs_unit_sort_key($suffix){
  $suffix = trim((string)$suffix);
  $s = preg_replace('/\s+/', ' ', $suffix);

  // Sätt huvud-enheten sist i listan
  if ($s === '') return array(9, 999999, 'zzz');

  // Enhet 12
  if (preg_match('/^enhet\s*(\d+)$/i', $s, $m)) {
    return array(1, (int)$m[1], '');
  }
  // RO 4
  if (preg_match('/^ro\s*(\d+)$/i', $s, $m)) {
    return array(2, (int)$m[1], '');
  }
  // 12a
  if (preg_match('/^(\d+)([a-z])$/i', $s, $m)) {
    return array(3, (int)$m[1], strtolower($m[2]));
  }
  // 12
  if (preg_match('/^(\d+)$/', $s, $m)) {
    return array(4, (int)$m[1], '');
  }
  // A/B/C
  if (preg_match('/^[A-ZÅÄÖ]$/u', $s)) {
    return array(5, 0, $s);
  }

  return array(8, 0, strtolower($s));
}

// ----------------------
// 1) Läs code + slug (om finns)
// ----------------------
$code = '';
$slug_in = '';

if (isset($_GET['code'])) $code = trim((string)$_GET['code']);
if (isset($_GET['slug'])) $slug_in = trim((string)$_GET['slug']);

$path = '';
if (isset($_SERVER['REQUEST_URI'])) {
  $path = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
  if ($path === null) $path = (string)$_SERVER['REQUEST_URI'];
}

if ($code === '') {
  if (preg_match('#/skola/([0-9]{6,12})(?:/([^/]+))?/?$#', (string)$path, $m)) {
    $code = $m[1];
    if (isset($m[2])) $slug_in = trim((string)$m[2]);
  }
}

if ($code === '' || !preg_match('/^[0-9]{6,12}$/', $code)) die404();

// ----------------------
// 2) Inkludera CI database.php korrekt
// ----------------------
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) die500("Hittar inte database.php:\n".$ci_db_config, $debug);

if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/system/');
$db = null;
include $ci_db_config;

if (!isset($db) || !isset($db['default'])) die500("DB config saknas: \$db['default'] finns inte efter include", $debug);

$dbc = $db['default'];
if (!isset($dbc['hostname']) || !isset($dbc['username']) || !isset($dbc['password']) || !isset($dbc['database'])) {
  die500("DB config saknar hostname/username/password/database", $debug);
}

// ----------------------
// 3) Koppla mysqli (utan get_result!)
// ----------------------
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) die500("DB connect error: ".$mysqli->connect_error, $debug);
@$mysqli->set_charset('utf8');

// ----------------------
// 4) Hämta skolan
// ----------------------
$sql = "SELECT skolenhetskod, namn, status, lan_slug, kommun_slug, kommun_namn, url
        FROM schools_gym
        WHERE skolenhetskod = ?
        LIMIT 1";

$stmt = $mysqli->prepare($sql);
if (!$stmt) die500("Prepare failed: ".$mysqli->error, $debug);

$stmt->bind_param('s', $code);

if (!$stmt->execute()) die500("Execute failed: ".$stmt->error, $debug);

$skolenhetskod = '';
$namn = '';
$status_txt = '';
$lan_slug = '';
$kommun_slug = '';
$kommun_namn = '';
$school_url = '';

$stmt->bind_result($skolenhetskod, $namn, $status_txt, $lan_slug, $kommun_slug, $kommun_namn, $school_url);

if (!$stmt->fetch()) {
  $stmt->close();
  $mysqli->close();
  die404();
}
$stmt->close();

// ----------------------
// 5) Canonical + 301-redirect
// ----------------------
$base = base_url_guess();

$kommun_for_slug = '';
if (trim($kommun_namn) !== '') $kommun_for_slug = trim($kommun_namn);
else if (trim($kommun_slug) !== '') $kommun_for_slug = trim($kommun_slug);

$expected_slug = '';
if ($kommun_for_slug !== '') $expected_slug = slugify_simple($namn . '-' . $kommun_for_slug);
else $expected_slug = slugify_simple($namn);

$canonical_url = rtrim($base, '/') . '/skola/' . $skolenhetskod . '/' . $expected_slug;

$need_redirect = false;

// a) Direkt scriptaccess: /skola.php?... (inte /skola/..)
if ($path !== '' && preg_match('#/skola\.php$#', $path)) $need_redirect = true;

// b) Pretty utan slug: /skola/{code}
if (!$need_redirect && $path !== '' && preg_match('#^/skola/([0-9]{6,12})/?$#', $path)) $need_redirect = true;

// c) Pretty med fel slug
if (!$need_redirect && $slug_in !== '' && $expected_slug !== '' && $slug_in !== $expected_slug) $need_redirect = true;

if ($need_redirect) {
  header("Location: ".$canonical_url, true, 301);
  $mysqli->close();
  exit;
}

// ----------------------
// 6) Kommun-sida (annonser + checklista)
// ----------------------
$kommun_label = trim($kommun_namn) !== '' ? trim($kommun_namn) : 'kommunen';

$home_url = rtrim($base, '/') . '/';
$kommun_url = '';
if (trim($lan_slug) !== '' && trim($kommun_slug) !== '') {
  $kommun_url = rtrim($base, '/') . '/visa/' . trim($lan_slug) . '/' . trim($kommun_slug);
}

$student_year = student_year_guess();

// ----------------------
// 7) Fler enheter (A/B/C, 1a/1b, RO1, Enhet 2, 1/2/3...)
// ----------------------
$base_name = afs_school_group_base($namn);
$suffix_now = afs_unit_suffix_from_names($namn, $base_name);

$units = array();
$has_units = false;

if (trim($lan_slug) !== '' && trim($kommun_slug) !== '' && trim($base_name) !== '') {

  // Vi hämtar "kandidater" som börjar med basnamnet (för att slippa full scan)
  $sqlU = "SELECT skolenhetskod, namn, kommun_namn
           FROM schools_gym
           WHERE lan_slug = ? AND kommun_slug = ?
             AND (namn = ? OR namn LIKE CONCAT(?, ' %'))
           ORDER BY namn ASC";

  $stU = $mysqli->prepare($sqlU);
  if ($stU) {
    $stU->bind_param('ssss', $lan_slug, $kommun_slug, $base_name, $base_name);

    if ($stU->execute()) {
      $u_code = '';
      $u_name = '';
      $u_kommun = '';
      $stU->bind_result($u_code, $u_name, $u_kommun);

      $tmp = array();
      while ($stU->fetch()) {
        $u_code = trim((string)$u_code);
        $u_name = trim((string)$u_name);
        $u_kommun = trim((string)$u_kommun);
        if ($u_kommun === '') $u_kommun = $kommun_label;

        if ($u_code === '' || $u_name === '') continue;

        // Matcha via samma bas (så att vi inte råkar ta med "bas X" som egentligen är annan skola)
        $u_base = afs_school_group_base($u_name);
        // Case-insensitive jämförelse på UTF-8: fallback på strtolower
        $k1 = function_exists('mb_strtolower') ? mb_strtolower($base_name, 'UTF-8') : strtolower($base_name);
        $k2 = function_exists('mb_strtolower') ? mb_strtolower($u_base, 'UTF-8') : strtolower($u_base);
        if ($k1 !== $k2) continue;

        $u_suffix = afs_unit_suffix_from_names($u_name, $base_name);

        $kommun_for_slug_u = $u_kommun !== '' ? $u_kommun : $kommun_for_slug;
        $slug_u = slugify_simple($u_name . '-' . $kommun_for_slug_u);

        $tmp[] = array(
          'code' => $u_code,
          'name' => $u_name,
          'suffix' => $u_suffix,
          'slug' => $slug_u
        );
      }

      if (count($tmp) > 1) {
        usort($tmp, function($a, $b){
          $ka = afs_unit_sort_key(isset($a['suffix']) ? $a['suffix'] : '');
          $kb = afs_unit_sort_key(isset($b['suffix']) ? $b['suffix'] : '');

          if ($ka[0] != $kb[0]) return ($ka[0] < $kb[0]) ? -1 : 1;
          if ($ka[1] != $kb[1]) return ($ka[1] < $kb[1]) ? -1 : 1;
          return strcasecmp((string)$ka[2], (string)$kb[2]);
        });

        $units = $tmp;
        $has_units = true;
      }
    }

    $stU->close();
  }
}

// ----------------------
// 8) Datum/bal om tabellen finns
// ----------------------
$event_student_date = '';
$event_student_time = '';
$event_bal_date = '';
$event_bal_time = '';
$event_source_url = '';

$dates_table_exists = false;
$res = $mysqli->query("SHOW TABLES LIKE 'school_student_dates'");
if ($res) {
  $dates_table_exists = ($res->num_rows > 0);
  $res->free();
}

if ($dates_table_exists) {
  $sql2 = "SELECT student_date, student_time, bal_date, bal_time, source_url
           FROM school_student_dates
           WHERE skolenhetskod = ?
           LIMIT 1";
  $st2 = $mysqli->prepare($sql2);
  if ($st2) {
    $st2->bind_param('s', $skolenhetskod);
    if ($st2->execute()) {
      $st2->bind_result($event_student_date, $event_student_time, $event_bal_date, $event_bal_time, $event_source_url);
      $st2->fetch();
    }
    $st2->close();
  }
}

$mysqli->close();

// ----------------------
// 9) SEO-texter + FAQ
// ----------------------
$student_date_pretty = trim($event_student_date) !== '' ? format_sv_date($event_student_date) : '';
$bal_date_pretty = trim($event_bal_date) !== '' ? format_sv_date($event_bal_date) : '';

$utspring_text = '';
if ($student_date_pretty !== '') {
  $utspring_text = $student_date_pretty;
  if (trim($event_student_time) !== '') $utspring_text .= ' kl. '.$event_student_time;
} else {
  $utspring_text = 'datum och tid är inte publicerat ännu';
}

$student_when_text = '';
if ($student_date_pretty !== '') {
  $student_when_text = "Studenten ".$student_year." på ".$namn." är ".$student_date_pretty.".";
  if (trim($event_student_time) !== '') $student_when_text .= " Utspring/tid: ".$event_student_time.".";
} else {
  $student_when_text = "Datum för studenten ".$student_year." på ".$namn." är inte publicerat ännu. Många skolor publicerar datum och tider under våren.";
}

$bal_when_text = '';
if ($bal_date_pretty !== '') {
  $bal_when_text = "Studentbalen kopplad till ".$namn." är ".$bal_date_pretty.".";
  if (trim($event_bal_time) !== '') $bal_when_text .= " Tid: ".$event_bal_time.".";
} else {
  $bal_when_text = "Datum för studentbalen publiceras ofta separat. Om datum saknas här än, kolla skolans/kommunens information senare under våren.";
}

$kommun_phrase = (trim($kommun_label) !== '' ? " i ".$kommun_label : "");
$title = "När är studenten ".$student_year." på ".$namn.$kommun_phrase."? | AlltFörStudenten";
$desc  = "När tar ".$namn." studenten ".$student_year.$kommun_phrase."? Se studentdatum, utspring och studentbal (om publicerat) samt länk till checklista för ".$kommun_label.".";

$faq = array();

// Q1
$q = "När är studenten ".$student_year." på ".$namn."?";
$a = ($student_date_pretty !== '')
  ? ("Studenten ".$student_year." på ".$namn." är ".$student_date_pretty.".". (trim($event_student_time) !== '' ? " Utspring/tid: ".$event_student_time."." : ""))
  : ("Datum för studenten ".$student_year." på ".$namn." är inte publicerat ännu. Många skolor publicerar datum och tider under våren.");
$faq[] = array($q, $a);

// Q2
$q = "När tar ".$namn." studenten ".$student_year."?";
$a = ($student_date_pretty !== '')
  ? ("" . $namn . " tar studenten ".$student_year." den ".$student_date_pretty.".". (trim($event_student_time) !== '' ? " Tid/utspring: ".$event_student_time."." : ""))
  : ("Det finns ännu inget publicerat studentdatum för ".$namn." ".$student_year.". Uppdateringar kommer ofta under våren.");
$faq[] = array($q, $a);

// Q3
$q = "Utspring ".$namn." – vilken tid är det?";
$a = (trim($event_student_time) !== '')
  ? ("Utspring/tid för ".$namn." är ".$event_student_time." den ".$student_date_pretty.".")
  : ("Utspringstid för ".$namn." är inte publicerad ännu. När skolan publicerar tider uppdateras sidan.");
$faq[] = array($q, $a);

// Q4
$q = "När är studentbalen för ".$namn."?";
$a = ($bal_date_pretty !== '')
  ? ("Studentbalen är ".$bal_date_pretty.".". (trim($event_bal_time) !== '' ? " Tid: ".$event_bal_time."." : ""))
  : ("Datum för studentbalen för ".$namn." är inte publicerat här ännu. Balens datum kan publiceras separat av skolan eller arrangör.");
$faq[] = array($q, $a);

// Q5
$q = "Var hittar jag allt jag behöver inför studenten i ".$kommun_label."?";
$a = ($kommun_url !== '')
  ? ("På kommunens checklista hittar du allt du behöver inför studenten i ".$kommun_label.". Besök checklistan via länken på sidan.")
  : ("På kommunens checklista hittar du allt du behöver inför studenten. Besök kommun-sidan via länken på sidan.");
$faq[] = array($q, $a);

// ----------------------
// 10) JSON-LD: FAQ + BreadcrumbList
// ----------------------
$breadcrumb_kommun_name = "Studenten i ".$kommun_label;
$breadcrumb_kommun_url = $kommun_url !== '' ? $kommun_url : $home_url;

$faq_json = '';
$faq_json .= "{";
$faq_json .= "\"@context\":\"https://schema.org\",";
$faq_json .= "\"@type\":\"FAQPage\",";
$faq_json .= "\"mainEntity\":[";
for ($i=0; $i<count($faq); $i++){
  $q = $faq[$i][0];
  $a = $faq[$i][1];
  if ($i>0) $faq_json .= ",";
  $faq_json .= "{";
  $faq_json .= "\"@type\":\"Question\",";
  $faq_json .= "\"name\":\"".json_escape($q)."\",";
  $faq_json .= "\"acceptedAnswer\":{";
  $faq_json .= "\"@type\":\"Answer\",";
  $faq_json .= "\"text\":\"".json_escape($a)."\"";
  $faq_json .= "}";
  $faq_json .= "}";
}
$faq_json .= "]";
$faq_json .= "}";

$bc_json = '';
$bc_json .= "{";
$bc_json .= "\"@context\":\"https://schema.org\",";
$bc_json .= "\"@type\":\"BreadcrumbList\",";
$bc_json .= "\"itemListElement\":[";
$bc_json .= "{";
$bc_json .= "\"@type\":\"ListItem\",\"position\":1,";
$bc_json .= "\"name\":\"Hem\",\"item\":\"".json_escape($home_url)."\"";
$bc_json .= "},";
$bc_json .= "{";
$bc_json .= "\"@type\":\"ListItem\",\"position\":2,";
$bc_json .= "\"name\":\"".json_escape($breadcrumb_kommun_name)."\",\"item\":\"".json_escape($breadcrumb_kommun_url)."\"";
$bc_json .= "},";
$bc_json .= "{";
$bc_json .= "\"@type\":\"ListItem\",\"position\":3,";
$bc_json .= "\"name\":\"".json_escape($namn)."\",\"item\":\"".json_escape($canonical_url)."\"";
$bc_json .= "}";
$bc_json .= "]";
$bc_json .= "}";

?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width,initial-scale=1">
  <title><?php echo h($title); ?></title>
  <meta name="description" content="<?php echo h($desc); ?>">
  <meta name="robots" content="index,follow">
  <link rel="canonical" href="<?php echo h($canonical_url); ?>">

  <script type="application/ld+json"><?php echo $faq_json; ?></script>
  <script type="application/ld+json"><?php echo $bc_json; ?></script>

  <style>
    :root{
      --bg:#f4f8ff; --card:#fff; --text:#0b1220; --muted:#5b6472; --line:#e6eaf0;
      --blue:#0b2a4a; --blue2:#0f4c81; --sky:#eaf2ff; --yellow:#f5c542;
      --r16:16px;
    }
    body{ margin:0; background:var(--bg); color:var(--text); font-family: Arial, sans-serif; }
    .wrap{ max-width: 980px; margin:0 auto; padding: 18px 14px 50px; }
    .crumb{
      background:#fff; border:1px solid var(--line); border-radius: var(--r16);
      padding:10px 12px; color: var(--muted); font-size: 13px;
    }
    .crumb a{ color: var(--blue2); font-weight: 700; text-decoration:none; }
    h1{ margin: 14px 0 10px; font-size: 28px; color: var(--blue); }
    .sub{ margin:0 0 8px; color: var(--muted); line-height:1.7; }
    .card{ background:var(--card); border:1px solid var(--line); border-radius: var(--r16); margin-top:14px; overflow:hidden; }
    .head{ padding: 12px 14px; border-bottom:1px solid #f0f2f6; background: var(--sky); font-weight:700; color: var(--blue); }
    .pad{ padding: 14px; }
    .lead{ margin:0; color: var(--muted); line-height:1.7; }
    .btn{ display:inline-block; padding:10px 12px; border-radius:12px; text-decoration:none; font-weight:700; border:1px solid var(--line); }
    .btnPrimary{ background: var(--yellow); color:#132033; border-color: rgba(0,0,0,.06); }
    .btnSoft{ background: #fff; color: var(--blue); }
    .row{ margin-top:12px; }
    .note{ margin-top:10px; font-size:12px; color:var(--muted); }
    .unitList{ margin:0; padding-left: 18px; color: var(--muted); }
    .unitList li{ margin: 6px 0; }
    .unitList a{ color: var(--blue2); font-weight: 700; text-decoration:none; }
    .unitTag{ font-size:12px; color:#3b5976; font-weight:700; }

    .seoBox{ margin-top:10px; padding: 10px 12px; background:#fff; border:1px dashed #d8e6ff; border-radius:12px; color:var(--muted); line-height:1.7; }
    .seoBox strong{ color: var(--blue); }

    .faq h3{ margin: 0 0 10px; color: var(--blue); font-size: 16px; }
    .faqItem{ border-top:1px solid #f0f2f6; padding: 10px 0; }
    .faqQ{ margin:0; font-weight:700; color: var(--text); }
    .faqA{ margin:6px 0 0; color: var(--muted); line-height:1.7; }
  </style>
</head>
<body>
<div class="wrap">

  <div class="crumb">
    <a href="<?php echo h($home_url); ?>">Hem</a>
    <?php if ($kommun_url): ?>
      &nbsp;›&nbsp; <a href="<?php echo h($kommun_url); ?>">Studenten i <?php echo h($kommun_label); ?></a>
    <?php endif; ?>
    &nbsp;›&nbsp; <span><?php echo h($namn); ?></span>
  </div>

  <h1>Studenten <?php echo (int)$student_year; ?> på <?php echo h($namn); ?></h1>

  <p class="sub">
    Här hittar du information om <strong>när <?php echo h($namn); ?> tar studenten <?php echo (int)$student_year; ?></strong>,
    utspring och studentbal (om det är publicerat), samt en länk till checklistan för <strong>studenten i <?php echo h($kommun_label); ?></strong>.
  </p>

  <div class="seoBox">
    <strong>När är studenten <?php echo (int)$student_year; ?> på <?php echo h($namn); ?>?</strong><br>
    <?php echo h($student_when_text); ?><br><br>
    <strong>Utspring <?php echo h($namn); ?>:</strong> <?php echo h($utspring_text); ?>.<br>
    <strong>Studentbal <?php echo h($namn); ?>:</strong> <?php echo ($bal_date_pretty !== '' ? h($bal_date_pretty) : 'inte publicerat ännu'); ?>.
  </div>

  <div class="card">
    <div class="head">När är studenten?</div>
    <div class="pad">
      <p class="lead"><?php echo h($student_when_text); ?></p>

      <?php if (trim($event_source_url) !== ''): ?>
        <div class="note">Källa: <a href="<?php echo h($event_source_url); ?>" rel="nofollow"><?php echo h($event_source_url); ?></a></div>
      <?php endif; ?>

      <?php if ($kommun_url): ?>
        <div class="row">
          <a class="btn btnPrimary" href="<?php echo h($kommun_url); ?>">Studenten i <?php echo h($kommun_label); ?> – checklista</a>
          &nbsp;
          <a class="btn btnSoft" href="<?php echo h(rtrim($base,'/').'/bal-student'); ?>">Bal &amp; Student</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <div class="card">
    <div class="head">Utspring</div>
    <div class="pad">
      <p class="lead">
        <?php if ($student_date_pretty !== ''): ?>
          Utspring på <?php echo h($namn); ?> sker <?php echo h($student_date_pretty); ?>
          <?php if (trim($event_student_time) !== ''): ?> kl. <?php echo h($event_student_time); ?><?php endif; ?>.
        <?php else: ?>
          Utspringstid för <?php echo h($namn); ?> är inte publicerad ännu. När skolan publicerar tider uppdateras sidan.
        <?php endif; ?>
      </p>
    </div>
  </div>

  <div class="card">
    <div class="head">Studentbal</div>
    <div class="pad">
      <p class="lead"><?php echo h($bal_when_text); ?></p>

      <?php if (trim($event_source_url) !== '' && $bal_date_pretty === ''): ?>
        <div class="note">Tips: om bal-datum finns på annan källa än studentdatum kan du lägga in separat via importen senare.</div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($has_units): ?>
    <div class="card">
      <div class="head">Fler enheter på <?php echo h($base_name); ?></div>
      <div class="pad">
        <p class="lead">
          <?php if (trim($suffix_now) !== ''): ?>
            Du tittar på <strong><?php echo h(afs_unit_label($suffix_now)); ?></strong>. Här är övriga enheter:
          <?php else: ?>
            Här är övriga enheter:
          <?php endif; ?>
        </p>

        <ul class="unitList">
          <?php foreach ($units as $u): ?>
            <?php
              $u_url = rtrim($base, '/') . '/skola/' . $u['code'] . '/' . $u['slug'];
              $is_current = ((string)$u['code'] === (string)$skolenhetskod);
              $label = afs_unit_label(isset($u['suffix']) ? $u['suffix'] : '');
            ?>
            <li>
              <?php if ($is_current): ?>
                <span class="unitTag"><?php echo h($label); ?> (denna sida)</span>
              <?php else: ?>
                <a href="<?php echo h($u_url); ?>"><?php echo h($label); ?></a>
              <?php endif; ?>
              &nbsp;–&nbsp;<span style="color:var(--muted)"><?php echo h($u['name']); ?></span>
            </li>
          <?php endforeach; ?>
        </ul>

        <?php if ($kommun_url): ?>
          <div class="row">
            <a class="btn btnSoft" href="<?php echo h($kommun_url); ?>">Till checklistan i <?php echo h($kommun_label); ?></a>
          </div>
        <?php endif; ?>
      </div>
    </div>
  <?php endif; ?>

  <div class="card">
    <div class="head">Vanliga frågor</div>
    <div class="pad faq">
      <h3>FAQ – <?php echo h($namn); ?> (studenten <?php echo (int)$student_year; ?>)</h3>

      <?php foreach ($faq as $qa): ?>
        <div class="faqItem">
          <p class="faqQ"><?php echo h($qa[0]); ?></p>
          <p class="faqA"><?php echo h($qa[1]); ?></p>
        </div>
      <?php endforeach; ?>

      <?php if ($kommun_url): ?>
        <div class="row">
          <a class="btn btnPrimary" href="<?php echo h($kommun_url); ?>">Var hittar jag allt inför studenten i <?php echo h($kommun_label); ?>?</a>
        </div>
      <?php endif; ?>
    </div>
  </div>

  <?php if ($debug): ?>
    <div class="card">
      <div class="head">DEBUG</div>
      <div class="pad">
        <pre style="white-space:pre-wrap;font-family:Consolas,monospace">
PHP: <?php echo h(PHP_VERSION); ?>

path: <?php echo h($path); ?>
code: <?php echo h($code); ?>
slug_in: <?php echo h($slug_in); ?>

expected_slug: <?php echo h($expected_slug); ?>
canonical_url: <?php echo h($canonical_url); ?>

student_date: <?php echo h($event_student_date); ?>
student_time: <?php echo h($event_student_time); ?>
bal_date: <?php echo h($event_bal_date); ?>
bal_time: <?php echo h($event_bal_time); ?>
source_url: <?php echo h($event_source_url); ?>

base_name: <?php echo h($base_name); ?>
suffix_now: <?php echo h($suffix_now); ?>
has_units: <?php echo $has_units ? 'YES' : 'NO'; ?>
units_count: <?php echo (int)count($units); ?>
        </pre>
      </div>
    </div>
  <?php endif; ?>

</div>
</body>
</html>