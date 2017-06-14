<?php $this->load->view('_header'); ?>

<div id="left">
	<div class="left_box">
		<h1>Byt lösenord</h1>
		
		<?php echo $this->session->flashdata('message'); ?>
		<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div>'; ?>
		
		<?php echo form_open($this->uri->uri_string());?>
		<dl>
			<dt><label>Gamla lösenordet</label></dt>
			<dd>
				<input type="password" class="textfield_medium" name="old_password" value="" />
				<p>Ändra endast om du vill byta.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Nya lösenordet</label></dt>
			<dd>
				<input type="password" class="textfield_medium" name="new_password" value="" />
				<p>Ändra endast om du vill byta.</p>
			</dd>
			
			<dd class="separator"></dd>
			
			<dt><label>Bekräfta nya lösenordet</label></dt>
			<dd>
				<input type="password" class="textfield_medium" name="confirm_new_password" value="" />
				<p>Ändra endast om du vill byta.</p>
			</dd>
			
			<dd class="separator"></dd>
			
		</dl>
		
		<input type="submit" id="progressbar" class="submit" value="Byt lösenord" />	
		</form>
	</div>
</div>

<div id="right">
	<div class="right_box">
		<?php $this->load->view('backend/_menu'); ?>
	</div>
</div>

<?php $this->load->view('_footer'); ?>