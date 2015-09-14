<div class="grid_24">

	<h1><span class="face1">Forgotten</span> <span class="face2">Your Password</span><span class="face1">?</span></h1>
	<p class="intro">Please enter your email address below and we'll send you a link so you can create a new password.</p>

	<?php echo $this->Session->flash(); ?>

	<?php echo $form->create('Customer', array('class' => 'form', 'action' => 'forgotten_password')); ?>

	<fieldset>
		<div class="inputs">
			<?php
			echo $form->input('email', array(
				'id' => 'join-email',
				'label' => 'Your e-mail address',
				'between' => '<div class="input-box">',
				'after' => '</div>',
				'placeholder' => 'you@example.com',
			));?>
			
		</div>
	</fieldset>
		
	<div class="form-actions">
		<?php
		echo $form->button('Continue', array(
			'id' => 'continue-button',
			'div' => false
		));
		?>
	</div>

	<?php echo $form->end(); ?>

</div>