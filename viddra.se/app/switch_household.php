<?php
require_once __DIR__ . '/../includes/bootstrap.php';
Auth::requireLogin();

if ($_SERVER['REQUEST_METHOD'] !== 'POST'){
  header("Location: /app/household.php"); exit;
}

if (!viddra_csrf_check($_POST['csrf'] ?? '')){
  header("Location: /app/household.php?err=csrf"); exit;
}

$hid = (int)($_POST['household_id'] ?? 0);
$uid = Auth::userId();

if ($hid > 0 && Household::userBelongsTo($uid, $hid)){
  Household::setCurrentId($hid);
  header("Location: /app/household.php?ok=switch"); exit;
}

header("Location: /app/household.php?err=not_member"); exit;
