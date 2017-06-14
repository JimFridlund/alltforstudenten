<?php $this->load->view('_header'); ?>

<script src="<?php echo base_url() ?>public/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#sort_list').sortable({
		stop: function(event, ui) { 
			$.ajax({
				type: "POST",
				url: "<?php echo base_url() ?>ajax/lan_order",
				data: $("#sort_list").sortable('serialize')
			});
		}
	});
});
</script>

<div id="left">
	<div class="left_box">
		<h1>Län &amp; kommuner</h1> <a href="<?php echo base_url() ?>backend/lagg_till/lan" class="heading_button">Nytt län</a>
		
		<p>Klicka på ett län för att se och redigera dess kommuner.</p>
		
		<?php echo $this->session->flashdata('message'); ?>
		
		<div id="info_list">
			<div style="width: 30px">&nbsp;</div>
			<div style="width: 178px">Titel</div>
			<div style="width: 134px">Permalänk</div>
			<div style="width: 100px">Kommuner</div>
			<div class="atgarder">Åtgärder</div>
		</div>
		
		<?php if($lan > 0): ?>
		<ul id="sort_list">
		<?php $alt = ''; ?>
		<?php foreach($lan as $data): ?>
		<li id="item_<?php echo $data->id; ?>" class="dashboard_item<?php if ($alt=='') { $alt = 'alt'; }
			else { echo ' alt'; $alt = ''; } ?>">
			
			<div class="move">
				<img src="<?php echo base_url() ?>public/images/icons/icon_move.gif" alt="Flytta" />
			</div>
			<div style="width: 178px"><a href="<?php echo base_url() ?>backend/redigera/lan/<?php echo $data->id; ?>" title="Redigera"><?php echo $data->title; ?></a></div>
			<div style="width: 164px"><?php echo $data->permalink; ?></div>
			<div style="width: 40px"><?php echo $this->studenten->count_kommuner($data->id); ?></div>
			<div class="actions">
				<a href="<?php echo base_url() ?>backend/redigera/lan/<?php echo $data->id; ?>" title="Redigera"><img src="<?php echo base_url() ?>public/images/icons/icon_edit.gif" alt="Redigera" /></a>
				<a href="<?php echo base_url() ?>backend/tabort/lan/<?php echo $data->id; ?>" onclick="return delete_lan();" title="Ta bort "><img src="<?php echo base_url() ?>public/images/icons/icon_delete.gif" alt="Ta bort" /></a>								
			</div>
		</li>
		<?php endforeach;?>
		</ul>
		<?php else :?>
		<div class="msg empty">Det finns inga län inlagda.</div>
		<?php endif; ?>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>