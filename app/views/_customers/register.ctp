<?php
	$this->set('body_id', 'register');
?>

<?php $options = array(
	'between' => '<div class="input-box">',
	'after' => '</div>'
); ?>

<div id="leftcol">
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">

	<?php echo $session->flash(); ?>
	<?php echo $session->flash('auth'); ?>

	<div class="header">
		<h1>Register</h1>
		<p class="intro">To sign up for your free account with us please fill in the form below. <span class="red">*</span> denotes a required field.</p>
	</div>
	
	<?php echo $form->create('Customer', array('class' => 'form', 'action' => 'register')); ?>
		
		<fieldset>
			<div class="heading">Personal Information</div>
			<div class="inputs">
				<?php
				echo $form->input('company_name', $options);
				echo $form->input('first_name', $options);
				echo $form->input('last_name', $options);
				echo $form->input('email', $options + array('label' => 'E-mail'));
				echo $form->input('phone', $options + array('label' => 'Telephone'));
				?>
			</div>
		</fieldset>
		
		<fieldset>
			<div class="heading">Login Information</div>
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
	
		<?php echo $form->submit('Continue', array('class' => 'send-button right')); ?>
	
	<?php echo $form->end(); ?>




</div>
<div class="clear"></div>