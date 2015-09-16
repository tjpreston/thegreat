<div id="pane-images" class="pane">

	<?php if (!empty($varImages)): ?>
		<div class="info-message">These images will not be used as variation image(s) have been uploaded.</div>
	<?php endif; ?>

	<div class="fieldset-header"><span>Images</span></div>
	<div class="fieldset-box">
		<div id="product-images">
			<?php echo $form->input('Product.id', array('value' => $this->data['Product']['id'])); ?>
			<?php if (!empty($images)): ?>
				<?php foreach ($images as $productImageID => $productImage): ?>
					<?php echo $this->element('admin/products/image', array('productImage' => $productImage)); ?>
				<?php endforeach; ?>
			<?php endif; ?>
		</div>
	</div>
	<?php // echo $form->submit('Save', array('div' => 'submit stay-left')); ?>
	<div class="fieldset-header"><span>Upload New Image(s)</span></div>
	<div class="fieldset-box">
		<div>
			<?php echo $form->file('NewProductImage.image'); ?>
		</div>
	</div>				
</div>
