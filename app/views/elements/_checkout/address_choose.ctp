<?php

//$var = $type . 'Addresses';
//$addresses = $$var;

?>

<div class="address-choose" id="choose-<?php echo $type; ?>"<?php echo (!empty($hidden)) ? ' style="display: none;"' : ''; ?>>
	<?php $i = 0; ?>
	<?php foreach ($addresses as $k => $address): ?>
		<?php
		$a = $address['CustomerAddress'];
		$last = ($i + 1 == count($addresses)) ? '' : '';
		?>
		<div class="address-choice" style="clear: <?php echo ($i % 3 == 0) ? 'left' : 'none'; ?>">
			
			<?php if (!empty($a['company_name'])): echo h($a['company_name']).'<br />'; endif; ?>

			<?php if ($type == 'shipping'): ?>
				<strong><?php echo h($a['first_name']); ?> <?php echo h($a['last_name']); ?></strong><br />
			<?php endif; ?>
			
			<?php echo h($a['address_1']); ?><br />
			<?php if (!empty($a['address_2'])): ?><?php echo h($a['address_2']); ?><br /><?php endif; ?>
			<?php echo h($a['town']); ?><br />
			<?php if (!empty($a['county'])): ?><?php echo h($a['county']); ?>, <?php endif; ?><?php echo h($a['postcode']); ?><br />
			<?php echo h($address['Country']['name']); ?><br />
			
			<div class="select-address">
				<?php
				$checked = (!empty($record[$model]['id']) && ($record[$model]['id'] == $a['id'])) ? true : false;
				echo $form->radio(
					$model . '.id', 
					array($a['id'] => 'Select address'), 
					array('value' => false,	'checked' => $checked
				));
				?>
			</div>
			
		</div>
		<?php $i++; ?>
	<?php endforeach; ?>
	<div style="clear:both"></div>
</div>