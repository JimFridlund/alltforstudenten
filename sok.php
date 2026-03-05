<?php

require_once 'system/database/DB.php'; // använd samma DB-anslutning som övriga scripts

$q = isset($_GET['q']) ? trim($_GET['q']) : '';

if(!$q){
header("Location: /");
exit;
}

$q = mb_strtolower($q);

/* =================================
   Sök kommun
================================= */

$sql = "SELECT slug, lan_slug 
FROM list_kommuner 
WHERE LOWER(title) LIKE ?
LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->execute(["%$q%"]);
$kommun = $stmt->fetch(PDO::FETCH_ASSOC);

if($kommun){

header("Location: /visa/".$kommun['lan_slug']."/".$kommun['slug']);
exit;

}

/* =================================
   Sök gymnasium
================================= */

$sql = "SELECT kommun_slug, lan_slug 
FROM list_schools 
WHERE LOWER(title) LIKE ?
LIMIT 1";

$stmt = $db->prepare($sql);
$stmt->execute(["%$q%"]);
$school = $stmt->fetch(PDO::FETCH_ASSOC);

if($school){

header("Location: /visa/".$school['lan_slug']."/".$school['kommun_slug']);
exit;

}

/* =================================
   fallback
================================= */

header("Location: /gymnasieskolor?q=".urlencode($q));
exit;