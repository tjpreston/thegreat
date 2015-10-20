<?php
xdebug_break();
$this->set('body_id', 'category');

	echo $this->element('template/breadcrumbs');
?>

<div class="grid_18 push_6 listing">
	<?php echo $this->element('catalog/category_header', array('record' => $record)); ?>

	<?php $i = 1; ?>
	<?php foreach ($childCategories as $k => $childCategory): ?>
		<?php
		echo $this->element('catalog/category_list_item', array(
			'category' => $childCategory,
			'onpage' => $childCategoryCount,
			'i' => $i,
			'baseUrl' => $baseUrl,
			'perrow' => 3
		));
		$i++;
		?>
	<?php endforeach; ?>


</div>

<div class="grid_6 pull_18 sidebar">
	<?php echo $this->element('catalog/product_list/sub_categories'); ?>
</div>