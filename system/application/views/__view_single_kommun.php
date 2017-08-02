<?php $this->load->view('_header'); ?>

<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $this->config->item('maps_key'); ?>" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.modal.js" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.gmap-1.0.4-min.js" type="text/javascript"></script>
<link href="<?php echo base_url() ?>public/css/modal.css" rel="stylesheet" type="text/css"/>
	
<div id="left">
	<div class="left_box">
		<h1><?php echo $page_title; ?></h1> <div class="heading_print" onclick="window.print(); return false;">Skriv ut</div>
		
		<div class="breadcrumb">
			<?php echo $this->studenten->make_breadcrumb(); ?>
		</div>
		
		<?php $alt = ''; ?>
		<?php foreach($ads as $ad): ?>
		<div class="company<?php if ($alt=='') { $alt = 'right'; }
			else { echo ' right'; $alt = ''; } ?>">
			<h2><?php echo $ad['category_title']; ?></h2>
			
			<div class="image">
			<?php if($ad['logo'] != ""): ?>
				<?php
				list($width, $height) = getimagesize(base_url().'uploads/logo/'. $ad['logo']);
				$size = $this->studenten->resize_dimensions(225, 110, $width, $height);
				$margin = $this->studenten->vertical_align(round($size['height']), 110);
				?>
				<img src="<?php echo base_url() ?>uploads/logo/<?php echo $ad['logo']; ?>" style="margin-top: <?php echo $margin; ?>px" height="<?php echo $size['height']; ?>" alt="" />
			<?php else: ?>
				<img src="<?php echo base_url() ?>public/images/company_empty.gif" style="margin-top: 30px" alt="" />
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
					<?php if($ad['www'] != ""): ?><a href="http://<?php echo $ad['www']; ?>" title="<?php echo $ad['company_title']; ?>" target="_blank"><?php echo $this->studenten->shorten($ad['www'], 22); ?></a>
					<?php else: ?>Saknas<?php endif; ?>
				</li>
				</ul>
			</div>
			<?php if($ad['lati'] != ""): ?>
			<div id="map_<?php echo $ad['company_id']; ?>" class="map" title="Visa på karta">Visa på karta</div>
			<?php endif; ?>
			<?php else: ?>
			<div class="info_empty">
				<a href="<?php echo base_url() ?>om-oss/kontakt">Vill du synas på studenten 2012?</a>
			</div>
			<?php endif; ?>
		</div>
		<?php endforeach;?>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<h5>Skolor</h5>
		<?php if($skola > 0): ?>
		<ul class="submenu schools">
		<?php foreach($skola as $data2): ?>
		<li><img src="<?php echo base_url() ?>public/images/icons/icon_<?php echo $data2->type; ?>.png" alt="" /><a href="<?php echo base_url() ?>out/?url=<?php echo $data2->url; ?>" target="_blank" title="<?php echo $data2->title; ?>"><?php echo $this->studenten->shorten($data2->title, 25); ?></a></li>
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
				zoom: 8,
				controls: ["GLargeMapControl3D"],
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