<div id="mini-basket-popup">

	<?php if (!empty($basket['Basket']['last_calculated_total_items'])): ?>
		<div class="mini-basket-row">
			<div class="mini-basket-name header">Name</div>
			<div class="mini-basket-qty header">Qty</div>
			<div class="mini-basket-price header">Price</div>
		</div>
		
		<?php foreach($basketItems as $k => $basketItem): ?>
			<div class="mini-basket-row">
				
					<div class="mini-basket-name"><?php echo $this->Text->truncate($basketItem['ProductName']['name'],17,array('ending' => '...','exact' => false)); ?></div>
					<div class="mini-basket-qty"><?php echo($basketItem['BasketItem']['qty']);?></div>
					<div class="mini-basket-price"><?php echo($basketItem['ProductPrice']['active_price']);?></div>
				
			</div>
		<?php endforeach; ?>
			



	
		
		<div class="mini-basket-row">
			<div class="mini-basket-totals">
				Total 
			</div>
			<div class="mini-basket-price">
				<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?>
			</div>
		</div>

		<div class="clear"></div>

		<a href="/basket" title="View your items">
			<img src="/img/buttons/checkout.png" class="checkout" alt="checkout" />
		</a>

		<a href="/basket/removeall" title="empty cart">
			<p class="empty">Empty cart</p>
		</a>

	<?php else: ?>
		<p>Basket is Currently Empty</p>
	<?php endif; ?>
	
</div>