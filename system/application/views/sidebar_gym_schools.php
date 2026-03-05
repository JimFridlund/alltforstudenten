<?php
// sidebar_gym_schools.php
// Kräver att variabeln $locality (t.ex. "Borås") är satt innan include.
// Hämtar skolor från schools_gym och listar i sidebar.

if (!isset($locality) || $locality === '') {
  return;
}

// Läs CI DB-config utan att använda CI (driftsäkert)
$ci_db_config = dirname(__FILE__) . '/../config/database.php';
$ci_db_config = realpath($ci_db_config);

if (!$ci_db_config || !file_exists($ci_db_config)) {
  echo "<!-- sidebar_gym_schools: missing database.php -->";
  return;
}

if (!defined('BASEPATH')) {
  define('BASEPATH', realpath(dirname(__FILE__) . '/../../../') . '/');
}

require $ci_db_config;

if (!isset($db) || !isset($db['default'])) {
  echo "<!-- sidebar_gym_schools: bad database.php -->";
  return;
}

$dbc = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) {
  echo "<!-- sidebar_gym_schools: db connect error -->";
  return;
}
$mysqli->set_charset('utf8mb4');

$loc = $mysqli->real_escape_string($locality);

// Matcha på locality (i praktiken ortnamn som “Borås”, “Stockholm”, etc)
$sql = "
  SELECT skolenhetskod, namn, status
  FROM schools_gym
  WHERE status IN ('AKTIV','PLANERAD')
    AND locality = '$loc'
  ORDER BY namn
  LIMIT 50
";

$res = $mysqli->query($sql);
if (!$res) {
  echo "<!-- sidebar_gym_schools: query error -->";
  return;
}

$rows = array();
while ($r = $res->fetch_assoc()) $rows[] = $r;
$res->free();

if (count($rows) === 0) {
  // Fallback: vissa kan ha locality = “Borås stad” eller liknande
  $loc2 = $mysqli->real_escape_string($locality . '%');
  $sql2 = "
    SELECT skolenhetskod, namn, status
    FROM schools_gym
    WHERE status IN ('AKTIV','PLANERAD')
      AND locality LIKE '$loc2'
    ORDER BY namn
    LIMIT 50
  ";
  $res2 = $mysqli->query($sql2);
  if ($res2) {
    while ($r = $res2->fetch_assoc()) $rows[] = $r;
    $res2->free();
  }
}

function h($s) { return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8'); }
function slugify($str) {
  $str = trim((string)$str);
  if (function_exists('mb_strtolower')) $str = mb_strtolower($str, 'UTF-8'); else $str = strtolower($str);
  $str = str_replace(array('å','ä','ö','Å','Ä','Ö'), array('a','a','o','a','a','o'), $str);
  $str = preg_replace('/[^a-z0-9\-\s]/', '', $str);
  $str = preg_replace('/[\s\-]+/', '-', $str);
  $str = trim($str, '-');
  return $str !== '' ? $str : 'skola';
}

?>

<aside class="sidebarBlock sidebarSchools">
  <h3>Gymnasieskolor i <?php echo h($locality); ?></h3>

  <?php if (count($rows) === 0): ?>
    <p style="margin:0;">Inga skolor hittades.</p>
  <?php else: ?>
    <ul style="margin:0; padding-left: 18px;">
      <?php foreach ($rows as $r): ?>
        <?php
          $code = $r['skolenhetskod'];
          $slug = slugify($r['namn']);
          $url  = "/skola/$code/$slug";
        ?>
        <li>
          <a href="<?php echo h($url); ?>"><?php echo h($r['namn']); ?></a>
          <?php if (!empty($r['status']) && $r['status'] !== 'AKTIV'): ?>
            (<?php echo h($r['status']); ?>)
          <?php endif; ?>
        </li>
      <?php endforeach; ?>
    </ul>

    <p style="margin:10px 0 0;">
      <a href="/gymnasieskolor?q=<?php echo rawurlencode($locality); ?>">Se fler skolor</a>
    </p>
  <?php endif; ?>
</aside>