<tr>
	<td class="desc">
		<div style="width: 80px; float: left; text-align: center;">
			<a href="/<?php echo $item['ProductMeta']['url']; ?>">
				<img class="tiny-basket-image" src="<?php echo $item['Product']['main_tiny_image_path']; ?>" />
			</a>
		</div>

		<h3><a href="/<?php echo $item['ProductMeta']['url']; ?>">
			<?php echo h($item['ProductName']['name']); ?><?php
				if(!empty($item['ProductOptionStock'])){
					echo ': ' . h($item['ProductOptionStock']['name']);
				}
			?>
		</a></h3>

		<h4><?php echo $item['Product']['sku']; ?></h4>
	</td>

	<td class="price"><?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['BasketItem']['price']), 2); ?></td>

	<td class="quantity">
		<?php echo $item['BasketItem']['qty']; ?>
	</td>

	<td class="total">
		<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['BasketItem']['price']) * $item['BasketItem']['qty'], 2); ?>
	</td>
</tr>