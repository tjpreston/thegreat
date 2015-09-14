
	<?php echo $session->flash(); ?>
	
	<h1>Choose a <?php echo ucwords($address); ?> Address</h1>
	<p class="intro">Please enter your details into the following form. <span class="red">*</span> denotes a required field.</p>
	
	<?php echo $this->element('checkout/nav', array('step' => 'details')); ?>
	
	<div id="choose-addresses">
		<?php $i = 0; ?>
		<?php foreach ($records as $k => $record): ?>
			<?php
			$a = $record['CustomerAddress'];
			$last = ($i + 1 == count($records)) ? '' : '';
			?>
			<p class="address-choice">
				<strong><?php echo h($a['first_name']); ?> <?php echo h($a['last_name']); ?></strong><br />
				<?php echo h($a['address_1']); ?><br />
				<?php if (!empty($a['address_2'])): ?><?php echo h($a['address_2']); ?><br /><?php endif; ?>
				<?php echo h($a['town']); ?><br />
				<?php if (!empty($a['county'])): ?><?php echo h($a['county']); ?>, <?php endif; ?><?php echo h($a['postcode']); ?><br />
				<?php echo h($record['Country']['name']); ?><br />
				<a href="/customer_addresses/view/<?php echo intval($a['id']); ?>"><img src="/img/bn-choose.png" alt="Choose" /></a> 
			</p>
			<?php $i++; ?>
		<?php endforeach; ?>
	</div>
	
	<?php $options = array(
		'between' => '<div class="input-box">',
		'after' => '</div>'
	); ?>
	
	<?php echo $form->create('CustomerAddress', array('class' => 'form', 'action' => 'save')); ?>
		
		<fieldset>
			<legend>Or Add a New Address</legend>
			<div class="inputs">
				<?php if ($address == 'shipping'): ?>
					<?php echo $form->input('first_name', $options); ?>
					<?php echo $form->input('last_name', $options); ?>
				<?php endif; ?>
				<?php			
				echo $form->input('address_1', $options + array('label' => 'Address Line 1'));				
				echo $form->input('address_2', $options + array('label' => 'Address Line 2'));	
				echo $form->input('town', $options);
				echo $form->input('county', $options);
				echo $form->input('country_id', $options);
				echo $form->input('postcode', $options);
				?>					
			</div>
			<div class="checkboxes">
				<?php
				echo $form->input('default_billing_address', array(
					'type' => 'checkbox',
					'value' => 1,
					'label' => '<img src="/img/icons/money.png" /> Make this address my default billing address',
				));
				echo $form->input('default_shipping_address', array(
					'type' => 'checkbox',
					'value' => 1,
					'label' => '<img src="/img/icons/lorry.png" /> Make this address my default delivery address'
				));
				?>	
			</div>
		</fieldset>
		
		<?php echo $form->submit('/img/buttons/continue.png'); ?>
	
	<?php echo $form->end(); ?>
	
	
</div>
