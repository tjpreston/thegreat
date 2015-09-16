<div class="main-basket-row">

	<div class="basket-row">
		
		<?php echo $this->element('collection/collection_row', array(
			'key' => 'BasketItem',
			'k' => $k,
			'item' => $item,
			'caller' => 'basket',
			'showRadios' => true
		)); ?>
		
		<div class="basket-total">
			<p>
				<?php echo $activeCurrencyHTML; ?> <?php echo number_format(floatval($item['BasketItem']['price'] * $item['BasketItem']['qty']), 2); ?>
				<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
					<br/>
					<span class="giftwrap-details giftwrap-total"><?php echo $activeCurrencyHTML; ?> <?php echo number_format(Configure::read('Giftwrapping.price') * $item['BasketItem']['qty'], 2); ?></span>
				<?php endif; ?>
			</p>
		</div>
		
		<div class="basket-remove">
			<a href="/basket/remove/<?php echo intval($item['BasketItem']['id']); ?>">
				<img src="/img/icons/delete.png" alt="Remove item" title="Remove item" />
			</a>
		</div>
		
	</div>

</div>