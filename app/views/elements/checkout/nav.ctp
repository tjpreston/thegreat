<?php

$nav = array(
	'details' => '<span class="face1">Your</span> <span class="face2">Details</span>',
	'confirmation' => '<span class="face1">Confirm</span> <span class="face2">Order</span>',
	'payment' => '<span class="face1">Make</span> <span class="face2">Payment</span>',
	'complete' => '<span class="face1">Your</span> <span class="face2">Receipt</span>',
);

$count = count($nav);
$gridClass = 'grid_' . floor(24 / $count);

$i = 0;

echo '<ul class="grid_24 alpha omega border-top-bottom" id="checkout-nav">';

foreach ($nav as $key => $value) {
	$class = array($gridClass);

	if ($i == 0) {
		$class[] = 'alpha';
	}

	if ($i == ($count - 1)) {
		$class[] = 'omega';
	}

	if ($key == $step) {
		$class[] = 'active';
	}

	$options = array('class' => implode(' ', $class));

	$value = ($i + 1) .'. ' . $value;

	echo $this->Html->tag('li', $value, $options);

	$i++;
}

echo '</ul>';

?>
<div class="clearfix"></div>