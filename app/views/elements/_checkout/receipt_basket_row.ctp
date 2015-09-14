<tr>
	<td class="desc" style="padding-left: 15px;">
		<h3><?php echo h($item['product_name']); ?></h3>
		<h4><?php echo h($item['product_sku']); ?></h4>
	</td>

	<td class="price"><?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['price']), 2); ?></td>

	<td class="quantity">
		<?php echo $item['qty']; ?>
	</td>

	<td class="total">
		<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['price']) * $item['qty'], 2); ?>
	</td>
</tr>