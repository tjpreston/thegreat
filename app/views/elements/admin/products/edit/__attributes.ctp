<div id="pane-attributes" class="pane">
	<div class="fieldset-header"><span><?php // echo $attributeSet['AttributeSetName']['name']; ?> Attributes</span></div>
	<div class="fieldset-box">
		
		<fieldset>
			
			<?php foreach ($attributes as $k => $attribute): ?>

				<?php if (!empty($attribute['AttributeValue'])): ?>

					<?php
					$list = array();
					foreach ($attribute['AttributeValue'] as $k2 => $value)
					{
						$list[$value['AttributeValue']['id']] = $value['AttributeValueName']['name'];
					}
					?>
					
					<div class="attribute input">
						<div class="attr-name">
							<?php
							echo $form->input('UseAttributes.' . $attribute['Attribute']['id'], array(
								'id' => 'attr-' . $attribute['Attribute']['id'],
								'type' => 'checkbox',
								'value' => 1,
								'div' => false,
								'label' => false,
								'checked' => (in_array($attribute['Attribute']['id'], $productAttributes))
							));
							?>
							<?php echo $attribute['AttributeName']['name']; ?>
						</div>
						<div id="attr-values-<?php echo $attribute['Attribute']['id']; ?>" class="attr-values <?php echo (in_array($attribute['Attribute']['id'], $productAttributes)) ? 'open' : 'closed'; ?>">
							<?php echo $form->input('Attribute.' . $attribute['Attribute']['id'] . '.AttributeValue', array(
								'type' => 'select',
								'multiple' => 'checkbox',
								'options' => $list,
								'label' => false,
								'selected' => $productAttributeValues,
								'div' => 'select'
							)); ?>						
						</div>
					</div>

				<?php endif; ?>
						
			<?php endforeach; ?>
	
		</fieldset>

	</div>	
</div>
