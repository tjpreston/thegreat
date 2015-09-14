<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Order</span> <span class="face2">History</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'history')); ?>

	<div class="grid_18 omega">

		<fieldset>
			<h2>ORDER SUMMARY</h2>
			<dl class="view-order">
				<dt>Order Ref:</dt>
				<dd><?php echo h($record['Order']['ref']); ?></dd>
				<dt>Order Placed:</dt>
				<dd><?php echo $time->format('j F Y', $record['Order']['created']); ?></dd>
			</dl>
		</fieldset>
			

		<?php if ($itemMode != 'shipments'): ?>


			<table cellspacing="0" class="basket-table" style="margin-top: 10px;">
				<thead class="subheader">
					<tr>
						<th style="width: 600px; padding-left: 20px;">Item(s)</th>
						<th>Price</th>
						<th style="width: 30px;">Qty</th>
						<th>Item Total</th>
					</tr>
				</thead>
				<tbody>
				<?php $i = 0; ?>
				<?php foreach ($record['OrderItem'] as $k => $item): ?>
					<tr<?php echo (($i + 1) == count($record['OrderItem'])) ? ' class="no-border"' : ''; ?>>
						<td style="width: 260px; padding-left: 20px;">
							<?php echo h($item['product_name']); ?>
							<?php if(!empty($item['giftwrap_product_name'])): ?>
								<br/>
								<span class="giftwrap-details giftwrap-item-name">Gift wrapping</span>
								<?php if (!empty($item['custom_text'])): ?>
									<br />
									<p>Gift Card Message: <?php echo h($item['custom_text']); ?></p>
								<?php endif; ?>
							<?php endif; ?>
							</td>
						</td>
						<td style="width: 100px">
							<?php echo $record['Currency']['html'] . number_format($item['price'], 2); ?>
							<?php if(!empty($item['giftwrap_product_name'])): ?>
								<br/>
								<span class="giftwrap-details giftwrap-price"><?php echo $activeCurrencyHTML; ?> <?php echo number_format(($item['giftwrap_price'] / $item['qty']), 2); ?>
							<?php endif; ?>
						</td>
						<td style="width: 30px;"><?php echo intval($item['qty']); ?></td>
						<td style="width: 100px">
							<?php echo $record['Currency']['html'] . number_format(($item['price'] * $item['qty']), 2); ?>
							<?php if(!empty($item['giftwrap_product_name'])): ?>
								<br/>
								<span class="giftwrap-details giftwrap-total"><?php echo $activeCurrencyHTML; ?> <?php echo number_format($item['giftwrap_price'], 2); ?></span>
							<?php endif; ?>
						</td>
				
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
				</tbody>
				<tfoot class="totals">
					<tr>
						<td colspan="3" class="label">Sub-Total</td>
						<td><?php echo $record['Currency']['html'] . number_format($record['Order']['subtotal'], 2); ?></td>
					</tr>
					<tr>
						<td colspan="3" class="label">Delivery</td>
						<td><?php echo $record['Currency']['html'] . number_format($record['Order']['shipping_cost'], 2); ?></td>
					</tr>

					<?php if(!empty($record['Order']['discount_total']) && $record['Order']['discount_total'] > 0):?>
					<tr>
						<td colspan="3" class="label">Discount</td>
						<td><?php echo $record['Order']['coupon_code']; ?> - <?php echo $record['Currency']['html'] . number_format($record['Order']['discount_total'], 2); ?></td>
					</tr>
					<?php endif;?>

					<tr>
						<td colspan="3" class="label">Grand Total</td>
						<td><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'], 2); ?></td>
					</tr>
				</tfoot>
			</table>

		<?php else: ?>
			
				<div class="heading no-margin">ORDER SHIPMENTS</div>
				<?php if (empty($record['Order']['shipped'])): ?>
					<table cellspacing="0" class="basket-table" style="margin-bottom: 0;">
						<thead class="subheader">
							<tr>
								<th class="shipment-header" colspan="3">Not Yet Shipped</th>
							</tr>
						</thead>
						<?php $i = 0; ?>
						<?php foreach ($record['OrderItem'] as $k => $item): ?>
							<tbody>
							<?php if (empty($item['qty_to_ship'])): continue; endif; ?>
								<tr<?php echo (($i + 1) == count($record['OrderItem'])) ? ' class="no-border extra-padding"' : ''; ?>>
									<td style="width: 100px; padding-left: 66px;"><?php echo $item['product_sku']; ?></td>
									<td style="width: 260px;"><?php echo $item['product_name']; ?></td>
									<td>Qty: <?php echo intval($item['qty_to_ship']); ?></td>
								</tr>
							<?php $i++; ?>
						<?php endforeach; ?>
						</tbody>
					</table>
				<?php endif; ?>
			
			<table cellspacing="0" class="basket-table">
			<?php foreach ($shipments as $k => $shipment): ?>
				<thead class="subheader">
					<tr>
						<th class="shipment-header" colspan="3">
							Delivery #<?php echo $k + 1; ?>: Dispatched on <?php echo $time->format('j F Y', $shipment['Shipment']['created']); ?>
							<?php echo (1) ? '<span><a href="#">Track</a><span>' : ''; ?>
						</th>
					</tr>
				</thead>
				<tbody>
				<?php $i = 0; ?>
				<?php foreach ($shipment['ShipmentItem'] as $item): ?>
					<tr<?php echo (($i + 1) == count($shipment['ShipmentItem'])) ? ' class="no-border extra-padding"' : ''; ?>>
						<td style="width: 100px; padding-left: 66px;"><?php echo $item['OrderItem']['product_sku']; ?></td>
						<td style="width: 260px;"><?php echo $item['OrderItem']['product_name'];; ?></td>
						<td><?php echo 'Qty Shipped: ' . $item['qty_shipped']; ?></td>
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
				</tbody>
			<?php endforeach; ?>
			</table>
			
		<?php endif; ?>


		<fieldset>
			<h2>DELIVERY INFORMATION</h2>
			<dl class="view-order">
				<!-- <dt>Delivery Method:</dt>

				<dd><?php //echo h($record['Order']['shipping_carrier_service_name']); ?></dd> -->
				
				<dt>Delivery Address:</dt>
				<dd>
					<?php
					echo h($record['Order']['shipping_first_name']) . ' ' . h($record['Order']['shipping_last_name']) . ',<br />';
					echo h($record['Order']['shipping_address_1']) . ',<br />';
					if (!empty($record['Order']['shipping_address_2']))
					{
						echo h($record['Order']['shipping_address_2']) . ',<br />';
					}
					echo h($record['Order']['shipping_town']) . ',<br />';
					if (!empty($record['Order']['shipping_county']))
					{
						echo h($record['Order']['shipping_county']) . ', ';
					}
					echo h($record['Order']['shipping_postcode']) . '<br />';
					echo h($record['ShippingCountry']['name']);
					?>
				</dd>
			</dl>
		</fieldset>
		
		<?php if(!empty($record['Order']['order_note'])):?>
			<fieldset>
				<h2>OTHER INFORMATION:</h2>
				
				<dl  class="view-order">
					<dt>Order Notes:</dt>
					<dd><?php echo h($record['Order']['order_note']); ?></dd>
				</dl>
				
			</fieldset>
		<?php endif;?>
		<fieldset>
			<h2>PAYMENT INFORMATION</h2>
			<dl class="view-order">
				<?php if (empty($record['Order']['processor'])): ?>
					
					<dt>Payment Method:</dt>
					<dd>N/A - On Account</dd>
					
				<?php else: ?>
					<dt>Payment Processed By:</dt>
					<dd><?php echo Configure::read('Payments.processor'); ?></dd>
					
					<dt>Payment Method:</dt>
					<dd><?php echo h($record[Configure::read('Payments.processor') . 'Order']['cardType']); ?> (last 4 digits: <?php echo $record[Configure::read('Payments.processor') . 'Order']['AVS']; ?>)</dd>
				
				<?php endif; ?>
				
				<dt>Billing Address:</dt>
				<dd>
					<?php
					echo h($record['Order']['billing_address_1']) . ',<br />';
					if (!empty($record['Order']['billing_address_2']))
					{
						echo h($record['Order']['billing_address_2']) . ',<br />';
					}
					echo h($record['Order']['billing_town']) . ',<br />';
					if (!empty($record['Order']['billing_county']))
					{
						echo h($record['Order']['billing_county']) . ', ';
					}
					echo h($record['Order']['billing_postcode']) . '<br />';
					echo h($record['BillingCountry']['name']);
					?>
				</dd>
			</dl>
		</fieldset>
		
		<?php /*
		<?php $totalTax = $record['Order']['subtotal_tax'] + $record['Order']['shipping_tax']; ?>
		<fieldset>
			
			<h2>ORDER TOTALS</h2>
			<dl class="view-order totals">
				<dt>Item(s) Subtotal:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['subtotal'], 2); ?></dd>
				<dt>Postage &amp; Packing:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['shipping_cost'], 2); ?></dd>
				<!-- <dt>Total Before VAT:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'] - $totalTax, 2); ?></dd>
				<dt>VAT (at <?php echo floatval($record['Order']['tax_rate']); ?>%):</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($totalTax, 2); ?></dd> -->
				<?php if(!empty($record['Order']['discount_total']) && $record['Order']['discount_total'] > 0):?>
					<dt>Discount:</dt>
					<dd><?php echo $record['Order']['coupon_code']; ?> - <?php echo $record['Currency']['html'] . number_format($record['Order']['discount_total'], 2); ?></dd>
				<?php endif;?>
				<dt>Grand Total:</dt>
				<dd><strong><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'], 2); ?></strong></dd>

			</dl>
		</fieldset>
		*/ ?>

	</div>

</div>