<?php

$optionsCount = count($options);

$values = array();
$valuesCount = array();

foreach ($options as $k => $option)
{
	$values[$k] = array();
	
	foreach ($option['ProductOptionValue'] as $value)
	{
		$vID = $value['ProductOptionValue']['id'];
		$vName = $value['CustomOptionValueName']['name'];
		$values[$k][] = array($vID, $vName);
	}
	
	$valuesCount[] = count($values[$k]);
	
}

$totalValues = array_product($valuesCount) * $optionsCount;

$inOption = 0;
$inValue = array();

foreach ($values as $k => $value)
{
	$inValue[$k] = 0;
}

$optionKeys = array_keys($inValue);
$lastKey = max($optionKeys);

?>

<table id="var-stock">
	
	<thead>
		<tr>
		<?php foreach ($options as $option): ?>
			<th><?php echo h($option['CustomOptionName']['name']); ?></th>
		<?php endforeach; ?>
			<th>&nbsp;</th>
		</tr>
	</thead>
	
	<tbody>
	
		<tr>
		

<?php 

$fieldName = '';
$p = 0;
$k = 0;

?>

<?php for ($i = 1; $i <= $totalValues; $i++): ?>

	<?php 
	
	$valueKey = $inValue[$inOption];
	$fieldName .= $values[$inOption][$valueKey][0] . '-';
	
	?>
		
		<td class="var-value">
			<?php echo $values[$inOption][$valueKey][1]; ?>
		</td>
		
		<?php
		
		$inOption++;
		
		if ($inOption >= $optionsCount)
		{
			$inValue[$lastKey]++;			
			
			if ($inValue[$lastKey] == $valuesCount[$lastKey])
			{
				$inValue[$lastKey] = 0;
				
				if ($optionsCount > 1)
				{
					$vals = array_reverse(range(0, $lastKey - 1));
					
					foreach ($vals as $v)
					{
						$allZero = true;
						
						for ($l = $v; $l >= 0; $l--)
						{
							if ($inValue[$v + 1] != 0)
							{
								$allZero = false;
								break 2;
							}
						}
						
						if ($allZero)
						{
							$inValue[$v]++;
						}
						
						if ($inValue[$v] >= $valuesCount[$v])
						{
							$inValue[$v] = 0;
						}
						
					}
				}
			}
			
			$fieldName = substr($fieldName, 0, -1);
			
			$fieldValues = array(
				'id' => '',
				'available' => false,
				'sku' => '',
				'stock_status_id' => Configure::read('Stock.default_stock_status'),
				'stock_lead_time' => '', 
				'stock_base_qty' => '',
				'modifier' => 0
			);
			
			foreach ($optionsStock as $stock)
			{
				if ($stock['ProductOptionStock']['value_ids'] == $fieldName)
				{
					$fieldValues = $stock['ProductOptionStock'];
					$priceValues = $stock['ProductOptionStockPrice'];
					$images = $stock['ProductOptionStockImage'];
				}
			}

			// Container cell
			echo '<td style="width: 20px;">';
			echo '&nbsp;';
			echo '</td>';
			echo '<td>';
			
			echo '<div id="varstock-general" class="varstock-data">';
			echo '<table>';
			echo '<tr>';
			
			echo '<td class="var-stock-avail">';
			echo '<label>Available?</label>';
			echo $form->input('ProductOptionStock.' . $k . '.available', array(
				'label' => false,
				'div' => false,
				'checked' => $fieldValues['available']
			));
			echo '</td>';

			echo '<td class="var-stock-default">';
			echo '<label>Default</label>';
			$checked = ($record['Product']['default_product_option_stock_id'] == $fieldValues['id']) ? ' checked="checked"' : '';
			echo '<input type="radio" name="data[Product][default_product_option_stock_id]" value="' . $fieldValues['id'] . '"' . $checked . ' />';
			echo '</td>';
			
			echo '<td>';
			echo '<label>SKU:</label>';
			echo $form->input('ProductOptionStock.' . $k . '.sku', array(
				'class' => 'small',
				'label' => false,
				'div' => false,
				'value' => $fieldValues['sku'],
				'style' => 'width: 40px;'
			));
			echo '</td>';

			// Container table
			echo '</tr>';
			echo '</table>';
			echo '</div>';

			echo '<div id="varstock-stock" style="display: none;" class="varstock-data">';
			echo '<table>';
			echo '<tr>';
			
			echo '<td class="var-stock-status">';
			
			echo $form->hidden('ProductOptionStock.' . $k . '.id', array('value' => $fieldValues['id']));
			echo $form->hidden('ProductOptionStock.' . $k . '.product_id', array('value' => $option['ProductOption']['product_id']));
			echo $form->hidden('ProductOptionStock.' . $k . '.value_ids', array('value' => $fieldName));
			
			echo '<label>Stock Status:</label>';
			echo $form->input('ProductOptionStock.' . $k . '.stock_status_id', array(
				'label' => false,
				'div' => false,
				'selected' => $fieldValues['stock_status_id'],
				'style' => 'width: 100px;'
			));
			echo '</td>';
			
			echo '<td>';
			echo '<label>Lead Time:</label>';
			echo $form->input('ProductOptionStock.' . $k . '.lead_time', array(
				'class' => 'smallest',
				'label' => false,
				'div' => false,
				'value' => $fieldValues['stock_lead_time'],
				'style' => 'margin-right: 20px !important;'
			));
			echo '</td>';
			
			echo '<td>';
			echo '<label>Qty:</label>';
			echo $form->input('ProductOptionStock.' . $k . '.base_qty', array(
				'class' => 'tiny',
				'label' => false,
				'div' => false,
				'value' => $fieldValues['stock_base_qty']
			));
			echo '</td>';

			// Container table
			echo '</tr>';
			echo '</table>';
			echo '</div>';

			echo '<div id="varstock-pricing" style="display: none;" class="varstock-data">';
			echo '<table>';
			echo '<tr>';
			
			echo '<td class="var-stock-price">';
			echo '<labe>Modifier:</label>';
			echo $form->input('ProductOptionStock.' . $k . '.modifier', array(
				'label' => false,
				'div' => false,
				'options' => array('' => 'No change', 'fixed' => 'Fixed', 'add' => 'Add', 'subtract' => 'Subtract'),
				'selected' => $fieldValues['modifier'],
				array('style' => 'margin-right: 6px; width: 90px;')
			));
			echo '</td>';
			
			echo '<td>';
			
			foreach ($currencies as $currency)
			{	
				$currencyID = $currency['Currency']['id'];
				
				echo $form->hidden('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p. '.product_option_stock_id', array(
					'value' => $fieldValues['id']
				));
				
				echo $form->hidden('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p. '.currency_id', array(
					'value' => $currencyID
				));
				
				if (!empty($priceValues[$currencyID]['id']))
				{
					echo $form->hidden('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p . '.id', array(
						'value' => $priceValues[$currencyID]['id']
					));
				}
				
				$priceModifierValue = (!empty($priceValues[$currencyID])) ? $priceValues[$currencyID]['modifier_value'] : '';
				
				echo '<labe>Price:</label>';
				echo $form->input('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p . '.modifier_value', array(
					'label' => false,
					'div' => false,
					'value' => $priceModifierValue,
					'class' => 'tiny currency-input currency-input-' . $currencyID
				));
				
				$p++;
				
			}
				
			echo '</td>';

			// Container table
			echo '</tr>';
			echo '</table>';
			echo '</div>';

			echo '<div id="varstock-images" style="display: none;" class="varstock-data">';
			echo '<table>';
			echo '<tr>';

			echo '<td class="var-images">';
			
			$slot = 1;

			foreach ($images as $ki => $image)
			{
				echo '<span>' . $slot . '.</span> ';
				echo '<img src="/img/icons/magnifier2.png" alt="View Image" class="view-var-image" />';

				echo '<div class="tooltip"><img src="/img/vars/thumb/' . $image['filename'] . '.' . $image['ext'] . '" /></div>';

				echo '<a id="var-image-edit-link-' . $image['id'] . '" class="var-image-edit-link" href="#"><img src="/img/icons/image_edit.png" alt="Edit Image" title="Edit Image" /></a>';
				
				echo '<span id="var-image-edit-' . $image['id'] . '" class="var-image-edit" style="display: none;">';
				echo $form->input('ProductOptionStockImage.' . $fieldValues['id'] . '.' . $image['id'], array(
					'type' => 'file',
					'label' => false,
					'div' => false
				));
				echo '</span>';

				echo '<a href="/admin/product_option_stock_images/delete/' . $image['id'] . '"><img src="/img/icons/image_delete.png" alt="Delete Image" title="Delete Image" class="del-image" /></a>';
				
				$slot++;

			}

			if (count($images) < 4)
			{
				echo '<span>' . $slot . '.</span> ' ;
				echo '<a id="var-image=add-link-' . $fieldValues['id'] . '" class="var-image-add-link" href="#"><img src="/img/icons/image_add.png" alt="Add Image" title="Add Image" /></a>';
				
				echo '<span id="var-image-add-' . $fieldValues['id'] . '" class="var-image-add" style="display: none;">';
				echo $form->input('ProductOptionStockImage.' . $fieldValues['id'] . '.new' , array(
					'type' => 'file',
					'label' => false,
					'div' => false
				));
				echo '</span>';

			}


			echo '</td>';

			echo '</tr>';

			// Container table
			echo '</table>';
			echo '</div>';

			// Container cell
			echo '</td>';

						
			if (array_product($valuesCount) > ($k + 1))
			{
				echo '<tr>';
			}

			$k++;
			
			$fieldName = '';
			
			$inOption = 0;
			
		}
		
		?>			
	
<?php endfor; ?>
	
	</tr>
	</tbody>
	
</table>

