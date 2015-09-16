
<?php if (!empty($optionSku)): ?>
	<!-- <p id="option-sku"><?php echo h($optionSku); ?></p> -->
<?php endif; ?>




<?php //echo $this->element('catalog/product_list/product_stock');?> 

<?php 

if (Configure::read('Catalog.use_tiered_customer_pricing'))
{
	echo $this->element('catalog/view_product/customer_discount_tiers', array(
		'priceData' => $record['ProductPrice'],
		'model' => (!empty($this->params['ajax'])) ? 'ProductOptionStockDiscount' : 'ProductPriceDiscount'
	));
}

?>

<?php

// echo $this->element('catalog/view_product/price_and_stock', array(
// 	'priceData' => $record['ProductPrice'],
// 	'inStock' => $record['StockStatus']['in_stock']
// ));

?>

<?php if (!empty($record['ProductOptionStock'])): ?>
	<?php echo $form->hidden('Basket.0.product_option_stock_id', array('value' => $record['ProductOptionStock']['id'])); ?>
<?php endif; ?>

<div id="add-to-basket">

	<?php if ($record['Product']['in_stock'] == 1): ?>
	<?php
		echo $this->element('catalog/view_product/qty_boxes');
		
	?>


		<?php //echo $this->element('delivery/delivery_countdown');

		echo $form->submit('Add to basket', array(
			'div' => false,
			'id' => 'add-to-basket-submit'
		));?>
		<div class="clear"></div>
		
	<?php else: ?>
	<div class="clear"></div>
		<p class="outofstock">Out of Stock</p>
	<?php endif; ?>
	<div class="clear"></div>

	
	
	<div class="clear-both"></div>

</div>