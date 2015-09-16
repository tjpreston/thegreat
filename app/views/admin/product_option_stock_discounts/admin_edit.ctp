
<div class="panes">

	<?php echo $session->flash(); ?>

	<div id="header">
		<h1><?php echo h($name); ?></h1>
	</div>
	
	<?php echo $form->create('ProductOptionStockDiscount', array('action' => 'save')); ?>
	
	<?php echo $form->hidden('id', array('value' => $stockID)); ?>

	<?php echo $this->element('admin/products/customer_discount_price_matrix', array(
		'fkID' => $stockID,
		'model' => 'ProductOptionStockDiscount',
		'fk' => 'product_option_stock_id'
	)); ?>

	<div class="fieldset-box">
		<?php echo $form->submit('Save', array('div' => 'submit single-line')); ?>
	</div>
	
	<?php echo $form->end(); ?>
	

</div>






