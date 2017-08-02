<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Skolor</h1> <a href="<?php echo base_url() ?>backend/lagg_till/skola" class="heading_button">Ny skola</a>
		
		<?php echo $this->session->flashdata('message'); ?>
		
		<div id="info_list">
			<div style="width: 233px">Namn</div>
			<div style="width: 191px">Kommun</div>
			<div style="width: 24px">#</div>
			<div class="atgarder">Åtgärder</div>
		</div>
		
		<?php if($skolor['results'] > 0): ?>
		<?php $alt = ''; ?>
		<?php foreach($skolor['results'] as $data): ?>
		<div class="dashboard_item<?php if ($alt=='') { $alt = 'alt'; }
			else { echo ' alt'; $alt = ''; } ?>">
			<div style="width: 228px; padding: 0 0 0 5px;"><a href="<?php echo base_url() ?>backend/redigera/skola/<?php echo $data->id; ?>"><?php echo $data->title; ?></a></div>
			<div style="width: 188px; padding: 0 0 0 5px;"><?php echo $this->studenten->get_kommun_name($data->parent_id); ?></div>
			<div style="width: 48px; padding: 0 0 0 5px;"><?php echo $data->clicks; ?></div>
			
			<div class="actions">
				<a href="<?php echo base_url() ?>backend/redigera/skola/<?php echo $data->id; ?>" title="Redigera"><img src="<?php echo base_url() ?>public/images/icons/icon_edit.gif" alt="Redigera" title="Redigera" /></a>
				<a href="<?php echo base_url() ?>backend/tabort/skola/<?php echo $data->id; ?>" onclick="return delete_school();" title="Ta bort"><img src="<?php echo base_url() ?>public/images/icons/icon_delete.gif" alt="Ta bort" /></a>
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

<?php $this->load->view('_footer'); ?>