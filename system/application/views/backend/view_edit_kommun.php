<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Redigera kommun</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>
		
		<?php $data = $kommun[0]; $selected = ' selected="selected"'; ?>

		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Tillhör län</label></dt>
			<dd>
				<div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<select name="parent" class="textfield_select">
					<option value="0">Saknas</option>
					<?php if($lan_list > 0): ?>
					<?php foreach($lan_list as $data2): ?>
					<option value="<?php echo $data2->id; ?>"<?php if($data2->id == $data->parent) echo $selected;?>><?php echo $data2->title; ?></option>
					<?php endforeach;?>
					<?php else :?>
					<?php endif; ?>
				</select> 
				<p>Välj det län som kommunen tillhör.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Kommunens namn</label></dt>
			<dd>
				<input type="text" class="textfield" name="title" value="<?php echo $data->title; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i kommunens namn.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Permalänk</label></dt>
			<dd>
				<input type="text" class="textfield" name="permalink" value="<?php echo $data->permalink; ?>" /> <div class="req" title="Obligatoriskt fält">Obligatorisk</div>
				<p>Fyll i kommunens permalänk. (lämna tom för automatgenererad)</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Latitud</label></dt>
			<dd>
				<input type="text" class="textfield" name="lati" value="<?php echo $data->lati; ?>" /> 
				<p>Latitud för karta</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Longitud</label></dt>
			<dd>
				<input type="text" class="textfield" name="longi" value="<?php echo $data->longi; ?>" /> 
				<p>Longitud för karta</p>
			</dd>
			
			<dd class="separator"></dd>
		</dl>
		
		<input type="submit" class="submit" value="Spara kommun" />	
		</form>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>