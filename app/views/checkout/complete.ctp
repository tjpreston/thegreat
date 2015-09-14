<?php
	$this->set('body_id', 'checkout4');
?>

<div class="grid_12" style="min-height:500px">
	<?php echo $this->element('checkout/nav', array('step' => 'complete')); ?>

	<div class="header">
		<h1><span class="face1">Order</span> <?php echo ($order['Order']['success']) ? '<span class="face2">Complete</span>' : '<span class="face2">Failed</span>'; ?></h1>
	</div>
	
	
	<?php if ($order['Order']['success']): ?>
		<p>Thank you for your order. You will receive a confirmation email shortly.</p>
		<p>Your order reference is 
			<strong>

				<?php if ($this->Session->check('Auth.Customer.id')): ?>
					<a class="blue" href="/orders/view/<?php echo intval($order['Order']['id']); ?>">
				<?php endif; ?>

				<?php echo h($order['Order']['ref']); ?>

				<?php if ($this->Session->check('Auth.Customer.id')): ?>
					</a>
				<?php endif; ?>

			</strong></p>
	<?php else: ?>
		<p>There was a problem with your order. Details returned from our payment provider are below.</p>
		<p><?php echo h($order['Order']['error']); ?></p>
	    <p>Please contact us for assistance quoting your order reference.</p>
	    <p>Your order reference is <strong><?php echo h($order['Order']['ref']); ?></strong></p>
	<?php endif; ?>




<?php

	/* Track sale with Google Analytics */

	/* Escape quotes in some variables that we'll be using */
	$shippingTown = addslashes($order['Order']['shipping_town']);
	$shippingCounty = addslashes($order['Order']['shipping_county']);
	$shippingCountry = addslashes($order['ShippingCountry']['name']);

	$analyticsCode = "
		_gaq.push(['_addTrans',
			'{$order['Order']['ref']}',				// order ID - required
			'', 									// Affiliate or store name (unused)
			'{$order['Order']['grand_total']}',		// total - required
			'', 									// Tax (unused)
			'{$order['Order']['shipping_cost']}',	// shipping
			'{$shippingTown}',						// city
			'{$shippingCounty}',					// state or province
			'{$shippingCountry}'					// country
		]);
	";

	foreach($order['OrderItem'] as $item){
		$productSku = addslashes($item['product_sku']);
		$productName = addslashes($item['product_name']);

		$analyticsCode .= "
			_gaq.push(['_addItem',
				'{$order['Order']['ref']}',	// order ID - required
				'{$productSku}',			// SKU/code - required
				'{$productName}',			// product name
				'', 						// Category or variation (unused)
				'{$item['price']}',			// unit price - required
				'{$item['qty']}'			// quantity - required
			]);
		";
	}

	$analyticsCode .= "
	_gaq.push(['_trackTrans']); //submits transaction to the Analytics servers
	";

	// Send tracking code to layout for inclusion in the page <head>
	$this->set(compact('analyticsCode'));

?>
</div>
<div class="clear"></div>