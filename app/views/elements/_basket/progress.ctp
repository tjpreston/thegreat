<?php

$steps = array(
	'bag' => 'My Bag',
	'details' => '1. Details',
	'support' => '2. Support',
	'confirm' => '3. Confirm',
	'receipt' => '4. Receipt',
);

echo '<ul class="progress">';
foreach($steps as $k => $v){
	echo '<li';
	if($selected == $k) echo ' class="selected"';
	echo '>';
	echo $v;
	echo '</li>';
}
echo '</ul>';

?>