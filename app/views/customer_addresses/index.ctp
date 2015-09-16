<div class="grid_24">

	<div class="grid_18 prefix_6 alpha omega account-header">
		<h1><span class="face1">Address</span> <span class="face2">Book</span></h1>
	</div>

	<?php echo $this->element('customers/my_account_nav', array('step' => 'address')); ?>

	<div class="grid_18 omega">

		<p class="intro">Speed up the checkout process by saving your most frequently used addresses here.</p>

		<?php echo $session->flash(); ?>
		<?php if (!empty($records)): ?>
			
			<ol id="addresses" class="grid_18 alpha omega">
				
				<?php $i = 0; ?>
				
				<?php foreach ($records as $k => $record): ?>
				
					<?php
					
					$a = $record['CustomerAddress'];
					$last = ($i + 1 == count($records)) ? '' : '';

					$class = array('grid_9', 'push_1');

					if (($i % 2) == 0) {
						$class[] = 'alpha';
					}

					if (($i % 2) == 1) {
						$class[] = 'omega';
					}
					
					?>
					<li class="<?php echo implode(' ', $class); ?>"<?php if (($i % 2) == 0) echo ' style="clear:left;"'; ?>>
						<span class="address-num pull_1"><?php echo $i + 1; ?>.</span>
						<p class="address">
							<strong><?php echo h($a['first_name']); ?> <?php echo h($a['last_name']); ?></strong><br />
							<?php if (!empty($a['company_name'])): ?><?php echo h($a['company_name']); ?><br /><?php endif; ?>
							<?php echo h($a['address_1']); ?><br />
							<?php if (!empty($a['address_2'])): ?><?php echo h($a['address_2']); ?><br /><?php endif; ?>
							<?php echo h($a['town']); ?><br />
							<?php if (!empty($a['county'])): ?><?php echo h($a['county']); ?>, <?php endif; ?><?php echo h($a['postcode']); ?><br />
							<?php echo h($record['Country']['name']); ?>
						</p>
						<?php if(!empty($a['phone'])): ?>
						<p class="phone">
							Tel: <?php echo h($a['phone']); ?>
						</p>
						<?php endif; ?>
						<div class="actions">
							<a href="/customer_addresses/view/<?php echo intval($a['id']); ?>" class="btn btn-small">Edit</a>
							<a href="/customer_addresses/delete/<?php echo intval($a['id']); ?>" class="btn btn-small">Delete</a>
						</div>
						
						
					</li>
					
					<?php $i++; ?>
				
				<?php endforeach; ?>
				
			</ol>

		<?php else: ?>
		
			<p>You don't have any addresses saved in your address book.</p>
		
		<?php endif; ?>
		
		<p style="margin-top: 30px;">
			<a href="/customer_addresses/view" class="dual">
				<span class="face1">Add a </span> 
				<span class="face2">New Address</span>
			</a>
		</p>
		
	</div>
</div>