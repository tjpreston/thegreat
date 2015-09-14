<!-- Begin LinkShare tracking image -->
<?php

$vatrate = Configure::read('Tax.rate');

$args = array();

// Merchant ID
$args['mid'] = Configure::read('LinkShare.merchant_id');

// Order reference
$args['ord'] = $order['Order']['ref'];

// Currency
$args['cur'] = 'GBP';

$skus = array();
$quantities = array();
$amounts = array();
$names = array();
foreach($order['OrderItem'] as $item){
	// Remove any pipe characters from item record - they cannot be included with this LinkShare image
	$item = str_replace('|', '_', $item);

	// Remove VAT and multiply by 100
	$price = $item['price'];
	$price = $price / ((100 + $vatrate) / 100);
	$price = $price * 100;

	$skus[] = $item['product_sku'];
	$quantities[] = $item['qty'];
	$amounts[] = $price;
	$names[] = $item['product_name'];
}

$args['skulist'] = implode('|', $skus);
$args['qlist'] = implode('|', $quantities);
$args['amtlist'] = implode('|', $amounts);
$args['namelist'] = implode('|', $names);

$url = '//track.linksynergy.com/ep';
$url .= '?' . http_build_query($args);

echo $this->Html->image($url, array('height' => 1, 'width' => 1)) . "\n";

?>
<!-- End LinkShare tracking image -->