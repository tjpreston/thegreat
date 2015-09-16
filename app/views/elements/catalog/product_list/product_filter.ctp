<?php if (!empty($products)): ?>
	<p class="heading"><span class="face1">Filter</span> <span class="face2">Results</span></p>

	<div class="refine">		
		<?php if (empty($options) || !isset($options['show_attributes']) || (isset($options['show_attributes']) && ($options['show_attributes'] === true))): ?>
			<?php echo $this->element('catalog/product_list/attribute_filter'); ?>
		<?php endif; ?>

		<?php if (empty($options) || empty($options['show_prices']) || (isset($options['show_prices']) && ($options['show_prices'] === true))): ?>
			<?php echo $this->element('catalog/product_list/price_range_filter'); ?>
		<?php endif; ?>

		<?php /* if (empty($options) || !isset($options['show_manufacturers']) || (isset($options['show_manufacturers']) && ($options['show_manufacturers'] === true))): ?>
			<div id="brands-header" class="panel-header">
				<h3>Brands</h3>
			</div>
			
			<div class="panel-content">
					<?php echo $this->element('catalog/product_list/manufacturer_filter'); ?>
			</div>
		<?php endif; */ ?>
	</div>
<?php endif;  ?>