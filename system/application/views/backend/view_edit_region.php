<?php $this->load->view('_header'); ?>

<script src="<?php echo base_url() ?>public/js/jquery-ui-1.8.4.custom.min.js" type="text/javascript"></script>
<script type="text/javascript">
$(document).ready(function() {
	$('#sort_list').sortable({
		stop: function(event, ui) { 
			$.ajax({
				type: "POST",
				url: "<?php echo base_url() ?>ajax/kommun_order",
				data: $("#sort_list").sortable('serialize')
			});
		}
	});
});
</script>

<div id="left">
	<div class="left_box">
		<?php $data_single = $lan[0]; $selected = ' selected="selected"'; ?>
		<h1>Redigera län</h1> <a href="<?php echo base_url() ?>backend/lagg_till/kommun/<?php echo $data_single->id; ?>" class="heading_button">Ny kommun</a>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>
		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Länets namn</label></dt>
			<dd>
				<input type="text" class="textfield" name="title" value="<?php echo $data_single->title; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i länets namn.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Permalänk</label></dt>
			<dd>
				<input type="text" class="textfield" name="permalink" value="<?php echo $data_single->permalink; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i länets permalänk. (lämna tom för automatgenererad)</p>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara län" />	
		</form>
		
		<p>&nbsp;</p>
		
		<h2>Kommuner i detta län</h2>
		
		<div id="info_list">
			<div style="width: 30px">&nbsp;</div>
			<div style="width: 208px">Titel</div>
			<div style="width: 144px">Permalänk</div>
			<div class="atgarder">Åtgärder</div>
		</div>
		
		<?php if($kommun > 0): ?>
		<ul id="sort_list">
		<?php $alt = ''; ?>
		<?php foreach($kommun as $data): ?>
		<li id="item_<?php echo $data->id; ?>" class="dashboard_item<?php if ($alt=='') { $alt = 'alt'; }
			else { echo ' alt'; $alt = ''; } ?>">
			
			<div class="move">
				<img src="<?php echo base_url() ?>public/images/icons/icon_move.gif" alt="Flytta" />
			</div>
			<div style="width: 208px"><a href="<?php echo base_url() ?>backend/redigera/kommun/<?php echo $data->id; ?>" title="Redigera"><?php echo $data->title; ?></a></div>
			<div style="width: 240px"><?php echo $data->permalink; ?></div>
			<div class="actions">
				<a href="<?php echo base_url() ?>backend/redigera/kommun/<?php echo $data->id; ?>" title="Redigera"><img src="<?php echo base_url() ?>public/images/icons/icon_edit.gif" alt="Redigera" /></a>
				<a href="<?php echo base_url() ?>backend/tabort/kommun/<?php echo $data->id; ?>" onclick="return delete_kommun();" title="Ta bort "><img src="<?php echo base_url() ?>public/images/icons/icon_delete.gif" alt="Ta bort" /></a>								
			</div>
		</li>
		<?php endforeach;?>
		</ul>
		<?php else :?>
		<div class="msg empty">Det finns inga kommuner till detta län inlagda.</div>
		<?php endif; ?>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>