<div class="grid_24">

	<h1><span class="face1">Reset</span> <span class="face2">Your Password</span></h1>

	<p class="intro">Please enter a new password for your account.</p>

	<?php echo $form->create('Customer', array('class' => 'form', 'url' => $this->here)); ?>
	<?php echo $form->hidden('hash', array('value' => $hash)); ?>

	<div class="heading">Enter Your New Password</div>
			
	<fieldset>
		<div class="inputs">
		<?php
			
			echo $form->input('Customer.password_main', array(
				'label' => 'New Password', 
				'type' => 'password', 
				'value' => '',
				'between' => '<div class="input-box">',
				'after' => '</div>'
			));
			
			echo $form->input('Customer.password_confirm', array(
				'label' => 'Confirm Password', 
				'type' => 'password', 
				'value' => '',
				'between' => '<div class="input-box">',
				'after' => '</div>'
			));
		?>
		</div>
	</fieldset>
	
	<div class="form-actions">	
		<?php 
			echo $form->button('Continue', array(
				'id' => 'continue-button', 
			));
			
		?>
	</div>

	<?php echo $form->end(); ?>

</div>
