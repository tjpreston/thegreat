<div class="tiny-product-item<?php echo (empty($k)) ? ' first' : ''; ?>">
	
	<div class="tiny-image-box">
		<a href="/<?php echo $product['ProductMeta']['url']; ?>">
			<img src="<?php echo $product['Product']['main_tiny_image_path']; ?>" alt=""  />
		</a>
	</div>
	
	<div class="info">
		<a href="/<?php echo $product['ProductMeta']['url']; ?>"><h4><?php echo h($product['ProductName']['name']); ?></h4></a>
		<div class="price">
			
				<?php echo $this->element('catalog/product_price_small', array(
					'priceData' => $product['ProductPrice'],
					'inStock' => $product['Product']['stock_in_stock']
				)); ?>
			
		</div>
	</div>

</div>
