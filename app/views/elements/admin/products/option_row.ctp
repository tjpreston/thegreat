<li>	
	New Value: 
	<?php echo $form->select(
		'NewProductOptionValues.' . $option['ProductOption']['id'],
		$customOptionValuesList[$option['ProductOption']['custom_option_id']],
		0,
		array('empty' => array(0 => '-'), 'style' => 'width: 160px; margin-left: 10px;')
	); ?>
</li>