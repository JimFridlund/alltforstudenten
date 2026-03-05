<?php
// public_html/admin_missing_dates.php
// Översikt: skolor som saknar studentdatum / källa för valt år
// PHP 5.6 + mysqli + Basic Auth (admin_studentdates_auth.php)

header('Content-Type: text/html; charset=utf-8');
@set_time_limit(60);
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
  if ($m > 7 || ($m == 7 && $d >= 1)) $y = $y + 1;
  @date_default_timezone_set($oldTz);
  return $y;
}

function slugify_sv($str){
  $str = trim((string)$str);
  if (function_exists('mb_strtolower')) $str = mb_strtolower($str, 'UTF-8');
  else $str = strtolower($str);

  $map = array(
    'å'=>'a','ä'=>'a','ö'=>'o','Å'=>'a','Ä'=>'a','Ö'=>'o',
    'é'=>'e','è'=>'e','ê'=>'e','ü'=>'u','á'=>'a','à'=>'a','í'=>'i','ó'=>'o','ø'=>'o',
    '&'=>'och'
  );
  $str = strtr($str, $map);

  $str = preg_replace('/[^a-z0-9\s-]/', '', $str);
  $str = preg_replace('/[\s-]+/', '-', $str);
  $str = trim($str, '-');
  return $str !== '' ? $str : 'skola';
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

// ---- DB config ----
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { http_response_code(500); echo "500"; exit; }
require $ci_db_config;
if (!isset($db) || !isset($db['default'])) { http_response_code(500); echo "500"; exit; }

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) { http_response_code(500); echo "DB error"; exit; }
@mysqli_set_charset($mysqli, 'utf8mb4');

// ---- Ensure tables ----
if (!table_exists($mysqli, 'schools_gym')) {
  echo "schools_gym saknas i DB."; $mysqli->close(); exit;
}
if (!table_exists($mysqli, 'school_student_dates')) {
  echo "school_student_dates saknas i DB."; $mysqli->close(); exit;
}

// ---- Inputs ----
$year = isset($_GET['year']) ? (int)$_GET['year'] : current_student_year();
if ($year < 2020 || $year > 2100) $year = current_student_year();

$filter_lan = isset($_GET['lan']) ? trim((string)$_GET['lan']) : '';
$filter_kommun = isset($_GET['kommun']) ? trim((string)$_GET['kommun']) : '';
$only_missing = isset($_GET['only_missing']) ? (int)$_GET['only_missing'] : 1; // default 1
$missing_mode = isset($_GET['missing_mode']) ? trim((string)$_GET['missing_mode']) : 'date_or_source';
// missing_mode: date_or_source | date_only | source_only

$limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 200;
if ($limit < 50) $limit = 50;
if ($limit > 2000) $limit = 2000;

// ---- Build filter dropdowns ----
$lans = array();
$resLan = $mysqli->query("SELECT DISTINCT lan_slug FROM schools_gym WHERE status IN ('AKTIV','PLANERAD') AND lan_slug <> '' ORDER BY lan_slug");
if ($resLan) {
  while ($r = $resLan->fetch_assoc()) $lans[] = $r['lan_slug'];
  $resLan->free();
}

$kommuner = array();
if ($filter_lan !== '') {
  $sqlK = "SELECT DISTINCT kommun_slug, kommun_namn FROM schools_gym
           WHERE status IN ('AKTIV','PLANERAD') AND lan_slug = ?
           ORDER BY kommun_namn";
  $stmtK = $mysqli->prepare($sqlK);
  if ($stmtK) {
    $stmtK->bind_param('s', $filter_lan);
    $stmtK->execute();
    $stmtK->store_result();
    $ks = ''; $kn = '';
    $stmtK->bind_result($ks, $kn);
    while ($stmtK->fetch()) {
      $kommuner[] = array('slug'=>$ks, 'namn'=>$kn);
    }
    $stmtK->close();
  }
}

// ---- Counts ----
$cnt_total = 0;
$cnt_with_date = 0;
$cnt_with_source = 0;
$cnt_with_both = 0;
$cnt_missing = 0;

// Total schools in selection
$sqlCnt = "SELECT COUNT(*) FROM schools_gym WHERE status IN ('AKTIV','PLANERAD')";
$params = array();
$types = '';
if ($filter_lan !== '') { $sqlCnt .= " AND lan_slug = ?"; $types .= 's'; $params[] = $filter_lan; }
if ($filter_kommun !== '') { $sqlCnt .= " AND kommun_slug = ?"; $types .= 's'; $params[] = $filter_kommun; }

$stmtCnt = $mysqli->prepare($sqlCnt);
if ($stmtCnt) {
  if ($types !== '') {
    // bind dynamically for PHP 5.6
    $bind = array($types);
    for ($i=0; $i<count($params); $i++) $bind[] = &$params[$i];
    call_user_func_array(array($stmtCnt, 'bind_param'), $bind);
  }
  $stmtCnt->execute();
  $stmtCnt->bind_result($cnt_total);
  $stmtCnt->fetch();
  $stmtCnt->close();
}

// With date/source counts via join
// We'll count distinct schools in selection with records for year
$sqlCnt2 = "SELECT
  SUM(CASE WHEN d.student_date IS NOT NULL AND d.student_date <> '0000-00-00' THEN 1 ELSE 0 END) as with_date,
  SUM(CASE WHEN d.source_url IS NOT NULL AND d.source_url <> '' THEN 1 ELSE 0 END) as with_source,
  SUM(CASE WHEN (d.student_date IS NOT NULL AND d.student_date <> '0000-00-00') AND (d.source_url IS NOT NULL AND d.source_url <> '') THEN 1 ELSE 0 END) as with_both
FROM schools_gym s
LEFT JOIN school_student_dates d
  ON d.skolenhetskod = s.skolenhetskod AND d.student_year = ?
WHERE s.status IN ('AKTIV','PLANERAD')";

$params2 = array($year);
$types2 = 'i';
if ($filter_lan !== '') { $sqlCnt2 .= " AND s.lan_slug = ?"; $types2 .= 's'; $params2[] = $filter_lan; }
if ($filter_kommun !== '') { $sqlCnt2 .= " AND s.kommun_slug = ?"; $types2 .= 's'; $params2[] = $filter_kommun; }

$stmtCnt2 = $mysqli->prepare($sqlCnt2);
if ($stmtCnt2) {
  $bind2 = array($types2);
  for ($i=0; $i<count($params2); $i++) $bind2[] = &$params2[$i];
  call_user_func_array(array($stmtCnt2, 'bind_param'), $bind2);
  $stmtCnt2->execute();
  $stmtCnt2->bind_result($cnt_with_date, $cnt_with_source, $cnt_with_both);
  $stmtCnt2->fetch();
  $stmtCnt2->close();
}

if ($cnt_total < 0) $cnt_total = 0;
$cnt_missing = $cnt_total - (int)$cnt_with_both; // not exact for mode, but useful headline

// ---- List query ----
$whereMissing = '';
if ($only_missing === 1) {
  if ($missing_mode === 'date_only') {
    $whereMissing = " AND (d.student_date IS NULL OR d.student_date = '0000-00-00') ";
  } else if ($missing_mode === 'source_only') {
    $whereMissing = " AND (d.source_url IS NULL OR d.source_url = '') ";
  } else { // date_or_source
    $whereMissing = " AND ((d.student_date IS NULL OR d.student_date = '0000-00-00') OR (d.source_url IS NULL OR d.source_url = '')) ";
  }
}

$sqlList = "SELECT
  s.skolenhetskod, s.namn, s.kommun_namn, s.kommun_slug, s.lan_slug,
  d.student_date, d.student_time, d.bal_date, d.bal_time, d.source_url, d.updated_at
FROM schools_gym s
LEFT JOIN school_student_dates d
  ON d.skolenhetskod = s.skolenhetskod AND d.student_year = ?
WHERE s.status IN ('AKTIV','PLANERAD')";

$paramsL = array($year);
$typesL = 'i';
if ($filter_lan !== '') { $sqlList .= " AND s.lan_slug = ?"; $typesL .= 's'; $paramsL[] = $filter_lan; }
if ($filter_kommun !== '') { $sqlList .= " AND s.kommun_slug = ?"; $typesL .= 's'; $paramsL[] = $filter_kommun; }
$sqlList .= $whereMissing;
$sqlList .= " ORDER BY s.lan_slug, s.kommun_namn, s.namn LIMIT " . (int)$limit;

$list = array();
$stmtL = $mysqli->prepare($sqlList);
if ($stmtL) {
  $bindL = array($typesL);
  for ($i=0; $i<count($paramsL); $i++) $bindL[] = &$paramsL[$i];
  call_user_func_array(array($stmtL, 'bind_param'), $bindL);

  $stmtL->execute();
  $stmtL->store_result();

  $code=''; $name=''; $knamn=''; $kslug=''; $lslug='';
  $sd=null; $st=null; $bd=null; $bt=null; $su=null; $upd=null;

  $stmtL->bind_result($code, $name, $knamn, $kslug, $lslug, $sd, $st, $bd, $bt, $su, $upd);
  while ($stmtL->fetch()) {
    $slug = slugify_sv($name . '-' . $knamn);
    $schoolPath = '/skola/' . $code . '/' . $slug;

    $missingFlags = array();
    $hasDate = ($sd !== null && $sd !== '' && $sd !== '0000-00-00');
    $hasSource = ($su !== null && trim((string)$su) !== '');
    if (!$hasDate) $missingFlags[] = 'datum';
    if (!$hasSource) $missingFlags[] = 'källa';

    $list[] = array(
      'code'=>$code,
      'name'=>$name,
      'kommun'=>$knamn,
      'lan'=>$lslug,
      'schoolPath'=>$schoolPath,
      'student_date'=>$sd,
      'student_time'=>$st,
      'bal_date'=>$bd,
      'bal_time'=>$bt,
      'source_url'=>$su,
      'updated_at'=>$upd,
      'missing'=>implode(', ', $missingFlags)
    );
  }
  $stmtL->close();
}

$base = rtrim(base_url_guess(), '/');

function build_qs($arr) {
  $parts = array();
  foreach ($arr as $k=>$v) {
    $parts[] = urlencode($k) . '=' . urlencode($v);
  }
  return implode('&', $parts);
}

// quick links preserve filters
$qsBase = array(
  'year'=>$year,
  'lan'=>$filter_lan,
  'kommun'=>$filter_kommun,
  'limit'=>$limit,
  'only_missing'=>$only_missing,
  'missing_mode'=>$missing_mode
);
?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <title>Admin – Saknar studentdatum</title>
  <style>
    body{font-family:Arial,Helvetica,sans-serif;margin:0;background:#fff;color:#111}
    .wrap{max-width:1100px;margin:0 auto;padding:18px}
    h1{margin:0 0 12px}
    .card{border:1px solid #e6e6e6;border-radius:12px;padding:14px;margin:12px 0;background:#fafafa}
    .grid{display:flex;gap:12px;flex-wrap:wrap}
    .grid .card{flex:1;min-width:260px}
    .muted{color:#666}
    .pill{display:inline-block;padding:4px 8px;border-radius:999px;font-size:12px;font-weight:bold}
    .pill-ok{background:#e9f7ec;color:#0a7a0a;border:1px solid #ccebd3}
    .pill-warn{background:#fffbea;color:#8a5a00;border:1px solid #ffe69c}
    table{border-collapse:collapse;width:100%;background:#fff}
    th,td{border:1px solid #eee;padding:8px;text-align:left;font-size:14px;vertical-align:top}
    th{background:#f7f7f7}
    input,select{padding:8px;border:1px solid #ddd;border-radius:8px}
    .btn{display:inline-block;padding:10px 12px;border-radius:8px;background:#111;color:#fff;text-decoration:none;border:none;cursor:pointer}
    .btn2{display:inline-block;padding:10px 12px;border-radius:8px;background:#1a5fb4;color:#fff;text-decoration:none;border:none;cursor:pointer}
    a{color:#1a5fb4;text-decoration:none}
    a:hover{text-decoration:underline}
    .small{font-size:12px}
    .mono{font-family:Consolas,Monaco,monospace}
  </style>
</head>
<body>
<div class="wrap">

  <h1>Admin – Skolor som saknar studentdatum/källa</h1>

  <div class="card">
    <form method="get" action="admin_missing_dates.php">
      <div class="grid">
        <div style="min-width:220px">
          <label>Studentår<br>
            <input type="number" name="year" value="<?php echo (int)$year; ?>" min="2020" max="2100">
          </label>
        </div>

        <div style="min-width:260px">
          <label>Län<br>
            <select name="lan" onchange="this.form.submit()">
              <option value="">Alla län</option>
              <?php for ($i=0; $i<count($lans); $i++) { $ls = $lans[$i]; ?>
                <option value="<?php echo h($ls); ?>" <?php echo ($filter_lan===$ls?'selected':''); ?>><?php echo h($ls); ?></option>
              <?php } ?>
            </select>
          </label>
        </div>

        <div style="min-width:280px">
          <label>Kommun<br>
            <select name="kommun">
              <option value="">Alla kommuner</option>
              <?php for ($i=0; $i<count($kommuner); $i++) { ?>
                <option value="<?php echo h($kommuner[$i]['slug']); ?>" <?php echo ($filter_kommun===$kommuner[$i]['slug']?'selected':''); ?>>
                  <?php echo h($kommuner[$i]['namn']); ?>
                </option>
              <?php } ?>
            </select>
          </label>
        </div>

        <div style="min-width:240px">
          <label>Visa<br>
            <select name="only_missing">
              <option value="1" <?php echo ($only_missing===1?'selected':''); ?>>Endast de som saknar</option>
              <option value="0" <?php echo ($only_missing===0?'selected':''); ?>>Alla (debug)</option>
            </select>
          </label>
        </div>

        <div style="min-width:240px">
          <label>Saknar vad?<br>
            <select name="missing_mode">
              <option value="date_or_source" <?php echo ($missing_mode==='date_or_source'?'selected':''); ?>>Datum eller källa</option>
              <option value="date_only" <?php echo ($missing_mode==='date_only'?'selected':''); ?>>Bara datum</option>
              <option value="source_only" <?php echo ($missing_mode==='source_only'?'selected':''); ?>>Bara källa</option>
            </select>
          </label>
        </div>

        <div style="min-width:220px">
          <label>Limit<br>
            <select name="limit">
              <?php
                $opts = array(200,500,1000,2000);
                for ($i=0; $i<count($opts); $i++) {
                  $v = $opts[$i];
                  echo '<option value="'.(int)$v.'" '.($limit===$v?'selected':'').'>'.(int)$v.'</option>';
                }
              ?>
            </select>
          </label>
        </div>
      </div>

      <p style="margin:12px 0 0">
        <button class="btn2" type="submit">Uppdatera</button>
        <a class="btn" href="admin_studentdates.php">Till import</a>
      </p>
    </form>
  </div>

  <div class="grid">
    <div class="card">
      <h2 style="margin-top:0">Sammanfattning</h2>
      <p style="margin:0">
        Urval: <strong><?php echo (int)$cnt_total; ?></strong> skolor (AKTIV/PLANERAD)<br>
        Med datum: <strong><?php echo (int)$cnt_with_date; ?></strong><br>
        Med källa: <strong><?php echo (int)$cnt_with_source; ?></strong><br>
        Med datum + källa: <strong><?php echo (int)$cnt_with_both; ?></strong><br>
      </p>
      <p class="muted small" style="margin:10px 0 0">
        Tips: Google gillar när datum har källa. För att få “Verifierat” på skolsidan krävs <span class="mono">source_url</span>.
      </p>
    </div>

    <div class="card">
      <h2 style="margin-top:0">Snabblänkar</h2>
      <p style="margin:0">
        <a href="admin_missing_dates.php?<?php echo h(build_qs(array_merge($qsBase, array('missing_mode'=>'date_or_source','only_missing'=>1)))); ?>">Saknar datum eller källa</a><br>
        <a href="admin_missing_dates.php?<?php echo h(build_qs(array_merge($qsBase, array('missing_mode'=>'date_only','only_missing'=>1)))); ?>">Saknar datum</a><br>
        <a href="admin_missing_dates.php?<?php echo h(build_qs(array_merge($qsBase, array('missing_mode'=>'source_only','only_missing'=>1)))); ?>">Saknar källa</a><br>
      </p>
      <p class="muted small" style="margin:10px 0 0">
        Du kan filtrera på län först, sen kommun.
      </p>
    </div>
  </div>

  <div class="card">
    <h2 style="margin-top:0">Lista (<?php echo count($list); ?> rader visade)</h2>
    <table>
      <thead>
        <tr>
          <th>Skola</th>
          <th>Kommun / Län</th>
          <th>Utspring</th>
          <th>Bal</th>
          <th>Källa</th>
          <th>Uppdaterad</th>
          <th>Saknar</th>
        </tr>
      </thead>
      <tbody>
        <?php for ($i=0; $i<count($list); $i++) {
          $r = $list[$i];
          $hasDate = ($r['student_date'] && $r['student_date'] !== '0000-00-00');
          $hasSource = ($r['source_url'] && trim((string)$r['source_url']) !== '');
        ?>
          <tr>
            <td>
              <div><strong><?php echo h($r['name']); ?></strong></div>
              <div class="small mono"><?php echo h($r['code']); ?></div>
              <div class="small">
                <a href="<?php echo h($r['schoolPath']); ?>" target="_blank">Öppna skolsida</a>
              </div>
            </td>
            <td>
              <div><?php echo h($r['kommun']); ?></div>
              <div class="small muted"><?php echo h($r['lan']); ?></div>
            </td>
            <td>
              <?php if ($hasDate) { ?>
                <span class="pill pill-ok">OK</span><br>
                <span class="small"><?php echo h($r['student_date']); ?> <?php echo h($r['student_time']); ?></span>
              <?php } else { ?>
                <span class="pill pill-warn">Saknas</span>
              <?php } ?>
            </td>
            <td>
              <?php if ($r['bal_date'] && $r['bal_date'] !== '0000-00-00') { ?>
                <span class="pill pill-ok">OK</span><br>
                <span class="small"><?php echo h($r['bal_date']); ?> <?php echo h($r['bal_time']); ?></span>
              <?php } else { ?>
                <span class="pill pill-warn">Saknas</span>
              <?php } ?>
            </td>
            <td>
              <?php if ($hasSource) { ?>
                <span class="pill pill-ok">OK</span><br>
                <a class="small" href="<?php echo h($r['source_url']); ?>" target="_blank" rel="nofollow noopener"><?php echo h($r['source_url']); ?></a>
              <?php } else { ?>
                <span class="pill pill-warn">Saknas</span>
              <?php } ?>
            </td>
            <td class="small">
              <?php echo $r['updated_at'] ? h($r['updated_at']) : '<span class="muted">-</span>'; ?>
            </td>
            <td>
              <?php echo h($r['missing']); ?>
            </td>
          </tr>
        <?php } ?>
      </tbody>
    </table>

    <p class="muted small" style="margin:10px 0 0">
      Om listan blir för stor: filtrera på län eller kommun, eller höj/sänk limit.
    </p>
  </div>

</div>
</body>
</html>
<?php
$mysqli->close();