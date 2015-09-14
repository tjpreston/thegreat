<h2 class="shadowed">Voucher Code</h2>

<?php if (!empty($basket['Basket']['coupon_code'])): ?>

<table cellspacing="0" cellpadding="0" border="0" class="basket" id="voucher-form">
	<tbody>
		<tr class="border-bottom">
			<td class="voucher">
				Voucher code: <strong><?php echo h($basket['Basket']['coupon_code']); ?></strong> (<a href="/basket/removediscountcode">Remove</a>)
			</td>
			<td align="right" class="values">
				<span><strong>Voucher Discount:</strong> &ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($basket['Basket']['last_calculated_discount_total'], 2); ?></span>
			</td>
		</tr>
		<!-- <tr class="border-bottom" id="basket-total">
			<td>&nbsp;</td>
			<td align="right"><span><strong>Goods Sub Total:</strong> £420.75</span></td>
		</tr> -->
	</tbody>
</table>

<?php else: ?>

<table cellspacing="0" cellpadding="0" border="0" class="basket" id="voucher-form">
	<tbody>
		<tr class="border-bottom">
			<td class="voucher">
				<h3>Enter Voucher Code</h3>
				<div class="formdiv">
					<?php

					echo $form->input('Coupon.code', array(
						'div' => false,
						'label' => false,
						'id' => 'vouchercode',
					));

					echo $this->Form->button(
						$this->Html->image('/img/buttons/apply.gif', array(
							'alt' => 'Apply',
						)),
						array(
							'escape' => false,
						)
					);

					?>
				</div>
			</td>
			<td align="right" class="values">
			</td>
		</tr>
	</tbody>
</table>

<?php endif; ?>