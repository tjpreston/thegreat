<p class="intro"><em>
	<?php if (!empty($totalBasketItemQuantities)): ?>
		<?php $plural = ($totalBasketItemQuantities == 1) ? '' : 's'; ?>
		You have <?php echo intval($totalBasketItemQuantities); ?> item<?php echo $plural; ?> in your basket. 
	<?php else: ?>
		Your basket is currently empty.
	<?php endif; ?>
</em></p>