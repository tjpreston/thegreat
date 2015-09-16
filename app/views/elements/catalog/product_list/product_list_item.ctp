<?php

if (!isset($onpage) && isset($paginator))
{
	$paginatorParams = $paginator->params('Product');
	$onpage = $paginatorParams['current'];
}

$class = array();

if((($i) == ($onpage - 1)) || ($i == $onpage) || isset($bottomRow)) {
	$class[] = 'bottom-row';
}

if($i % 3 == 1){
	$class[] = 'alpha';
}

if($i % 3 == 0){
	$class[] = 'omega';
}

$class[] = 'grid_6';

?>

<div class="list-item <?php echo implode(' ', $class); ?>">
	<?php echo $this->element('catalog/product_list/product_list_item_image', array('product' => $product)); ?>
	
	<div class="list-item-details">
		<h2 class="border-top-bottom">
			<a href="<?php echo $this->Catalog->getProductUrl($product); ?>" title="<?php echo h($product['ProductName']['name']); ?>">
				<?php echo $text->truncate(h($product['ProductName']['name']), 30, array(
					'ending' => '...',
					'exact' => true
				)); ?>
			</a>
		</h2>
		
		<?php if (Configure::read('Catalog.show_product_short_description_on_list') && !empty($product['ProductDescription']['short_description'])): ?>
			<p class="product-list-desc">
				<?php echo $text->truncate(h($product['ProductDescription']['short_description']), 70, array(
					'ending' => ' ...',
					'exact' => false
				)); ?>
			</p>
		<?php endif; ?>

		<a href="<?php echo $this->Catalog->getProductUrl($product); ?>" class="border-top-bottom right">
			<span class="face2">More</span>
		</a>
		<div class="product-list-prices">
			<?php echo $this->element('catalog/product_price_small', array(
				'priceData' => $product['ProductPrice'],
				'inStock' => $product['Product']['stock_in_stock']
			)); ?>
		</div>

	</div>
</div>