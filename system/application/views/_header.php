<!DOCTYPE html>
<html lang="sv">

<head>

<meta charset="utf-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?php echo $meta_title; ?></title>

<link rel="preconnect" href="https://fonts.googleapis.com">
<link rel="preconnect" href="https://fonts.gstatic.com" crossorigin>

<link href="https://fonts.googleapis.com/css2?family=Fraunces:wght@600;700&family=Inter:wght@400;500;600&display=swap" rel="stylesheet">

<style>

body{
margin:0;
font-family:Inter;
background:#f3f4f6;
}

.container{
max-width:1200px;
margin:auto;
padding:20px;
}

/* HEADER */

.site-header{
background:white;
border-bottom:1px solid #eee;
}

.header-inner{
display:flex;
align-items:center;
justify-content:space-between;
padding:14px 20px;
max-width:1200px;
margin:auto;
}

.logo{
font-family:Fraunces;
font-size:28px;
font-weight:700;
}

.logo span{
color:#d4a017;
}

/* NAV */

.nav{
display:flex;
gap:22px;
}

.nav a{
text-decoration:none;
color:#0b2a4a;
font-weight:500;
}

/* BURGER */

.burger{
display:none;
font-size:26px;
cursor:pointer;
}

.mobile-menu{
display:none;
flex-direction:column;
background:white;
border-top:1px solid #eee;
}

.mobile-menu a{
padding:14px 20px;
border-bottom:1px solid #eee;
text-decoration:none;
color:#0b2a4a;
}

/* MOBILE */

@media(max-width:768px){

.nav{
display:none;
}

.burger{
display:block;
}

.mobile-menu.active{
display:flex;
}

}

</style>

</head>

<body>

<header class="site-header">

<div class="header-inner">

<div class="logo">
AlltFörStudenten<span>.se</span>
</div>

<nav class="nav">
<a href="/">Hem</a>
<a href="/gymnasieskolor">Gymnasieskolor</a>
<a href="/bal">Bal</a>
<a href="/om-oss">Om oss</a>
<a href="/kontakt">Kontakt</a>
</nav>

<div class="burger" onclick="toggleMenu()">
☰
</div>

</div>

<div class="mobile-menu" id="mobileMenu">

<a href="/">Hem</a>
<a href="/gymnasieskolor">Gymnasieskolor</a>
<a href="/bal">Bal</a>
<a href="/om-oss">Om oss</a>
<a href="/kontakt">Kontakt</a>

</div>

</header>

<script>

function toggleMenu(){
document.getElementById("mobileMenu").classList.toggle("active");
}

</script>

<div class="container">