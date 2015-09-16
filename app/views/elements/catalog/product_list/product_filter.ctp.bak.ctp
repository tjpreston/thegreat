

<!-- <?php if (!empty($options['category_filter']) && ($options['category_filter'] === true)): ?>
	<?php echo $this->element('catalog/product_list/sub_categories'); ?>
<?php endif; ?>

<?php if (!empty($options['category_list']) && ($options['category_list'] === true)): ?>
	<?php echo $this->element('catalog/product_list/category_list', array(
		'base_url' => $base_url
	)); ?>
<?php endif; ?> -->


<?php if (!empty($products)): ?>
	<h4>Refine your results by:</h4>
	<div class="refine">
		<div class="interior">
	
			<ul id="filters">
				<?php if (empty($options) || empty($options['show_prices']) || (isset($options['show_prices']) && ($options['show_prices'] === true))): ?>
					<?php echo $this->element('catalog/product_list/price_range_filter'); ?>
				<?php endif; ?>
				
				<?php if (empty($options) || !isset($options['show_attributes']) || (isset($options['show_attributes']) && ($options['show_attributes'] === true))): ?>
					<?php echo $this->element('catalog/product_list/attribute_filter'); ?>
				<?php endif; ?>

				<?php /* if (empty($options) || !isset($options['show_manufacturers']) || (isset($options['show_manufacturers']) && ($options['show_manufacturers'] === true))): ?>
					<div id="brands-header" class="panel-header">
						<h3>Brands</h3>
					</div>
					
					<div class="panel-content">
							<?php echo $this->element('catalog/product_list/manufacturer_filter'); ?>
					</div>
				<?php endif; */ ?>
			</ul>
			<div style="clear:both"></div>

		</div>
		<div class="base"></div>
	</div>
<?php endif;  ?>