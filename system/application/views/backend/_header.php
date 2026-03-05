<?php if ( ! defined('BASEPATH')) exit('No direct script access allowed'); ?>
<!doctype html>
<html lang="sv">
<head>
  <meta charset="utf-8" />
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <title><?php echo isset($meta_title) ? $meta_title : 'Backend'; ?></title>

  <link rel="stylesheet" href="<?php echo base_url(); ?>public/css/backend.css?v=1" />
  <style>
    body{font-family:Arial, sans-serif;background:#f6f8fb;margin:0;}
    .backendTop{
      background:#0b2a4a;color:#fff;padding:12px 18px;
      display:flex;align-items:center;justify-content:space-between;gap:12px;
    }
    .backendTop a{color:#fff;text-decoration:none;font-weight:700;}
    .backendWrap{max-width:1200px;margin:0 auto;padding:18px;}
  </style>
</head>
<body>

  <div class="backendTop">
    <div>
      <a href="<?php echo base_url(); ?>backend">Översikt</a>
      &nbsp;|&nbsp;
      <a href="<?php echo base_url(); ?>backend/annonser">Alla annonser</a>
      &nbsp;|&nbsp;
      <a href="<?php echo base_url(); ?>backend/lagg_till/annons">Lägg till annons</a>
      &nbsp;|&nbsp;
      <a href="<?php echo base_url(); ?>backend/kategorier">Kategorier</a>
      &nbsp;|&nbsp;
      <a href="<?php echo base_url(); ?>backend/regioner">Län & kommuner</a>
      &nbsp;|&nbsp;
      <a href="<?php echo base_url(); ?>backend/skolor">Skolor</a>
    </div>
    <div>
      <a href="<?php echo base_url(); ?>auth/logout">Logga ut</a>
    </div>
  </div>

  <div class="backendWrap">