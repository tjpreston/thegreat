<?php
	$this->set('body_id', 'search-results');

	echo $this->element('template/breadcrumbs');
?>

<?php $catalog->setSearchUrls($keyword); ?>

<div class="grid_18 push_6 search">

	<?php echo $this->element('catalog/category_header', array('name' => 'Search results')); ?>

	<?php if (isset($products)): ?>
		<?php echo $this->element('catalog/product_list/product_list', array(
			'message' => 'Your search returned no products.'
		)); ?>
	<?php else: ?>
		<p>Please enter a search term.</p>
	<?php endif; ?>

</div>

<div class="grid_6 pull_18 sidebar">
	<?php echo $this->element('catalog/product_list/sub_categories'); ?>
	<?php echo $this->element('catalog/product_list/product_filter'); ?>
</div>