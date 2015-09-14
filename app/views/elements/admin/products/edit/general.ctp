<div id="pane-general" class="pane">
	<div class="fieldset-header"><span>General</span></div>
	<div class="fieldset-box">
		<fieldset>
			<?php
			echo $form->input('id');
			echo $form->input('sku', array('label' => 'SKU', 'class' => 'smaller'));
			echo $form->input('till_sku', array('label' => 'Till SKU', 'class' => 'smaller'));
			
			
			echo $form->input('attribute_set_id', array('empty' => array(0 => 'Choose Product Type'), 'label' => 'Product Type'));
			

			echo $form->input('manufacturer_id', array('empty' => array(0 => 'Choose Manufacturer')));
			echo $form->input('weight', array('label' => 'Weight', 'class' => 'smallest'));
			echo $form->input('active');
			// echo $form->input('in_stock');
			echo $form->hidden('visibility', array(
				'options' => Configure::read('Catalog.visibilities'),
				'value' => 'catalogsearch'
			));
			// echo $form->input('taxable');
			// echo $form->input('featured');
			// echo $form->input('new_product');
			// echo $form->input('best_seller');
			// echo $form->input('virtual_product');
			// echo $form->input('free_shipping');
			echo $form->input('courier_shipping_only');
			?>
		</fieldset>
	</div>	
</div>

