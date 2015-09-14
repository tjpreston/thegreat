<div id="pane-stock" class="pane">
	<div class="fieldset-header"><span>Videos</span></div>
	<div class="fieldset-box">
		<fieldset>
			<?php foreach (range(1, Configure::read('Catalog.youtube_videos')) as $i): ?>
				<?php echo $form->input('Product.youtube_' . $i, array(
					'type' => 'textarea'
				)); ?>
			<?php endforeach; ?>
		</fieldset>
	</div>
</div>
