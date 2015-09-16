
<div id="gift-voucher">
	
	<h2>Gift Voucher</h2>
	
	<?php if (!empty($basket['Basket']['coupon_code'])): ?>

		<div class="clear"></div>
		
		<p id="voucher-applied">
			Gift voucher <strong><?php echo h($basket['Basket']['coupon_code']); ?></strong> currently applied to your basket
		</p>
		
		<a id="voucher-remove" href="/basket/removediscountcode">
			<img src="/img/icons/delete.png" alt="Remove gift voucher" title="Remove gift voucher" />
		</a>
		
		<p id="voucher-amount">&ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($basket['Basket']['last_calculated_discount_total'], 2); ?></p>
		
	<?php else: ?>


	<div class="voucher-input">
		<?php echo $form->input('Coupon.code', array(
			'label' => false,
			'id' => 'voucher-code-input',
			'placeholder' => 'Enter voucher code'
		)); ?>
		<?php echo $this->Form->button('Apply', array(
			'class' => 'btn btn-small',
			'id' =>'voucher-submit',
		)); ?>
	</div>
		
	<?php endif; ?>
</div>

