<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Redigera skola</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>
		
		<?php $data = $skola[0]; $selected = ' selected="selected"'; ?>

		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Tillhör kommun</label></dt>
			<dd>
				<div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<select name="parent" class="textfield_select">
					<option value="">Saknas</option>
					<?php echo $regions; ?>
				</select> 
				<p>Välj den kommunen som skolan tillhör.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Typ av skola</label></dt>
			<dd>
				<div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<select name="type" class="textfield_select">
					<option value="f"<?php if($data->type == "f") echo $selected;?>>Friskola</option>
					<option value="k"<?php if($data->type == "k") echo $selected;?>>Kommunal skola</option>
				</select> 
				<p>Välj den typ skolan är.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Skolans namn</label></dt>
			<dd>
				<input type="text" class="textfield" name="title" value="<?php echo $data->title; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i skolans namn.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Utlänk</label></dt>
			<dd>
				<input type="text" class="textfield" name="url" value="<?php echo $data->url; ?>" />
				<p>Fyll i länken till skolans hemsida.</p>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara skola" />	
		</form>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>