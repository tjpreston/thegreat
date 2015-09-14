<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Account</span> <span class="face2">Information</span></h1>
	</div>
	
	<?php echo $this->element('customers/my_account_nav', array('step' => 'information')); ?>

	<div class="grid_18 omega">

		<p class="intro">Use the boxes below to update or edit your personal details.</p>

		<?php $options = array(
			'between' => '<div class="input-box">',
			'after' => '</div>'
		); ?>

		<?php echo $session->flash(); ?>

		<?php echo $form->create('Customer', array('class' => 'form', 'action' => 'save_account_information')); ?>
			
			<fieldset>
				<div class="heading">EDIT YOUR ACCOUNT INFORMATION</div>
				<div class="inputs">
					<?php
					echo $form->input('company_name', $options);
					echo $form->input('first_name', $options);
					echo $form->input('last_name', $options);
					echo $form->input('email', $options);
					echo $form->input('phone', $options);
					echo $form->input('mobile', $options);
					?>
				</div>
			</fieldset>
			
			<div class="form-actions">
				<?php echo $form->button('Save Changes'); ?>
			</div>
			
		<?php echo $form->end(); ?>

	</div>
</div>