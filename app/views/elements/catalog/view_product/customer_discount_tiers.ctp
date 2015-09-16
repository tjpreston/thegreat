<?php if (!empty($record['ProductPriceDiscount']) && (count($record['ProductPriceDiscount']) > 1)): ?>

	<div id="customer-tiers">		
		<h3>Buy More and Save</h3>		
		<dl>
			<?php foreach ($record['ProductPriceDiscount'] as $discount): ?>
			
				<?php if ($discount['min_qty'] == 1) { continue; } ?>
			
				<?php $actualDiscountAmount = number_format($priceData['active_price'] - ($priceData['active_price'] * floatval('0.' . ($discount['discount_amount']))), 2); ?>
				
				<dt><?php echo $discount['min_qty'] . '+'; ?></dt>
				<dd><?php echo $activeCurrencyHTML . $actualDiscountAmount; ?> each (<?php echo intval($discount['discount_amount']); ?>%)</dd>
				
			<?php endforeach; ?>
		</dl>	
	</div>
	
<?php endif; ?>


