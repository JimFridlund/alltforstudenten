<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'class' => 'form_input',
	'value' =>  set_value('username')
);

$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'class' => 'form_input',
	'value' => set_value('password')
);

$confirm_password = array(
	'name'	=> 'confirm_password',
	'id'	=> 'confirm_password',
	'class' => 'form_input',
	'value' => set_value('confirm_password')
);

$email = array(
	'name'	=> 'email',
	'id'	=> 'email',
	'maxlength'	=> 80,
	'class' => 'form_input',
	'value'	=> set_value('email')
);

$captcha = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha'
);
$button = array(
	'class' => 'class="form_submit"'
);
?>
<?php $this->load->view('header'); ?>
	
	
		
		<fieldset><legend>Registrera ny användare</legend>
		<?php echo form_open($this->uri->uri_string())?>

		<input type="hidden" name="role" value="3" />
		<dl>
			<dt><?php echo form_label('Användarnamn', $username['id']);?></dt>
			<dd>
				<?php echo form_input($username)?>
				<?php echo form_error($username['name']); ?>
				<div class="required"></div>
			</dd>

			<dt><?php echo form_label('Lösenord', $password['id']);?></dt>
			<dd>
				<?php echo form_password($password)?>
				<?php echo form_error($password['name']); ?>
				<div class="required"></div>
			</dd>

			<dt><?php echo form_label('Bekräfta lösenord', $confirm_password['id']);?></dt>
			<dd>
				<?php echo form_password($confirm_password);?>
				<?php echo form_error($confirm_password['name']); ?>
				<div class="required"></div>
			</dd>

			<dt><?php echo form_label('E-mail', $email['id']);?></dt>
			<dd>
				<?php echo form_input($email);?>
				<?php echo form_error($email['name']); ?>
				<div class="required"></div>
			</dd>
				
		<?php if ($this->dx_auth->captcha_registration): ?>

			<dt>Enter the code exactly as it appears. There is no zero.</dt>
			<dd><?php echo $this->dx_auth->get_captcha_image(); ?></dd>

			<dt><?php echo form_label('Confirmation Code', $captcha['id']);?></dt>
			<dd>
				<?php echo form_input($captcha);?>
				<?php echo form_error($captcha['name']); ?>
			</dd>
			
		<?php endif; ?>

			<dt></dt>
			<dd><?php echo form_submit('register', 'Registrera', $button['class']);?></dd>
		</dl>

		<?php echo form_close()?>
		</fieldset>

		
	
	<?php $this->load->view('footer'); ?>