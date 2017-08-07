<?php $this->load->view('_header'); ?>

<?php if($result > 0): ?>
<script src="https://maps.googleapis.com/maps/api/js?sensor=false" type="text/javascript"></script>
<script src="https://www.studenten2018.se/public/js/jquery.gmap.min.js" type="text/javascript"></script>
<!-- 
<script src="http://maps.google.com/maps?file=api&amp;v=2&amp;sensor=false&amp;key=<?php echo $this->config->item('maps_key'); ?>" type="text/javascript"></script> 
<script src="<?php echo base_url() ?>public/js/jquery.gmap-1.0.4-min.js" type="text/javascript"></script>
-->
<?php endif; ?>

	
<div class="wide_box searchbox">
	<p class="search_heading">Sök på skola eller kommun, eller välj ditt län i listan</p>
	<div class="search_container">
		<?php echo form_open('sok');?>
		<input type="text" class="fritext" name="s_value" value="Skola eller kommun" />
		<input type="submit" class="submit" value="Sök" title="Sök" />
		</form>
	</div>
	
	<div class="list_container">
		<div class="alignleft">Inget län valt</div>
		<div class="list_button" title="Välj ditt län">Välj län</div>
		
		<div class="lan_container">
			<?php if($lan > 0): ?>
			<?php $i = 0; ?>
			<div>
			<?php foreach($lan as $data): ?>
			<div class="lan_box"><a href="<?php echo base_url() ?>visa/<?php echo $data->permalink; ?>" title="<?php echo $data->title; ?>"><?php echo $data->title; ?></a></div>
			<?php endforeach;?>
			</div>
			<?php else :?>
			<?php endif; ?>
		</div>
	</div>
</div>

<div id="left">
	<div class="left_box">
		<h1>Sökresultat för "<?php echo $s; ?>"</h1>

	<ul class="submenu single list" style="list-style-type: none;margin: 0;font-size: 14px;font-weight: bold;">	
		<?php if($result > 0): ?>
		<?php $alt = ''; ?>
		<?php foreach($result as $data): ?>
		

			<li style="padding:5px;">
			<img src="https://www.studenten2018.se/public/images/icons/icon_mark.gif" alt="">
			<a href="<?php echo base_url() ?>visa/<?php echo $this->studenten->parent_permalink($data->parent); ?>/<?php echo $data->permalink; ?>"><?php echo $data->region_title; ?></a>
			</li>
		
		<?php endforeach; ?>
	</ul>

			<p>&nbsp;</p>
			<h2>Karta</h2>
			<div id="map_canvas_small"></div>
		<?php else: ?>
			<div class="msg empty">Din sökning genererade inga resultat</div>
		<?php endif; ?>
	</div>
</div>

<div id="right">
	<div class="right_box">
		
	</div>
</div>

<script type="text/javascript">
$(document).ready(function() {
	$(".list_container").click(function() {
	   $('.lan_container').slideToggle('fast', function() {

		});
		$(".list_button").toggleClass('open');
	});
});
</script>

<?php if($result > 0): ?>
<script type="text/javascript">
$(document).ready(function() { 
	$("#map_canvas_small").gMap({ 
		zoom: 7,
		controls: ["GLargeMapControl3D"],
		markers: [
			<?php foreach($result as $pin):?>
			{
				latitude: <?php echo $pin->lati; ?>,
				longitude: <?php echo $pin->longi; ?>,
				html: "<strong><?php echo $pin->region_title; ?></strong><br /><br /><a href='<?php echo base_url() ?>visa/<?php echo $this->studenten->parent_permalink($pin->parent); ?>/<?php echo $pin->permalink; ?>' title='Visa <?php echo $pin->region_title; ?>'>Visa <?php echo $pin->region_title; ?></a>",
				icon: {
					image: "<?php echo base_url() ?>public/images/icons/marker.png",
					iconsize: [20, 34],
					iconanchor: [9, 34],
					infowindowanchor: [8, 2]
				}
			} ,
			<?php endforeach;?>
		]
	});
});
</script>
<?php endif; ?>
	
<?php $this->load->view('_footer'); ?>