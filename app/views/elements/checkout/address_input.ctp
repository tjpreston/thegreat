
<?php $options = array(
	'between' => '<div class="input-box">',
	'after' => '</div>'
); ?>

<div class="inputs">
	
	<?php

	if ($address == 'shipping')
	{
		echo $form->input($model . '.company_name', $options);
		echo $form->input($model . '.first_name', $options);
		echo $form->input($model . '.last_name', $options);
	}
	
	$varname = $address . 'Countries';
	$countries = $varname;
	
	echo $form->input($model . '.address_1', $options + array('label' => 'Address'));
	echo $form->input($model . '.address_2', $options + array('label' => '&nbsp;'));
	echo $form->input($model . '.town', $options + array('label' => 'Town / City'));
	echo $form->input($model . '.county', $options);
	echo $form->input($model . '.postcode', $options);
	echo $form->input($model . '.country_id', $options);

	echo $form->input($model . '.phone', $options + array('label' => 'Telephone'));
	
	?>
	
	<?php if (!empty($register)): ?>
		<?php echo $form->input('BillingAddress.remember', array(
			'type' => 'checkbox',
			'label' => 'Save this address for my next order?'
		)); ?>
	<?php endif; ?>
	
</div>

<div class="form-actions">
	<p id="cancel-new-<?php echo $address; ?>"<?php echo (empty($showCancel)) ? ' style="display: none;"' : ''; ?>>
		<a href="#" class="btn btn-small">Cancel</a>
	</p>
</div>
	

		