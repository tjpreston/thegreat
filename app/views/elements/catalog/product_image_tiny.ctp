<a href="/<?php echo $product['ProductMeta']['url']; ?>">
	<?php if (!empty($product['ProductImage'][0])): ?>
		<img src="<?php echo $product['ProductImage'][0]['tiny_web_path']; ?>" alt=""  />
	<?php else: ?>
		<img src="/img/products/product-no-img-tiny.png" class="product-list-image" alt="" />
	<?php endif; ?>	
</a>

