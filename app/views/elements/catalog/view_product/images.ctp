<?php
	if(empty($images)){
		$images = array();

		if(!empty($record['ProductImage'])){
			$images = $record['ProductImage'];
		}
	}
?>
<div id="image-box">
	<?php echo $this->element('catalog/view_product/images_combined', array('images' => $images)); ?>
</div>

<?php if (!empty($optionsStock)): ?>
	<?php echo $this->element('catalog/view_product/var_js_arrays'); ?>
<?php endif; ?>