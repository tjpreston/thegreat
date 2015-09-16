<?php

$tabs = array();
$k = 0;

$tabs['details'] = array($k++, 'Details');
// $tabs['conditions'] = array($k++, 'Conditions');
$tabs['actions'] = array($k++, 'Actions');

$tabIndex = 0;
if (!empty($this->params['named']['tab']) && !empty($tabs[$this->params['named']['tab']]))
{
	$tabIndex = $tabs[$this->params['named']['tab']][0];
}
else if (!empty($initTab))
{
	$tabIndex = $tabs[$initTab][0];
}

?>

<div id="admin-content">
	
	<div id="side-col">
	
		<p><a href="/admin/basket_discounts" class="icon-link back-link">Back to List</a></p>
		
		
		
	</div>
	
	<?php echo $form->create('BasketDiscount', array('action' => 'save', 'id' => 'product-form')); ?>
		
		<div id="pane-details" class="panes">
			
			<?php echo $session->flash(); ?>
			
			<div id="header">
				<?php if (!empty($this->data['BasketDiscount']['name'])): ?>
					<a href="/admin/basket_discounts/delete/<?php echo intval($this->data['BasketDiscount']['id']); ?>" class="icon-link delete-link">Delete</a>
					<h1><?php echo h($this->data['BasketDiscount']['name']); ?> Basket Discount</h1>
				<?php else: ?>
					<h1>New Basket Discount</h1>
				<?php endif; ?>
			</div>
			
			<div id="pane-general" class="pane">
				<div class="fieldset-header"><span>Details</span></div>
				<div class="fieldset-box">
					<fieldset>
						
						<?php
						echo $form->input('id');
						echo $form->input('name', array('class' => 'small'));
						echo $form->input('coupon_code', array('class' => 'smaller'));
						?>
						
						<div class="input text multiple">
							<p>Available</p>
							<?php foreach (array('from', 'to') as $dir): ?>
								<?php echo $this->element('admin/date_input', array(
									'dir' => $dir,
									'value' => isset($this->data['BasketDiscount']['active_' . $dir]) ? $this->data['BasketDiscount']['active_' . $dir] : '',
									'field' => 'active_' . $dir,
									'input' => '[BasketDiscount][active_' . $dir . ']'
								)); ?>
							<?php endforeach; ?>
						</div>

						<?php

						echo $form->input('use_limit', array('class' => 'tiny', 'div' => array('id' => 'use-limit'), 'after' => '(0 for infinite)'));
						
						echo $form->input('uses', array('readonly' => 'readonly', 'disabled' => true, 'label' => 'Current Use Count', 'class' => 'tiny'));

						echo $form->input('min_basket_subtotal', array(
							'class' => 'smallest currency-input currency-input-1'
						));

						echo $form->input('min_basket_items_count', array(
							'class' => 'smallest',
							'label' => 'Min No of Items',
						));

						echo $form->input('applies_to', array(
							'options' => array(
								'basket' => 'All Baskets',
								'products' => 'Specific Products',
								'categories' => 'Specific Categories',
							),
							'label' => 'Applies to',
							'default' => 'basket',
						));

						?>

						<div id="categories" class="alt-input applies_to_option">
							<?php

							$category->modelName = 'Category.Category';
							if(empty($checkedCategories)){
								$checkedCategories = array();
							}
							$category->setProductCategories($checkedCategories);
							$category->generateCategoryCheckboxes($categories);

							?>
							<label class="left">Select Categories</label>
							<div id="product-cats" style="padding: 5px; float: left">
								<?php echo $category->getNestedTree(); ?>
							</div>
							<div style="clear:both"></div>
						</div>

						<div id="products" class="alt-input applies_to_option"><?php

							echo $this->Form->input('Product.Product', array(
								'options' => $productList,
								'multiple' => true,
								'style' => 'height:250px',
								'label' => 'Select Products',
							));

						?></div>

						<?php echo $form->input('modifier', array(
							'id' => 'modifier',
							'type' => 'select',
							'options' => array('fixed' => 'Fixed', 'percentage' => 'Percentage'),
							'class' => 'smaller'
						));	?>

						<div id="money-values" class="input" style="display: none;">
							<?php foreach ($currencies as $currency): ?>
								<?php
								
								$vid = (!empty($this->data['BasketDiscountPrice'][$currency['Currency']['id']]['id'])) ? 
									$this->data['BasketDiscountPrice'][$currency['Currency']['id']]['id'] : '';
								
								echo $form->input('BasketDiscountPrice.' . $currency['Currency']['id'] . '.id', array(
									'type' => 'hidden',
									'value' => $vid
								));
								$id = (!empty($this->data['BasketDiscount']['id'])) ? $this->data['BasketDiscount']['id'] : '';
								echo $form->input('BasketDiscountPrice.' . $currency['Currency']['id'] . '.basket_discount_id', array(
									'type' => 'hidden',
									'value' => $id
								));
								echo $form->input('BasketDiscountPrice.' . $currency['Currency']['id'] . '.currency_id', array(
									'type' => 'hidden',
									'value' => $currency['Currency']['id']
								));
								echo $form->input('BasketDiscountPrice.' . $currency['Currency']['id'] . '.modifier_value', array(
									'label' => 'Discount ' . '(' . $currency['Currency']['code'] . ')',
									'class' => 'smallest currency-input currency-input-' . $currency['Currency']['id']
								));
								?>
							<?php endforeach; ?>
						</div>
						
						<div id="percentage-value" class="input" style="display: none;">
							<?php echo $form->input('modifier_percentage_value', array(
								'label' => 'Discount',
								'class' => 'tiny',
								'after' => '%'
							)); ?>
						</div>
						
						<?php
						
						echo $form->input('active');
						// echo $form->input('sort', array('class' => 'tiny'));
						echo $form->input('notes', array(
							'type' => 'textarea',
							'label' => 'Notes<br /><span>(Internal use only)</span>'
						));
						
						?>

						
						
					</fieldset>
					
				</div>
			</div>
			
			<!--
			<div id="pane-conditions" class="pane">
				<div class="fieldset-header">Conditions</div>
				<div class="fieldset-box">
					<?php echo $form->submit('Save'); ?>
				</div>
			</div>
			-->
			
			

			<div class="fieldset-box">
				<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
			</div>
			
		</div>
		
	<?php echo $form->end(); ?>
	
</div>


<script>
	
$(function() {
	
	$("ul#product-nav").tabs("div.panes > div.pane", {
		initialIndex: <?php echo intval($tabIndex); ?> 
	});
	
	var api = $("ul#product-nav").data("tabs");
	
	$("#product-form").submit(function() {
		var pane = $(api.getCurrentPane())
		var id = pane.attr("id").substring(5);
		$("#last-pane").val(id);
		return true;
	});

	function checkModifier() {
		if ($("#modifier").val() == "fixed") {
			$("#percentage-value").hide();
			$("#money-values").show();
		}
		else {
			$("#money-values").hide();
			$("#percentage-value").show();
		}
	}
	
	$("#modifier").change(checkModifier);

	checkModifier();
	
});

function appliesToChange(){
	var appliesTo = $('#BasketDiscountAppliesTo');
	var selected = $('option:selected', appliesTo).val();

	$('.applies_to_option').hide();

	if(selected !== 'basket' && selected !== undefined){
		$('#' + selected).show();
	}
}

$(document).ready(function(){
	appliesToChange();
	$('#BasketDiscountAppliesTo').change(appliesToChange);
});

</script>




