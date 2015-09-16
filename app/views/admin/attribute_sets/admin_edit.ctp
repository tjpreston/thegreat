<div id="admin-content">
	
	<div id="side-col">
		<ul id="admin-links">
			<li><a href="/admin/attribute_sets" class="icon-link back-link">Back to List</a></li>
		</ul>
	</div>
	
	<?php echo $form->create('AttributeSet', array('action' => 'save', 'id' => 'product-form')); ?>
	
		<?php echo $form->input('id'); ?>
	
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['AttributeSetName'][Configure::read('Languages.main_lang_id')]['name'])): ?>
					<a href="/admin/attribute_sets/delete/<?php echo intval($this->data['AttributeSet']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['AttributeSetName'][Configure::read('Languages.main_lang_id')]['name']); ?> Attribute Set</h1>
				<?php else: ?>
					<h1>New Attribute Set</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>General</span></div>
				<div class="fieldset-box">
					<?php // echo $this->element('admin/language_nav'); ?>
					<div class="fieldsets">
						
						<?php foreach ($languages as $languageID => $languageName): ?>
						<fieldset>
							
							<?php
							
							echo $form->hidden('AttributeSetName.' . $languageID . '.language_id', array('value' => $languageID));		
							
							if (!empty($this->data['AttributeSetName'][$languageID]['id']))
							{
								echo $form->input('AttributeSetName.' . $languageID . '.id', array(
									'value' => $this->data['AttributeSetName'][$languageID]['id']
								));
							}
							
							$name = (!empty($this->data['AttributeSetName'][$languageID]['name'])) ? $this->data['AttributeSetName'][$languageID]['name'] : '';
							echo $form->input('AttributeSetName.' . $languageID . '.name', array(
								'value' => $name
							));
							
							?>
							
						</fieldset>
					<?php endforeach; ?>

					<fieldset class="checkbox-multi-select" style="margin-left: 0;">
						<?php echo $form->input('Attribute.Attribute', array(
							'label' => 'Attributes',
							'multiple' => 'checkbox',
							'between' => '<div class="multi-checks">',
							'after' => '</div>'
						)); ?>
					</fieldset>
					
				</div>				
				
			</div>
			
			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>