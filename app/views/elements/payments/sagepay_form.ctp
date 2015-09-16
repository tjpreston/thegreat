<?php

$sagepayForm->setTxRef($txRef);
$sagepayForm->setBasket($basket);
$sagepayForm->setBasketItems($basketItems);

?>

<form action="<? echo $sagepayForm->getUrl(); ?>" method="POST" id="SagePayForm" name="SagePayForm">
	
	<input type="hidden" name="navigate" value="" />
	<input type="hidden" name="VPSProtocol" value="2.23" />
	<input type="hidden" name="TxType" value="<?php echo Configure::read('SagepayForm.tx_type'); ?>" />
	<input type="hidden" name="Vendor" value="<?php echo $sagepayForm->getVendorName(); ?>" />
	<input type="hidden" name="Crypt" value="<?php echo $sagepayForm->encrypt($sagepayForm->getPostData()); ?>" />
