<?php

$options = array(
	'label' => false,
	'div' => false,
);

$errorOptions = array(
	'escape' => false,
	'wrap' => false,
);

$countryOptions = $options;
switch ($record['Basket']['shipping_zone_id']) {
	case 1:
		$countryOptions['default'] = 232;
		break;

	case 2:
		$countryOptions['default'] = 110;
		break;
	
	default:
		# code...
		break;
}

?>

<table cellspacing="0" cellpadding="0" border="0"<?php if ($address == 'shipping') echo ' style="margin-top: 10px;"'; ?>>
	<tbody>
		<?php if ($address == 'shipping'): ?>
			<tr<?php echo $form->error($model . '.first_name', ' class="error"', $errorOptions); ?>>
				<th class="mandatory">First Name</th>
				<td class="error"><?php echo $form->input($model . '.first_name', $options); ?></td>
			</tr>
			<tr<?php echo $form->error($model . '.last_name', ' class="error"', $errorOptions); ?>>
				<th class="mandatory">Surname</th>
				<td class="error"><?php echo $form->input($model . '.last_name', $options); ?></td>
			</tr>
			<tr<?php echo $form->error($model . '.company_name', ' class="error"', $errorOptions); ?>>
				<th>Company Name</th>
				<td class="error"><?php echo $form->input($model . '.company_name', $options); ?></td>
			</tr>
		<?php endif; ?>
		<tr<?php echo $form->error($model . '.address_1', ' class="error"', $errorOptions); ?>>
			<th class="mandatory">Address *</th>
			<td class="error"><?php echo $form->input($model . '.address_1', $options); ?></td>
		</tr>
		<tr<?php echo $form->error($model . '.address_2', ' class="error"', $errorOptions); ?>>
			<th>&nbsp;</th>
			<td><?php echo $form->input($model . '.address_2', $options); ?></td>
		</tr>
		<tr<?php echo $form->error($model . '.town', ' class="error"', $errorOptions); ?>>
			<th class="mandatory">Town/City *</th>
			<td class="error"><?php echo $form->input($model . '.town', $options); ?></td>
		</tr>
		<tr<?php echo $form->error($model . '.county', ' class="error"', $errorOptions); ?>>
			<th>County</th>
			<td class="error"><?php echo $form->input($model . '.county', $options); ?></td>
		</tr>
		<tr id="postcode"<?php echo $form->error($model . '.postcode', ' class="error"', $errorOptions); ?>>
			<th class="mandatory">Postcode *</th>
			<td class="error"><?php echo $form->input($model . '.postcode', $options); ?></td>
		</tr>
		<tr<?php echo $form->error($model . '.country_id', ' class="error"', $errorOptions); ?>>
			<th class="mandatory">Country *</th>
			<td class="error"><?php echo $form->input($model . '.country_id', $countryOptions); ?></td>
		</tr>
	</tbody>
</table>


<?php if (!empty($register)): ?>
	<?php echo $form->input('BillingAddress.remember', array(
		'type' => 'checkbox',
		'label' => 'Save this address for my next order?'
	)); ?>
<?php endif; ?>


<p id="cancel-new-<?php echo $address; ?>"<?php echo (empty($showCancel)) ? ' style="display: none;"' : ''; ?>>
	<a href="#" class="btn btn-small">Cancel</a>
</p>