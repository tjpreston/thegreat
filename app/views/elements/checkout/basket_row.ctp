

<div class="main-basket-row">

	<div class="basket-row">
		
		<?php echo $this->element('collection/collection_row', array(
			'key' => 'BasketItem',
			'k' => $k,
			'item' => $item,
			'caller' => 'conf',
			'showRadios' => false
		)); ?>
		
		<div class="basket-total">
			<p><?php echo $activeCurrencyHTML; ?> <?php echo number_format(floatval($item['BasketItem']['price'] * $item['BasketItem']['qty']), 2); ?></p>
		</div>
		
	</div>
	
</div>

