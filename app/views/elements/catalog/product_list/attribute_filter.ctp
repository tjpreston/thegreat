<?php if (!empty($allAttributes)): ?>

<dl class="filter">
	
	<?php
	$attrCount = count($allAttributes);
	$selectedValues = $this->Catalog->rekeySelectedAttributeValues($selected_attributes_filter_values);
	?>
	
	<?php foreach ($allAttributes as $attribute): ?>
	
		<?php

		$attributeID = $attribute['Attribute']['id'];
		$valueCount = count($attribute['AttributeValue']);
		$j = 1;

		?>

		<?php if (!empty($availableAttributes[$attributeID]['AttributeValue'])): ?>
	
			<dl class="filter">
		
				<?php $aname = $attribute['AttributeName']; ?>
				<dt>
					<?php echo (!empty($aname['display_name'])) ? h($aname['display_name']) : h($aname['name']); ?>
				</dt>
				
				<?php foreach ($attribute['AttributeValue'] as $value): ?>
					<?php
					
					$valueID = $value['AttributeValue']['id'];
					$valueName = $value['AttributeValueName']['name'];
					$valueUrl = $value['AttributeValueName']['url'];
					
					$classes = array('attr-' . $attributeID . '-value');
					
					if (!empty($selectedValues[$attributeID]) && in_array($valueUrl, $selectedValues[$attributeID]))
					{
						$classes[] = 'selected';
					}
					
					if ($j == $valueCount)
					{
						$classes[] = 'last';
					}
					
					$class = (!empty($classes)) ? ' class="' . implode(' ', $classes) . '"' : '';
					
					?>
					<?php if (!empty($availableAttributes[$attributeID]['AttributeValue'][$valueID])): ?>
						
						<dd<?php echo $class; ?>>

							<?php
								if (!empty($selectedValues[$attributeID]) && in_array($valueUrl, $selectedValues[$attributeID])){
									$url = $catalog->getUrl() . 'attrex[]=' . h($valueUrl) . ':' . intval($attributeID) . ':' . intval($valueID);
								} else {
									$url = $catalog->getUrl() . 'attrinc[]=' . h($valueUrl) . ':' . intval($attributeID) . ':' . intval($valueID);
								}
							?>
							<a href="<?php echo $url; ?>" rel="nofollow">
					  			<?php echo h($valueName); ?>
								<span class="count">(<?php echo intval($availableAttributes[$attributeID]['AttributeValue'][$valueID]['AttributeValue']['count']); ?>)</span>
								<div class="icon"></div>
							</a>

						</dd>
					
					<?php else: ?>
					
						<!-- <dd class="disabled"><?php echo h($valueName); ?></dd> -->
					
					<?php endif; ?>
					
					<?php $j++; ?>
				
				<?php endforeach; ?>

			</dl>
		
		<?php endif; ?>

	<?php endforeach; ?>

</dl>
	
<?php endif; ?>