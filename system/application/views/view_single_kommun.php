<?php 
error_reporting(-1);
ini_set('display_errors', 'On');
?>



<?php $this->load->view('_header'); ?>


<style>
.ordr{margin-top: 10px;background-color: lightpink;padding: 5px;}
</style>


<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.gmap.min.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.modal.js" type="text/javascript"></script>
<link href="<?php echo base_url() ?>public/css/modal.css" rel="stylesheet" type="text/css"/>
	

<script src="<?php echo base_url() ?>public/js/jquery.betterTooltip.js" type="text/javascript" ></script>    

<script type="text/javascript">
		$(window).load(function(){
			$('.tTip').betterTooltip({speed: 10, delay: 10});
		});
</script>
       
 

    
    
<div id="left">




	<div class="left_box">
		<h1><?php echo $page_title; ?></h1> <div class="heading_print" onclick="window.print(); return false;">Skriv ut</div>
		
		<div class="breadcrumb">
			<?php echo $this->studenten->make_breadcrumb(); ?>
		</div>
		

			<div style="width:555px; margin-bottom:15px;float:left"><script async src="//pagead2.googlesyndication.com/pagead/js/adsbygoogle.js"></script>
<!-- hp1 -->
<ins class="adsbygoogle"
     style="display:block"
     data-ad-client="ca-pub-6139488901705044"
     data-ad-slot="1107917217"
     data-ad-format="auto"></ins>
<script>
(adsbygoogle = window.adsbygoogle || []).push({});
</script></div>
				
<?php if($this->dx_auth->is_logged_in()): ?>
				<?php endif; ?>


		<?php $alt = ''; ?>
		<?php foreach($ads as $ad): ?>
		<div class="company<?php if ($alt=='') { $alt = 'right'; }
			else { echo ' right'; $alt = ''; } ?>">
			<h2><?php echo $ad['category_title']; ?></h2>
			
			

<?php if($this->dx_auth->is_logged_in()): ?>

<?php if($ad['orderid'] != ""): ?>
<div class="ordr">
	<?php echo $ad['orderid']; ?>, 
	<?php if($ad['seller'] != ""): ?><?php echo $ad['seller']; ?>, <?php endif; ?>
	<?php if($ad['date_expire'] != ""): ?><?php echo $ad['date_expire']; ?><?php endif; ?>
	<?php if($ad['sort_id_ad'] != "-50"): ?> Placering <?php // echo $ad['sort_id_ad']; ?><?php endif; ?>
</div>
<?php endif; ?> 

<?php endif; ?>
			
<div class="image">
			<?php if($ad['logo'] != ""): ?>
				<?php
				//list($width, $height) = getimagesize(base_url().'uploads/logo/'. $ad['logo']);
				//$size = $this->studenten->resize_dimensions(225, 110, $width, $height);
				//$margin = $this->studenten->vertical_align(round($size['height']), 110);
				?>
                <div class="tTip" id="cloud1" title="<?php if($ad['text_info'] != ""): ?><?php echo $ad['text_info']; ?><?php else: ?>Kontakta gärna <?php echo $ad['company_title']; ?> för mer information.<?php endif; ?>">
                

                
                <?php if($ad['www'] != ""): ?>
				
				<!-- Jarl Sandin -->
				<?php if($ad['orderid'] == "1265"): ?>
				<a href="http://shop.jarlsandin.se/" title="<?php echo $ad['company_title']; ?>" target="_blank" rel="nofollow">
				<img src="https://www.studenten2019.se/uploads/logo/<?php echo $ad['logo']; ?>"  alt="" /></div>
				</a>
				<?php else: ?>
				<!-- End -->
				
				<a href="http://<?php echo $ad['www']; ?>" title="<?php echo $ad['company_title']; ?>" target="_blank" data-advertiser="<?php echo $ad['company_title']; ?>, <?php echo $page_title; ?>" data-type="Studenten" rel="nofollow">
				<img src="<?php echo base_url() ?>uploads/logo/<?php echo $ad['logo']; ?>" alt="" style="width:200px;" /></div>
				</a>
				<?php endif; ?>
				
				<?php else: ?>
				<img src="<?php echo base_url() ?>uploads/logo/<?php echo $ad['logo']; ?>" style="width:200px;" data-advertiser="<?php echo $ad['company_title']; ?>, <?php echo $page_title; ?>" data-type="Studenten"  alt="" /></div>
				<?php endif; ?>
				
				

			<?php else: ?>
				<img src="<?php echo base_url() ?>public/images/company_empty.gif" style="margin-top: 30px" alt="" />

			

			<?php if($this->dx_auth->is_logged_in()): ?>
			
				<?php endif; ?>

			<?php endif; ?>
			</div>
            	
            

			<?php if($ad['company_title'] != ""): ?>
			<div class="info">
				<ul>
				<li>
					<img src="<?php echo base_url() ?>public/images/icons/company_tel.png" alt="tel" />
					<?php if($ad['tel'] != ""): ?><?php echo $ad['tel']; ?>
					<?php else: ?>Saknas<?php endif; ?>
				</li>
				<li>
					<img src="<?php echo base_url() ?>public/images/icons/company_www.png" alt="www" />
					<?php if($ad['www'] != ""): ?><a href="http://<?php echo $ad['www']; ?>" title="<?php echo $ad['company_title']; ?>" data-advertiser="<?php echo $ad['company_title']; ?>, <?php echo $page_title; ?>" data-type="Studenten" target="_blank" rel="nofollow"><?php echo $this->studenten->shorten($ad['www'], 22); ?></a>
					<?php else: ?>Saknas<?php endif; ?>
				</li>

















<?php if($this->dx_auth->is_logged_in()): ?>

<li>
					
					<?php if($ad['email'] != ""): ?>
<img src="<?php echo base_url() ?>public/images/icons/company_tel.png" alt="tel" />

					<!-- <?php echo $ad['email']; ?>-->
<a href="javascript:void();" onclick="document.getElementById('underlay-<?php echo $ad['company_id']; ?>').style.display='block'; document.getElementById('lightbox-<?php echo $ad['company_id']; ?>').style.display='block';" style="color:#F11;">Boka direkt!</a>


  
 <style>

#underlay-<?php echo $ad['company_id']; ?>{
	display:none;
	position:fixed;
	top:0;
	left:0;
	width:100%;
	height:100%;
	background-color:#000;
	-moz-opacity:0.5;
	opacity:.50;
	filter:alpha(opacity=50);
	z-index:9;
}
#lightbox-<?php echo $ad['company_id']; ?>{
	display:none;
	position:fixed;
	top:150px;
	left:28%;
	width:600px;
	height:auto;
	background-color:#fff;
	z-index:9;
	border:1px solid #eee;
	padding:10px;
	border-radius:5px;
}
 </style>
        	
       
<!-- Lightbox -->
<div id="underlay-<?php echo $ad['company_id']; ?>">
</div>
<div id="lightbox-<?php echo $ad['company_id']; ?>">

<div style="padding:10px;">
<h3 style="display:inline;"><?php echo $ad['company_title']; ?></h3><br />

</div>

<div style="padding:10px; border:1px solid #aaa; border-radius:5px;">
<table style=" padding:20px;background:#FFF;">
<tr>
<td style="border:0px solid #F00;">
<img src="<?php echo base_url() ?>uploads/logo/<?php echo $ad['logo']; ?>"  height="<?php echo $size['height']; ?>" alt="" />
</td>
<td style="border:0px solid #F00; vertical-align:top; padding-left:40px;">

<img src="<?php echo base_url() ?>public/images/icons/company_tel.png" alt="tel" /><?php echo $ad['tel']; ?>
<br />
<img src="<?php echo base_url() ?>public/images/icons/company_www.png" alt="www" /><?php echo $ad['www']; ?>


</td>
</tr>

</table>

</div>


<div style="padding:10px;">
<p>
<?php echo $ad['boka_text']; ?>
</p>
</div>
<!--
<div style="padding:10px;">

<?php echo $ad['email']; ?>
 
</div>
-->
<hr />

<br >
<!-- - - - - - - - - -->
<!--
<?php 
if ($_POST["email"]<>'') { 
    $ToEmail = 'material@studeravidare.se'; 
    $EmailSubject = 'Site contact form'; 
    $mailheader = "From: ".$_POST["email"]."\r\n"; 
    $mailheader .= "Reply-To: ".$_POST["email"]."\r\n"; 
    $mailheader .= "Content-type: text/html; charset=iso-8859-1\r\n"; 
    $MESSAGE_BODY = "Name: ".$_POST["name"].""; 
    $MESSAGE_BODY .= "Email: ".$_POST["email"].""; 
    $MESSAGE_BODY .= "Comment: ".nl2br($_POST["comment"]).""; 
    mail($ToEmail, $EmailSubject, $MESSAGE_BODY, $mailheader) or die ("Failure"); 
?> 
Your message was sent
<?php 
} else { 
?> 
<form action="email" method="post">
<table width="400" border="0" cellspacing="2" cellpadding="0">
<tr>
<td width="29%" class="bodytext">Your name:</td>
<td width="71%"><input name="name" type="text" id="name" size="32"></td>
</tr>
<tr>
<td class="bodytext">Email address:</td>
<td><input name="email" type="text" id="email" size="32"></td>
</tr>
<tr>
<td class="bodytext">Comment:</td>
<td><textarea name="comment" cols="45" rows="6" id="comment" class="bodytext"></textarea></td>
</tr>
<tr>
<td class="bodytext"> </td>
<td align="left" valign="top"><input type="submit" name="Submit" value="Send"></td>
</tr>
</table>
</form> 
<?php 
}; 
?>



-->

<? 
if(isset($_POST['submit_button']))
 { 
//Here we add the send mail script 
$from="From:$name<$mail>"; 
$tomail="material@studeravidare.se"; 
if (mail($tomail,$comment,$body,$from)) echo "Thank You For using our mail form"; 
}else 
//We added action=$PHP_SELF 
//$PHPSELF means current page 
//action normally contains the page to which the variables are sent 
echo "<form method=POST name=form1 action='$PHP_SELF'> 
<label>Name :</label><input name=name type=text><br> 
<label>E-Mail :</label><input name=email type=text><br> 
<label>Comment :</label><input name=comment type=text><br> 

<input type=submit name=submit value=OK> 
</form>"; 
?> 



<!-- - - - - - - - - -->



<div style=";padding:10px;">
<a href="javascript:void();" onclick="document.getElementById('underlay-<?php echo $ad['company_id']; ?>').style.display='none'; document.getElementById('lightbox-<?php echo $ad['company_id']; ?>').style.display='none';">Close</a>
</div>



</div>

<!-- End Lightbox -->

					<?php else: ?>
					<?php endif; ?>
				</li>


				<?php endif; ?>



















				</ul>
			</div>
			<?php if($ad['lati'] != ""): ?>
			<div id="map_<?php echo $ad['company_id']; ?>" class="map" title="Visa på karta">Visa på karta</div>
			

			
			
			<?php endif; ?>
			<?php else: ?>
			<div class="info_empty">
				<a href="<?php echo base_url() ?>om-oss/kontakt">Vill du synas på studenten 2017?</a>
			</div>
			<?php endif; ?>
		</div>
		<?php endforeach;?>
	</div>

</div>


<div id="right">





	<div class="right_box">
	
	<h5>Länkar</h5>
	
	<ul class="submenu schools">

		<?php foreach($ads as $ad): ?>
			<?php if($ad['logo'] != ""): ?>	
				<?php echo $ad['category_title']; ?>
					
					<li style="font-size:12px;">
						<?php if($ad['www'] != ""): ?><a href="http://<?php echo $ad['www']; ?>" title="<?php echo $ad['company_title']; ?>" data-advertiser="<?php echo $ad['company_title']; ?>, <?php echo $page_title; ?>" data-type="Studenten" target="_blank" rel="nofollow"><?php echo $this->studenten->shorten($ad['company_title'], 22); ?></a>
						<?php else: ?><?php endif; ?>
					</li>
				<?php endif; ?>			
		<?php endforeach;?>

		</ul>

	
	
	
	
	
		<h5>Skolor</h5>
		<?php if($skola > 0): ?>
		<ul class="submenu schools">
		<?php foreach($skola as $data2): ?>
		<li><img src="<?php echo base_url() ?>public/images/icons/icon_<?php echo $data2->type; ?>.png" alt="" /><a href="<?php echo base_url() ?>out/?url=<?php echo $data2->url; ?>" target="_blank" rel="nofollow" title="<?php echo $data2->title; ?>"><?php echo $this->studenten->shorten($data2->title, 25); ?></a></li>
		<?php endforeach;?>
		</ul>
		<?php else :?>
		<ul class="submenu schools">
			<li>Inga skolor inlagda.</li>
		</ul>
		<?php endif; ?>
		
		<ul class="submenu single desc">
			<li><img src="<?php echo base_url() ?>public/images/icons/icon_f.png" alt="" />= &nbsp;Friskola</a></li>
			<li id="last"><img src="<?php echo base_url() ?>public/images/icons/icon_k.png" alt="" />= &nbsp;Kommunal skola</a></li>
		</ul>
		<p></p>
		
		<ul class="submenu single desc">
			<li><a href="http://www.studeravidare.se">Vad ska du göra efter gymnasiet? studeravidare.se</a></li>
			
		</ul>
			<? //readfile("http://178.79.129.108/links.php"); ?>	
			<? // include("http://178.79.129.108/links.php"); ?>	
			
			
<h5>Övrigt</h5>
<ul style="list-style-type: none;">
	<li>
          <a href="http://babycard.se" title="Babycard - We love parents.">Babycard</a>
	</li>
	<li>
          <a href="http://www.studeravidare.se/om-hogskolan/meritvarde" title="Räkna ut meritvärde">Meritvärde</a>
	</li>

	<li>
         <a href="http://www.foraldraledighet.se">Föräldraledighet</a>
	</li>
	<li>
          <a href="http://www.studeravidare.se/om-hogskolan/urvalsgrupper">Urvalsgrupper högskola</a>
	</li>
	<?php if($this->uri->segment(3)=="stockholms-stad"):?>
	
	<?php endif;?>
	<?php if($this->uri->segment(3)=="boras-stad"):?>
	<li>
		<a href="http://www.nyttkontor.nu/din-stad/boras/" target="_blank" title="Söker du kontor i Borås?">Lokaler i Borås</a>
	</li>
	<?php endif;?>

</ul>	
			<p id="lastone"></p>
	</div>

</div>

<div class="modal_window" id="karta">
	<div class="modal_top"><a href="#" class="jqmClose" title="Stäng fönstret">Close</a></div>
	<div class="modal_content">
		<div id="map_canvas_modal"></div>
	</div>
	<div class="modal_bott"></div>
</div>

<script type="text/javascript">
$(document).ready(function() { 
	$('#karta').jqm();
	$(".map").click(function() {
		
		var this_id = $(this).attr('id');

		$.post("<?php echo base_url(); ?>ajax/generate_map", { id:this_id }, function(data) {
			var coords = data.split("<|>"); 
			var lati = parseFloat(coords[0]);
			var longi = parseFloat(coords[1]);
			
			$('#karta').jqmShow({toTop: true});
			
			$("#map_canvas_modal").gMap({ 
				zoom: 12,
				markers: [{ 
					latitude: lati,
					longitude: longi,
					icon: {
						image: "<?php echo base_url() ?>public/images/icons/marker.png",
						iconsize: [20, 34],
						iconanchor: [9, 34],
						infowindowanchor: [8, 2]
					}
				}]
			});
		});
	});
});
</script>










<?php $this->load->view('_footer'); ?>



