<?php

if($type == 'billing'){
	$address = $basket['CustomerBillingAddress'];
	$country = $basket['CustomerBillingAddressCountry'];
} else {
	if($basket['Basket']['ship_to_billing_address']){
		$address = $basket['CustomerBillingAddress'];
		$country = $basket['CustomerBillingAddressCountry'];
	} else {
		$address = $basket['CustomerShippingAddress'];
		$country = $basket['CustomerShippingAddressCountry'];
	}
}

$addr = '';
if($type != 'billing'){
	$addr .= $address['first_name'] . ' ' . $address['last_name'] . "\n";
}

$addr .= $address['address_1'] . "\n";
if(!empty($address['address_2'])){
	$addr .= $address['address_2'] . "\n";
}
$addr .= $address['town'] . "\n";
$addr .= $address['county'] . "\n";
$addr .= $address['postcode'] . "\n";
$addr .= $country['name'];

?>
<table cellspacing="0" cellpadding="0" border="0">
	<tbody>
		<tr>
			<th valign="top" style="line-height:1.6">
				<?php echo ucfirst($type); ?> Address
				<?php if($type == 'shipping' && $basket['Basket']['ship_to_billing_address']): ?>
					<br/><span style="color: #ccc">(same as billing)</span>
				<?php endif; ?>
			</th>
			<td style="line-height:1.6">
				<?php echo nl2br(h($addr)); ?>
			</td>
		</tr>
	</tbody>
</table>