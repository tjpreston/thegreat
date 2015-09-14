<?php
	$this->set('body_id', 'checkout2');

	echo $this->Html->css(array(
		'checkout',
	), null, array('inline' => false));
?>

<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
</div>

<div id="content">

<?php echo $this->element('basket/progress', array('selected' => 'confirm')); ?>
<h1>Confirm Your Order</h1>

<?php echo $session->flash(); ?>

<div class="checkout">

	<h4>Personal Information</h4>
	<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<th>First Name</th>
				<td><?php echo h($basket['Customer']['first_name']); ?></td>
			</tr>
			<tr>
				<th>Surname</th>
				<td><?php echo h($basket['Customer']['last_name']); ?></td>
			</tr>
			<tr>
				<th>Email</th>
				<td><?php echo h($basket['Customer']['email']); ?></td>
			</tr>
			<tr>
				<th>Telephone</th>
				<td><?php echo h($basket['Customer']['phone']); ?></td>
			</tr>
			<?php if(!empty($basket['Customer']['mobile'])): ?>
			<tr>
				<th>Mobile</th>
				<td><?php echo h($basket['Customer']['mobile']); ?></td>
			</tr>
			<?php endif; ?>
		</tbody>
	</table>

	<h4>Billing &amp; Delivery</h4>
	<?php echo $this->element('checkout/confirm_address', array('type' => 'billing')); ?>
	<?php echo $this->element('checkout/confirm_address', array('type' => 'shipping')); ?>

	<h4>Order Summary</h4>
</div>

<table cellspacing="1" cellpadding="0" border="0" id="basket">
	<tbody>
		<tr>
			<th style="text-align:left">Description</th>
			<th style="text-align:left">Price</th>
			<th style="text-align:left">Quantity</th>
			<th style="text-align:left">Total</th>
		</tr>
		<?php
			foreach ($basketItems as $k => $item){
				echo $this->element('checkout/basket_row', array('k' => $k, 'item' => $item));
			}

			$rowspan = 2;

			if($basket['Basket']['tax_rate'] == 0){
				$rowspan++;

				$taxRate = Configure::read('Tax.rate');
				$taxRate = (100 + $taxRate) / 100;

				$vatReduction = ($basket['Basket']['last_calculated_grand_total'] * $taxRate) - $basket['Basket']['last_calculated_grand_total'];
			}

			if(!empty($basket['Basket']['coupon_code'])){
				$rowspan++;

				$voucher = $basket['Basket']['coupon_code'];
				$discount = $basket['Basket']['last_calculated_discount_total'];
			}
		?>
		<tr>
			<td rowspan="<?php echo $rowspan; ?>" class="postage">
				<h3>Post &amp; Packaging</h3>
				<p>Sent via: <?php echo h($shippingInfo['ShippingCarrier']['name']) . ' ' . h($shippingInfo['ShippingCarrierService']['name']); ?></p>
				<div class="location">
					<span style="margin-right:12px">Price</span> 
					<?php

					$price = $shippingInfo['Price']['price'];
					if($price == 0){
						$price = ' <strong>FREE</strong>';
					}

					?>
					<span>£ <?php echo $price; ?></span>
				</div>
			</td>
			<td colspan="3" class="subtotal">
				<strong>Sub Total</strong>
				<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?>
			</td>
		</tr>
		<?php if(!empty($vatReduction)): ?>
		<tr>
			<td colspan="3" class="subtotal" style="font-size:100%">
				<strong>VAT Reduction</strong>
				&ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($vatReduction, 2); ?>
			</td>
		</tr>
		<?php endif; ?>
		<?php if(!empty($voucher)): ?>
		<tr>
			<td colspan="3" class="subtotal" style="font-size:100%">
				<strong>Voucher Code (<?php echo h($voucher); ?>)</strong>
				&ndash; <?php echo $activeCurrencyHTML; ?><?php echo number_format($discount, 2); ?>
			</td>
		</tr>
		<?php endif; ?>
		<tr>
			<td colspan="3" class="totalcost">
				<strong>Total</strong>
				<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_grand_total']), 2); ?>
			</td>
		</tr>
	</tbody>
</table>

<div class="checkout">

	<?php if($this->Session->read('Auth.Customer.trade') != 1): ?>
	<h4>Additional Options</h4>
	<table cellspacing="0" cellpadding="0" border="0">
		<tbody>
			<tr>
				<th>Gift Wrap?</th>
				<td><?php echo ($basket['Basket']['gift_wrap'] ? 'Yes' : 'No'); ?></td>
			</tr>
			<tr>
				<th>Gift Message</th>
				<td><?php echo (empty($basket['Basket']['gift_message']) ? 'None' : h($basket['Basket']['gift_message'])); ?></td>
			</tr>
			<tr>
				<th>Watch Sizing?</th>
				<td><?php echo ($basket['Basket']['watch_sizing'] ? 'Yes' : 'No'); ?></td>
			</tr>
			<tr>
				<th>Wrist Size</th>
				<td><?php echo (empty($basket['Basket']['wrist_size']) ? 'Not specified' : h($basket['Basket']['wrist_size']) . ' cm'); ?></td>
			</tr>
		</tbody>
	</table>
	<?php endif; ?>

	<h4>Your Payment Details</h4>
	<div class="payment-provider">
	<?php if ($session->read('Auth.Customer.allow_payment_by_account')): ?>
		<h3>Payment on Account</h3>
		<p>This order will be paid on account, and you will be invoiced for it separately.</p>
	<?php else: ?>
		<img src="/img/checkout/sagepay.gif" alt="SagePay" align="right">
		<h3>Safe &amp; Secure Payments Provided By SagePay</h3>
		<p>Please click &ldquo;Continue...&rdquo; to proceed to the SagePay secure website and enter your credit card information to complete your order.</p>
	<?php endif; ?>
	</div>


</div>
	
	<?php if ($session->read('Auth.Customer.allow_payment_by_account')): ?>

		<div class="basket-continue">
			<a href="/checkout/pay_on_account" style="float:right"><img src="/img//buttons/pay-on-account.gif" alt="Pay On Account" /></a>
			<a href="/checkout"><img src="/img/buttons/back-details.gif" alt="Back to Details"></a>
		</div>

	<?php else: ?>

		<?php echo $this->element('payments/sagepay_form'); ?>
			<div class="basket-continue">
		      <?php echo $form->submit('/img/buttons/continue-secure.gif', array('style' => 'float: right;')); ?>
		      <a href="/checkout/support"><img src="/img/buttons/back-to-support.gif" alt="Back to Support"></a>
		    </div>
		<?php echo $form->end(); ?>
		
	<?php endif; ?>

</div>

<script src="//config1.veinteractive.com/tags/E8BDB55F/0665/4B59/90DC/C7A7E952145D/tag.js" type="text/javascript" async></script>
<img src="//drs2.veinteractive.com/DataReceiverService.asmx/Pixel?journeycode=E8BDB55F-0665-4B59-90DC-C7A7E952145D" width="1" height="1"/>