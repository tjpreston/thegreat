
<?php

echo $form->input('CustomerAddress.' . $k . '.company_name');
echo $form->input('CustomerAddress.' . $k . '.first_name');
echo $form->input('CustomerAddress.' . $k . '.last_name');
echo $form->input('CustomerAddress.' . $k . '.address_1');
echo $form->input('CustomerAddress.' . $k . '.address_2');
echo $form->input('CustomerAddress.' . $k . '.town');

echo $form->input('CustomerAddress.' . $k . '.country_id', array(
	'empty' => array(0 => 'Please Select -----------------')
));
echo $form->input('CustomerAddress.' . $k . '.county');

echo $form->input('CustomerAddress.' . $k . '.postcode');
echo $form->input('CustomerAddress.' . $k . '.phone');

?>


<?php if (is_numeric($id)): ?>

	<!--
	<div class="radio-inputs">
		
		<?php $billingChecked = (!empty($defaultBillingAddress) && ($defaultBillingAddress == $id)) ? ' checked="checked"' : ''; ?>
		<div class="input radio">
			<input type="radio" name="data[Customer][default_billing_address_id]" value="<?php echo $id; ?>"<?php echo $billingChecked; ?> />
			<label>Default Billing Address</label>
		</div>
		
		<?php $shippingChecked = (!empty($defaultShippingAddress) && ($defaultShippingAddress == $id)) ? ' checked="checked"' : ''; ?>
		<div class="input radio">
			<input type="radio" name="data[Customer][default_shipping_address_id]" value="<?php echo $id; ?>"<?php echo $shippingChecked; ?> />
			<label>Default Shipping Address</label>
		</div>
		
	</div>
	-->

<?php endif; ?>
