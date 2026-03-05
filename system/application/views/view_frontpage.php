<?php $this->load->view('_header'); ?>

<style>

.hero{
background:url('<?php echo base_url(); ?>public/images/hero_students.jpg') center/cover no-repeat;
border-radius:18px;
padding:90px 40px;
color:white;
text-align:center;
position:relative;
margin-bottom:40px;
}

.hero:before{
content:"";
position:absolute;
inset:0;
background:rgba(0,0,0,0.35);
border-radius:18px;
}

.hero-inner{
position:relative;
z-index:2;
max-width:760px;
margin:auto;
}

.hero h1{
font-family:Fraunces;
font-size:60px;
margin:0;
}

.hero p{
font-size:20px;
margin-top:14px;
}

.search-box{
margin-top:28px;
display:flex;
justify-content:center;
gap:10px;
}

.search-box input{
padding:16px;
font-size:16px;
width:360px;
border-radius:10px;
border:none;
}

.search-box button{
padding:16px 26px;
background:#0b2a4a;
color:white;
border:none;
border-radius:10px;
font-weight:600;
cursor:pointer;
}

/* SEO BOXAR */

.start-grid{
margin-top:50px;
display:grid;
grid-template-columns:repeat(auto-fit,minmax(220px,1fr));
gap:20px;
}

.start-card{
background:white;
padding:24px;
border-radius:14px;
box-shadow:0 6px 18px rgba(0,0,0,0.08);
text-decoration:none;
color:#0b2a4a;
font-weight:600;
font-size:18px;
}

.start-card:hover{
box-shadow:0 8px 22px rgba(0,0,0,0.12);
}

.start-info{
margin-top:60px;
display:grid;
grid-template-columns:1fr 1fr;
gap:40px;
}

.start-info h2{
font-family:Fraunces;
}

@media(max-width:768px){

.hero{
padding:60px 20px;
}

.hero h1{
font-size:38px;
}

.hero p{
font-size:16px;
}

.search-box{
flex-direction:column;
}

.search-box input{
width:100%;
}

.search-box button{
width:100%;
}

.start-info{
grid-template-columns:1fr;
}

}

</style>


<section class="hero">

<div class="hero-inner">

<h1>Allt inför din student</h1>

<p>Checklistor, datum och tips för din stora dag</p>

<form action="/sok" method="get" class="search-box">

<input
type="text"
name="q"
placeholder="Sök på skola eller kommun"
required
>

<button type="submit">Sök</button>

</form>

</div>

</section>


<section class="start-grid">

<a class="start-card" href="/balen/frisor">Frisör till studentbalen</a>

<a class="start-card" href="/balen/klanning">Balklänning</a>

<a class="start-card" href="/balen/smink">Smink till balen</a>

<a class="start-card" href="/balen/kostym">Kostym / Smoking</a>

<a class="start-card" href="/balen/fotograf">Fotograf till balen</a>

<a class="start-card" href="/balen/limousine">Limousine till balen</a>

</section>


<section class="start-info">

<div>

<h2>AlltFörStudenten</h2>

<p>
Checklistor, inspiration och lokala guider inför studenten.
Här hittar du företag, studentflak, bal-kläder och fotografer
i din stad.
</p>

</div>

<div>

<h2>Så använder du sidan</h2>

<p>
Sök på din kommun eller ditt gymnasium för att hitta
allt inför studenten där du bor.
</p>

</div>

</section>


<?php $this->load->view('_footer'); ?>