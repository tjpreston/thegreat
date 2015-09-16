
<div id="delivery">

	<?php if(!empty($invalidFields['shipping_zone_id']) || !empty($invalidFields['shipping_carrier_service_id'])): ?>
		<div id="flashMessage" class="failure">Please choose your delivery location.</div>
	<?php endif; ?>

	<div class="clear"></div>
  
	<?php

	$options = array(
		'label' => 'Delivery to',
		'options' => $availableShippingZones,
		'id' => 'shipping-location-select',
		'selected' => $basket['Basket']['shipping_zone_id']
	);
	if (count($availableShippingZones) > 1)
	{
		$options['empty'] = array(0 => 'Please select zone...');
		echo $form->input('Basket.shipping_zone_id', $options + array('div' => array('id' => 'del-location')));
	}
	else
	{
		echo $form->input('Basket.shipping_zone_id', $options + array(
			'value' => key($availableShippingZones),
			'type' => 'hidden'
		));
	}
	$forceCourier = false;
	foreach ($basketItems as $basketItem) {
		if($basketItem['Product']['courier_shipping_only']) {
			$forceCourier = true;
		}
	}
	?>

	<div id="del-services">
		<?php if($forceCourier): ?>
			<p>Matches can only be sent via our UK Courier</p>
			<?php if($basket['Basket']['shipping_zone_id'] != 1):?>
			<p>If you would like these items delivered outside of the UK please <a href="mailto:<?php echo Configure::Read('Site.email'); ?>">email The Great British Shop</a></p>
			<?php endif;?>
		<?php endif;?>
		<?php if(!empty($availableShippingServices)) :?>
			<?php foreach ($availableShippingServices as $p => $shippingService): ?>
				<?php if(($forceCourier && $shippingService['ShippingCarrier']['id'] == 2) || !$forceCourier):?>
					<div class="del-service-row">
						
						<?php $checked = ($basket['Basket']['shipping_carrier_service_id'] == $shippingService['ShippingCarrierService']['id']) ? ' checked="checked"' : ''; ?>
						<div class="del-check">
							<input type="radio" name="data[Basket][shipping_carrier_service_id]" value="<?php echo intval($shippingService['ShippingCarrierService']['id']); ?>"<?php echo $checked; ?> />
						</div>
						
						<div class="del-details">
							<label>
								<?php echo h($shippingService['ShippingCarrierService']['name']); ?>
								
								<?php if (!empty($shippingService['ShippingCarrierService']['delivery_time'])): ?>
									<?php echo h($shippingService['ShippingCarrierService']['delivery_time']); ?>.
								<?php endif; ?>
								<?php if (!empty($shippingService['Price']['id'])): ?>
									<?php
									$price = floatval($shippingService['Price']['price']);
									$price = (!empty($price)) ? $activeCurrencyHTML . ' ' . number_format($price, 2) : 'Free';
									echo '<span class="del-price">Price: ' . $price . '</span>';
									?>
								<?php else: ?>
									<span class="del-will-contact">Please <a href="/pages/contact">contact us</a> for an accurate shipping cost</span>
								<?php endif; ?>
								
							</label>
						</div>
					</div>
				<?php endif;?>

			<?php endforeach; ?>
		<?php else:?>
			<p>Sorry, we cannot deliver these items to your chosen location, please <a href="mailto:<?php echo Configure::Read('Site.email'); ?>">email The Great British Shop</a> to discuss alternative delivery.</p>
		<?php endif;?>
	</div>
	<?php if($forceCourier && $basket['Basket']['shipping_carrier_service_id'] != 2): ?>
	<?php else: ?>
		<?php if (isset($shippingInfo['Price']['price'])): ?>
		
			<div id="delivery-cost">
				<p id="delivery-amount"><?php echo ($shippingInfo['Price']['price'] > 0 ? $activeCurrencyHTML . number_format(floatval($shippingInfo['Price']['price']), 2) : 'FREE'); ?></p>
				<p id="delivery-label">Delivery:</p>
			</div>
			
		<?php endif; ?>
	<?php endif; ?>
  
</div>
