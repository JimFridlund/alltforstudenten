<h5>Undersidor</h5>
<ul class="submenu single">
	<li<?php if($this->uri->segment(1)=="om-oss" && $this->uri->segment(2)=="") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_question.png" alt="" /><a href="<?php echo base_url() ?>om-oss">Om oss</a></li>
	<li<?php if($this->uri->segment(2)=="webbplatsen") echo ' class="active"'?>><img src="<?php echo base_url() ?>public/images/icons/icon_site.png" alt="" /><a href="<?php echo base_url() ?>om-oss/webbplatsen">Webbplatsen</a></li>
	<li<?php if($this->uri->segment(2)=="kontakt") echo ' class="active"'?> id="last"><img src="<?php echo base_url() ?>public/images/icons/icon_listings.gif" alt="" /><a href="<?php echo base_url() ?>om-oss/kontakt">Kontakt</a></li>
</ul>