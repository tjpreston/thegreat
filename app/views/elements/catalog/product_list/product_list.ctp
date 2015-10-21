<?php if (!empty($products)): ?>
<?php //xdebug_break(); ?>
	<?php echo $this->element('catalog/product_list/product_nav', array('pos' => 'top')); ?>

	<div id="listing" class="clearfix">
		<?php $i = 1; $count = count($products); ?>
		<?php foreach ($products as $k => $product): ?>
			<?php echo $this->element('catalog/product_list/product_list_item', array(
				'product' => $product,
				'i' => $k + 1
			)); ?>
            
			<?php //echo (($i % 3 == 0) && ($i < $count)) ? '<div style="clear: both;"></div>' : ''; ?>
			<?php $i++; ?>

		<?php endforeach; ?>	
	</div>

	<?php echo $this->element('catalog/product_list/product_nav', array('pos' => 'bottom')); ?>
<?php else: ?>
	<p><?php echo h($message); ?></p>
<?php endif; ?>