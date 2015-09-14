<?php
	$this->set('body_id', 'checkout1');

	echo $this->Html->css(array(
		'checkout',
	), null, array('inline' => false));
?>
<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
</div>

<div id="content">
	<?php echo $this->element('basket/progress', array('selected' => 'details')); ?>

	<h1>Details</h1>

	<?php echo $this->element('template/breadcrumbs'); ?>
	
	<?php $register = ($mode == 'register'); ?>

	<?php echo $form->create('Customer', array('class' => 'form', 'url' => '/checkout/save')); ?>

	<?php
	echo $form->hidden('mode', array('value' => $mode));

	$options = array(
		'label' => false,
		'div' => false,
	);

	$errorOptions = array(
		'escape' => false,
		'wrap' => false,
	);
	?>
	<?php echo $session->flash(); ?>
	
	<div class="checkout">
		<p class="mandatory">Note! Fields marked * are mandatory</p>

		<h4>Your Contact Details</h4>
		
	<?php if ($session->check('Auth.Customer.id')): ?>
		<table cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr>
					<th>First Name</th>
					<td class="error"><?php echo h($customer['Customer']['first_name']); ?></td>
				</tr>
				<tr>
					<th>Surname</th>
					<td class="error"><?php echo h($customer['Customer']['last_name']); ?></td>
				</tr>
				<tr>
					<th>Email</th>
					<td class="error"><?php echo h($customer['Customer']['email']); ?></td>
				</tr>
				<tr>
					<th>Telephone</th>
					<td class="error"><?php echo h($customer['Customer']['phone']); ?></td>
				</tr>
				<?php if(!empty($customer['Customer']['mobile'])): ?>
				<tr>
					<th>Mobile</th>
					<td class="error"><?php echo h($customer['Customer']['mobile']); ?></td>
				</tr>
				<?php endif; ?>
			</tbody>
		</table>

	<?php else: ?>

		<table cellspacing="0" cellpadding="0" border="0">
			<tbody>
				<tr<?php echo $form->error('Customer.first_name', ' class="error"', $errorOptions); ?>>
					<th class="mandatory">First Name *</th>
					<td class="error"><?php echo $form->input('Customer.first_name', $options); ?></td>
				</tr>
				<tr<?php echo $form->error('Customer.last_name', ' class="error"', $errorOptions); ?>>
					<th class="mandatory">Surname *</th>
					<td class="error"><?php echo $form->input('Customer.last_name', $options); ?></td>
				</tr>
				<tr<?php echo $form->error('Customer.email', ' class="error"', $errorOptions); ?>>
					<th class="mandatory">Email *</th>
					<td class="error"><?php echo $form->input('Customer.email', $options); ?></td>
				</tr>
				<tr<?php echo $form->error('Customer.phone', ' class="error"', $errorOptions); ?>>
					<th class="mandatory">Telephone *</th>
					<td class="error"><?php echo $form->input('Customer.phone', $options); ?></td>
				</tr>
				<tr<?php echo $form->error('Customer.mobile', ' class="error"', $errorOptions); ?>>
					<th>Mobile</th>
					<td class="error"><?php echo $form->input('Customer.mobile', $options); ?></td>
				</tr>
			</tbody>
		</table>

	<?php endif; ?>
	
	<h4>Your Billing Address</h4>

	<?php if (!empty($addresses)): ?>
	
		<?php echo $form->hidden('CustomerBillingAddress.new_address', array(
			'id' => 'new-billing-address',
			'value' => (!empty($newBillingAddress)) ? 1 : 0
		)); ?>
		
		<?php if (empty($newBillingAddress)): ?>
		
			<?php if (isset($validBillingAddress) && !$validBillingAddress): ?>
				<div class="failure" id="billingFlashMessage" style="margin-top: 20px; margin-bottom: 6px;">Please select a billing address.</div>
			<?php endif; ?>
			
			<?php echo $this->element('checkout/address_choose', array(
				'type' => 'billing', 
				'model' => 'CustomerBillingAddress'
			)); ?>
			
			<div id="new-billing-form" style="display: none;">
				<?php echo $this->element('checkout/address_input', array(
					'address' => 'billing',
					'model' => 'CustomerBillingAddress',
					'showCancel' => true
				)); ?>
			</div>
			<div class="clear-both"></div>
			<p id="new-billing"><a href="#" class="btn btn-small">Enter a new address</a></p>

		<?php else: ?>
			
			<?php echo $this->element('checkout/address_choose', array(
				'type' => 'billing',
				'model' => 'CustomerBillingAddress',
				'hidden' => true
			)); ?>
			
			<div id="new-billing-form">
				<?php echo $this->element('checkout/address_input', array(
					'address' => 'billing',
					'model' => 'CustomerBillingAddress',
					'showCancel' => true
				)); ?>
			</div>
			<div class="clear-both"></div>
			<p id="new-billing" style="display: none;"><a href="#" class="btn btn-small">Enter a new address</a></p>
			
		<?php endif; ?>

	<?php else: ?>
	
		<?php echo $form->hidden('CustomerBillingAddress.new_address', array(
			'id' => 'new-billing-address',
			'value' => 1
		)); ?>
	
		<?php echo $this->element('checkout/address_input', array(
			'address' => 'billing',
			'model' => 'CustomerBillingAddress',
			'showCancel' => false
		)); ?>
	
	<?php endif; ?>

	<h4>Your Delivery Address</h4>
	
	<div class="ship-to-billing" style="margin: 15px 0 25px 0;">
		<?php 
		$checked = (!empty($showShippingAddress)) ? '' : 'checked';
		echo $form->input('Basket.ship_to_billing_address', array(
			'type' => 'checkbox',
			'label' => 'Deliver to my billing address',
			'checked' => $checked,
			'id' => 'deliver-to-billing',
			'style' => 'margin:0 0 2px 0; vertical-align:middle'
		));
		?>
	</div>

	<div id="CustomerShippingAddressInput">
			
		<?php if (!empty($addresses)): ?>
		
			<?php echo $form->hidden('CustomerShippingAddress.new_address', array(
				'id' => 'new-shipping-address',
				'value' => (!empty($newShippingAddress)) ? 1 : 0
			)); ?>
			
			<?php if (empty($newShippingAddress)): ?>
			
				<?php if (empty($newShippingAddress) && (isset($validShippingAddress) && !$validShippingAddress)): ?>
					<div class="failure" id="shippingFlashMessage" style="margin-top: 6px; margin-bottom: 6px;">Please select a shipping address.</div>
				<?php endif; ?>
				
				<?php echo $this->element('checkout/address_choose', array(
					'type' => 'shipping', 
					'model' => 'CustomerShippingAddress'
				)); ?>
				
				<div id="new-shipping-form" style="display: none;">
					<?php echo $this->element('checkout/address_input', array(
						'address' => 'shipping',
						'model' => 'CustomerShippingAddress',
						'showCancel' => true
					)); ?>
				</div>
				<div class="clear-both"></div>
				<p id="new-shipping"><a href="#" class="btn btn-small">Enter a new address</a></p>

			<?php else: ?>
				
				<?php echo $this->element('checkout/address_choose', array(
					'type' => 'shipping', 
					'model' => 'CustomerShippingAddress',
					'hidden' => true
				)); ?>
				
				<div id="new-shipping-form">
					<?php echo $this->element('checkout/address_input', array(
						'address' => 'shipping',
						'model' => 'CustomerShippingAddress',
						'showCancel' => true
					)); ?>
				</div>
				<div class="clear-both"></div>
				<p id="new-shipping" style="display: none;"><a href="#" class="btn btn-small">Enter a new address</a></p>
				
			<?php endif; ?>

		<?php else: ?>
		
			<?php echo $form->hidden('CustomerShippingAddress.new_address', array(
				'id' => 'new-shipping-address',
				'value' => 1
			)); ?>
		
			<div id="CustomerShippingAddressInput">
			<?php echo $this->element('checkout/address_input', array(
				'address' => 'shipping',
				'model' => 'CustomerShippingAddress',
				'showCancel' => false
			)); ?>
			</div>
		
		<?php endif; ?>

	</div>

	<?php if (!empty($register)): ?>
		<fieldset>
			<div class="heading">Login Information</div>	
			<div class="inputs">
				<?php
				echo $form->input('Customer.password_main', $options + array(
					'label' => 'Password',
					'type' => 'password',
					'value' => ''
				));
				echo $form->input('Customer.password_confirm', $options + array(
					'label' => 'Confirm Password',
					'type' => 'password',
					'value' => ''
				));
				?>
			</div>
		</fieldset>
	<?php endif; ?>

	<?php echo $this->element('checkout/newsletter_signup'); ?>

	<?php
		if($record['Basket']['watch_sizing']){
			echo $this->element('checkout/watch_sizing');
		}

		if($record['Basket']['gift_wrap']){
			echo $this->element('checkout/gift_message');
		}
	?>

</div>

<div class="basket-continue">
	<?php echo $form->submit('/img/buttons/continue-secure.gif', array('div' => false, 'style' => 'float:right')); ?>
	<a href="/basket/"><img src="/img/buttons/back-to-my-bag.gif" alt="Back to My Bag"></a>
</div>

<?php echo $form->end(); ?>

</div>

<script src="//config1.veinteractive.com/tags/E8BDB55F/0665/4B59/90DC/C7A7E952145D/tag.js" type="text/javascript" async></script>
