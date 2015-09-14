<div id="leftcol">
	<?php echo $this->element('template/account_nav_panel'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">


	<div class="header">
		<h1>Order History</h1>
		<p class="intro">View your orders that you've placed with us.</p>
	</div>
	
	<div class="content-pad">
		
			<h2>Order Summary</h2>
			<dl class="view-order">
				<dt>Order Ref:</dt>
				<dd><?php echo h($record['Order']['ref']); ?></dd>
				<dt>Order Placed:</dt>
				<dd><?php echo $time->format('j F Y', $record['Order']['created']); ?></dd>
				<dt>Order Total:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'], 2); ?></dd>

				<?php if(!empty($record['Order']['purchase_order'])): ?>
				<dt>Purchase Order:</dt>
				<dd><?php echo $record['Order']['purchase_order']; ?></dd>
				<?php endif; ?>

			</dl>
			

		<?php if ($itemMode != 'shipments'): ?>


			<table cellspacing="0" class="order-table" style="margin: 40px 0;">
				<thead class="subheader">
					<tr>
						<th style="width: 30px; padding-left: 20px;">Qty</th>
						<th>Code</th>
						<th>Product Description</th>
						<th>Price</th>
					</tr>
				</thead>
				<tbody>
				<?php $i = 0; ?>
				<?php foreach ($record['OrderItem'] as $k => $item): ?>
					<tr<?php if(($k % 2) === 1){ echo ' class="even"'; } ?>>
						<td style="padding-left: 20px;"><?php echo intval($item['qty']); ?></td>
						<td style="width: 100px;"><?php echo h($item['product_sku']); ?></td>
						<td style="width: 260px;"><?php echo h($item['product_name']); ?></td>
						<td><?php echo $record['Currency']['html'] . number_format($item['price'], 2); ?></td>
				
					</tr>
					<?php $i++; ?>
				<?php endforeach; ?>
				</tbody>
			</table>

		<?php else: ?>
			
				<div class="heading no-margin">Order Shipments</div>
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


		<h2>Delivery Information</h2>
		<dl class="view-order">
			<dt>Delivery Method:</dt>

			<dd><?php echo h($record['Order']['shipping_carrier_service_name']); ?></dd>
			
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
		
		<?php if(!empty($record['Order']['order_note'])):?>
			<fieldset>
				<div class="heading">Other Information:</div>
				
				<dl  class="view-order">
					<dt>Order Notes:</dt>
					<dd><?php echo h($record['Order']['order_note']); ?></dd>
				</dl>
				
			</fieldset>
		<?php endif;?>


			<h2>Payment Information</h2>
			<dl class="view-order">

				<?php if (empty($record['Order']['processor'])): ?>
					
					<dt>Payment Method:</dt>
					<dd>N/A - On Account</dd>
					
				<?php else: ?>
				
					<dt>Payment Processed By:</dt>
					<dd>SagePay</dd>
					
					<dt>Payment Method:</dt>
					<dd><?php echo h($record['SagepayFormOrder']['card_type']); ?> (last 4 digits: <?php echo intval($record['SagepayFormOrder']['last_4_digits']); ?>)</dd>
				
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

		<?php $totalTax = $record['Order']['subtotal_tax'] + $record['Order']['shipping_tax']; ?>
			
			<h2>Order Totals</h2>
			<dl class="view-order totals">
				<dt>Item(s) Subtotal:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['subtotal'], 2); ?></dd>
				<!--<dt>Postage &amp; Packing:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['shipping_cost'], 2); ?></dd>
				<dt>Total Before VAT:</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'] - $totalTax, 2); ?></dd>
				<dt>VAT (at <?php echo floatval($record['Order']['tax_rate']); ?>%):</dt>
				<dd><?php echo $record['Currency']['html'] . number_format($totalTax, 2); ?></dd> -->
				<dt>Grand Total:</dt>
				<dd><strong><?php echo $record['Currency']['html'] . number_format($record['Order']['grand_total'], 2); ?></strong></dd>
			</dl>

	</div>



</div>
<div class="clear"></div>