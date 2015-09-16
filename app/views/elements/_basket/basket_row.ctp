<tr>
	<td class="desc">
		<div style="width: 80px; float: left; text-align: center;">
			<a href="/<?php echo $item['ProductMeta']['url']; ?>">
				<img class="tiny-basket-image" src="<?php echo $item['Product']['main_tiny_image_path']; ?>" />
			</a>
		</div>

		<h3><a href="/<?php echo $item['ProductMeta']['url']; ?>">
			<?php echo h($item['ProductName']['name']); ?><?php
				if(!empty($item['ProductOptionStock']) && empty($item['BasketItem']['additional_strap_name'])){
					echo ': ' . h($item['ProductOptionStock']['name']);
				}
			?>
		</a></h3>

		<h4><?php 
			if(!empty($item['ProductOptionStock']) && !empty($item['BasketItem']['additional_strap_name'])){
				echo 'Straps: ' . h($item['ProductOptionStock']['name']);
				echo ', ' . $item['BasketItem']['additional_strap_name'];
			}

		?></h4>

		<h4><?php echo $item['Product']['sku']; ?></h4>
	</td>

	<td class="price"><?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['BasketItem']['price']), 2); ?></td>

	<td class="quantity">
		<?php echo $this->Form->hidden('BasketItem.' . $k . '.id', array('value' => $item['BasketItem']['id'])); ?>
		<span class="decrement">-</span>
		<?php echo $this->Form->input('BasketItem.' . $k . '.qty', array(
			'value' => $item['BasketItem']['qty'],
			'label' => false,
			'div' => false,
		)); ?>
		<span class="increment">+</span>
		<a href="#" class="update">Update</a>
	</td>

	<td class="total">
		<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($item['BasketItem']['price']) * $item['BasketItem']['qty'], 2); ?>
	</td>
</tr>