<?php if (!empty($recentlyViewed)): ?>
	<div class="heading green-block-heading">
		<h2>Recently Viewed</h2>
	</div>
			<?php foreach ($recentlyViewed as $k => $product): ?>	    	
				<?php echo $this->element('catalog/product_item_tiny', array('product' => $product, 'k' => $k)); ?>	  
			<?php endforeach; ?>	  
		
	

<?php endif; ?>
