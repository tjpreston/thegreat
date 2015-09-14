<div id="pane-documents" class="pane">
	<div class="fieldset-header"><span>Associated Document Downloads</span></div>
	<div class="fieldset-box">
		<fieldset>
			<div class="attribute input">
				<div class="attr-values">
					<?php echo $form->input('Document.Document', array(
						'type' => 'select',
						'multiple' => true,
						'label' => false,
						'div' => 'select',
						'style' => 'width: 300px; height: 300px;',
						'selected' => $assocDocs
					)); ?>						
				</div>
			</div>	
		</fieldset>		
	</div>	
</div>
