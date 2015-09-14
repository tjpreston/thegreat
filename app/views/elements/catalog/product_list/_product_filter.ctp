<?php if (!empty($products)): ?>

	<div class="panel">	  
		<h3>Filter products by:</h3>	  
		
		<div class="panel-content">
		
			<?php if (empty($options) || empty($options['show_prices']) || (isset($options['show_prices']) && ($options['show_prices'] === true))): ?>
				<?php // echo $this->element('catalog/product_list/price_range_filter'); ?>
			<?php endif; ?>	    
			
			<?php if (empty($options) || !isset($options['show_manufacturers']) || (isset($options['show_manufacturers']) && ($options['show_manufacturers'] === true))): ?>
				<?php echo $this->element('catalog/product_list/manufacturer_filter'); ?>
			<?php endif; ?>
			
			<?php if (empty($options) || !isset($options['show_attributes']) || (isset($options['show_attributes']) && ($options['show_attributes'] === true))): ?>
				<?php echo $this->element('catalog/product_list/attribute_filter'); ?>
			<?php endif; ?>
		
		</div>
	</div>

<?php endif; ?>

