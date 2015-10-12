<div id="pane-images" class="pane">


    <div class="fieldset-header"><span>Images</span></div>
    <div class="fieldset-box">
        <div id="product-images">
            <!-- <?php //xdebug_break(); ?> -->
			<?php echo $this->Form->create('StaticpagesImage', array('action' => 'save', 'type' => 'file')); ?>
                            <?php if (!empty($images)): ?>
				<?php foreach ($images as $staticpageImageID => $staticpageImage): ?>

            <div class="product-image">
                <a href="<?php echo $staticpageImage['filename']; ?>">
                    <img class="product-image-thumb" style="border: 1px solid #ccc; width: 200px;" src="<?php echo $staticpageImage['filename']; ?>" alt="Product Image" />
                </a>
                        <?php echo $staticpageImage['title']; ?>
                        <?php echo ' Resolution: ' . $staticpageImage['original_width'] . ' x ' . $staticpageImage['original_height']; ?>
                        <?php echo $form->input('StaticpagesImage.id', array('type' => 'hidden', 'value' => $staticpageImage['id'])); ?>
                        <?php echo $form->input('currentFilename', array('type' => 'hidden', 'value' => $staticpageImage['filename'])); ?>
                        <?php echo $form->file('StaticpagesImage.filename'); ?>
                        <?php echo $this->Form->end('Save'); ?>
            </div>
			<?php endforeach; ?>
			<?php endif; ?>
        </div>
    </div>

</div>