
<?php
	$this->set('body_id', 'basket');
?>

<?php if (Configure::read('Giftwrapping.enabled')): ?>
	
	<?php

	echo $this->Html->script(array(
		'vendors/jquery.fancybox-1.3.4.pack.js'
	), array('inline' => false));

	echo $this->Html->css(array(
		'vendors/jquery.fancybox-1.3.4.css'
	), null, array('inline' => false));

	?>

	<script type="text/javascript">
	var addGiftWrapMsg = '<?php echo Configure::read('Giftwrapping.add_msg'); ?>';
	var delGiftWrapMsg = '<?php echo Configure::read('Giftwrapping.rem_msg'); ?>';
	</script>

<?php endif; ?>

<?php echo $this->element('template/breadcrumbs'); ?>

<div class="grid_24">

	<div id="basket-header" class="header">
		<h1><span class="face1">Your</span> <span class="face2">Basket</span></h1>
		<?php echo $this->element('basket/basket_status'); ?>
	</div>
	<div id="continue-shopping">
		<a href="/"><span class="face1">Continue</span> <span class="face2">Shopping</span></a>
	</div>
	
	<?php echo $session->flash(); ?>
	<div class="clear"></div>
	
	<?php if (!empty($basketItems)): ?>
	
		<?php echo $form->create('BasketItem', array('url' => '/basket/update', 'id' => 'basket-form', 'class' => 'form')); ?>
	
		<div id="basket-headings" class="basket-row">
		  <div class="basket-desc">DESCRIPTION</div>
		  <div class="basket-price">PRICE</div>
		  <div class="basket-qty">QTY</div>
		  <div class="basket-total">TOTAL</div>
		</div>
	
		<?php foreach ($basketItems as $k => $item): ?>
			<?php echo $this->element('basket/basket_row', array('k' => $k, 'item' => $item)); ?>
		<?php endforeach; ?>
		
		<div id="subtotal">
		  <button class="send-button dual" type="submit"><span class="face1">Update</span> <span class="face2">Basket</span></button>

		<p id="subtotal-amount"><span class="face1">Sub Total:</span> <span class="face2"><?php echo $activeCurrencyHTML . ' ' . number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?></span>
		</p>
		
		</div>
		<?php echo $this->element('basket/discount_code'); ?>

		<?php echo $this->element('basket/shipping'); ?>

		<div id="delivery-extras">
			<p><a href="#" class="show_hide">Add delivery instructions or other notes to your order.</a></p>
			<div class="slidingDiv">
				<?php echo $this->Form->input('Basket.order_note',array(
				'label' => false,
				'type' => 'textfield',
				'class' => 'order-notes',
				'value' => $basket['Basket']['order_note'] )); ?>
				<button class="send-button dual" type="submit"><span class="face1">Save</span> <span class="face2">Note</span></button>
			</div>
			<?php if(!empty($basket['Basket']['order_note'])):?>
				<p><strong>Your current order notes:  </strong></p>
				<p><?php echo h($basket['Basket']['order_note']); ?> </p>
				<a href="#" class="show_hide dual"><span class="face1">Edit</span> <span class="face2">Note</span></a>
			<?php endif; ?>
		</div>
		



		<?php if (!empty($basket['Basket']['tax_rate']) && Configure::read('Tax.show_tax_total_on_basket')): ?>
			<div id="tax">
				<p id="tax-amount"><?php echo $activeCurrencyHTML . ' ' . number_format(floatval($basket['Basket']['last_calculated_subtotal_tax'] + $basket['Basket']['last_calculated_shipping_tax']), 2); ?></p>
				<p id="tax-label">VAT:</p>
			</div>
		<?php endif; ?>


		
		<div id="total">
				
				<p id="total-amount"><span class="face2"><?php echo  $activeCurrencyHTML . ' ' . number_format(floatval($basket['Basket']['last_calculated_grand_total']), 2); ?></span></p>
				<p id="total-label"><span class="face1">Grand Total:</span></p>
				<div style="clear:right;"></div>
				
		</div>

		<div class="send-button button right">
		   <a href="/checkout" class="dual checkout"><span class="face1">Continue</span> <span class="face2">To Checkout</span></a>
		 </div>
		
		<div class="checkout-row">
			<img class="cards-img" src="/img/accepted-cards.png" />
			<!-- PayPal Logo --><img src="https://www.paypalobjects.com/webstatic/mktg/Logo/pp-logo-100px.png" class="paypal-img";" alt="PayPal Logo"></a><!-- PayPal Logo -->
			<div class="arrow-right"></div>
		</div>
                <!-- yes I know the style padding css here is a hack but give a fuck - TJP 8/10/15 -->
                <div style="float: left;"><span class="face1">Powered by:</span><span  style="float: right; padding-right: 560px;"><a href="http://www.worldpay.com/" target="_blank" title="Payment Processing - WorldPay - Opens in new browser window"><img style="padding-left: 5px" src="/img/icons/worldpay.png" /></a></span>
                </div>
		<?php echo $form->end(); ?>
	
	<?php endif; ?>

</div>
