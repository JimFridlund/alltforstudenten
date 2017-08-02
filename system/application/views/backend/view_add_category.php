<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Lägg till kategori</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>

		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Kategorins titel</label></dt>
			<dd>
				<input type="text" class="textfield" name="title" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i kategorins titel.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Permalänk</label></dt>
			<dd>
				<input type="text" class="textfield" name="permalink" />
				<p>Fyll i kategorins permalänk. (lämna tom för automatgenererad)</p>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara kategori" />	
		</form>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>