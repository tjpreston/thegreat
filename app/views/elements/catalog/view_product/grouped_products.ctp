<?php if (!empty($record['GroupedProducts'])): ?>

	<div id="group-products">
		
		<?php foreach ($record['GroupedProducts'] as $k => $product): ?>
			
			<div class="group-product">
				<p class="group-product-name"><?php echo $product['ProductName']['name']; ?></p>
	  			
	  			<?php if (!empty($product['ProductOption'])): ?>
					<?php echo $this->element('catalog/view_product/custom_options', array('record' => $product)); ?>
				<?php endif; ?>
				
			</div>
	
		<?php endforeach; ?>
	  
	</div>
	
	<div id="stock-price-buy">
	
		<?php if (!empty($record['Product']['grouped_products_have_options'])): ?>
			<?php echo $this->element('catalog/view_product/please_select_options'); ?>
		<?php else: ?>
			<?php echo $this->element('catalog/view_product/add_to_basket'); ?>
		<?php endif; ?>
		
	</div>

<?php endif; ?>


