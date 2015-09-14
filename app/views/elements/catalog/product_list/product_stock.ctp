
<p class="product-list-stock <?php echo (!empty($product['StockStatus']['in_stock'])) ? 'in' : 'out'; ?>-stock">
	<?php echo h($product['StockStatus']['name']); ?>
</p>