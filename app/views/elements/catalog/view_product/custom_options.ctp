<?php if (!empty($record['ProductOption'])): ?>
	
	<div class="custom-options">

		<?php
		
		$i = 0;
		
		foreach ($record['ProductOption'] as $k => $option)
		{
			$optionID = $option['ProductOption']['id'];
			$options = array();
			
			foreach ($option['ProductOptionValue'] as $k => $value)
			{
				$valueID = $value['ProductOptionValue']['id'];
				$valueName = ucwords($value['CustomOptionValueName']['name']);
				$options[$valueID] = $valueName;
			}
			
			$name = (!empty($option['ProductOptionName']['name'])) ? $option['ProductOptionName']['name'] : 'Option';
			
			$selected = (!empty($defaultVarValues[$i])) ? $defaultVarValues[$i] : false;
			
			echo $form->input('Basket.0.ProductOption.productoption-' . $optionID, array(
				'type' => 'select',
				'options' => $options,
				'label' => $option['ProductOptionName'][0]['name'],
				'empty' => array(0 => 'Select ' . $name),
				'after' => '<img src="/img/ajax-loader.gif" alt="Loading" />',
				'selected' => $selected,
				'escape' => false
			));
			
			$i++;
		}
		
		?>
		
	</div>

<?php endif; ?>