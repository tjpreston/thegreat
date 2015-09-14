<?php
	$this->set('body_id', 'checkout2');
?>


<div class="grid_24">
	<?php echo $this->element('checkout/nav', array('step' => 'confirmation')); ?>

	<div class="header">
		<h1><span class="face1">Confirm</span> <span class="face2">Order</span></h1>
		<p class="intro">Please review your order confirmation below.</p>
	</div>

<?php echo $session->flash(); ?>

<fieldset>	
	<div class="heading">
		PERSONAL INFORMATION
	</div>

	<div class="confirmation-group">
		<dl>
			<dt>First Name</dt>
			<dd><?php echo h($basket['Customer']['first_name']); ?></dd>
			<dt>Last Name</dt>
			<dd><?php echo h($basket['Customer']['last_name']); ?></dd>
			<?php if (!empty($basket['Customer']['company_name'])):?>
				<dt>Company Name</dt>
				<dd><?php echo h($basket['Customer']['company_name']); ?></dd>
			<?php endif;?>
			<dt>Email</dt>
			<dd><?php echo h($basket['Customer']['email']); ?></dd>
			<dt>Telephone</dt>
			<dd><?php echo h($basket['Customer']['phone']); ?></dd>
			<dt>Mobile</dt>
			<dd><?php echo h($basket['Customer']['mobile']); ?></dd>
		</dl>
		<a href="/checkout" class="checkout-edit dual">
			<span class="face1">Edit</span> <span class="face2">Details</span>
		</a>
	</div>
</fieldset>
<fieldset>
	<div class="heading">BILLING ADDRESS</div>
	
	<div class="confirmation-group">
		<dl>
			<dt>First Name</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['first_name']); ?></dd>
			<dt>Last Name</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['last_name']); ?></dd>
	
			<?php if (!empty($basket['CustomerBillingAddress']['company_name'])):?>
				<dt>Company Name</dt>
				<dd><?php echo h($basket['CustomerBillingAddress']['company_name']); ?></dd>
			<?php endif;?>
			<dt>Address</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['address_1']); ?></dd>
			<?php if (!empty($basket['CustomerBillingAddress']['address_2'])): ?>
			<dt>&nbsp;</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['address_2']); ?></dd>
			<?php endif; ?>
			<dt>Town / City</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['town']); ?></dd>
			<dt>County</dt>
			<dd><?php echo h($basket['CustomerBillingAddress']['county']); ?></dd>
			<dt>Postcode</dt>
			<dd><?php echo h(strtoupper($basket['CustomerBillingAddress']['postcode'])); ?></dd>
			<dt>Country</dt>
			<dd><?php echo h($basket['CustomerBillingAddressCountry']['name']); ?></dd>
		</dl>
		<a href="/checkout" class="checkout-edit dual">
			<span class="face1">Change</span> <span class="face2">Address</span>
		</a>
	</div>
</fieldset>	
<fieldset>
	<div class="heading">DELIVERY ADDRESS</div>
	<div class="confirmation-group">
	
		<?php if (!empty($basket['CustomerShippingAddress']['id'])): ?>
			
			<dl>
				<dt>First Name</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['first_name']); ?></dd>
				<dt>Last Name</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['last_name']); ?></dd>
				<?php if (!empty($basket['CustomerShippingAddress']['company_name'])):?>
					<dt>Company Name</dt>
					<dd><?php echo h($basket['CustomerShippingAddress']['company_name']); ?></dd>
				<?php endif;?>
				<dt>Address</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['address_1']); ?></dd>
				<?php if (!empty($basket['CustomerShippingAddress']['address_2'])): ?>
				<dt>&nbsp;</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['address_2']); ?></dd>
				<?php endif; ?>
				<dt>Town / City</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['town']); ?></dd>
				<dt>County</dt>
				<dd><?php echo h($basket['CustomerShippingAddress']['county']); ?></dd>
				<dt>Postcode</dt>
				<dd><?php echo h(strtoupper($basket['CustomerShippingAddress']['postcode'])); ?></dd>
				<dt>Country</dt>
				<dd><?php echo h($basket['CustomerShippingAddressCountry']['name']); ?></dd>
			</dl>
			<div>
				<p>
					<a href="/checkout" class="checkout-edit dual">
						<span class="face1">Change</span> <span class="face2">Address</span>
					</a>
				</p>
			</div>
			
		<?php else: ?>	
			<p style="padding-left: 36px;">Deliver to billing address as above.</p>
		<?php endif; ?>
		
	</div>
</fieldset>	
	
	<table cellspacing="0" class="basket-table" style="margin-top: 20px;">
		<thead>
			<tr>
				<th class="basket-item-name-col">Item(s)</th>
				<th>Price</th>
				<th>Qty</th>
				<th>Item Total</th>
				<th>&nbsp;</th>
			</tr>
		</thead>

		<tbody>
		
			<?php $i = 0; ?>
			
			<?php foreach ($basketItems as $k => $item): ?>
			
				<tr class="item-row<?php echo ($basket['Basket']['last_calculated_total_items'] == ($i + 1)) ? ' last-row' : ''; ?>">
					<td class="basket-item-name-col">
						<?php echo h($item['ProductName']['name']); ?>

						<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
							<br/>
							<span class="giftwrap-details giftwrap-item-name">Gift wrapping</span>
							<?php if (!empty($item['BasketItem']['custom_text'])): ?>
								<br />
								<p>Gift Card Message: <?php echo h($item['BasketItem']['custom_text']); ?></p>
							<?php endif; ?>
						<?php endif; ?>
					</td>
					<td>
						<?php echo $activeCurrencyHTML . ' ' . number_format(floatval($item['BasketItem']['price']), 2); ?>

						<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
							<br/>
							<span class="giftwrap-details giftwrap-price"><?php echo $activeCurrencyHTML; ?> <?php echo number_format(Configure::read('Giftwrapping.price'), 2); ?></span>
						<?php endif; ?>
					</td>
					<td><?php echo intval($item['BasketItem']['qty']); ?></td>
					<td>
						<?php echo $activeCurrencyHTML . ' ' . number_format(floatval($item['BasketItem']['price'] * $item['BasketItem']['qty']), 2); ?>

						<?php if (!empty($item['BasketItem']['giftwrap_product_id'])): ?>
							<br/>
							<span class="giftwrap-details giftwrap-total"><?php echo $activeCurrencyHTML; ?> <?php echo number_format(Configure::read('Giftwrapping.price') * $item['BasketItem']['qty'], 2); ?></span>
						<?php endif; ?>
					</td>
					<td class="basket-actions-col"><a href="/basket?item=<?php echo intval($item['BasketItem']['id']); ?>"><img src="/img/icons/pencil.png" alt="" /></a></td>
				</tr>

				<?php $i++; ?>
				
			<?php endforeach; ?>

		</tbody>

		<tfoot>
			
			<tr id="subtotal-row">
				<td colspan="3" class="align-right"><span>Sub-Total</span></td>
			    <td>&pound;<?php echo number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?></td>
				<td>&nbsp;</td>
			</tr>
			
			<?php $discount = floatval($basket['Basket']['last_calculated_discount_total']); ?>

			<?php if (!empty($discount)): ?>
				<tr id="discount-row">
					<td colspan="3" class="align-right"><span>Gift voucher: <strong><?php echo h($basket['Basket']['coupon_code']); ?></strong></span></td>
					<td colspan="2">- <?php echo '&pound;' . number_format($discount, 2); ?></td>
				</tr>
			<?php endif; ?>
			
			<?php if(!empty($basket['Basket']['tax_reduction'])):	?>
				<tr id="tax-reduction-row">
					<td colspan="3" class="align-right"><span>Tax Reduction</span></td>
					<td colspan="2">&ndash; <?php echo '&pound;' . number_format($basket['Basket']['tax_reduction'], 2); ?></td>
				</tr>
			<?php endif; ?>
			
			<tr id="delivery-row">
				<td colspan="3" class="align-right"><span><?php echo h($shippingInfo['ShippingCarrierService']['name']); ?></span></td>
				<td colspan="2"><?php echo '&pound;' . number_format(floatval($shippingInfo['Price']['price']), 2); ?></td>
			</tr>
			
			<?php if (Configure::read('Tax.show_tax_total_on_basket')): ?>
				<tr>
					<td id="tax-cost" class="align-right" colspan="3"><span>VAT</span></td>
					<td colspan="2"><?php echo '&pound;' . number_format(floatval($basket['Basket']['last_calculated_subtotal_tax'] + $basket['Basket']['last_calculated_shipping_tax']), 2); ?></td>
				</tr>
			<?php endif; ?>
			
			<tr id="total-row">
				<td colspan="3"><span>Grand Total</span></td>
				<td colspan="2" id="grand-total"><?php echo '&pound;' . number_format(floatval($basket['Basket']['last_calculated_grand_total']), 2); ?></td>
			</tr>

		</tfoot>
	
	</table>
	
	<?php echo $this->element('payments/worldpay_form'); ?>
	<div class="right">
		<?php echo $form->button('<span class="face1">Continue</span> <span class="face2">To Payment</span>', array('class' => 'send-button', 'div' => false, 'style' => 'margin-top:10px', 'class' => 'dual')); ?>
	</div>
	<div class="checkout-row" style="margin-bottom: 0; margin-top: 30px;">
		<img class="cards-img" src="/img/accepted-cards.png"><a href="http://www.worldpay.com/" target="_blank" title="Payment Processing - WorldPay - Opens in new browser window"><img src="/img/icons/worldpay.png" class="left" /></a>
		<!-- PayPal Logo --><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" style="margin-top:13px;" alt="PayPal Logo"></a><!-- PayPal Logo -->
		<div class="arrow-right"></div>
	</div>

	<?php echo $form->end(); ?>
	
	<?php if ($session->read('Auth.Customer.allow_payment_by_account')): ?>

		<div class="checkout-row" id="pay-on-account">
			<p class="cards-row">Pay on Account<br /><span>This order will be subject to your account with * being in good standing.</span></p>
			<a href="/checkout/pay_on_account"><img src="/img/buttons/continue.png" alt="Pay On Account" /></a>
		</div>
		
	<?php endif; ?>





</div>
<div class="clear"></div>