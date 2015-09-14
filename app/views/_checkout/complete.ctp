<?php
	$this->set('body_id', 'checkout4');

	echo $this->Html->css(array(
		'checkout',
	), null, array('inline' => false));
?>
<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
</div>
<div id="content">
	<?php echo $this->element('basket/progress', array('selected' => 'receipt')); ?>

	<h1><?php echo ($order['Order']['success']) ? 'Order Receipt' : 'Order Failed'; ?></h1>
	<?php echo $this->element('template/breadcrumbs'); ?>
	
	<?php if ($order['Order']['success']): ?>

	<div class="checkout">
		<h4 class="ref">
			<span>Thank You For Your Order</span>
			<strong>REF: #<?php echo h($order['Order']['ref']); ?></strong>
		</h4>
		<p class="msg">Your order has been placed successfully a copy of this confirmation has been sent to <?php echo h($order['Order']['customer_email']); ?></p>

		<table cellspacing="1" cellpadding="0" border="0" id="basket" class="receipt">
			<tbody>
				<tr>
					<th style="text-align:left; width: 700px;">Description</th>
					<th style="text-align:left">Price</th>
					<th style="text-align:left">Quantity</th>
					<th style="text-align:left">Total</th>
				</tr>
				<?php
					foreach ($order['OrderItem'] as $k => $item){
						echo $this->element('checkout/receipt_basket_row', array('k' => $k, 'item' => $item));
					}

					$rowspan = 2;

					if($order['Order']['tax_rate'] == 0){
						$rowspan++;

						$taxRate = Configure::read('Tax.rate');
						$taxRate = (100 + $taxRate) / 100;

						$vatReduction = ($order['Order']['grand_total'] * $taxRate) - $order['Order']['grand_total'];
					}

					if(!empty($order['Order']['coupon_code'])){
						$rowspan++;

						$voucher = $order['Order']['coupon_code'];
						$discount = $order['Order']['discount_total'];
					}
				?>
				<tr>
					<td rowspan="<?php echo $rowspan; ?>" class="postage">
						<h3>Post &amp; Packaging</h3>
						<p>Sent via: <?php echo h($order['Order']['shipping_carrier_service_name']); ?></p>
						<div class="location">
							<span style="margin-right:12px">Price</span> 
							<?php

							$price = $order['Order']['shipping_cost'];
							if($price == 0){
								$price = ' <strong>FREE</strong>';
							}

							?>
							<span>£ <?php echo $price; ?></span>
						</div>
					</td>
					<td colspan="3" class="subtotal">
						<strong>Sub Total</strong>
						<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($order['Order']['subtotal']), 2); ?>
					</td>
				</tr>
				<?php if(!empty($vatReduction)): ?>
				<tr>
					<td colspan="3" class="subtotal" style="font-size:100%">
						<strong>VAT Reduction</strong>
						&ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($vatReduction, 2); ?>
					</td>
				</tr>
				<?php endif; ?>
				<?php if(!empty($voucher)): ?>
				<tr>
					<td colspan="3" class="subtotal" style="font-size:100%">
						<strong>Voucher Code (<?php echo h($voucher); ?>)</strong>
						&ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($discount, 2); ?>
					</td>
				</tr>
				<?php endif; ?>
				<tr>
					<td colspan="3" class="totalcost">
						<strong>Total</strong>
						<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($order['Order']['grand_total']), 2); ?>
					</td>
				</tr>
			</tbody>
		</table>

		<div class="pane" id="pane-a">
			<h2>Delivery Address:</h2>
			<p><?php
				echo '<strong>' . h($order['Order']['shipping_first_name'] . ' ' . $order['Order']['shipping_last_name']) . '</strong>' . '<br/>';
				echo h($order['Order']['shipping_address_1']) . '<br/>';
				if(!empty($order['Order']['shipping_address_2'])){
					echo h($order['Order']['shipping_address_2']) . '<br/>';
				}
				echo h($order['Order']['shipping_town']) . '<br/>';
				echo h($order['Order']['shipping_county']) . '<br/>';
				echo h($order['Order']['shipping_postcode']) . '<br/>';
				echo h($order['ShippingCountry']['name']);
			?></p>
			<p>
				<strong>Tel:</strong> <?php echo h($order['Customer']['phone']); ?><br/>
				<strong>Email:</strong> <?php echo h($order['Order']['customer_email']); ?>
			</p>
		</div>

		<?php if($this->Session->read('Auth.Customer.trade') != 1): ?>
		<div class="pane" id="pane-b">
			<h2>Options:</h2>
			<p>
				<strong>Gift Wrap:</strong> <?php echo($order['Order']['gift_wrap'] ? 'Yes' : 'No'); ?><br/>
				<?php if($order['Order']['gift_wrap']): ?>
				<strong>Gift Message:</strong> <?php echo h($order['Order']['gift_message']); ?>
				<?php endif; ?>
			</p>
			<p>
				<strong>Watch Sizing:</strong> <?php echo($order['Order']['watch_sizing'] ? 'Yes' : 'No'); ?><br/>
				<?php if($order['Order']['watch_sizing']): ?>
				<strong>Wrist Size:</strong> <?php echo (empty($order['Order']['wrist_size']) ? 'Not specified' : h($order['Order']['wrist_size']) . ' cm'); ?>
				<?php endif; ?>
			</p>
		</div>

		<div class="pane" id="pane-c">
			<h2>After Sales Retailer:</h2>
			<?php if(!empty($order['StockistCommission']['Stockist'])): ?>
				<p><?php
					$stockist = $order['StockistCommission']['Stockist'];
					echo '<strong>' . h($stockist['name']) . '</strong>' . "<br/>";
					echo h($stockist['address_1']) . "<br/>";
					if(!empty($stockist['address_2'])){
						echo h($stockist['address_2']) . "<br/>";
					}
					if(!empty($stockist['address_3'])){
						echo h($stockist['address_3']) . "<br/>";
					}
					echo h($stockist['town']) . "<br/>";
					echo h($stockist['county']) . "<br/>";
					echo h($stockist['postcode']);
				?></p>

				<p><?php
					if(!empty($stockist['telephone'])){
						echo '<strong>Tel:</strong> ' . h($stockist['telephone']) . '<br/>';
					}
					if(!empty($stockist['email'])){
						echo '<strong>Email:</strong> ' . h($stockist['email']) . '<br/>';
					}
					if(!empty($stockist['website'])){
						echo '<strong>Website:</strong> ' . h($stockist['website']) . '<br/>';
					}
				?></p>

			<?php else: ?>
			<p>None chosen.</p>
			<?php endif; ?>
		</div>
		<?php endif; ?>

	</div>

	<?php else: ?>
		<p>Your card was declined. The reason given to us by our banking system for this was:<br/>
		<strong><?php echo h($order['Order']['error']); ?></strong></p>
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

<!-- REACT Conversion Pixel - Michel Herbelin - DO NOT MODIFY -->
<script src="https://secure.adnxs.com/px?id=54185&t=1" type="text/javascript"></script>
<!-- End of REACT Conversion Pixel -->

<?php echo $this->element('checkout/linkshare_tracking'); ?>


</div>
<div class="clear"></div>

<script src="//config1.veinteractive.com/tags/E8BDB55F/0665/4B59/90DC/C7A7E952145D/tag.js" type="text/javascript" async></script>
<img src="//drs2.veinteractive.com/DataReceiverService.asmx/Pixel?journeycode=E8BDB55F-0665-4B59-90DC-C7A7E952145D" width="1" height="1"/>