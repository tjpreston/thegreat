<form action="https://secure<?php echo (Configure::read('Worldpay.test_mode')) ? '-test' : ''; ?>.worldpay.com/wcc/purchase" method="POST">

	<?php if (Configure::read('Worldpay.test_mode')): ?>
		<input type="hidden" name="testMode" value="100">
	<?php endif; ?>

	<input type="hidden" name="instId" value="<?php echo Configure::read('Worldpay.installation_id'); ?>">
	
	<input type="hidden" name="cartId" value="<?php echo $basket['Basket']['id']; ?>">
	
	<input type="hidden" name="amount" value="<?php echo number_format($basket['Basket']['last_calculated_grand_total'], 2); ?>">
	
	<input type="hidden" name="currency" value="GBP">
	
	<input type="hidden" name="desc" value="<?php echo Configure::read('Site.name'); ?> Online Order">

	<input type="hidden" name="fixContact">
	<input type="hidden" name="hideContact">
	<input type="hidden" name="name" value="<?php echo $basket['Customer']['first_name'] . ' ' . $basket['Customer']['last_name']; ?>">
	<input type="hidden" name="address1" value="<?php echo $basket['CustomerBillingAddress']['address_1']; ?>">
	<input type="hidden" name="address2" value="<?php echo $basket['CustomerBillingAddress']['address_2']; ?>">
	<input type="hidden" name="address3" value="">
	<input type="hidden" name="town" value="<?php echo $basket['CustomerBillingAddress']['town']; ?>">
	<input type="hidden" name="region" value="<?php echo $basket['CustomerBillingAddress']['county']; ?>">
	<input type="hidden" name="postcode" value="<?php echo $basket['CustomerBillingAddress']['postcode']; ?>">
	<input type="hidden" name="country" value="<?php echo $basket['CustomerBillingAddressCountry']['iso']; ?>">
	<input type="hidden" name="tel" value="<?php echo $basket['Customer']['phone']; ?>">
	<input type="hidden" name="email" value="<?php echo $basket['Customer']['email']; ?>">
	
	