<div id="pane-flags" class="pane">
	<div class="fieldset-header"><span>Badges</span></div>
	<div class="fieldset-box">
		<fieldset id="flags">
			<?php echo $form->input('ProductFlag.ProductFlag', array(
				'type' => 'select',
				'multiple' => 'checkbox',
				'label' => false,
				'div' => 'checkboxes-only',
				'selected' => $assocFlags
			)); ?>					
		</fieldset>		
	</div>	
</div>
