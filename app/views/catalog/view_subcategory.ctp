<?php

	$this->set('body_id', 'sub-category');

	$catalog->setCategoryUrls($categoryPath);

	echo $this->element('template/breadcrumbs');

?>

<div class="grid_18 push_6 listing">
	<?php echo $this->element('catalog/category_header', array('record' => $record)); ?>

	<?php echo $this->element('catalog/product_list/product_list', array(
		'message' => 'No products found in this category.'
	)); ?>
</div>

<div class="grid_6 pull_18 sidebar">
	<?php echo $this->element('catalog/product_list/sub_categories'); ?>
	<?php echo $this->element('catalog/product_list/product_filter'); ?>
</div>