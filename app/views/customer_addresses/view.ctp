<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Address</span> <span class="face2">Book</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'address')); ?>

	<div class="grid_18 omega">

		<?php $options = array(
			'between' => '<div class="input-box">',
			'after' => '</div>'
		); ?>
		
		<p class="intro">Speed up the checkout process by saving your most frequently used addresses here.</p>

		<?php echo $this->Session->flash(); ?>
		<?php echo $form->create('CustomerAddress', array('class' => 'form', 'action' => 'save')); ?>

		<fieldset>
			<?php if(!empty($this->data['CustomerAddress']['id'])): ?>
				<div class="heading">EDIT ADDRESS</div>
			<?php else: ?>
				<div class="heading">ADD ADDRESS</div>
			<?php endif; ?>
			<div class="inputs">
				<?php
				echo $form->input('id');
				echo $form->input('first_name', $options);
				echo $form->input('last_name', $options);
				echo $form->input('company_name', $options);
				echo $form->input('address_1', am($options, array('label' => 'Address Line 1')));
				echo $form->input('address_2', am($options, array('label' => 'Address Line 2')));
				echo $form->input('town', $options);
				echo $form->input('county', $options);
				echo $form->input('country_id', $options);
				echo $form->input('postcode', $options);
				echo $form->input('phone', am($options, array('label' => 'Telephone')));
				?>
			</div>
		</fieldset>
			
		<div class="form-actions">
			<?php echo $this->Html->link('<span class="face2">Cancel</span>', array('action' => 'index'), array('class' => 'dual', 'escape' => false, 'style' => 'margin-right:10px')); ?>
			<?php echo $form->button('Save'); ?>
		</div>
			
		<?php echo $form->end(); ?>

	</div>

</div>