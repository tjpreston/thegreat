<?php

$options = array(
	1 => 'Yes',
	0 => 'No',
);

$attributes = array(
	'default' => false,
	'legend' => false,
);

?>

<h2 class="shadowed">Additional Options</h2>

<div class="delivery-options">

	<div class="formdiv"><?php
		echo $this->Form->radio('Basket.gift_wrap', $options, $attributes + array('value' => $basket['Basket']['gift_wrap']));
	?></div>
	<h3>Free Gift Wrapping Service</h3>
	<p>Why not have your order gift wrapped for free? <a href="/ordering/giftwrap.html" class="fancybox">Whats included...</a></p>

	<div class="formdiv"><?php
		echo $this->Form->radio('Basket.watch_sizing', $options, $attributes + array('value' => $basket['Basket']['watch_sizing']));
	?></div>
	<h3>Free Watch Sizing Service</h3>
	<p>Would you like your watch sized for free? <a href="/ordering/watchsizing.html" class="fancybox">How to measure your wrist...</a></p>

</div>

<script>
	$(document).ready(function(){
		$('.delivery-options input').change(function(){
			$(this).parents('form').submit();
		});
	});
</script>