<?php
	$this->set('body_id', 'register');
?>

<?php $options = array(
	'between' => '<div class="input-box">',
	'after' => '</div>'
); ?>

<div class="grid_24">

		<?php echo $session->flash(); ?>
		<?php echo $session->flash('auth'); ?>

		<div class="header">
			<h1 class="margin">Register</h1>
			<p class="intro">By creating an account with The great British Shop, you will be able to move through the checkout process faster, store multiple delivery addresses, view and track your orders in your account and more.</p>
		</div>
		
		<?php echo $form->create('Customer', array('class' => 'form', 'action' => 'register')); ?>
			
			<fieldset>
				<legend>Personal Information</legend>
				<div class="inputs">
					<?php
					echo $form->input('first_name', $options);
					echo $form->input('last_name', $options);
					echo $form->input('email', $options + array('label' => 'E-mail'));
					echo $form->input('phone', $options + array('label' => 'Telephone'));
					?>
				</div>
			</fieldset>
			
			<fieldset>
				<legend>Login Information</legend>
				<div class="inputs">
					<?php
					echo $form->input('password_main', $options + array(
						'label' => 'Password',
						'type' => 'password',
						'value' => ''
					));
					echo $form->input('password_confirm', $options + array(
						'label' => 'Confirm Password',
						'type' => 'password',
						'value' => ''
					));
					?>
				</div>
			</fieldset>
			<?php echo $form->button('Register', array('style' => 'margin-left:320px')); ?>
		
		<?php echo $form->end(); ?>

</div>


