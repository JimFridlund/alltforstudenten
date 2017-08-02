<h5>Navigering</h5>
<ul class="menu">
	<li<?php if($this->uri->segment(1)=="backend" && $this->uri->segment(2)=="") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_dash.gif" alt="" /><a href="<?php echo base_url() ?>backend">Översikt</a></li>
	<li<?php if($this->uri->segment(2)=="annonser" OR $this->uri->segment(3)=="annons") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_document.gif" alt="" /><a href="<?php echo base_url() ?>backend/annonser">Alla annonser</a></li>
	<li<?php if($this->uri->segment(2)=="kategorier" OR $this->uri->segment(3)=="kategori") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_listings.gif" alt="" /><a href="<?php echo base_url() ?>backend/kategorier">Kategorier</a></li>
	<li<?php if($this->uri->segment(2)=="regioner" OR $this->uri->segment(3)=="lan" OR $this->uri->segment(3)=="kommun") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_map2.png" alt="" /><a href="<?php echo base_url() ?>backend/regioner">Län &amp; kommuner</a></li>
	<li<?php if($this->uri->segment(2)=="skolor" OR $this->uri->segment(3)=="skola") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_school.png" alt="" /><a href="<?php echo base_url() ?>backend/skolor">Skolor</a></li>
	<li<?php if($this->uri->segment(2)=="installningar") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_settings.gif" alt="" /><a href="<?php echo base_url() ?>backend/installningar">Inställningar</a></li>
	<li<?php if($this->uri->segment(2)=="losenord") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_pass.png" alt="" /><a href="<?php echo base_url() ?>backend/losenord">Byt lösenord</a></li>
	<li id="last"><img src="<?php echo base_url() ?>public/images/icons/icon_power.gif" alt="" /><a href="<?php echo base_url() ?>anvandare/loggaut">Logga ut</a></li>
</ul>
<a href="<?php echo base_url() ?>backend/lagg_till/annons" class="right_button">Lägg till annons</a>