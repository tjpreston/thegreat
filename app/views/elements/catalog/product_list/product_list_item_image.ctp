<a href="<?php echo $this->Catalog->getProductUrl($product); ?>">
	<div class="list-item-image">
		
			<?php if (!empty($product['ProductImage'][0])): ?>
				<img src="<?php echo $product['ProductImage'][0]['thumb_web_path']; ?>" alt=""  />
			<?php else: ?>
				<img src="/img/products/no-thumb.png" alt="" />
			<?php endif; ?>	
		
		<?php if (Configure::read('Catalog.show_special_offer_overlay') && !empty($product['ProductPriceType']['collected']['on_special'])): ?>
			<div class="special-offer-product"></div>
		<?php endif; ?>

		<?php if (Configure::read('Catalog.show_new_overlay') && !empty($product['Product']['new_product']) == '1'): ?>
			<div class="new-item"></div>
		<?php endif; ?>

		<?php if (Configure::read('Catalog.show_featured_overlay') && !empty($product['Product']['featured']) == '1'): ?>
			<div class="featured-item"></div>
		<?php endif; ?>

		<?php if (Configure::read('Catalog.show_best_seller_overlay') && !empty($product['Product']['best_seller']) == '1'): ?>
			<div class="best-seller-item"></div>
		<?php endif; ?>
		
	</div>
</a>
