<?php

$activeServiceID = $basket['Basket']['shipping_carrier_service_id'];
$activeService = array();

foreach($availableShippingServices as $service){
	$id = $service['ShippingCarrierService']['id'];
	if($activeServiceID == $id){
		$activeService = $service;
	}
}

?>

<h3>Post &amp; Packaging</h3>

<p>Sent via: <?php echo $activeService['ShippingCarrier']['name'] . ' ' . $activeService['ShippingCarrierService']['name']; ?></p>

<div class="location">
	<?php
	
	$options = array(
		'label' => 'Delivery Location',
		'options' => $availableShippingZones,
		'id' => 'shipping-location-select',
		'selected' => $basket['Basket']['shipping_zone_id']
	);

	if (count($availableShippingZones) > 1)
	{
		//$options['empty'] = array(0 => 'Please Select');
		echo $form->input('Basket.shipping_zone_id', $options + array('div' => false));
	}
	else
	{
		echo $form->input('Basket.shipping_zone_id', $options + array(
			'value' => key($availableShippingZones),
			'type' => 'hidden'
		));
	}

	$price = $activeService['ShippingCarrierServiceCountriesPerItemPrice']['price'];
	if($price == 0){
		$price = ' <strong>FREE</strong>';
	}

	echo '<span>' . $activeCurrencyHTML . $price . '</span>';

	?>

</div>