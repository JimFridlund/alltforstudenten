
	<div id="footer">
		
					<div class="alignleft">Copyright &copy; 2011 - <? echo date("Y"); ?> <a href="<?php echo base_url() ?>">Studenten2016.se</a> - <a href="<?php echo base_url() ?>sitemap.xml">sitemap</a> | <a href="http://www.studeravidare.se/om-hogskolan/meritvarde">Meritvärde</a> | <a href="http://www.foraldraledighet.se" title="Föräldraledig">Föräldraledighet</a> | <a href="http://www.babycard.se/">Rabattkort för barnfamiljer</a><?php if($this->uri->segment(3)=="boras-stad"):?> | <a href="http://www.webbjobb.io/">Lediga webbjobb på nätet</a><?php endif;?></div>
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


			
    <div id="slidebox">
    <a class="close"></a>
    <div style="float:left; border:0px solid #f00; width:200px;">
   
    <p>Har du koll på vilka högskolor och universitet som finns?</p>

    <ul style="margin-left:17px;">
         	<li><a href="http://www.studeravidare.se/om-hogskolan/meritvarde/" target="_blank">Meritvärde</a></li> 
         	<li><a href="http://www.studeravidare.se/skolor" target="_blank">Alla högskolor</a></li>
         	<li><a href="http://www.studeravidare.se/om-hogskolan" target="_blank">Om högskolan</a></li>
            <li><a href="http://www.studeravidare.se/om-hogskolan/hogskoleprovet/" target="_blank">Högskoleprovet</a></li>
            
            <li><a href="http://www.studeravidare.se/utbildningar" target="_blank">Utbildningar</a></li>
         </ul><br />

<a class="more" href="http://www.studeravidare.se" target="_blank"> Läs mer på <br />studeravidare.se</a></div>
    <div style="float:left; border:0px solid #f00; width:200px;">
      <a href="http://www.studeravidare.se/" id="title" style="display: block; top: 0px;" target="_blank">
     
     	<img src="https://www.studeravidare.se/img/assets/logo.png" alt="Studeravidare.se - Allt om att studera vidare" style=" margin-top:15px;">
     </a>
   </div>


    
</div>

			
<script type="text/javascript">
$(function() {
    $(window).scroll(function(){
        var distanceTop = $('#lastone').offset().top - $(window).height();
 
        if  ($(window).scrollTop() > distanceTop)
            $('#slidebox').animate({'right':'0px'},300);
        else
            $('#slidebox').stop(true).animate({'right':'-430px'},100);
    });
 
    $('#slidebox .close').bind('click',function(){
        $(this).parent().remove();
    });
});
</script>			
		
</body>
</html>