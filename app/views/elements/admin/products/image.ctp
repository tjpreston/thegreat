<?php
	
$productImageID = intval($productImage['id']);

$imageSize = getimagesize($productImage['tiny_root_path']);

//$width = $imageSize[0];
//$height = $imageSize[1];

$width = 700;
$height = 500;

$label = $productImage['label'];
$sortOrder = $productImage['sort_order'];

?>

<div class="product-image">
	<a href="<?php echo $productImage['large_web_path']; ?>">
		<img class="product-image-thumb" style="border: 1px solid #ccc; width: <?php echo intval(Configure::read('Images.product_tiny_width')); ?>px;" src="<?php echo $productImage['tiny_web_path']; ?>" alt="Product Image" />
	</a>
	<div class="product-image-inputs">
		<input type="hidden" name="data[ProductImage][<?php echo $productImageID; ?>][id]" value="<?php echo $productImageID; ?>" />
		<div class="product-image-input">
			<label for="">Label</label>
			<input type="text" name="data[ProductImage][<?php echo $productImageID; ?>][label]" value="<?php echo $label; ?>" class="product-image-label-input" />
		</div>
		<div class="product-image-input product-image-sort-input-box">
			<label for="">Sort Order</label>
			<input type="text" name="data[ProductImage][<?php echo $productImageID; ?>][sort_order]" class="tiny" value="<?php echo $sortOrder; ?>" />
		</div>
	</div>
	<div class="product-image-actions">
		<input type="hidden" name="data[image][<?php echo $productImageID; ?>][width]" id="image_<?php echo $productImageID; ?>_width" value="<?php echo $width; ?>" />
		<input type="hidden" name="data[image][<?php echo $productImageID; ?>][height]" id="image_<?php echo $productImageID; ?>_height" value="<?php echo $height; ?>" />
		<a id="product-image-<?php echo $productImageID; ?>" href="/admin/product_images/edit/<?php echo $productImageID; ?>" class="edit-image-popup"><img src="/img/icons/image_edit.png" alt="Edit Image" title="Edit Image" /></a>
		<a href="/admin/product_images/delete/<?php echo $productImageID; ?>"><img src="/img/icons/image_delete.png" alt="Delete Image" title="Delete Image" /></a>
	</div>
</div>