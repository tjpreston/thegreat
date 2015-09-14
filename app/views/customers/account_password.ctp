<div class="grid_24">
	<?php $options = array(
		'between' => '<div class="input-box">',
		'after' => '</div>',
		'type' => 'password'
	); ?>

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Account</span> <span class="face2">Password</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'password')); ?>

	<div class="grid_18 omega">
		
		<p class="intro">Below you can update or edit the password you use to login to your account.</p>
		<?php echo $form->create('Customer', array('class' => 'form', 'url' => '/customers/save_account_password')); ?>
			
			<fieldset>
				<div class="heading">CHANGE YOUR PASSWORD</div>
				<div class="inputs">
					<?php
					echo $form->input('password_current', $options + array('label' => 'Current Password'));
					echo $form->input('password_main', $options + array('label' => 'New Password'));
					echo $form->input('password_confirm', $options + array('label' => 'Confirm Password'));
					?>
				</div>
			</fieldset>
			
			<div class="form-actions">
				<?php echo $form->button('Save Changes'); ?>
			</div>
			
		<?php echo $form->end(); ?>

	</div>

</div>