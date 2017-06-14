<?php
$username = array(
	'name'	=> 'username',
	'id'	=> 'username',
	'class' => 'form_input',
	'tabindex' => '1',
	'value' => set_value('username')
);
$password = array(
	'name'	=> 'password',
	'id'	=> 'password',
	'tabindex' => '2',
	'class' => 'form_input'
);

$remember = array(
	'name'	=> 'remember',
	'id'	=> 'remember',
	'value'	=> 1,
	'checked'	=> set_value('remember')
);

$confirmation_code = array(
	'name'	=> 'captcha',
	'id'	=> 'captcha',
	'maxlength'	=> 8
);
$button = array(
	'name' => 'login',
	'tabindex' => '3',
	'value' => 'Logga in',
	'class' => 'button login'
);
?>

<?php $this->load->view('_header_splash'); ?>

<div class="splash_container">
	<h1 class="user">Logga in till din kontrollpanel</h1>
	<?php echo form_open($this->uri->uri_string())?>

	<?php echo $this->dx_auth->get_auth_error(); ?>
	<dl>	
		<dt><?php echo form_label('E-mailadress:', $username['id']);?></dt>
		<dd>
			<?php echo form_input($username)?>
		</dd>

		<dt><?php echo form_label('Lösenord:', $password['id']);?></dt>
		<dd>
			<?php echo form_password($password)?> &nbsp;&nbsp;<?php echo anchor($this->dx_auth->forgot_password_uri, 'Glömt?');?>
		</dd>

	<?php if ($show_captcha): ?>

		<dt>Enter the code exactly as it appears. There is no zero.</dt>
		<dd><?php echo $this->dx_auth->get_captcha_image(); ?></dd>

		<dt><?php echo form_label('Confirmation Code', $confirmation_code['id']);?></dt>
		<dd>
			<?php echo form_input($confirmation_code);?>
		</dd>
		
	<?php endif; ?>
	</dl>
	
	<div class="separator"></div>
		
	<div class="login_bottom">
		<div class="rem_me">
			<div class="alignleft_text"><?php echo form_checkbox($remember);?></div>
			<div class="alignleft">Kom ihåg mig till nästa inloggning</div>
		</div>
		
		<?php echo form_submit($button);?>
	</div>

	<?php echo form_close()?>
</div>
		
<?php $this->load->view('_footer_splash'); ?>