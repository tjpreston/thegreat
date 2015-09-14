<div class="price">
	<?php if($priceData['on_special'] == '1'): ?>
		<span class="base second-color">Was <?php echo $activeCurrencyHTML; ?><?php echo number_format($priceData['base_price'], 2); ?></span>
		<span class="sale">Now <?php echo $activeCurrencyHTML; ?><?php echo number_format($priceData['active_price'], 2); ?></span>
	<?php else: ?>
		<span class="active first-color"><?php echo !empty($priceData['lowest_price']) ? 'from ' . $activeCurrencyHTML . number_format($priceData['lowest_price'], 2) : $activeCurrencyHTML . number_format($priceData['active_price'], 2); ?></span>
	<?php endif; ?>
</div>