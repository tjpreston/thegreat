<?php

$html->script(array(
	'vendors/tiny_mce/tiny_mce.js'
), array('inline' => false));

?>

<script type="text/javascript">

tinyMCE.init({
    theme : "advanced",
    mode : "textareas",
    convert_urls : "specific_textareas",
	editor_selector: "tinymce",
	width: 520,
	height: 280,
	plugins : "paste",
	paste_auto_cleanup_on_paste : true,
	theme_advanced_buttons3_add : "|,pastetext,pasteword,selectall",
	theme_advanced_toolbar_align : "left"
});

$(function() {	
	$("ul.lang-nav").tabs("div.fieldsets > fieldset");	
});

</script>


<div id="admin-content">
	
	<div id="side-col">
		
		<ul id="admin-links">
			<a href="/admin/products/" class="icon-link back-link">Return to list</a></p>
		</ul>
		
		<?php echo $this->element('admin/products/filter'); ?>

	</div>
	
	<?php echo $form->create('Product', array('action' => 'add')); ?>
		
		<div class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<h1>Add New Product</h1>
			</div>
			
			<div class="pane">

				<?php if (Configure::read('Catalog.grouped_products_enabled')): ?>
				
					<div class="fieldset-header"><span>Product Type</span></div>
					<div class="fieldset-box">
						<?php echo $form->input('type', array(
							'type' => 'select',
							'options' => array('simple' => 'Simple', 'grouped' => 'Grouped'),
							'style' => 'width: 100px;'
						)); ?>
					</div>

				<?php else: ?>

					<?php echo $form->input('type', array(
						'type' => 'hidden',
						'value' => 'simple'
					)); ?>

				<?php endif; ?>
								
				
				<div class="fieldset-header"><span>Name &amp; Descriptions</span></div>
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
								?>
								
								<?php
								echo $form->hidden('ProductDescription.' . $languageID . '.language_id', array(
									'value' => $languageID
								));
								?>
								
								<?php
								$fields = array(
									'id' => array(),
									'short_description' => array(),
									'long_description' => array('class' => 'tinymce')
									//'specification' => array('class' => 'spec')
								);
								?>
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
				
				<div class="fieldset-header"><span>General</span></div>
				<div class="fieldset-box">
					<?php 
					echo $form->input('ProductCategory.ProductCategory.primary_category_id', array(
						'options' => $treeList,
						'selected' => 0,
						'type' => 'select'
					));
					echo $form->input('sku', array('label' => 'SKU', 'class' => 'smaller'));
					//echo $form->input('manufacturer_id', array('empty' => array(0 => 'Please Choose --------------')));
					//echo $form->input('weight', array('label' => 'Weight', 'class' => 'smallest'));

					echo $form->hidden('visibility', array(
						'options' => Configure::read('Catalog.visibilities'),
						'value' => 'catalogsearch'
					));
					echo $form->input('active');
					//echo $form->input('taxable');
					echo $form->input('featured',array(
							'label' =>'Best Seller'
						));
					echo $form->input('new_product');
					// echo $form->input('best_seller');
					//echo $form->input('virtual_product');
					//echo $form->input('free_shipping');
					?>
				</div>
									
				<div class="fieldset-header"><span>Pricing</span></div>
				<div class="fieldset-box">
					<?php // echo $this->element('admin/currency_nav'); ?>
					<div class="fieldsets">
						
						<?php foreach ($currencies as $c => $currency): ?>
							
							<?php $currencyID = $currency['Currency']['id']; ?>
							
							<fieldset>
								
								<?php $fields = array(
									'id' => array(),
									'base_price' => array(
										'class' => 'smallest currency-input currency-input-' . intval($currencyID),
										'label' => 'Price'
									)
									//'base_rrp' => array(
									//	'class' => 'smallest currency-input currency-input-' . intval($currencyID),
									//	'label' => 'RRP'
									//),
								); ?>

								<?php foreach ($fields as $field => $attrs): ?>
									<?php
									$priceData[$field] = (!empty($this->data['ProductPrice'][$currencyID][$field])) ? $this->data['ProductPrice'][$currencyID][$field] : '';
									$fieldAttrs = array('value' => $priceData[$field]);
									if (!empty($attrs))
									{
										$fieldAttrs = array_merge($fieldAttrs, $attrs);
									}
									echo $form->input('ProductPrice.' . $currencyID . '.' . $field, $fieldAttrs);
									?>
								<?php endforeach; ?>

								<?php echo $form->hidden('ProductPrice.' . $currencyID . '.currency_id', array(
									'value' => $currencyID
								));	?>

							</fieldset>

						<?php endforeach; ?>

					</div>
				</div>

				<div class="fieldset-box">
					<?php echo $form->submit('Save', array('div' => 'submit', 'style' => 'float: right;')); ?>
				</div>
				
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>

