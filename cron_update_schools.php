<?php
header('Content-Type: text/plain; charset=utf-8');
@set_time_limit(300);

// ===== TOKEN =====
$TOKEN = 'FridlundGren2017';
$token = isset($_GET['token']) ? $_GET['token'] : '';
if ($token !== $TOKEN) { http_response_code(403); echo "Forbidden\n"; exit; }

// ===== DB (CI 1.x) =====
$ci_db_config = __DIR__ . '/system/application/config/database.php';
if (!file_exists($ci_db_config)) { http_response_code(500); echo "Hittar inte: $ci_db_config\n"; exit; }
if (!defined('BASEPATH')) define('BASEPATH', __DIR__ . '/system/');
require $ci_db_config;
if (!isset($db) || !isset($db['default'])) { http_response_code(500); echo "Saknar \$db['default']\n"; exit; }

$dbc  = $db['default'];
$mysqli = @new mysqli($dbc['hostname'], $dbc['username'], $dbc['password'], $dbc['database']);
if ($mysqli->connect_errno) { http_response_code(500); echo "DB connect error: ".$mysqli->connect_error."\n"; exit; }
$mysqli->set_charset('utf8mb4');

// ===== Skolverket =====
$base = 'https://api.skolverket.se/skolenhetsregistret';
$list_url = $base . '/v2/school-units';

$mode = isset($_GET['mode']) ? $_GET['mode'] : 'import';

// ---- import listan (som tidigare)
if ($mode === 'import') {
    $raw = http_get($list_url);
    $j = json_decode($raw, true);
    if (!isset($j['data']['attributes']) || !is_array($j['data']['attributes'])) { echo "Bad list format\n"; exit; }
    $items = $j['data']['attributes'];

    $stmt = $mysqli->prepare("
        INSERT INTO schools (skolenhetskod, namn, raw_json, updated_at)
        VALUES (?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE namn=VALUES(namn), raw_json=VALUES(raw_json), updated_at=NOW()
    ");
    if (!$stmt) { echo "Prepare failed: ".$mysqli->error."\n"; exit; }

    $saved = 0;
    foreach ($items as $a) {
        $code = isset($a['schoolUnitCode']) ? $a['schoolUnitCode'] : null;
        $name = isset($a['name']) ? $a['name'] : null;
        if (!$code || !$name) continue;
        $raw_json = json_encode($a, JSON_UNESCAPED_UNICODE);
        $stmt->bind_param('sss', $code, $name, $raw_json);
        if ($stmt->execute()) $saved++;
    }
    echo "DONE import. Saved/updated: $saved\n";
    exit;
}

// ---- export_gym_batch: fyll schools_gym med detaljer (batch)
// Kör: ?token=...&mode=export_gym_batch&limit=60
if ($mode === 'export_gym_batch') {

    $limit = isset($_GET['limit']) ? (int)$_GET['limit'] : 60;
    if ($limit <= 0) $limit = 60;
    if ($limit > 150) $limit = 150;

    // Ta gym-rader som INTE finns i schools_gym ännu (eller saknar street_address)
    $sql = "
        SELECT s.skolenhetskod, s.namn, s.status, s.school_types
        FROM schools s
        LEFT JOIN schools_gym g ON g.skolenhetskod = s.skolenhetskod
        WHERE s.is_gym=1
          AND s.status IN ('AKTIV','PLANERAD')
          AND (g.skolenhetskod IS NULL OR g.street_address IS NULL)
        ORDER BY s.skolenhetskod
        LIMIT " . (int)$limit . "
    ";

    $res = $mysqli->query($sql);
    if (!$res) { echo "Query failed: ".$mysqli->error."\n"; exit; }

    $rows = array();
    while ($r = $res->fetch_assoc()) $rows[] = $r;

    if (count($rows) === 0) {
        echo "No more rows to export.\n";
        echo gym_summary($mysqli);
        exit;
    }

    echo "Export batch: ".count($rows)." (limit=$limit)\n";

    $stmt = $mysqli->prepare("
        INSERT INTO schools_gym
        (skolenhetskod, namn, status, school_types, municipality_code, locality, postal_code, street_address, lat, lng, url, email, updated_at)
        VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ON DUPLICATE KEY UPDATE
          namn=VALUES(namn),
          status=VALUES(status),
          school_types=VALUES(school_types),
          municipality_code=VALUES(municipality_code),
          locality=VALUES(locality),
          postal_code=VALUES(postal_code),
          street_address=VALUES(street_address),
          lat=VALUES(lat),
          lng=VALUES(lng),
          url=VALUES(url),
          email=VALUES(email),
          updated_at=NOW()
    ");
    if (!$stmt) { echo "Prepare failed: ".$mysqli->error."\n"; exit; }

    $done = 0; $failed = 0;

    foreach ($rows as $i => $r) {
        $code = $r['skolenhetskod'];
        $unit_url = $base . '/v2/school-units/' . rawurlencode($code);

        $raw = http_get_soft($unit_url, $http_status);
        if ($raw === null) { $failed++; continue; }

        $j = json_decode($raw, true);
        if (!isset($j['data']['attributes']) || !is_array($j['data']['attributes'])) { $failed++; continue; }

        $a = $j['data']['attributes'];

        // Plocka fält vi vet finns i ditt exempel
        $displayName = isset($a['displayName']) ? $a['displayName'] : $r['namn'];
        $status      = isset($a['status']) ? $a['status'] : $r['status'];
        $url         = isset($a['url']) ? $a['url'] : null;
        $email       = isset($a['email']) ? $a['email'] : null;
        $munCode     = isset($a['municipalityCode']) ? $a['municipalityCode'] : null;

        // Adresser: ta BESOKSADRESS först om den finns
        $street = null; $postal = null; $locality = null; $lat = null; $lng = null;

        if (isset($a['addresses']) && is_array($a['addresses'])) {
            $best = null;
            foreach ($a['addresses'] as $addr) {
                if (isset($addr['type']) && $addr['type'] === 'BESOKSADRESS') { $best = $addr; break; }
            }
            if ($best === null && count($a['addresses']) > 0) $best = $a['addresses'][0];

            if ($best) {
                $street   = isset($best['streetAddress']) ? $best['streetAddress'] : null;
                $postal   = isset($best['postalCode']) ? $best['postalCode'] : null;
                $locality = isset($best['locality']) ? $best['locality'] : null;

                if (isset($best['geoCoordinates']) && is_array($best['geoCoordinates'])) {
                    $lat = isset($best['geoCoordinates']['latitude']) ? $best['geoCoordinates']['latitude'] : null;
                    $lng = isset($best['geoCoordinates']['longitude']) ? $best['geoCoordinates']['longitude'] : null;
                }
            }
        }

        $types = $r['school_types'];

        // bind lat/lng som string (null ok)
        $lat_str = (is_numeric($lat)) ? (string)((float)$lat) : null;
        $lng_str = (is_numeric($lng)) ? (string)((float)$lng) : null;

        $stmt->bind_param(
            'ssssssssssss',
            $code,
            $displayName,
            $status,
            $types,
            $munCode,
            $locality,
            $postal,
            $street,
            $lat_str,
            $lng_str,
            $url,
            $email
        );

        if ($stmt->execute()) $done++; else $failed++;

        if (($i % 20) === 0) echo "Progress: ".($i+1)."/".count($rows)."\n";
    }

    echo "DONE export batch. Done: $done, Failed: $failed\n";
    echo gym_summary($mysqli);
    exit;
}

echo "Unknown mode\n";
exit;

// ===== helpers =====
function http_get($url) {
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    if ($body === false) { echo "HTTP error: $err\n"; exit; }
    if ($code < 200 || $code >= 300) { echo "HTTP status: $code\n"; echo substr($body,0,1200)."\n"; exit; }
    return $body;
}

function http_get_soft($url, &$http_status) {
    $http_status = 0;
    $ch = curl_init();
    curl_setopt($ch, CURLOPT_URL, $url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_TIMEOUT, 25);
    curl_setopt($ch, CURLOPT_CONNECTTIMEOUT, 10);
    curl_setopt($ch, CURLOPT_HTTPHEADER, array('Accept: application/json'));
    $body = curl_exec($ch);
    $err  = curl_error($ch);
    $code = (int)curl_getinfo($ch, CURLINFO_HTTP_CODE);
    curl_close($ch);
    $http_status = $code;
    if ($body === false) return null;
    if ($code < 200 || $code >= 300) return null;
    return $body;
}

function gym_summary($mysqli) {
    $out = "";
    $r1 = $mysqli->query("SELECT COUNT(*) AS c FROM schools WHERE is_gym=1 AND status IN ('AKTIV','PLANERAD')");
    $r2 = $mysqli->query("SELECT COUNT(*) AS c FROM schools_gym");
    $r3 = $mysqli->query("
        SELECT COUNT(*) AS c
        FROM schools s
        LEFT JOIN schools_gym g ON g.skolenhetskod=s.skolenhetskod
        WHERE s.is_gym=1 AND s.status IN ('AKTIV','PLANERAD') AND g.skolenhetskod IS NULL
    ");
    if ($r1) $out .= "Gym (AKTIV/PLANERAD) in schools: " . $r1->fetch_assoc()['c'] . "\n";
    if ($r2) $out .= "Rows in schools_gym: " . $r2->fetch_assoc()['c'] . "\n";
    if ($r3) $out .= "Remaining to export: " . $r3->fetch_assoc()['c'] . "\n";
    return $out;
}