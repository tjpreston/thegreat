<?php $register = ($mode == 'register'); ?>

<?php echo $form->create('Customer', array('class' => 'form', 'url' => '/checkout/save')); ?>
	
<?php
echo $form->hidden('mode', array('value' => $mode));
$options = array(
	'between' => '<div class="input-box">',
	'after' => '</div>'
);
?>

<fieldset>
	<legend>Personal Information</legend>
	<div class="inputs">
		<?php
		echo $form->input('Customer.first_name', $options);
		echo $form->input('Customer.last_name', $options);
		echo $form->input('Customer.email', $options + array('label' => 'E-mail'));
		echo $form->input('Customer.phone', $options + array('label' => 'Telephone'));
		?>
	</div>
</fieldset>

<fieldset>
	<legend>Billing Address</legend>
	<div class="inputs">
		<?php
		echo $form->input('CustomerBillingAddress.address_1', $options + array('label' => 'Address'));
		echo $form->input('CustomerBillingAddress.address_2', $options + array('label' => '&nbsp;'));
		echo $form->input('CustomerBillingAddress.town', $options + array('label' => 'Town / City'));
		echo $form->input('CustomerBillingAddress.county', $options);
		echo $form->input('CustomerBillingAddress.postcode', $options);
		echo $form->input('CustomerBillingAddress.country', $options + array(
			'value' => $basket['DeliveryCountry']['name'], 
			'readonly' => 'readonly'
		));
		?>
		<?php if (!empty($register)): ?>
			<?php echo $form->input('BillingAddress.remember', array(
				'type' => 'checkbox',
				'label' => 'Save this address for my next order?'
			)); ?>
		<?php endif; ?>
	</div>
</fieldset>

<fieldset>
	<legend>Delivery Address</legend>
	<div class="checkboxes">
		<?php 
		$checked = (!empty($showShippingAddress)) ? '' : 'checked';
		echo $form->input('Basket.ship_to_billing_address', array(
			'type' => 'checkbox',
			'label' => 'Deliver to my billing address',
			'checked' => $checked,
			'id' => 'deliver-to-billing'
		));
		?>
	</div>
	<script type="text/javascript">
	$(function() {
		if ($("#deliver-to-billing").attr('checked') === true) {
			$("#delivery-adddress-box").hide();
		}			
		$("#deliver-to-billing").click(function() {
			if ($(this).attr("checked") === false) {
				$("#delivery-adddress-box").show();
			}
			else {
				$("#delivery-adddress-box").hide();
			}
		});
	});
	</script>
	<div id="delivery-adddress-box" class="inputs">
		<?php
		echo $form->input('CustomerShippingAddress.first_name', $options);
		echo $form->input('CustomerShippingAddress.last_name', $options);
		echo $form->input('CustomerShippingAddress.address_1', $options + array('label' => 'Address'));
		echo $form->input('CustomerShippingAddress.address_2', $options + array('label' => '&nbsp;'));
		echo $form->input('CustomerShippingAddress.town', $options + array('label' => 'Town / City'));
		echo $form->input('CustomerShippingAddress.county', $options);
		echo $form->input('CustomerShippingAddress.postcode', $options);
		echo $form->input('CustomerShippingAddress.country', $options + array(
			'value' => $basket['DeliveryCountry']['name'], 
			'readonly' => 'readonly'
		));
		?>
		<?php 
		/* 
		if (!empty($register)): ?>
			<?php echo $form->input('DeliveryAddress.remember', array(
				'type' => 'checkbox',
				'label' => 'Save this address for my next order?'
			)); ?>
		<?php endif;
		*/
		?>
	</div>
</fieldset>

<?php if (!empty($register)): ?>
	<fieldset>
		<legend>Login Information</legend>
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

<div id="checkout-proceed">
	<p class="cards-row">Proceed to Confirmation</p>
	<?php echo $form->submit('/img/bn-continue.png', array('id' => 'continue-button', 'class' => 'green-button', 'div' => false)); ?>
</div>

<?php echo $form->end(); ?>


