<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Inställningar</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>

		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Site title</label></dt>
			<dd>
				<input type="text" class="textfield" name="site_title" value="<?php echo $this->settings->option['site_title']; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Visas i slutet på meta title.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Meta title</label></dt>
			<dd>
				<input type="text" class="textfield" name="meta_title" value="<?php echo $this->settings->option['meta_title']; ?>" />
				<p>Endast för startsidan.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Meta keywords</label></dt>
			<dd>
				<input type="text" class="textfield" name="meta_kw" value="<?php echo $this->settings->option['meta_kw']; ?>" />
				<p>Endast för startsidan.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Meta description</label></dt>
			<dd>
				<textarea name="meta_desc"><?php echo $this->settings->option['meta_desc']; ?></textarea>
				<p>Endast för startsidan.</p>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara inställningar" />	
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>