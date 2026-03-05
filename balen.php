<?php

$uri = $_SERVER['REQUEST_URI'];
$parts = explode('/', trim($uri,'/'));

$kategori = null;

if(isset($parts[1])) {
    $kategori = $parts[1];
}
require_once "index.php";

/*
 Enkel router för bal-kategorier
 URL: /balen/frisor
*/

$slug = '';

if(isset($_GET['slug'])){
$slug = $_GET['slug'];
}

if(!$slug){
$slug = 'start';
}

$data = [];
$data['kategori_slug'] = $slug;

/*
 Ladda CodeIgniter view
*/

$CI =& get_instance();

$CI->load->view('view_bal_kategori',$data);
