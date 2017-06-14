
	<div id="footer">
		
					<div class="alignleft">Copyright &copy; 2011 - <? echo date("Y"); ?> <a href="<?php echo base_url() ?>">Studenten2014.se</a> - <a href="<?php echo base_url() ?>sitemap.xml">sitemap</a> | <a href="http://www.studeravidare.se">Studeravidare.se - Högskoleprov & Meritvärde</a> | <a href="http://www.foraldraledighet.se" title="Föräldraledig">Föräldraledighet</a> | <a href="http://www.xn--brllopet-o4a.se/">Bröllop</a></div>
		<div class="alignright">
		
			<a href="<?php echo base_url() ?>om-oss" <?php if($this->uri->segment(1)=="om-oss") echo 'class="active"';?>>Om oss</a>
			<a href="<?php echo base_url() ?>" <?php if($this->uri->segment(1)=="") echo 'class="active"';?>>Hem</a>
			
			
			<?php if($this->dx_auth->is_logged_in()): ?>
			<a href="<?php echo base_url() ?>backend" rel="nofollow" <?php if($this->uri->segment(1)=="backend") echo 'class="active"';?> title="GÃ¥ till kontrollpanelen">Admin</a>
			<?php else: ?>

			<?php endif; ?>			
			
		</div>
		<p id="lastone"></p>
	</div>


			


</div>



		
		
</body>
</html>