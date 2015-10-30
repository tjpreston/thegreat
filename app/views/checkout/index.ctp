<?php
	$this->set('body_id', 'checkout1');
?>

<div class="grid_24">
	<?php echo $this->element('checkout/nav', array('step' => 'details')); ?>

	<div class="header">
		<h1><span class="face1">Your</span> <span class="face2">Details</span></h1>
		<p class="intro">Please enter your details into the following form. <span class="red">*</span> denotes a required field.</p>
	</div>
	
	<?php echo $session->flash(); ?>
	
	<?php $register = ($mode == 'register'); ?>

	<?php echo $form->create('Customer', array('class' => 'form', 'url' => '/checkout/save')); ?>
		
	<?php
	echo $form->hidden('mode', array('value' => $mode));
	$options = array(
		'between' => '<div class="input-box">',
		'after' => '</div>'
	);
	?>
	
	<?php if ($session->check('Auth.Customer.id')): ?>
		<fieldset>
			<div class="heading">PERSONAL INFORMATION</div>	
			<div class="confirmation-group">
				<dl>
					<dt>First Name</dt>
					<dd><?php echo h($customer['Customer']['first_name']); ?></dd>
					<dt>Last Name</dt>
					<dd><?php echo h($customer['Customer']['last_name']); ?></dd>
					<?php if (!empty($customer['Customer']['company_name'])): ?>
						<dt>Company Name</dt>
						<dd><?php echo h($customer['Customer']['company_name']); ?></dd>
					<?php endif;?>
					<dt>Email</dt>
					<dd><?php echo h($customer['Customer']['email']); ?></dd>
					<dt>Telephone</dt>
					<dd><?php echo h($customer['Customer']['phone']); ?></dd>
					<dt>Mobile</dt>
					<dd><?php echo h($customer['Customer']['mobile']); ?></dd>
				</dl>
				<a href="/customers/account_information" class="checkout-edit dual">
					<span class="face1">Edit</span> <span class="face2">Details</span>
				</a>
			</div>
		</fieldset>
	<?php else: ?>

		<fieldset>
			<div class="heading">PERSONAL INFORMATION</div>	
			<div class="inputs">				
				<?php
				echo $form->input('Customer.first_name', $options);
				echo $form->input('Customer.last_name', $options);
				echo $form->input('Customer.company_name', $options);
				echo $form->input('Customer.email', $options + array('label' => 'E-mail'));
				echo $form->input('Customer.phone', $options + array('label' => 'Telephone'));
				echo $form->input('Customer.mobile', $options);
				?>
			</div>
		</fieldset>

	<?php endif; ?>
	
	


	<fieldset>		
		<div class="heading">BILLING ADDRESS</div>	
	
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
				<div class="clearfix"></div>
				<p id="new-billing"><a href="#" class="btn btn-small">+ Enter a new address</a></p>

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
				<div class="clearfix"></div>
				<p id="new-billing" style="display: none;"><a href="#" class="btn btn-small">+ Enter a new address</a></p>
				
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
		
	</fieldset>

	



	
	<fieldset>
		
		<div class="heading">DELIVERY ADDRESS</div>
                <?php //xdebug_break(); ?>
               <!-- <?php if($basket['Basket']['shipping_carrier_service_id'] == 4): ?> 
                    
                <div class="intro" style="margin-left: 36px; margin-bottom: 60px; ">
                    <p>You have selected Click & Collect</p>
                </div>
                
                <?php else: ?> -->
		<div class="checkboxes" style="margin-left: 36px;">
			<?php
			$checked = (!empty($showShippingAddress)) ? '' : 'checked';
			echo $form->input('Basket.ship_to_billing_address', array(
				'type' => 'checkbox',
				'label' => 'Deliver to my billing address',
				'checked' => $checked,
				'id' => 'deliver-to-billing',
				'style' => 'margin-left: 0;'
			));
			?>
		</div>
		
		<div id="delivery-address-box">
		
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
					<div class="clearfix"></div>
					<p id="new-shipping"><a href="#" class="btn btn-small">+ Enter a new address</a></p>
	
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
					<div class="clearfix"></div>
					<p id="new-shipping" style="display: none;"><a href="#" class="btn btn-small">+ Enter a new address</a></p>
					
				<?php endif; ?>
	
			<?php else: ?>
			
				<?php echo $form->hidden('CustomerShippingAddress.new_address', array(
					'id' => 'new-shipping-address',
					'value' => 1
				)); ?>
			
				<?php echo $this->element('checkout/address_input', array(
					'address' => 'shipping',
					'model' => 'CustomerShippingAddress',
					'showCancel' => false
				)); ?>
			
			<!-- <?php endif; ?> -->
			
		</div>
		
	</fieldset>
	<?php endif; ?>		
			




	<?php if (!empty($register)): ?>
		<fieldset>
			<div class="heading">LOGIN INFORMATION</div>	
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


	<?php /* fieldset>
		<div class="heading">Order Information</div>	
		<div class="inputs">
			<?php
			echo $form->input('Basket.purchase_order', $options + array(
				'label' => 'Purchase Order <small>(optional)</small>',
				'type' => 'text',
				'value' => ''
			));
			?>
		</div>
	</fieldset> */ ?>

<div class="NewsletterOptOut clearfix">
	<?php echo $form->input('Customer.newsletter_opt_out', array(
		'label' => 'Please check the box if you would prefer not to be added to our newsletter mailing list',
		'type' => 'checkbox',
	)); ?>
</div>

	<div class="right">
		<?php echo $form->button('<span class="face1">Continue</span> <span class="face2">Checkout</span>', array('class' => 'send-button', 'div' => false, 'style' => 'margin-top:18px', 'class' => 'dual')); ?>
	</div>
	<div class="checkout-row">
			<img class="cards-img" src="/img/accepted-cards.png" />
			<!-- PayPal Logo --><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" class="paypal-img";" alt="PayPal Logo"></a><!-- PayPal Logo -->
			<div class="arrow-right"></div>
		</div>
                <div style="float: left;"><span class="face1">Powered by:</span><span  style="float: right; padding-right: 560px;"><a href="http://www.worldpay.com/" target="_blank" title="Payment Processing - WorldPay - Opens in new browser window"><img style="padding-left: 5px" src="/img/icons/worldpay.png" /></a></span>
	
	<?php echo $form->end(); ?>



	</div>	
<div class="clear"></div>