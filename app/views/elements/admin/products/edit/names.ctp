<div id="pane-desc" class="pane">			
	<div class="fieldset-header"><span>Descriptions</span></div>
	<div class="fieldset-box">
		<?php // echo $this->element('admin/language_nav'); ?>
		<div class="fieldsets">

			<?php foreach ($languages as $languageID => $languageName): ?>
				<fieldset>
					
					<?php 									
					echo $form->hidden('ProductName.' . $languageID . '.language_id', array('value' => $languageID));
					
					$nameID = (!empty($this->data['ProductName'][$languageID]['id'])) ? $this->data['ProductName'][$languageID]['id'] : '';
					echo $form->input('ProductName.' . $languageID . '.id', array('value' => $nameID));
					
					$nameName = (!empty($this->data['ProductName'][$languageID]['name'])) ? $this->data['ProductName'][$languageID]['name'] : '';
					echo $form->input('ProductName.' . $languageID . '.name', array('value' => $nameName));
					
					// $nameSubName = (!empty($this->data['ProductName'][$languageID]['sub_name'])) ? $this->data['ProductName'][$languageID]['sub_name'] : '';
					// echo $form->input('ProductName.' . $languageID . '.sub_name', array('value' => $nameSubName));
					
					?>
					
					<?php 
					echo $form->hidden('ProductDescription.' . $languageID . '.product_id', array(
						'value' => intval($record['Product']['id'])
					)); 
					echo $form->hidden('ProductDescription.' . $languageID . '.language_id', array(
						'value' => $languageID
					));
					?>
					
					<?php $fields = array(
						'id' => array(),
						// 'short_description' => array(),
						'long_description' => array('class' => 'tinymce'),
						// 'specification' => array(),
						'keywords' => array(),
					); ?>
					
					<?php foreach ($fields as $field => $attrs): ?>
						<?php
						$languageData[$field] = (!empty($this->data['ProductDescription'][$languageID][$field])) ? $this->data['ProductDescription'][$languageID][$field] : '';
						$fieldAttrs = array('value' => $languageData[$field]);
						if (!empty($attrs))
						{
							$fieldAttrs = array_merge($fieldAttrs, $attrs);
						}
						echo $form->input('ProductDescription.' . $languageID . '.' . $field, $fieldAttrs);
						?>
					<?php endforeach; ?>
					
				</fieldset>
			<?php endforeach; ?>
		
		</div>
	</div>

</div>



