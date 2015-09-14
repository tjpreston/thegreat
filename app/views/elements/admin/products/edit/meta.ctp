<div id="pane-meta" class="pane">
	<div class="fieldset-header"><span>Meta Information</span></div>
	<div class="fieldset-box">
		<?php // echo $this->element('admin/language_nav'); ?>
		<div class="fieldsets">
			<?php foreach ($languages as $languageID => $languageName): ?>
				<fieldset>
					<?php echo $form->hidden('ProductMeta.' . $languageID . '.language_id', array('value' => $languageID)); ?>
					<?php $fields = array(
						'id' => array(),
						'page_title' => array(),
						'keywords' => array(),
						'description' => array(),
						'url' => array('label' => 'URL')
					); ?>
					<?php foreach ($fields as $field => $attrs): ?>
						<?php
						$languageData[$field] = (!empty($this->data['ProductMeta'][$languageID][$field])) ? $this->data['ProductMeta'][$languageID][$field] : '';
						$fieldAttrs = array('value' => $languageData[$field]);
						if (!empty($attrs))
						{
							$fieldAttrs = array_merge($fieldAttrs, $attrs);
						}
						echo $form->input('ProductMeta.' . $languageID . '.' . $field, $fieldAttrs);
						?>
					<?php endforeach; ?>
					
					<?php
					/*
					$url = (!empty($this->data['ProductMeta'][$languageID]['url'])) ? $this->data['ProductMeta'][$languageID]['url'] : '';
					echo $form->input('ProductMeta.' . $languageID . '.url', array('value' => $url));
					*/
					?>
					
				</fieldset>
			<?php endforeach; ?>
		</div>	
	</div>	
</div>