<div id="pane-attributes" class="pane">
	<div class="fieldset-header"><span><?php echo $attributeSet['AttributeSetName']['name']; ?> Attributes</span></div>
	<div class="fieldset-box">
		<fieldset>
			
			<?php foreach ($attributes as $k => $attribute): ?>
			
				<?php
				$list = array();
				foreach ($attribute['AttributeValue'] as $k2 => $value)
				{
					$list[$value['AttributeValue']['id']] = $value['AttributeValueName']['name'];
				}
				?>			
				
				<div class="attribute input">
					<div class="attr-name"><?php echo $attribute['AttributeName']['name']; ?></div>
					<div class="attr-values">
						<?php echo $form->input('Attribute.' . $attribute['Attribute']['id'] . '.AttributeValue', array(
							'type' => 'select',
							'multiple' => true,
							'options' => $list,
							'label' => false,
							'selected' => $productAttributeValues,
							'div' => 'select'
						)); ?>						
					</div>
				</div>
			
			<?php endforeach; ?>
	
		</fieldset>		
		<p style="margin-left: 140px;">Ctrl click to select multiple values.</p>

	</div>	
</div>
