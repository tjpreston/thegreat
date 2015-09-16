<?php

$split = explode('_', $link);

$tab = $split[0];
$model = Inflector::classify($link);
$name = Inflector::humanize($link);
$controller = $link;

?>


<div id="pane-<?php echo $tab; ?>" class="pane">
	
	<div class="fieldset-header"><span><?php echo $name; ?></span></div>
	<div class="fieldset-box">
		
		<?php if (!empty($linkedProducts)): ?>
		
			<table class="assoc-products" cellspacing="0">
				
				<thead>
					<tr>
						<th>&nbsp;</th>
						<th>Product</th>
						<th>Sort</th>
						<th></th>
					</tr>
				</thead>
				
				<tbody>
					<?php $k = 0; ?>							
					<?php foreach ($linkedProducts as $k => $prod): ?>
						<tr>
							<td class="assoc-prod-num">
								<?php 
								echo $form->hidden($model . '.' . $prod[$model]['id'] . '.id', array(
									'value' => $prod[$model]['id']
								));
								echo $form->hidden($model . '.' . $prod[$model]['id'] . '.from_product_id', array(
									'value' => $record['Product']['id']
								));
								echo $form->hidden($model . '.' . $prod[$model]['id'] . '.to_product_id', array(
									'value' => $prod[$model]['to_product_id']
								));
								?>
								<?php echo ($k + 1); ?>:
							</td>
							<td class="assoc-prod-select"><?php echo h($prod['ProductName']['name']); ?></td>
							<td class="assoc-prod-sort">
								<?php
								echo $form->text($model . '.' . $prod[$model]['id'] . '.sort', array(
									'value' => $prod[$model]['sort']
								));
								?>
							</td>
							<td class="assoc-prod-del">
								<a href="/admin/<?php echo $controller; ?>/delete/<?php echo intval($prod[$model]['id']); ?>">
									<img src="/img/icons/delete.png" alt="" />
								</a>
							</td>
						</tr>
					<?php endforeach; ?>
				</tbody>
				
			</table>						
		
		<?php endif; ?>
		
	</div>
	
	<div class="fieldset-box">
		<p><a id="inline-<?php echo $model; ?>" href="#add-<?php echo $model; ?>">Add <?php echo $name; ?></a></p>
	</div>
	
</div>

<div style="display: none;">
	<div id="add-<?php echo $model; ?>">
		<div class="fieldset-header"><span>Add <?php echo $name; ?></span></div>					
		<!--
		<div class="fieldset-box contains-table">
			<div style="padding: 14px;">
				<?php echo $form->input('product_search', array(
					'id' => 'add-' . $model . '-product-query',
					'after' => $form->button('Search', array('type' => 'button', 'id' => 'do-add-' . $model . '-products-search'))
				)); ?>
			</div>
		</div>
		<div id="add-<?php echo $model; ?>-product-list-box" class="prod-list">
			Please search for products.
		</div>
		-->
		
		<?php echo $form->input('Links.' . $model, array(
			'id' => 'add-' . $model . '-products',
			'type' => 'select',
			'multiple' => true,
			'options' => $productList,
			'label' => false,
			'style' => 'width: 100%; height: 260px; margin: 20px 0;'
		)); ?>
		<div style="float: right;">
			<?php echo $form->submit('Save', array('id' => 'save-new-' . $model . '-products', 'class' => 'submit')); ?>
		</div>
		
		
	</div>
</div>

<script>
$(function() {
	$("#do-add-<?php echo $model; ?>-products-search").click(function() {
		$("#add-<?php echo $model; ?>-product-list-box").load('/admin/products/getlist/cross/0/' + $("#add-<?php echo $model; ?>-product-query").val());
	});
	$("a#inline-<?php echo $model; ?>").fancybox({
		'hideOnContentClick': false,
		'autoDimensions': false,
		'height': 400,
		'width': 480
	});
	$("#save-new-<?php echo $model; ?>-products").click(function() {
		var ids = $("#add-<?php echo $model; ?>-products").val();
		$("#<?php echo $model; ?>-data").val(ids);
		$("#product-form").submit();
	});
});
</script>
	
	
	