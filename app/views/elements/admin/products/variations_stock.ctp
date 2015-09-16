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
		
		<?php
		
		$p = 0;
		$k = 0;
		
		?>
	
		<?php foreach ($rekeyedOptionsStock as $valueID => $stock): ?>
			
			<tr>
				
				<?php
				
				$fieldValues = $stock['ProductOptionStock'];
				$priceValues = $stock['ProductOptionStockPrice'];
				$images = $stock['ProductOptionStockImage'];
				
				$ids = explode('-', $fieldValues['value_ids']);
				foreach ($ids as $id)
				{
					echo '<td class="var-value">' . $valueNames[$id] . '</td>';
				}
				
				// Container cell
				echo '<td style="width: 10px;">';
				echo '&nbsp;';
				echo '</td>';
				echo '<td>';
				
				echo '<div id="varstock-general" class="varstock-data">';
				echo '<table>';
				echo '<tr>';
				
				echo '<td class="var-stock-avail">';
				echo '<label>Avail?</label>';
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
					'class' => 'var-sku',
					'label' => false,
					'div' => false,
					'value' => $fieldValues['sku']
				));
				echo '</td>';
				
				echo '<td>';
				echo '<label>Name:</label>';
				echo $form->input('ProductOptionStock.' . $k . '.name', array(
					'label' => false,
					'div' => false,
					'value' => $fieldValues['name'],
					'class' => 'var-name'
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
				echo $form->hidden('ProductOptionStock.' . $k . '.value_ids', array('value' => $fieldValues['value_ids']));
				
				echo '<label>Current Qty:</label>';
				echo $form->input('ProductOptionStock.' . $k . '.stock_base_qty', array(
					'label' => false,
					'div' => false,
					'value' => $fieldValues['stock_base_qty'],
					'class' => 'tiny'
				));
				echo '</td>';
				
				echo '<td>';
				echo '<label>Lead Time:</label>';
				echo $form->input('ProductOptionStock.' . $k . '.stock_lead_time', array(
					'class' => 'smallest',
					'label' => false,
					'div' => false,
					'value' => $fieldValues['stock_lead_time'],
					'style' => 'margin-right: 20px !important;'
				));
				echo '</td>';
				
				//echo '<td>';
				//echo '<label>Qty:</label>';
				//echo $form->input('ProductOptionStock.' . $k . '.base_qty', array(
				//	'class' => 'tiny',
				//	'label' => false,
				//	'div' => false,
				//	'value' => $fieldValues['stock_base_qty']
				//));
				//echo '</td>';

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
					
					echo '<label>Price:</label>';
					echo $form->input('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p . '.modifier_value', array(
						'label' => false,
						'div' => false,
						'value' => $priceModifierValue,
						'class' => 'currency-input currency-input-' . $currencyID
					));

					// $tradePriceModifierValue = (!empty($priceValues[$currencyID])) ? $priceValues[$currencyID]['trade_modifier_value'] : '';

					// echo '<label>Trade Price:</label>';
					// echo $form->input('ProductOptionStock.' . $k . '.ProductOptionStockPrice.' . $p . '.trade_modifier_value', array(
					// 	'label' => false,
					// 	'div' => false,
					// 	'value' => $tradePriceModifierValue,
					// 	'class' => 'currency-input currency-input-' . $currencyID
					// ));
					
					$p++;
					
				}
				
				if (Configure::read('Catalog.use_tiered_customer_pricing'))
				{
					echo '<a href="/admin/product_option_stock_discounts/edit/' . $fieldValues['id'] . '" class="link-discount">Customer Discounts</a>';
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

				echo '<ol class="var-img-sort">';

				foreach ($images as $ki => $image)
				{
					echo '<li id="r' . $image['id'] . '">';
					
					echo '<span class="slot">' . $slot . '.</span> ';
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

					echo '</li>';
					
					$slot++;

				}

				echo '</ol><div style="clear:both"></div>';

				echo '<input type="hidden" name="data[ProductOptionStockImage][' . $fieldValues['id'] . '][sort]" value="" id="ProductOptionStockImage317Sort" class="sort-field" />';

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
				
				?>
				
			</tr>
			
			<?php $k++; ?>
			
		<?php endforeach; ?>
		
	</tbody>
	
</table>


<script>

$(function() {
	$(".link-discount").click(function() {
		window.open(this.href, "discounts", "toolbar=0, width=840, height=800, scrollbars=1");
		return false;
	});
});

</script>



