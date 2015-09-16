
<?php
	$this->set('body_id', 'basket-page');
?>

<div id="leftcol">
	<?php echo $this->element('template/customer_services'); ?>
	<?php echo $this->element('catalog/featured_and_recent'); ?>
</div>
<div id="content">


	<?php echo $this->element('basket/progress', array('selected' => 'bag')); ?>

	<div id="basket-header" class="header">
		<h1>My Bag</h1>
		<?php echo $this->element('template/breadcrumbs'); ?>
	</div>
	
	<?php echo $session->flash(); ?>
	
	<?php if (!empty($basketItems)): ?>
	
		<?php echo $form->create('BasketItem', array('url' => '/basket/update', 'id' => 'basket-form')); ?>

		<table cellspacing="1" cellpadding="0" border="0" id="basket">
			<tbody>
				<tr>
					<th style="text-align:left">Description</th>
					<th style="text-align:left">Price</th>
					<th style="text-align:left">Quantity</th>
					<th style="text-align:left">Total</th>
				</tr>
				<?php
				foreach($basketItems as $k => $item){
					echo $this->element('basket/basket_row', array('k' => $k, 'item' => $item));
				}
				?>
				<?php

				if($basket['Basket']['tax_rate'] == 0){
					$rowspan = 3;
				} else {
					$rowspan = 2;
				}

				?>
				<tr>
					<td rowspan="<?php echo $rowspan; ?>" class="postage">
						<?php echo $this->element('basket/shipping'); ?>
					</td>
					<td colspan="3" class="subtotal">
						<strong>Sub Total</strong>
						<?php echo $activeCurrencyHTML; ?><?php echo number_format(floatval($basket['Basket']['last_calculated_subtotal']), 2); ?>
					</td>
				</tr>

				<?php
				if($basket['Basket']['tax_rate'] == 0){
					$taxRate = Configure::read('Tax.rate');
					$taxRate = (100 + $taxRate) / 100;

					$vatReduction = ($basket['Basket']['last_calculated_grand_total'] * $taxRate) - $basket['Basket']['last_calculated_grand_total'];
				?>
				<tr>
					<td colspan="3" class="subtotal" style="font-size: 100%">
						<strong>VAT Reduction</strong>
						&ndash; <?php echo $activeCurrencyHTML . number_format(floatval($vatReduction), 2); ?>
					</td>
				</tr>
				<?php } ?>

				<tr>
					<td colspan="3" class="totalcost">
						<strong>Total</strong>
						<?php echo $activeCurrencyHTML . number_format(floatval($basket['Basket']['last_calculated_grand_total']), 2); ?>
					</td>
				</tr>
				
			</tbody>
		</table>

		<?php
			if($this->Session->read('Auth.Customer.trade') == 0){
				echo $this->element('basket/discount_code');
				echo $this->element('basket/additional_options');
			}
		?>

		<div class="basket-continue">
			<a href="/checkout" style="float: right;"><img src="/img/buttons/proceed-to-checkout.gif" alt="Proceed to Checkout"></a>
			<a href="/"><img src="/img/buttons/continue-shopping.gif" alt="Continue Shopping"></a>
		</div>
<!--
		<div class="send-button button right">
		   <a href="/checkout"><p style="padding-top:0px">Continue</p></a>
		 </div>
		
		<div class="checkout-row">
			<img class="cards-img" src="/img/basket/bank-cards.png" />
			<p class="cards-row">Proceed to Secure Checkout</p>
			<div class="arrow-right">
				&nbsp;
			</div>
		</div> -->
		
		<?php echo $form->end(); ?>

	<?php else: ?>

	<h3 style="padding-left: 9px;">Your Bag is currently empty.</h3>
	
	<?php endif; ?>

</div>

<div class="clear"></div>

<script src="//config1.veinteractive.com/tags/E8BDB55F/0665/4B59/90DC/C7A7E952145D/tag.js" type="text/javascript" async></script>
