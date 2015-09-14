<?php

$html->script(array('vendors/jquery.Jcrop.min.js'), array('inline' => false));
$html->css(array('vendors/jquery.Jcrop.css'), null, array('inline' => false));

$file = $record['ProductImage']['filename'] . '.' . $record['ProductImage']['ext'];

$originalPath = 'img/products/original/' . $file;
$editedPath = 'img/products/edited/' . $file;

$path = (file_exists(WWW_ROOT . $editedPath)) ? $editedPath  : $originalPath;

$imageSize = getimagesize(WWW_ROOT . $path);

?>

<script type="text/javascript">	
jQuery(function() {
	jQuery('#cropbox').Jcrop({
		onChange: setCoords,
		onSelect: setCoords,
		boxWidth: 700, 
		boxHeight: 700
		<?php if (!isset($this->params['url']['disable_aspect'])) echo ', aspectRatio: ' . Configure::read('Images.product_medium_width') . '/' . Configure::read('Images.product_medium_height'); ?>
	});
	$("#rotate").click(function() {
		window.location.href = "/admin/product_images/rotate/<?php echo intval($record['ProductImage']['id']); ?>";
		return false;
	});
	$("#restore").click(function() {
		if (confirm("Are you sure? Any changes will be lost.")) {
			window.location.href = "/admin/product_images/restore/<?php echo intval($record['ProductImage']['id']); ?>";
		}
		return false;
	});
});
function setCoords(coords) {
	$('#ProductImageX1').val(coords.x);
	$('#ProductImageY1').val(coords.y);
	$('#ProductImageX2').val(coords.x2);
	$('#ProductImageY2').val(coords.y2);
	$('#ProductImageW').val(coords.w);
	$('#ProductImageH').val(coords.h);
};
</script>

<div id="header">
	<h1>Product Name</h1>
</div>

<div style="margin-bottom: 20px;">
	<img src="/<?php echo $path; ?>" id="cropbox" />
</div>

<div style="width: 704px;">	
	<button id="restore" style="float: left; border: 1px solid #bbb; padding: 2px;">Restore Original</button>
	<!-- <button id="rotate" style="float: right; border: 1px solid #bbb; padding: 2px; margin-left: 12px; width: 50px;">Rotate</button> -->
	<?php
	echo $form->create('ProductImage', array('id' => 'product-image-crop-form', 'url' => '/admin/product_images/crop'));
	echo $form->hidden('id', array('value' => intval($record['ProductImage']['id'])));
	echo $form->hidden('x1');
	echo $form->hidden('y1');
	echo $form->hidden('x2');
	echo $form->hidden('y2');
	echo $form->hidden('w');
	echo $form->hidden('h');
	echo $form->submit('Crop', array('style' => 'width: 50px; float: right;'));
	echo $form->end();
	?>
	
	<?php $arg = (!empty($this->params['url']['aspect'])) ? '' : '?disable_aspect'; ?>
	<div style="float: right; line-height: 24px; margin-right: 12px;"><a href="/admin/product_images/edit/<?php echo intval($record['ProductImage']['id']) . $arg; ?>"><?php echo (isset($this->params['url']['disable_aspect'])) ? 'Enable' : 'Disable'; ?> resizing to aspect ratio</a></div>
</div>

