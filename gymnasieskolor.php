<?php
// public_html/gymnasieskolor.php
// Listar gymnasieskolor från schools_gym och länkar till /skola/<code>/<slug>

header('Content-Type: text/html; charset=utf-8');

// DB-config (CI 1.x i /system/application)
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { http_response_code(500); echo "500"; exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/system/');
require $ci_db_config;

if (!isset($db) || !isset($db['default'])) { http_response_code(500); echo "500"; exit; }

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) { http_response_code(500); echo "500"; exit; }
$mysqli->set_charset('utf8mb4');

$base = base_url_guess();

// Enkel sök (valfri): ?q=...
$q = isset($_GET['q']) ? trim($_GET['q']) : '';
$q_like = '';

$where = "status IN ('AKTIV','PLANERAD')";
if ($q !== '') {
    // mycket enkel sanering
    $q_like = $mysqli->real_escape_string($q);
    $where .= " AND (namn LIKE '%$q_like%' OR locality LIKE '%$q_like%')";
}

$sql = "
SELECT skolenhetskod, namn, locality, status
FROM schools_gym
WHERE $where
ORDER BY namn
LIMIT 2000
";
$res = $mysqli->query($sql);
if (!$res) { http_response_code(500); echo "500"; exit; }

$rows = array();
while ($row = $res->fetch_assoc()) $rows[] = $row;
$res->free();

$title = "Gymnasieskolor i Sverige | AlltFörStudenten";
$canonical = rtrim($base, '/') . "/gymnasieskolor";
if ($q !== '') {
    $title = "Sök gymnasieskolor | AlltFörStudenten";
    $canonical = rtrim($base, '/') . "/gymnasieskolor?q=" . rawurlencode($q);
}

$desc = "Lista över gymnasieskolor och anpassade gymnasieskolor i Sverige. Sök på skola eller ort.";

?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8">
  <title><?php echo h($title); ?></title>
  <link rel="canonical" href="<?php echo h($canonical); ?>">
  <meta name="robots" content="index,follow">
  <meta name="description" content="<?php echo h($desc); ?>">
</head>
<body>

  <h1>Gymnasieskolor</h1>

  <form method="get" action="/gymnasieskolor">
    <label for="q">Sök skola eller ort:</label>
    <input id="q" name="q" value="<?php echo h($q); ?>" placeholder="t.ex. Borås eller Amerikanska Gymnasiet">
    <button type="submit">Sök</button>
    <?php if ($q !== ''): ?>
      <a href="/gymnasieskolor">Rensa</a>
    <?php endif; ?>
  </form>

  <p>
    Visar <?php echo (int)count($rows); ?> skolor<?php echo ($q !== '' ? " för sökning: <strong>" . h($q) . "</strong>" : ""); ?>.
  </p>

  <ul>
    <?php foreach ($rows as $r): ?>
      <?php
        $code = $r['skolenhetskod'];
        $slug = slugify($r['namn']);
        $url  = "/skola/$code/$slug";
      ?>
      <li>
        <a href="<?php echo h($url); ?>"><?php echo h($r['namn']); ?></a>
        <?php if (!empty($r['locality'])): ?>
          – <?php echo h($r['locality']); ?>
        <?php endif; ?>
        <?php if (!empty($r['status']) && $r['status'] !== 'AKTIV'): ?>
          (<?php echo h($r['status']); ?>)
        <?php endif; ?>
      </li>
    <?php endforeach; ?>
  </ul>

</body>
</html>
<?php

function h($s) {
    return htmlspecialchars((string)$s, ENT_QUOTES, 'UTF-8');
}

function slugify($str) {
    $str = trim((string)$str);
    if (function_exists('mb_strtolower')) $str = mb_strtolower($str, 'UTF-8');
    else $str = strtolower($str);

    $str = str_replace(array('å','ä','ö','Å','Ä','Ö'), array('a','a','o','a','a','o'), $str);
    $str = preg_replace('/[^a-z0-9\-\s]/', '', $str);
    $str = preg_replace('/[\s\-]+/', '-', $str);
    $str = trim($str, '-');
    return $str !== '' ? $str : 'skola';
}

function base_url_guess() {
    $https = (!empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off');
    $scheme = $https ? 'https' : 'http';
    $host = isset($_SERVER['HTTP_HOST']) ? $_SERVER['HTTP_HOST'] : '';
    return $scheme . '://' . $host;
}