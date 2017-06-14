<?php $this->load->view('_header'); ?>

<script src="<?php echo base_url() ?>public/js/jquery.modal.js" type="text/javascript"></script>
<link href="<?php echo base_url() ?>public/css/modal.css" rel="stylesheet" type="text/css"/>

<div id="left">
	<div class="left_box">
		<h1>Alla annonser</h1> <a href="<?php echo base_url() ?>backend/lagg_till/annons" class="heading_button">Ny annons</a>
		
		<?php echo $this->session->flashdata('message'); ?>
		
		<div id="info_list">
			<div style="width: 270px">Rubrik</div>
			<div style="width: 110px">Kommuner</div>
			<div class="atgarder">Åtgärder</div>
		</div>
		<?php if($ads['results'] > 0): ?>
		<?php $alt = ''; ?>
		<?php foreach($ads['results'] as $data): ?>
		<div class="dashboard_item<?php if ($alt=='') { $alt = 'alt'; }
			else { echo ' alt'; $alt = ''; } ?>">
			<div style="width: 270px; padding: 0 0 0 5px;"><a href="<?php echo base_url() ?>backend/redigera/annons/<?php echo $data->id; ?>"><?php echo $data->title; ?></a> (<?php echo $data->orderid; ?>)<br>Publicerad: <?php echo $data->date_added; ?> <br />
				Utgår: <?php echo $data->date_expire; ?><br />
				Säljare: <?php echo $data->seller; ?>
			</div>
			<div style="width: 170px"><?php echo $this->studenten->count_kommuner_to_ad($data->id); ?> - <a href="#" id="ad_<?php echo $data->id; ?>" class="regions_show">Visa kommuner</a></div>
			
			<div class="actions">
				<a href="<?php echo base_url() ?>backend/redigera/annons/<?php echo $data->id; ?>" title="Redigera"><img src="<?php echo base_url() ?>public/images/icons/icon_edit.gif" alt="Redigera" title="Redigera" /></a>
				<a href="<?php echo base_url() ?>backend/tabort/annons/<?php echo $data->id; ?>" onclick="return delete_ad();" title="Ta bort"><img src="<?php echo base_url() ?>public/images/icons/icon_delete.gif" alt="Ta bort" /></a>
			</div>
		</div>
		<?php endforeach; ?>
		<?php else: ?>
			<div class="msg empty">Du har inte lagt in några annonser ännu.</div>
		<?php endif; ?>
		
		<?php if($pagination) echo '<div class="pagination"><strong>Sidan: </strong>'.$pagination.'</div>'; ?>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<div class="modal_window" id="kommuner">
	<div class="modal_top"><a href="#" class="jqmClose" title="Stäng fönstret">Close</a></div>
	<div class="modal_content">
		<ul id="kommun_lista" style="font-size: 14px; list-style-type: none;">
		
		</ul>
	</div>
	<div class="modal_bott"></div>
</div>

<script type="text/javascript">
$(document).ready(function() { 
	$('#kommuner').jqm();
	$(".regions_show").click(function() {
		$('#kommun_lista').empty();
		var this_id = $(this).attr('id');
		$.post("<?php echo base_url(); ?>ajax/list_regions_to", { id:this_id }, function(data) {
			$('#kommun_lista').append(data);
			$('#kommuner').jqmShow({toTop: true});
		});
	});
});
</script>

<?php $this->load->view('_footer'); ?>