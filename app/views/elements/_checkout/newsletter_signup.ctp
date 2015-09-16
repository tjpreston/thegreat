<?php

$options = array(
	1 => 'Yes',
	0 => 'No',
);

$attributes = array(
	'default' => 1,
	'legend' => false,
);

?>
<h4>&nbsp;</h4>
<div class="choice" style="padding-bottom: 16px;">
	<div class="formdiv"><?php
			echo $this->Form->radio('Basket.newsletter_signup', $options, $attributes);
	?></div>
	<p>Yes, I would love to be added to the Michel Herbelin mailing list.</p>
</div>