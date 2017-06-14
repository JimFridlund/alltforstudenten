<?php $this->load->view('_header'); ?>

<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="<?php echo base_url() ?>public/js/jquery.gmap.min.js" type="text/javascript"></script>


<!--
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $this->config->item('maps_key'); ?>" type="text/javascript"></script>
-->



	
<div id="left">
	<div class="left_box">
		<h1>Kommuner i <?php echo $page_title; ?></h1>
		
		<div class="breadcrumb">
			<?php echo $this->studenten->make_breadcrumb(); ?>
		</div>
		
		
		<div id="map_canvas"></div>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<h5>Kommuner</h5>
		<?php if($kommuner > 0): ?>
		<ul class="submenu single list">
		<?php foreach($kommuner as $data): ?>
			<li><img src="<?php echo base_url() ?>public/images/icons/icon_mark.gif" alt="" /><a href="<?php echo base_url() ?>visa/<?php echo $page_permalink; ?>/<?php echo $data->permalink; ?>" title="<?php echo $data->title; ?>"><?php echo $data->title; ?></a></li>
		<?php endforeach;?>
		</ul>
		<?php else :?>
		<ul class="submenu single">
			<li>Inga kommuner inlagda.</li>
		</ul>
		<?php endif; ?>
	</div>
</div>






<script type="text/javascript">
$(document).ready(function() { 
	$("ul.list li:last-child").attr('id', 'last');
});

$(document).ready(function() { 
	$("#map_canvas").gMap({ 
		zoom: 7,
		latitude: <?php echo $lan_lati; ?>,
		longitude: <?php echo $lan_long; ?>,
		markers: [
			<?php if ($markers > 0): ?>
			<?php foreach($markers as $pin):?>
			{
				latitude: <?php echo $pin->lati; ?>,
				longitude: <?php echo $pin->longi; ?>,
				html: "<strong><?php echo $pin->title; ?></strong><br /><br /><a href='<?php echo base_url() ?>visa/<?php echo $page_permalink; ?>/<?php echo $pin->permalink; ?>' title='Visa <?php echo $pin->title; ?>'>Visa <?php echo $pin->title; ?></a>",
				icon: {
					image: "<?php echo base_url() ?>public/images/icons/marker.png",
					iconsize: [20, 34],
					iconanchor: [9, 34],
					infowindowanchor: [8, 2]
				}
			} ,
			<?php endforeach;?>
			<?php endif; ?>
		]
	});
});
</script>

	
<?php $this->load->view('_footer'); ?>