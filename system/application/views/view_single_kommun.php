<?php $this->load->view('_header'); ?>

<style>

.page-layout{
display:flex;
gap:40px;
align-items:flex-start;
}

.left-col{
flex:2;
}

.right-col{
flex:1;
}

/* GRID */

.ads-grid{
display:grid;
grid-template-columns:repeat(2,1fr);
gap:22px;
}

/* ANNONS */

.ad-box{
background:#fff;
border-radius:14px;
padding:18px;
box-shadow:0 8px 24px rgba(0,0,0,0.08);
border-left:4px solid #e5c66c;
display:flex;
flex-direction:column;
justify-content:space-between;
}

.category{
font-size:14px;
color:#a38b45;
margin-bottom:6px;
font-weight:600;
}

.ad-header{
display:flex;
gap:12px;
align-items:center;
}

.logo{
width:200px;
flex-shrink:0;
}

.logo img{
width:200px;
height:100px;
object-fit:contain;
}

.ad-title{
font-family:Fraunces,serif;
font-size:18px;
margin:0;
}

.tel{
font-size:14px;
color:#6b7280;
}

/* BUTTONS */

.buttons{
margin-top:12px;
display:flex;
gap:8px;
flex-wrap:wrap;
}

.btn{
padding:8px 12px;
border-radius:6px;
font-weight:600;
text-decoration:none;
font-size:14px;
}

.btn-primary{
background:#0b2a4a;
color:#fff;
}

.btn-offer{
background:#f1d27a;
color:#0b2a4a;
}

/* EMPTY */

.empty{
background:#fffdf5;
border-left:4px dashed #e5c66c;
}

.empty-title{
font-family:Fraunces,serif;
color:#a08b4a;
}

/* SKOLOR */

.school-box{
background:#fff;
border-radius:14px;
padding:22px;
box-shadow:0 8px 24px rgba(0,0,0,0.08);
position:sticky;
top:30px;
}

.school-box h2{
font-family:Fraunces,serif;
margin-top:0;
}

/* MOBILE */

@media(max-width:900px){

.page-layout{
flex-direction:column;
}

.ads-grid{
grid-template-columns:1fr;
}

.school-box{
position:relative;
margin-top:30px;
}

}

</style>

<div class="container">

<h1 style="font-family:Fraunces,serif;font-size:36px;">
Studenten i <?php echo $page_title; ?>
</h1>

<p style="margin-bottom:30px;color:#4b5563;">
Här hittar du allt inför studenten i <?php echo $page_title; ?> – studentflak, plakat, kläder och andra tjänster inför den stora dagen.
</p>

<div class="page-layout">

<div class="left-col">

<?php

$booked = [];
$empty = [];

foreach($ads as $a){

if(!empty($a['company_title'])){
$booked[]=$a;
}else{
$empty[]=$a;
}

}

shuffle($booked);

?>

<div class="ads-grid">

<?php foreach($booked as $a): ?>

<div class="ad-box">

<div class="category">
<?php echo $a['category_title']; ?>
</div>

<div class="ad-header">

<div class="logo">

<?php if(!empty($a['logo'])): ?>

<img src="<?php echo base_url(); ?>uploads/logo/<?php echo $a['logo']; ?>">

<?php endif; ?>

</div>

<div>

<h3 class="ad-title">
<?php echo $a['company_title']; ?>
</h3>

<?php if(!empty($a['tel'])): ?>

<div class="tel">
Tel: <?php echo $a['tel']; ?>
</div>

<?php endif; ?>

</div>

</div>

<div class="buttons">

<?php if(!empty($a['www'])): ?>

<a class="btn btn-primary" href="<?php echo $a['www']; ?>" target="_blank">
Besök webb
</a>

<?php endif; ?>

<?php if(!empty($a['text_info'])): ?>

<div class="btn btn-offer">
<?php echo $a['text_info']; ?>
</div>

<?php endif; ?>

</div>

</div>

<?php endforeach; ?>

<?php foreach($empty as $a): ?>

<div class="ad-box empty">

<div class="category">
<?php echo $a['category_title']; ?>
</div>

<h3 class="empty-title">
Här kan ditt företag synas
</h3>

<p>
Ledig plats i denna kategori
</p>

<div class="buttons">

<a class="btn btn-primary" href="/kontakt">
Boka denna plats
</a>

</div>

</div>

<?php endforeach; ?>

</div>

</div>

<div class="right-col">

<?php if(!empty($skola)): ?>

<div class="school-box">

<h2>Gymnasieskolor i <?php echo $page_title; ?></h2>

<ul>

<?php foreach($skola as $s): ?>

<li><?php echo $s->title; ?></li>

<?php endforeach; ?>

</ul>

</div>

<?php endif; ?>

</div>

</div>

</div>

<?php $this->load->view('_footer'); ?>