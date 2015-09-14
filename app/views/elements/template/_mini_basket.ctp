<div id="mini-basket">
		
	<?php if (!empty($basket['Basket']['last_calculated_total_items'])): ?>
		
		<?php if (Configure::read('Template.products_in_minibasket') && !empty($basketItems)): ?>
			<?php foreach ($basketItems as $k => $item): ?>
				
				<p><?php echo intval($item['BasketItem']['qty']); ?> x 
					<a href="/<?php echo h($item['ProductName']['url']); ?>">
						<?php echo h($item['ProductName']['name']); ?>
					</a>
				</p>
		
			<?php endforeach; ?>
		<?php endif; ?>
		
		<p>
			<strong><?php echo intval($totalBasketItemQuantities); ?></strong> Item<?php echo ($totalBasketItemQuantities > 1) ? 's' : ''; ?> in Basket<br />
			<strong id="mini-subtotal"><?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?></strong>
		</p>
	
	<?php else: ?>
		<p>Your basket is empty</p>
	<?php endif; ?>
	
	<?php if (count($currencies) > 1): ?>
		<ul id="currencies">
			<?php foreach ($currencies as $currency): ?>
				<?php 
				$id = $currency['Currency']['id'];
				$current = ($activeCurrencyID == $id) ? ' class="current"' : '';
				?>
				<li><a<?php echo $current; ?> href="/currencies/set_currency/<?php echo intval($id); ?>" style="background-image: url(/img/icons/currencies/<?php echo intval($id); ?>.png);"><?php echo $currency['Currency']['html']; ?></a></li>
			<?php endforeach; ?>
		</ul>
	<?php endif; ?>
		
	<?php if (!empty($basket['Basket']['last_calculated_total_items'])): ?>	
		<div id="button-box"><a href="/basket"><img src="/img/app/template/bn-basket.png" alt="Basket" /></a></div>
	<?php endif; ?>
	
</div>

    
   