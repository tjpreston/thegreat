Thank you very much for your order from <?php echo Configure::read('Site.name'); ?>

Your order reference: #<?php echo $orderRef; ?> 

--------------------------------------------------------------------
<?php if(!empty($basket['Basket']['purchase_order'])): ?>
Purchase order: <?php echo $basket['Basket']['purchase_order']; ?>

--------------------------------------------------------------------
<?php endif; ?>

Billing Address:

<?php
$billingAddress  = '    ' . $basketAndCustomer['Customer']['first_name'] . ' ' . $basketAndCustomer['Customer']['last_name'] . "\n";
$billingAddress .= '    ' . $basketAndCustomer['CustomerBillingAddress']['address_1'] . "\n";
if (!empty($basketAndCustomer['CustomerBillingAddress']['address_2']))
{
	$billingAddress .= '    ' . $basketAndCustomer['CustomerBillingAddress']['address_2'] . "\n";
}	
$billingAddress .= '    ' . $basketAndCustomer['CustomerBillingAddress']['town'] . "\n";
if (!empty($basketAndCustomer['CustomerBillingAddress']['county']))
{
	$billingAddress .= '    ' . $basketAndCustomer['CustomerBillingAddress']['county'] . "\n";
}
$billingAddress .= '    ' .$basketAndCustomer['CustomerBillingAddress']['postcode'];
echo $billingAddress;
?> 

Delivery Address:

<?php 
if (empty($basket['Basket']['ship_to_billing_address']))
{
	echo '    ' . $basketAndCustomer['CustomerShippingAddress']['first_name'] . ' ' . $basketAndCustomer['CustomerShippingAddress']['last_name'] . "\n";
	echo '    ' . $basketAndCustomer['CustomerShippingAddress']['address_1'] . "\n";
	if (!empty($basketAndCustomer['CustomerShippingAddress']['address_2'])): echo '    ' . $basketAndCustomer['CustomerShippingAddress']['address_2'] . "\n"; endif;
	echo '    ' . $basketAndCustomer['CustomerShippingAddress']['town'] . "\n";
	if (!empty($basketAndCustomer['CustomerShippingAddress']['county'])): echo '    ' . $basketAndCustomer['CustomerShippingAddress']['county'] . "\n"; endif;
	echo '    ' .$basketAndCustomer['CustomerShippingAddress']['postcode'];
}
else
{
	echo $billingAddress;
}
?> 

--------------------------------------------------------------------

Order Summary:
<?php foreach ($basketItems as $item): ?>
<?php echo $item['BasketItem']['qty']; ?> x <?php

echo $item['ProductName']['name'];

if (!empty($item['ProductOptionStock']['name']))
{
	$itemName = ' (' . $item['ProductOptionStock']['name'];

	$itemName .= ')';

	echo $itemName;
}

?>; £<?php echo $item['BasketItem']['price']; ?>

<?php

if(!empty($item['BasketItem']['giftwrap_product_id'])){
	$gift_wrap_id = $item['BasketItem']['giftwrap_product_id'];
	echo '    + Gift wrapping; £' . number_format(($item['BasketItem']['qty'] * Configure::read('Giftwrapping.price')), 2);
	echo "\n";
	if(!empty($item['BasketItem']['custom_text'])){
		echo'    + Gift message:' . $item['BasketItem']['custom_text'];
	}
	echo "\n";
}

?>

<?php endforeach; ?>

Subtotal: £<?php echo $basketAndCustomer['Basket']['last_calculated_subtotal']; ?> 
<?php if(!empty($basketAndCustomer['Basket']['coupon_code'])): ?>
Voucher code: <?php echo $basket['Basket']['coupon_code']; ?> <?php if(!empty($discountAmount)) echo ': -£' . $discountAmount; ?> 
<?php endif; ?>
Delivery: <?php echo (!empty($basket['Basket']['free_shipping'])) ? "Free Shipping\n" : $shipping['ShippingCarrierService']['name'] . ' '; ?><?php 
if (!empty($shipping['Price']['price'])): ?>: <?php if($shipping['Price']['price'] > 0){ ?>£<?php echo $shipping['Price']['price']; ?><?php } else { echo "FREE\n"; } ?>
<?php endif; ?>

Order Total: £<?php echo $basketAndCustomer['Basket']['last_calculated_grand_total']; ?> 

--------------------------------------------------------------------

We'll notify you when your order has been shipped.
