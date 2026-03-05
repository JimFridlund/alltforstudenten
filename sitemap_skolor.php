<?php
// public_html/sitemap_skolor.php
// XML-sitemap för schools_gym (AKTIV + PLANERAD)
// Matchar canonical i skola.php exakt

header('Content-Type: application/xml; charset=utf-8');

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

// Hämta skolor (inkl. kommun_namn för korrekt slug)
$sql = "
SELECT skolenhetskod, namn, kommun_namn, updated_at
FROM schools_gym
WHERE status IN ('AKTIV','PLANERAD')
ORDER BY skolenhetskod
";
$res = $mysqli->query($sql);
if (!$res) { http_response_code(500); echo "500"; exit; }

echo "<?xml version=\"1.0\" encoding=\"UTF-8\"?>\n";
echo "<urlset xmlns=\"http://www.sitemaps.org/schemas/sitemap/0.9\">\n";

while ($row = $res->fetch_assoc()) {
    $code = $row['skolenhetskod'];

    // Matchar exakt canonical i skola.php
    $slug = slugify($row['namn'] . '-' . $row['kommun_namn']);

    $loc  = rtrim($base, '/') . "/skola/$code/$slug";

    $lastmod = '';
    if (!empty($row['updated_at'])) {
        $ts = strtotime($row['updated_at']);
        if ($ts) $lastmod = date('Y-m-d', $ts);
    }

    echo "  <url>\n";
    echo "    <loc>" . xml($loc) . "</loc>\n";
    if ($lastmod !== '') echo "    <lastmod>$lastmod</lastmod>\n";
    echo "  </url>\n";
}

echo "</urlset>\n";

function xml($s) {
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