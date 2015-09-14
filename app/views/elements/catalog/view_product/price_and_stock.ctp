<?php if($record['Product']['stock_in_stock']): ?>
	<div class="stock in">
		Item is In Stock
	</div>
<?php else: ?>
	<div class="stock out">
		Item is Out of Stock
	</div>
<?php endif; ?>

<div class="price">
	<?php if($record['ProductPrice']['on_special'] == '1'): ?>
		<span class="base second-color">Was <?php echo $activeCurrencyHTML; ?><?php echo number_format($record['ProductPrice']['base_price'], 2); ?></span>
		<span class="sale">Now <?php echo $activeCurrencyHTML; ?><?php echo number_format($record['ProductPrice']['active_price'], 2); ?></span>
	<?php else: ?>
		<span class="active first-color"><?php echo $activeCurrencyHTML; ?><?php echo number_format($record['ProductPrice']['active_price'], 2); ?></span>
	<?php endif; ?>
</div>

<div class="purchasing">
	<?php if($record['Product']['stock_in_stock']): ?>
		<?php if (!empty($record['ProductOptionStock'])): ?>
			<?php echo $form->hidden('Basket.0.product_option_stock_id', array('value' => $record['ProductOptionStock']['id'])); ?>
		<?php endif; ?>
	<?php
		echo $this->Form->hidden('Basket.0.product_id', array('value' => $record['Product']['id']));
		echo $this->Form->input('Basket.0.qty', array('class' => 'quantity', 'id' => 'quantity', 'value' => 1, 'div' => false, 'label' => 'Qty'));
	?>

	<div class="button">
	<?php
		echo $this->Form->button('<span class="face1">Add</span> <span class="face2">To Basket</span>');
	?>
	</div>
	<?php endif; ?>
</div>