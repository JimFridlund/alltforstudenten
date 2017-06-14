<?php
$login = array(
	'name'	=> 'login',
	'id'	=> 'login',
	'class' => 'form_input',
	'maxlength'	=> 80,
	'size'	=> 30,
	'value' => set_value('login')
);
$button = array(
	'class' => 'class="button send"'
);
?>

<?php $this->load->view('_header_splash'); ?>

<div class="splash_container">
	<h1 class="user">Glömt lösenordet?</h1>
	<?php echo form_open($this->uri->uri_string()); ?>

	<?php echo $this->dx_auth->get_auth_error(); ?>
	<?php if(validation_errors()) echo '<div id="validation_errors">'.validation_errors().'</div><div class="separator"></div>'; ?>

	<dl>
		<dt><?php echo form_label('E-mailadress', $login['id']);?></dt>
		<dd>
			<?php echo form_input($login); ?> 
		</dd>
	</dl>
	
	<div class="separator"></div>
		
	<div class="login_bottom">
		<?php echo form_submit('reset', 'Återställ', $button['class']); ?>
	</div>

	<?php echo form_close()?>
</div>	
<div class="splash_bottom"></div>
		
<?php $this->load->view('_footer_splash'); ?>